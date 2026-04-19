@extends('layouts.app')
@php
    use Carbon\Carbon;
    use App\Models\Cart;
    Carbon::setLocale('es');
    $success = session('success');
    $error = session('error');
@endphp
@section('title', 'Videos - '.config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/remixes.css') }}" />
    <style>
        .bottom-player{
            width: 500px;
            height: 500px;
            bottom: 20px;
            right: 20px;
            left: auto;
            border: 0.5px solid var(--primary);
            padding: 0;
            margin: 0 auto;
        }

        @media (max-width: 700px){
            .bottom-player{
                width: 300px;
                height: 300px;
            }
        }

        .bottom-player .player-inner{
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 100%;
            padding: 15px;
            gap: 5px;

            & .video-player{
                width: 100%;
                height: 100%;

                & .plyr{
                    width: 100%;
                    height: 100%;
                }
            }

            & .player-controls{
                width: 100%;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                gap: 10px;

                & .waveform{
                    width: 100%;
                    height: auto;
                    min-height: 30px;
                }

                & .main-play{
                    width: 30px;
                    height: 30px;

                    &:hover{
                        color: var(--text-muted)
                    }
                }
            }

            & .player-track{
                display: grid;
                width:100%;
                position: relative;
                gap: 10px;
                grid-template-columns: 40px 1fr;
                padding-right: 15px;

                & .close{
                    position: absolute;
                    top: 10px;
                    right: 0;
                }
            }
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
            <select class="filter-dropdown" onChange="document.getElementById('filter-form').submit()"  name="genre" value="{{ request()->get('genre') }}" data-aos="fade-in" data-aos-delay="300">
                <option value=""><span><label>Género:</label> TODOS</span></option>
                @foreach ($genres as $genre)
                    <option {{ request()->get('genre') === str_replace(' ', '_',$genre->name) ? 'selected' : '' }} value="{{ str_replace(' ', '_',$genre->name) }}"><span><label>Género:</label> {{ $genre->name }}</span></option>
                @endforeach
            </select>
            <select class="filter-dropdown" onChange="document.getElementById('filter-form').submit()"  name="bpm" value="{{ request()->get('bpm') }}" data-aos="fade-in" data-aos-delay="500">
                <option value=""><span><label>BPM:</label> TODOS</span></option>
                @foreach ($bpms as $bpm)
                    <option {{ request()->get('bpm') === str_replace(' ', '_',$bpm->bpm) ? 'selected' : '' }} value="{{ str_replace(' ', '_', $bpm->bpm) }}"><span><label>BPM:</label> {{ $bpm->bpm }}</span></option>
                @endforeach
            </select>
            <select class="filter-dropdown" onChange="document.getElementById('filter-form').submit()"  name="dj" data-aos="fade-in" data-aos-delay="700">
                <option value=""><span><label>DJ:</label> TODOS</span></option>
                @foreach ($djs as $dj)
                    <option {{ request()->get('dj') === str_replace(' ', '_',$dj->name) ? 'selected' : '' }} value="{{ str_replace(' ', '_',$dj->name) }}"><span><label>DJ:</label> {{ $dj->name }}</span></option>
                @endforeach
            </select>
            <input 
                type="text" 
                class="filter-dropdown" 
                placeholder="Nombre: " 
                onChange="document.getElementById('filter-form').submit()" 
                name="title" 
                value="{{ request()->get('title') }}"
                data-aos="fade-in" data-aos-delay="900"
            />
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
            <div class="empty">
                <span><i class="fas fa-close"></i></span>
                <h3>No se han enontrado <span class="text-primary">VIDEOS</span> que cumplan con los filtros</h3>
            </div>
        @endif
        {{ $tracks->onEachSide(1)->links() }}
    </div>

    <!-- Exclusives -->
    <div class="exclusives" data-aos="fade-right" data-aos-delay="300">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
            <h2><i class="fas fa-lock"></i> <em>EXCLUSIVOS</em>  DE LA SEMANA</h2>
            @if ($exclusives->count() > 0)
                <a class="btn btn-outline" href="{{ route('videos.exclusives') }}">VER TODOS <i class="fas fa-angles-right"></i></a>
            @endif
        </div>
        <div id="exclusives-list">
            @foreach ($exclusives as $e)
                <div class="exclusive-card">
                    <div class="exclusive-icon"><i class="fas fa-lock"></i></div>
                    <div class="exclusive-info">
                        <h3>{{ $e->name }} </h3>
                        <p>{{ $e->user->name }}</p>
                        <div class="exclusive-meta">
                            @foreach ($e->categories as $cat)
                                <span class="track-badge club">{{ $cat->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    <a href="{{ route('file.add.cart', $e->id) }}" class="btn-subscribe">COMPRAR</a>
                </div>
            @endforeach
            @if ($exclusives->count() == 0)
                <div style="text-align:center;padding:4rem 1rem;color:var(--fg-muted);">
                    <i class="fa-solid fa-face-grin-beam-sweat" style="font-size:3rem;margin-bottom:1rem;"></i>
                    <div style="font-size:1.25rem;font-weight:600;margin-bottom:1rem;">Sin exclusivos esta semana</div>
                    <a class="btn btn-outline" href="{{ route('videos.exclusives') }}">VER TODOS <i class="fas fa-angles-right"></i></a>
                </div>
            @endif
        </div>
    </div>
    @include('partials.bottom-video-player')
    <div style="height:80px"></div>
@endsection

@push('scripts')
    <script>

        const audioPlayer = document.getElementById('plyr-video-player');

        // ===== PLAYER STATE =====
        let currentTrack = null;
        let isPlaying = false;
        let currentTime = 0;
        let duration = 0;
        let timerInterval = null;

        function cleanCards(){
            document.querySelectorAll('.track-card').forEach(card => {
                const wf = card.querySelector('.waveform').classList.remove('playing');
                card.querySelector('.play-btn i').className = 'fa-solid fa-play';
            });
        }

        function closePlayer(){
            const player = document.getElementById('bottom-player');
            player.classList.remove('active');
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
                document.querySelectorAll('.track-card').forEach(card => {
                    const wf = card.querySelector('.waveform');
                    wf.classList.remove('playing');
                });
                return
            }
            el.classList.add('active');
            let trackData = document.getElementById(currentTrack);
            document.getElementById('player-img').src = trackData.querySelector('img').src;
            document.getElementById('player-title').textContent = trackData.querySelector('.track-title').textContent;
            document.getElementById('player-artist').textContent = trackData.querySelector('.track-artist').textContent;
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

        function setLoader(e){
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

            audio = document.createElement('video');
            audio.src = url;

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
