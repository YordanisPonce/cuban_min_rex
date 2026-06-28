@extends('layouts.app')

@section('title', 'Preparando descarga – ' . config('app.name'))

@push('styles')
    <style>
        .download-status-card {
            max-width: 640px;
            margin: 80px auto;
            padding: 32px;
            border-radius: 16px;
            background: var(--card-bg, #111);
            border: 1px solid rgba(255, 255, 255, 0.08);
            text-align: center;
        }

        .download-status-card h1 {
            font-size: 1.5rem;
            margin-bottom: 12px;
        }

        .download-status-card p {
            color: var(--fg-muted, #aaa);
            margin-bottom: 20px;
        }

        .progress-bar-wrap {
            width: 100%;
            height: 10px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 999px;
            overflow: hidden;
            margin: 24px 0 12px;
        }

        .progress-bar-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--primary, #ff2ec4), #7c5cff);
            transition: width .4s ease;
        }

        .status-meta {
            font-size: .9rem;
            color: var(--fg-muted, #aaa);
        }

        .spinner {
            width: 42px;
            height: 42px;
            border: 3px solid rgba(255, 255, 255, 0.15);
            border-top-color: var(--primary, #ff2ec4);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .status-error {
            color: #ff6b6b;
        }
    </style>
@endpush

@section('content')
    <div class="download-status-card">
        <div class="spinner" id="status-spinner"></div>
        <h1>Preparando tu descarga</h1>
        <p>
            Estamos empaquetando <strong>{{ $playlist->name }}</strong>.
            Las playlists grandes pueden tardar varios minutos. No cierres esta página.
        </p>

        <div class="progress-bar-wrap">
            <div class="progress-bar-fill" id="progress-bar"></div>
        </div>

        <div class="status-meta" id="status-text">Iniciando...</div>
        <div class="status-meta status-error" id="status-error" style="display:none;"></div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const statusUrl = @json($statusUrl);
            const progressBar = document.getElementById('progress-bar');
            const statusText = document.getElementById('status-text');
            const statusError = document.getElementById('status-error');
            const spinner = document.getElementById('status-spinner');

            function updateProgress(added, total) {
                if (!total || total <= 0) {
                    statusText.textContent = 'Procesando archivos...';
                    return;
                }

                const percent = Math.max(5, Math.round((added / total) * 100));
                progressBar.style.width = percent + '%';
                statusText.textContent = 'Archivos procesados: ' + added + ' / ' + total;
            }

            async function pollStatus() {
                try {
                    const response = await fetch(statusUrl, {
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin',
                    });

                    if (!response.ok) {
                        throw new Error('No se pudo consultar el estado.');
                    }

                    const data = await response.json();

                    if (data.status === 'ready' && data.download_url) {
                        progressBar.style.width = '100%';
                        statusText.textContent = 'Descarga lista. Redirigiendo...';
                        window.location.href = data.download_url;
                        return;
                    }

                    if (data.status === 'failed') {
                        spinner.style.display = 'none';
                        statusError.style.display = 'block';
                        statusError.textContent = data.message || 'No se pudo generar el ZIP.';
                        statusText.textContent = 'La descarga falló.';
                        return;
                    }

                    updateProgress(data.tracks_added || 0, data.tracks_total || 0);
                    setTimeout(pollStatus, 5000);
                } catch (error) {
                    setTimeout(pollStatus, 8000);
                }
            }

            pollStatus();
        })();
    </script>
@endpush
