@extends('layouts.app')

@section('title', 'PlayLists - ' . config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/playlist.css') }}" />
    <style>
        .filters-bar {
            max-width: 1400px;
        }
        .btn-next,
        .btn-prev{
            color: white;
            cursor: pointer;
            transition: all .2s
        }
        .btn-next:hover,.btn-prev:hover {
            transform: scale(1.2);
            color: var(--primary)
        }
        .track-title{
            justify-content: space-between;
            display: flex;
            gap: 20px;
        }
    </style>
@endpush

@section('content')
    <!-- HERO -->
    <section class="hero container">
        <div class="hero-slides" id="heroSlides"></div>
        <div class="hero-gradient"></div>
        <div class="hero-gradient-b"></div>
        <div class="container">
            <h1 data-aos="fade-right" data-aos-delay="300">PLAYLISTS<br><span class="accent">PARA DJS
                    LATINOS</span></h1>
            <p data-aos="fade-right" data-aos-delay="500">Sets listos para pista, organizados por energía, género y momento.</p>
            <a data-aos="zoom-right" data-aos-delay="700" class="btn-primary" style="padding:12px 28px;font-size:.9rem"
                href="{{ route('plans') }}"><i class="fas fa-crown"></i> EXPLORAR PLAYLISTS</a>
            <div class="hero-stats">
                <span data-aos="fade-right" data-aos-delay="900"><span class="dot"></span> +100 PLAYLISTS
                    EXCLUSIVAS</span>
                <span data-aos="fade-right" data-aos-delay="1100">✓ ACTUALIZACIONES SEMANALES</span>
            </div>
        </div>
    </section>
    
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
            <select class="filter-dropdown" onChange="document.getElementById('filter-form').submit()" name="folder"
                data-aos="fade-in" data-aos-delay="700">
                <option value=""><span><label>Género:</label> TODOS</span></option>
                @foreach ($folders as $f)
                    <option {{ request()->get('folder') === str_replace(' ', '_', $f->name) ? 'selected' : '' }}
                        value="{{ str_replace(' ', '_', $f->name) }}"><span><label>Género:</label> {{ $f->name }}</span>
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
            <div class="cards-grid">
                @foreach ($playlists as $playlist)
                    @include('partials.playlist-card', ['item' => $playlist])
                @endforeach
            </div>
        </div>
    </div>

    <div class="container" data-aos="fade-up" data-aos-delay="300" style="margin-bottom: 1rem">
        @if ($playlists->count() === 0)
            <div class="empty">
                <span><i class="fas fa-close"></i></span>
                <h3>No se han enontrado <span class="text-primary">PLAYLIST</span> que cumplan con los filtros</h3>
            </div>
        @endif
        {{ $playlists->onEachSide(1)->links() }}
    </div>
    @include('partials.playlist-bottom-player')
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

        const audioPlayer = document.getElementById('plyr-audio-player');

        function cleanCards() {
            document.querySelectorAll('.playlist-card').forEach(card => {
                let icon = card.querySelector('.play-overlay i');
                if (icon) {
                    icon.className = 'fa-solid fa-play';
                }
                card.classList.remove('playing');
            });
        }

        function closePlayer() {
            const player = document.getElementById('bottom-player');
            player.classList.remove('active');
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
                return
            }
            el.classList.add('active');
            document.getElementById('player-img').src = currentTrack.img;
            document.getElementById('player-title').textContent = '#' + (getRemixIndex(currentTrack.id) + 1) + ' ' +
                currentTrack.title;
            document.getElementById('player-artist').textContent = currentTrack.artist;
            document.querySelectorAll('.playlist-card').forEach(card => {
                const id = card.id;
                const btn = card.querySelector('.play-overlay i');
                card.classList.remove('playing');
                if (id == currentTrack.playlist_id) {
                    if (btn) btn.className = 'fa-solid fa-pause';
                    card.classList.add('playing');
                }
            });
        }

        function setLoader(e) {
            document.querySelectorAll('.playlist-card').forEach(card => {
                const id = card.id;
                const btn = card.querySelector('.play-overlay i');
                if (id == e) {
                    if (btn) btn.className = 'fa fa-spinner fa-spin';
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
                cleanCards();
            });

            audioPlayer.addEventListener("ended", () => {
                if (currentTrack != tracks[tracks.length - 1]) {
                    playNextTrack();
                } else {
                    isPlaying = false;
                    cleanCards();
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
            if(!isPlaying){
                cleanCards();
                setLoader(id);

                if (currentTrack) {
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
            } else {
                togglePlay();
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
