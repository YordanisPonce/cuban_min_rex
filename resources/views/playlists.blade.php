@extends('layouts.app')

@section('title', 'PlayLists - ' . config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/playlist.css') }}" />
    <style>
        .filters-bar {
            max-width: 1400px;
        }
    </style>
@endpush

@section('content')
    <!-- HERO -->
    <section class="hero container">
        <div class="hero-bg">
            <img style="width: 100%" src="{{ asset('assets/img/hero-base.jpeg') }}" alt="hero-banner">
            <div class="overlay"></div>
        </div>
        <div class="container">
            <h1 data-aos="fade-right" data-aos-delay="300">PLAYLISTS<br>PARA <span class="text-primary">DJs LATINOS</span>
            </h1>
            <p data-aos="fade-right" data-aos-delay="500">Sets listos para pista, organizados por energía, género y momento.
            </p>
            <div class="hero-btns">
                <button class="btn btn-primary" data-aos="fade-right" data-aos-delay="700"><i class="fas fa-play"></i>
                    EXPLORAR PLAYLISTS</button>
                <button class="btn btn-outline" data-aos="fade-right" data-aos-delay="900"><i class="fas fa-crown"></i>
                    CREAR MI PLAYLIST</button>
            </div>
        </div>
    </section>

    {{-- 
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
    </div> --}}

    <div class="container">
        <div class="section" data-aos="fade-up" data-aos-delay="300">
            <div class="section-title">
                <div><span class="dot"></span><span class="gold">DESTACADAS</span></div>
                <a class="btn btn-outline" href="{{ route('playlist.list') }}">VER TODAS</a>
            </div>
            <div class="cards-grid">
                @foreach ($playlists as $playlist)
                    @include('partials.playlist-card', ['item' => $playlist])
                @endforeach
            </div>
        </div>


        <div class="section" data-aos="fade-up" data-aos-delay="500">
            <div class="section-title">
                <div><span class="dot"></span> POR <span class="gold">GÉNERO</span></div>
            </div>
            <div class="cards-grid">
                @foreach ($folders as $f)
                    @include('partials.folder-card', ['item' => $f])
                @endforeach
            </div>
        </div>
    </div>

    <!-- BOTTOM PLAYER -->
    <div class="bottom-player" id="bottom-player">
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
                    <button onclick="playPreviousTrack()"><i class="fa-solid fa-backward-fast"></i></button>
                    <!--<button><i class="fa-solid fa-backward-step"></i></button>-->
                    <button class="main-play" id="player-play-btn"><i class="fa-solid fa-play"></i></button>
                    <!--<button><i class="fa-solid fa-forward-step"></i></button>-->
                    <button onclick="playNextTrack()"><i class="fa-solid fa-forward-fast"></i></button>
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
        let tracks = [];

        async function getTracks(id) {
            const res = await fetch(`/playlists-get-tracks/${id}`);
            let data;

            try {
                data = await res.json();
            } catch {
                Swal.fire("Error", "PlayList no encontrada", "error");
            }

            if (res.ok) {
                tracks = data.tracks;
            } else {
                Swal.fire("Error", "No se encontraron tracks", "error");
            }
        }

        // ===== PLAYER STATE =====
        let currentTrack = null;
        let isPlaying = false;
        let currentTime = 0;
        let duration = 0;
        let timerInterval = null;
        let randomPlay = false;

        const audioPlayer = document.createElement('audio');

        function cleanCards() {
            document.querySelectorAll('.playlist-card').forEach(card => {
                card.querySelector('.play-overlay i').className = 'fa-solid fa-play';
                card.classList.remove('playing');
            });
        }

        function closePlayer() {
            const player = document.getElementById('bottom-player');
            player.classList.remove('active');
            player.querySelector(".waveform").classList.remove('playing');
            isPlaying = false;
            cleanCards();
            audioPlayer.pause();
            currentTrack = null;
        }

        function updatePlayerUI() {
            isPlaying ? audioPlayer.play() : audioPlayer.pause();
            const el = document.getElementById('bottom-player');
            if (!currentTrack) {
                el.classList.remove('active');
                el.querySelector(".waveform").classList.remove('playing');
                return
            }
            el.classList.add('active');
            let waves = el.querySelector(".waveform");
            isPlaying ? waves.classList.add('playing') : waves.classList.remove('playing');
            document.getElementById('player-img').src = currentTrack.img;
            document.getElementById('player-title').textContent = '#' + (getRemixIndex(currentTrack.id) + 1) + ' ' +
                currentTrack.title;
            document.getElementById('player-artist').textContent = currentTrack.artist;
            const icon = document.querySelector('#player-play-btn i');
            icon.className = isPlaying ? 'fa-solid fa-pause' : 'fa-solid fa-play';
            document.querySelectorAll('.playlist-card').forEach(card => {
                const id = card.id;
                const btn = card.querySelector('.play-overlay i');
                card.classList.remove('playing');
                if (id == currentTrack.playlist_id) {
                    btn.className = 'fa-solid fa-pause';
                    card.classList.add('playing');
                }
            });
        }

        function setLoader(e) {
            document.querySelectorAll('.playlist-card').forEach(card => {
                const id = card.id;
                const btn = card.querySelector('.play-overlay i');
                if (id == e) {
                    btn.className = 'fa fa-spinner fa-spin';
                    card.classList.add('playing');
                }
            });
        }

        function playTrack(track) {

            audio = new Audio(track.url);

            audio.addEventListener("canplaythrough", () => {
                audioPlayer.src = audio.src;
                audioPlayer.play();
            });

            audioPlayer.addEventListener("play", () => {
                currentTrack = track;
                currentTime = 0;
                duration = track.duration;
                isPlaying = true;
                updatePlayerUI();
            });

            audioPlayer.addEventListener("pause", () => {
                isPlaying = false;
                updatePlayerUI();
            });

            audioPlayer.addEventListener("ended", () => {
                if (currentTrack != tracks[tracks.length - 1]) {
                    playNextTrack();
                } else {
                    isPlaying = false;
                    updatePlayerUI();
                }
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

        // ===== EVENT HANDLERS =====
        async function handlePlay(id) {
            cleanCards();
            setLoader(id);

            if (currentTrack && currentTrack.id === id) {
                togglePlay();
                return
            }

            await getTracks(id);

            const r = tracks[0];

            if (r) {
                playTrack(r);
            } else {
                Swal.fire("Error", "Track not found", "error");
                cleanCards();
            }
        }

        function getRemixIndex(id) {
            return tracks.findIndex(x => x.id == id);
        }

        //reproducir siguiente al currenttrack si es el ultimo reproducir el primero
        function playNextTrack() {
            if (!currentTrack) return;
            const idx = getRemixIndex(currentTrack.id);
            if (idx >= 0 && idx < tracks.length - 1) {
                playTrack(tracks[idx + 1]);
            } else {
                playTrack(tracks[0]);
            }
        }

        // reproducir anterior al currenttrack si es el primero reproducir el ultimo
        function playPreviousTrack() {
            if (!currentTrack) return;
            const idx = getRemixIndex(currentTrack.id);
            if (idx > 0) {
                playTrack(tracks[idx - 1]);
            } else {
                playTrack(tracks[tracks.length - 1]);
            }
        }

        document.getElementById('player-play-btn').addEventListener('click', togglePlay);
    </script>
@endpush
