@extends('layouts.app')
@php
    use Carbon\Carbon;
    Carbon::setLocale('es');
    $success = session('success');
    $error = session('error');
@endphp
@section('title', 'Packs - '.config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/playlist.css') }}" />
    <style>
        .filters-bar{
            max-width: 1400px;
        }

        .card-title:hover{
            color: white !important;
        }
    </style>
@endpush

@section('content')
    <section class="hero container">
        <div class="hero-slides" id="heroSlides"></div>
        <div class="hero-gradient"></div>
        <div class="hero-gradient-b"></div>
        <div class="container">
            <h1 data-aos="fade-right" data-aos-delay="300">PACKS<br><span class="accent">PARA DJS
                    LATINOS</span></h1>
            <p data-aos="fade-right" data-aos-delay="500">Descarga edits, intros, mashups y más musical para hacer historia
                en la pista.</p>
            <a data-aos="zoom-right" data-aos-delay="700" class="btn-primary" style="padding:12px 28px;font-size:.9rem"
                href="{{ route('plans') }}"><i class="fas fa-crown"></i> REMIXES
                    EXCLUSIVOS</a>
            <div class="hero-stats">
                <span data-aos="fade-right" data-aos-delay="900"><span class="dot"></span> +10 PACKS
                    EXCLUSIVOS</span>
                <span data-aos="fade-right" data-aos-delay="1100">✓ ACTUALIZACIONES SEMANALES</span>
            </div>
        </div>
    </section>

    <!-- Filters -->
    <div class="filters-bar">
        <form id="filter-form" class="filter-dropdowns">
            <select class="filter-dropdown" onChange="document.getElementById('filter-form').submit()" name="dj"
                data-aos="fade-in" data-aos-delay="700">
                <option value=""><span><label>DJ:</label> TODOS</span></option>
                @foreach ($djs as $dj)
                    <option {{ request()->get('dj') === str_replace(' ', '_', $dj->name) ? 'selected' : '' }}
                        value="{{ str_replace(' ', '_', $dj->name) }}"><span><label>DJ:</label> {{ $dj->name }}</span>
                    </option>
                @endforeach
            </select>
            <input type="text" class="filter-dropdown" placeholder="Nombre: "
                onChange="document.getElementById('filter-form').submit()" name="title"
                value="{{ request()->get('title') }}" data-aos="fade-in" data-aos-delay="900" />
        </form>
    </div>

    <div class="container">
        <div class="section" data-aos="fade-up" data-aos-delay="300">
            <div class="section-title"><div><span class="dot"></span> PACKS <span class="gold">PARA DJS</span></div></div>
            <div class="cards-grid">
                @foreach ($packs as $pack)
                    @include('partials.pack-card', ['item' => $pack])
                @endforeach
            </div>
            @if ($packs->count() === 0)
                <div class="empty">
                    <span><i class="fas fa-close"></i></span>
                    <h3>No se han enontrado <span class="text-primary">PACKS</span> que cumplan con los filtros</h3>
                </div>
            @endif
    
            <div class="container">
                {{ $packs->onEachSide(1)->links() }}
            </div>
        </div>
    </div>

    <!-- BOTTOM PLAYER -->
    <div class="bottom-player container" id="bottom-player">
        <div class="player-inner">
            <div class="player-track">
                <img id="player-img" src="" alt="">
                <div class="track-info">
                    <div class="track-title" id="player-title">—</div>
                    <div class="track-artist" id="player-artist">—</div>
                </div>
            </div>
            <div class="player-controls">
                <div class="waveform">
                    @for ($i = 0; $i < 60; $i++)  
                        <div class="bar"></div>
                    @endfor
                </div>
                <div class="controls">
                    <button class="disabled"><i class="fa-solid fa-backward-fast"></i></button>
                    <!-- <button><i class="fa-solid fa-backward-step"></i></button> -->
                    <button class="main-play" id="player-play-btn"><i class="fa-solid fa-play"></i></button>
                    <!-- <button><i class="fa-solid fa-forward-step"></i></button> -->
                    <button class="disabled"><i class="fa-solid fa-forward-fast"></i></button>
                    <div class="close">
                        <button onclick="closePlayer()"><i class="fa-solid fa-close"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>

        const audioPlayer = document.createElement('audio');

        // ===== PLAYER STATE =====
        let currentTrack = null;
        let isPlaying = false;
        let currentTime = 0;
        let duration = 0;
        let timerInterval = null;

        function cleanCards(){
            document.querySelectorAll('.playlist-card').forEach(card => {
                card.classList.remove('playing');
                card.querySelector('.play-overlay i').className = 'fa-solid fa-play';
            });
        }

        function closePlayer(){
            const player = document.getElementById('bottom-player');
            player.classList.remove('active');
            player.querySelector(".waveform").classList.remove('playing');
            cleanCards();
            isPlaying = false;
            audioPlayer.pause();
            currentTrack = null;
        }

        function updatePlayerUI() {
            isPlaying ? audioPlayer.play() : audioPlayer.pause();
            const el = document.getElementById('bottom-player');
            if (!currentTrack) {
                el.classList.remove('active');
                el.querySelector(".waveform").classList.remove('playing');
                cleanCards();
                return
            }
            el.classList.add('active');
            let waves = el.querySelector(".waveform");
            isPlaying ? waves.classList.add('playing') : waves.classList.remove('playing');
            let trackData = document.getElementById(currentTrack);
            document.getElementById('player-img').src = trackData.querySelector('img').src;
            document.getElementById('player-title').textContent = trackData.querySelector('.pack-name').textContent;
            document.getElementById('player-artist').textContent = trackData.querySelector('.dj-name').textContent;
            const icon = document.querySelector('#player-play-btn i');
            icon.className = isPlaying ? 'fa-solid fa-pause' : 'fa-solid fa-play';
            // Update mini-player buttons
            document.querySelectorAll('.playlist-card').forEach(card => {
                const id = card.id;
                const icon = card.querySelector('.play-overlay i');
                card.classList.remove('playing');
                if (id == currentTrack && isPlaying) {
                    icon.className = 'fa-solid fa-pause';
                    card.classList.add('playing');
                } else {
                    icon.className = 'fa-solid fa-play'
                }
            });
        }

        function setLoader(e){
            document.querySelectorAll('.playlist-card').forEach(card => {
                const id = card.id;
                const btn = card.querySelector('.play-overlay i');
                if (id == e) {
                    btn.className = 'fa fa-spinner fa-spin';
                    card.classList.add('playing');
                }
            });
        }

        function playTrack(id, url) {
            setLoader(id);

            audio = new Audio(url);

            audio.addEventListener("canplaythrough", () => {
                audioPlayer.src = audio.src;
                audioPlayer.play();
            });

            audioPlayer.addEventListener("play", () => {
                currentTrack = id;
                currentTime = 0;
                duration = 120;
                isPlaying = true;
                updatePlayerUI();
            });

            audioPlayer.addEventListener("pause", () => {
                isPlaying = false;
                updatePlayerUI();
            });

            audioPlayer.addEventListener("ended", () => {
                isPlaying = false;
                updatePlayerUI();
            });
        }

        function togglePlay() {
            if (!currentTrack) return;
            isPlaying = !isPlaying;
            if (isPlaying) {
                audioPlayer.play();
            } else {
                audioPlayer.pause();
            }
            updatePlayerUI();
        }

        function handlePlay(id) {
            if (currentTrack && currentTrack === id) {
                togglePlay();
                return
            }
            cleanCards();
            let url = document.getElementById(`${id}`).dataset.intro;
            playTrack(id, url);
        }

        document.getElementById('player-play-btn').addEventListener('click', togglePlay);
    </script>
    <script>
        // HERO SLIDESHOW
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
@endpush
