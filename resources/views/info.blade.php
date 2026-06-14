@extends('layouts.app')

@section('title', 'Info ' . $item['name'] . ' - ' . config('app.name'))

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

            .action-btns{
                position: unset !important;
                margin-top: 1rem;
            }
        }
        .form-card{
            height: 100%;
            position: relative;
        }
        .action-btns{
            position: absolute;
            bottom: 1.5rem;
            right: 1.5rem;
            left: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        .action-btns a{
            width: 100%
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
                <img src="{{ $item['poster'] ?? config('app.logo_alter') }}" alt="Portada del remix">
                <div class="play-overlay" onclick="playTrack()"><i class="fas fa-play"></i></div>
            </div>
        </div>

        <div class="form-card">
            <div class="file-title">{{ $item['name'] }}</div>
            <div class="file-artist"><i class="fas fa-user-circle" style="margin-right:.3rem;color:var(--primary);"></i>
                {{ $item['artist'] ?? 'Desconocido' }}</div>

            <div class="file-meta">
                <div class="meta-item"><i class="fas fa-file-audio"></i> Formato
                    <strong style="text-transform: uppercase">{{ $item['ext'] }}</strong>
                </div>
                <div class="meta-item"><i class="fas fa-hdd"></i> Tamaño <strong>{{ $item['size'] }}</strong></div>
                <div class="meta-item"><i class="fas fa-clock"></i> Duración <strong>-:-</strong></div>
                <div class="meta-item"><i class="fas fa-tachometer-alt"></i> BPM <strong>{{ $item['bpm'] ?? '-' }}</strong></div>
                <div class="meta-item"><i class="fas fa-music"></i> Key <strong>{{ $item['note'] ?? '-' }}</strong>
                </div>
                <div class="meta-item"><i class="fas fa-calendar"></i> Subido
                    <strong>{{ $item['date'] }}</strong>
                </div>
            </div>

            <div class="file-tags">
                @php
                    $genres = $item['categories'] ?? [];
                @endphp
                @foreach ($genres as $genre)
                    <span>{{ $genre }}</span>
                @endforeach
            </div>

            <div class="file-desc">
                <i class="fas fa-info-circle"></i> {{ $item['description'] ?? 'No hay descripción disponible para este archivo.' }}
            </div>

            <div class="file-stats">
                <span><i class="fas fa-download"></i> {{ $item['downloads'] ?? 0 }} descargas</span>
            </div>

            <div class="action-btns">
                @if($item['canBeDownload'])
                    <a class="download-btn" href="{{ $item['download_link'] }}"><i class="fa fa-download"></i> Descargar</a>
                @else
                    <a class="download-btn" href="{{ $item['addToCart'] }}"><i class="ti tabler-shopping-cart-plus"></i> Añadir al Carrito</a>
                @endif
            </div>
        </div>
    </div>

    @include('partials.bottom-player')
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
    </script>
    <script>
        const audioPlayer = document.getElementById('plyr-audio-player');

        // ===== PLAYER STATE =====
        let isPlaying = false;
        let currentTime = 0;
        let duration = 0;
        let timerInterval = null;

        function closePlayer() {
            const player = document.getElementById('bottom-player');
            player.classList.remove('active');
            isPlaying = false;
            audioPlayer.pause();
            currentTrack = null;
        }

        function updatePlayerUI() {
            isPlaying ? audioPlayer.play() : audioPlayer.pause();
            const el = document.getElementById('bottom-player');
            el.classList.add('active');
            document.getElementById('player-img').src = "{{ $item['poster'] ?? config('app.logo_alter') }}";
            document.getElementById('player-title').textContent = "{{ $item['name'] }}";
            document.getElementById('player-artist').textContent = "{{ $item['artist'] ?? 'Desconocido' }}";
        }

        function setLoader() {
            document.querySelector('.play-overlay').style.opacity = '1';
            document.querySelector('.play-overlay i').className = 'fa fa-spinner fa-spin';
        }

        function setPause() {
            document.querySelector('.play-overlay i').className = 'fas fa-pause';
        }

        function reset() {
            document.querySelector('.play-overlay').style.opacity = '0';
            document.querySelector('.play-overlay i').className = 'fas fa-play';
        }


        function playTrack() {
            setLoader();

            const url = "{{ $item['intro'] }}";

            audio = new Audio(url.replace('cubanminiles', "cubanmin/files"));

            audio.addEventListener("canplaythrough", () => {
                audioPlayer.src = audio.src;
                audioPlayer.play();
            });

            audioPlayer.addEventListener("play", () => {
                currentTime = 0;
                duration = 120;
                isPlaying = true;
                setPause();
                updatePlayerUI();
            });

            audioPlayer.addEventListener("pause", () => {
                isPlaying = false;
                reset();
                updatePlayerUI();
            });

            audioPlayer.addEventListener("ended", () => {
                isPlaying = false;
                reset();
                updatePlayerUI();
            });
        }
    </script>
@endpush
