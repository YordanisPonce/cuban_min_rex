@extends('layouts.app')
@php
    use Carbon\Carbon;
    use App\Models\Cart;
    Carbon::setLocale('es');
    $success = session('success');
    $error = session('error');
@endphp
@section('title', 'Remixes - ' . config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/remixes.css') }}" />
@endpush

@section('content')
    <!-- HERO -->
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

    <!-- Filters -->
    <div class="filters-bar">
        <form id="filter-form" class="filter-dropdowns">
            <select class="filter-dropdown" onChange="document.getElementById('filter-form').submit()" name="genre"
                value="{{ request()->get('genre') }}" data-aos="fade-in" data-aos-delay="300">
                <option value=""><span><label>Género:</label> TODOS</span></option>
                @foreach ($genres as $genre)
                    <option {{ request()->get('genre') === str_replace(' ', '_', $genre->name) ? 'selected' : '' }}
                        value="{{ str_replace(' ', '_', $genre->name) }}"><span><label>Género:</label>
                            {{ $genre->name }}</span></option>
                @endforeach
            </select>
            <select class="filter-dropdown" onChange="document.getElementById('filter-form').submit()" name="bpm"
                value="{{ request()->get('bpm') }}" data-aos="fade-in" data-aos-delay="500">
                <option value=""><span><label>BPM:</label> TODOS</span></option>
                @foreach ($bpms as $bpm)
                    <option {{ request()->get('bpm') === str_replace(' ', '_', $bpm->bpm) ? 'selected' : '' }}
                        value="{{ str_replace(' ', '_', $bpm->bpm) }}"><span><label>BPM:</label>
                            {{ $bpm->bpm }}</span></option>
                @endforeach
            </select>
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

    <!-- Tracks -->
    <div class="tracks-container" id="tracks-list" data-aos="fade-up" data-aos-delay="300">
        @foreach ($tracks as $track)
            @include('partials.remix-card', ['item' => $track])
        @endforeach
    </div>

    <div class="container" data-aos="fade-up" data-aos-delay="300">
        @if ($tracks->count() === 0)
            <div class="empty" style="color: var(--fg-muted) !important;">
                <span><i class="fas fa-face-grin-beam-sweat"></i></span>
                <h3>No se han enontrado <span class="text-primary">REMIXES</span> que cumplan con los filtros</h3>
            </div>
        @endif
        {{ $tracks->onEachSide(1)->links() }}
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

    <div style="height:80px"></div>
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

        function cleanCards() {
            document.querySelectorAll('.track-card').forEach(card => {
                const wf = card.querySelector('.waveform').classList.remove('playing');
                card.querySelector('.play-btn i').className = 'fa-solid fa-play';
            });
        }

        function closePlayer() {
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
                document.querySelectorAll('.track-card').forEach(card => {
                    const wf = card.querySelector('.waveform');
                    wf.classList.remove('playing');
                });
                return
            }
            el.classList.add('active');
            let waves = el.querySelector(".waveform");
            isPlaying ? waves.classList.add('playing') : waves.classList.remove('playing');
            let trackData = document.getElementById(currentTrack);
            document.getElementById('player-img').src = trackData.querySelector('img').src;
            document.getElementById('player-title').textContent = trackData.querySelector('.track-title').textContent;
            document.getElementById('player-artist').textContent = trackData.querySelector('.track-artist').textContent;
            const icon = document.querySelector('#player-play-btn i');
            icon.className = isPlaying ? 'fa-solid fa-pause' : 'fa-solid fa-play';
            // Update mini-player buttons
            document.querySelectorAll('.track-card').forEach(card => {
                const id = card.id;
                const icon = card.querySelector('.play-btn i');
                if (id == currentTrack && isPlaying) {
                    icon.className = 'fa-solid fa-pause'
                } else {
                    icon.className = 'fa-solid fa-play'
                }
                const wf = card.querySelector('.waveform');
                if (id == currentTrack && isPlaying) {
                    wf.classList.add('playing');
                } else {
                    wf.classList.remove('playing');
                }
            });
        }

        function setLoader(e) {
            document.querySelectorAll('.track-card').forEach(card => {
                const id = card.id;
                const btn = card.querySelector('.play-btn i');
                if (id == e) {
                    btn.className = 'fa fa-spinner fa-spin';
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

        renderExclusives();

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
    @isset($error)
        <script>
            Swal.fire({
                title: 'Error',
                text: '{{ $error }}',
                icon: 'error'
            });
        </script>
    @endisset
    @isset($success)
        <script>
            Swal.fire({
                title: ' ',
                text: '{{ $success }}',
                icon: 'success'
            });
        </script>
    @endisset
@endpush
