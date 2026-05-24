@extends('layouts.app')

@section('title', 'Descargar ' . $file->name . ' - ' . config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/free-download.css') }}">
    <style>
        :root {
            --muted: #8a7a66;
            --border: #2a2520;
            --surface: #1e1b17;
            --danger: #e04040;
            --success: #2ecc71;
        }
        @media(max-width: 800px){
            .file-card{
                position: unset;
            }
        }
    </style>
@endpush

@section('content')

    <section class="hero container">
        <div class="hero-slides" id="heroSlides"></div>
        <div class="hero-gradient"></div>
        <div class="hero-gradient-b"></div>
        <div class="container">
            <h1 data-aos="fade-right" data-aos-delay="300">REMIXES<br>EXCLUSIVOS<br><span class="accent">PARA DJS
                    LATINOS</span></h1>
            <p data-aos="fade-right" data-aos-delay="500">Descarga edits, intros, mashups y más musical para hacer historia
                en la pista.</p>
            <a data-aos="zoom-right" data-aos-delay="700" class="btn-primary" style="padding:12px 28px;font-size:.9rem"
                href="{{ route('plans') }}"><i class="fas fa-crown"></i> REMIXES
                EXCLUSIVOS</a>
            <div class="hero-stats">
                <span data-aos="fade-right" data-aos-delay="900"><span class="dot"></span> +1000 REMIXES
                    EXCLUSIVOS</span>
                <span data-aos="fade-right" data-aos-delay="1100">✓ ACTUALIZACIONES SEMANALES</span>
            </div>
        </div>
    </section>

    <div class="main">
        <!-- FILE INFO -->
        <div class="file-card">
            <div class="file-cover">
                <img src="{{ $file->getPosterUrl() ?? config('app.logo_alter') }}" alt="Portada del remix">
                <span class="free-badge">Gratis</span>
                <div class="play-overlay"><i class="fas fa-play"></i></div>
            </div>

            <div class="file-title">{{ $file->name }}</div>
            <div class="file-artist"><i class="fas fa-user-circle" style="margin-right:.3rem;color:var(--primary);"></i>
                {{ $file->user?->name ?? 'Desconocido' }}</div>

            <div class="file-meta">
                <div class="meta-item"><i class="fas fa-file-audio"></i> Formato
                    <strong style="text-transform: uppercase">{{ $file->getExtension() }}</strong>
                </div>
                <div class="meta-item"><i class="fas fa-hdd"></i> Tamaño <strong>{{ $file->getSize() }}</strong></div>
                <div class="meta-item"><i class="fas fa-clock"></i> Duración <strong>-:-</strong></div>
                <div class="meta-item"><i class="fas fa-tachometer-alt"></i> BPM <strong>{{ $file->bpm }}</strong></div>
                <div class="meta-item"><i class="fas fa-music"></i> Key <strong>{{ $file->musical_note ?? '-' }}</strong>
                </div>
                <div class="meta-item"><i class="fas fa-calendar"></i> Subido
                    <strong>{{ $file->created_at->format('d M Y') }}</strong>
                </div>
            </div>

            <div class="file-tags">
                @php
                    $genres = $file->categories()->pluck('name')->toArray();
                @endphp
                @foreach ($genres as $genre)
                    <span>{{ $genre }}</span>
                @endforeach
            </div>

            <div class="file-desc">
                <i class="fas fa-info-circle"></i> {{ $file->description ?? 'No hay descripción disponible para este archivo.' }}
            </div>

            <div class="file-stats">
                <span><i class="fas fa-download"></i> {{ $file->downloads->count() }} descargas</span>
            </div>
        </div>

        <!-- FORM -->
        <div class="form-card">
            <div class="steps">
                <div class="step active"><i class="fas fa-user"></i> Datos</div>
                <span class="step-arrow"><i class="fas fa-chevron-right"></i></span>
                <div class="step"><i class="fas fa-download"></i> Descarga</div>
            </div>

            <div class="form-header">
                <i class="fas fa-envelope-open-text"></i>
                <div>
                    <h2>Obtén tu descarga</h2>
                    <p>Completa los datos para desbloquear el archivo</p>
                </div>
            </div>

            <form id="downloadForm" novalidate action="{{ route('file.free.download.post', $file->id) }}" method="POST">
                @csrf

                <input type="hidden" name="file_id" value="{{ $file->id }}">

                <div class="form-group" id="emailGroup">
                    <label for="email">Correo electrónico <span class="required">*</span></label>
                    <input type="email" id="email" name="email" placeholder="tu@email.com" required>
                    <div class="error-msg"><i class="fas fa-exclamation-circle"></i> Introduce un correo válido.</div>
                </div>

                <div class="form-group" id="phoneGroup">
                    <label for="phone">Número de teléfono <span class="required">*</span></label>
                    <input type="tel" id="phone" name="phone" placeholder="+1 (555) 123-4567" required>
                    <div class="error-msg"><i class="fas fa-exclamation-circle"></i> Introduce un número de teléfono válido.
                    </div>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="terms" name="terms">
                    <label for="terms">Acepto los <a href="{{ route('terms') }}">Términos de Uso</a> y la <a
                            href="{{ route('privacy') }}">Política de
                            Privacidad</a>.
                        <span class="required">*</span></label>
                </div>

                <button type="submit" class="download-btn" id="submitBtn">
                    <i class="fas fa-download"></i> Descargar archivo gratis
                </button>
            </form>

            <div class="trust-badges">
                <span><i class="fas fa-shield-alt"></i> Datos seguros</span>
                <span><i class="fas fa-bolt"></i> Descarga instantánea</span>
                <span><i class="fas fa-infinity"></i> Sin límite</span>
            </div>
        </div>
    </div>

    <!-- TOAST -->
    <div class="toast" id="toast"><i class="fas fa-check-circle"></i><span id="toastMsg">Mensaje</span></div>
@endsection

@push('scripts')
    <script>
        const heroImages = @json($banners);

        const slidesEl = document.getElementById('heroSlides');
        let currentSlide = 0;
        heroImages.forEach((src, i) => {
            const slide = document.createElement('div');
            slide.className = 'hero-slide' + (i === 0 ? ' active' : '');
            slide.innerHTML = `<img src="${src}" alt="DJ hero ${i + 1}">`;
            slidesEl.appendChild(slide);
        });

        function goToSlide(n) {
            document.querySelectorAll('.hero-slide').forEach((s, i) => s.classList.toggle('active', i === n));
            currentSlide = n;
        }
        setInterval(() => goToSlide((currentSlide + 1) % heroImages.length), 5000);

        const form = document.getElementById('downloadForm');
        const emailInput = document.getElementById('email');
        const phoneInput = document.getElementById('phone');
        const termsInput = document.getElementById('terms');
        const emailGroup = document.getElementById('emailGroup');
        const phoneGroup = document.getElementById('phoneGroup');
        const toast = document.getElementById('toast');
        const toastMsg = document.getElementById('toastMsg');

        function showToast(msg, isError = false) {
            toastMsg.textContent = msg;
            toast.querySelector('i').className = isError ? 'fas fa-exclamation-circle' : 'fas fa-check-circle';
            toast.classList.toggle('error', isError);
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3500);
        }

        function validateEmail(v) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
        }

        function validatePhone(v) {
            return v.replace(/\D/g, '').length >= 7;
        }

        emailInput.addEventListener('input', () => {
            emailGroup.classList.remove('error');
        });
        phoneInput.addEventListener('input', () => {
            phoneGroup.classList.remove('error');
        });

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            let ok = true;

            if (!validateEmail(emailInput.value.trim())) {
                emailGroup.classList.add('error');
                ok = false;
            }
            if (!validatePhone(phoneInput.value.trim())) {
                phoneGroup.classList.add('error');
                ok = false;
            }
            if (!termsInput.checked) {
                showToast('Debes aceptar los términos para continuar.', true);
                ok = false;
            }

            if (ok) {
                showToast('¡Descarga iniciada!');
                const btn = document.getElementById('submitBtn');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Preparando...';
                btn.disabled = true;

                formData = new FormData(form);

                fetch(form.action, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector(
                            'input[name="_token"]').value
                    },
                    body: formData
                })
                .then(async res => {
                    if (!res.ok) {
                        const errorData = await res.json();
                        showToast(errorData.message || 'Error en la descarga', true);
                    }
                    btn.innerHTML = '<i class="fas fa-check"></i> Descargado';
                    btn.style.background = 'var(--success)';
                    const blob = await res.blob();
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement("a");
                    a.href = url;
                    a.download = "{{ $file->name }}.{{ $file->getExtension() }}";
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    URL.revokeObjectURL(url);
                });
            }
        });
    </script>
@endpush
