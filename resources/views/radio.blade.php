@extends('layouts.app')
@php
    use Carbon\Carbon;
    use App\Models\Cart;
    use App\Models\Setting;
    use App\Enums\SectionEnum;
    Carbon::setLocale('es');
    $setting = Setting::first();
    $cup_aviable = $setting
        ? $setting->currency_convertion_rate != null &&
            $setting->credit_card_info != null &&
            $setting->confirmation_phone != null &&
            $setting->confirmation_email != null
        : false;
    $success = session('success');
    $error = session('error');
@endphp

@section('title', 'Emisora')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/remixes.css') }}" />
    <style>
        :root {
            --azul: #0079FF;
            --rojo: #ff0000;

            --primary: var(--red) !important;
        }

        .frames-section {
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            width: 100%;
            gap: 1rem;
            padding: 1rem 0;

            & iframe {
                height: 100%;
                min-height: 200px;
                flex: 1;
            }
        }

        .tracks-sections h2 {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .content {
            display: flex;
            flex-direction: column-reverse;
            gap: 1rem;

            &>div {
                flex: 1;
            }
        }

        .btn-outline {
            align-items: center;
            display: flex;
            gap: 10px;
        }

        .track-right {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .track-price {
            display: flex;
            flex-direction: column;
            gap: 10px;

            &>div {
                justify-content: space-between;
            }
        }

        @media (max-width: 1200px) {
            .track-card {
                grid-template-columns: 75% 25%;
            }
        }

        @media (max-width: 800px) {
            .frames-section {
                flex-direction: column;
            }

            .track-price {
                flex-direction: row;
            }
        }
    </style>
@endpush

@section('content')
    <section class="hero container">
        <div class="hero-bg">
            <img style="width: 100%" src="{{ asset('assets/img/emisora-portada.png') }}" alt="hero-banner">
            <div class="overlay"></div>
        </div>
        <div class="container">
            <h1 data-aos="fade-right" data-aos-delay="300">NUESTRA <span class="text-primary">EMISORA</span></h1>
            <p data-aos="fade-right" data-aos-delay="500">Escucha música a tu gusto directamente desde nuestra emisora.</p>
            <ul>
                <li data-aos="fade-right" data-aos-delay="700"><strong>+1000 tracks</strong> actualizados semanalmente.</li>
                <li data-aos="fade-right" data-aos-delay="900"><strong>Locutores en línea</strong></li>
            </ul>
        </div>
    </section>

    <div class="container content">
        <div class="tracks-sections">
            <div class="tracks-container" id="tracks-list" data-aos="fade-up" data-aos-delay="300">
                <h2><span>SESIONES EN VIVO</span><a class="btn-outline" href="{{ route('radio.remixes', ['genre' => SectionEnum::CUBANDJS_LIVE_SESSIONS->value]) }}">VER TODOS »</a></h2>
                @foreach ($lives as $track)
                    @include('partials.radio-remix-card', ['item' => $track])
                @endforeach
            </div>
            <div class="tracks-container" id="tracks-list" data-aos="fade-up" data-aos-delay="300">
                <h2><span>MIX & REMIX</span><a class="btn-outline" href="{{ route('radio.remixes', ['genre' => SectionEnum::CUBANDJS->value]) }}">VER TODOS »</a></h2>
                @foreach ($mixes as $track)
                    @include('partials.radio-remix-card', ['item' => $track])
                @endforeach
            </div>
        </div>
        <div class="frames-section">
            <iframe
                src="https://public-player-widget.webradiosite.com/?cover=1&current_track=1&schedules=1&link=1&popup=1&share=1&embed=0&auto_play=1&source=10382&theme=dark&color=4&link_to=cubandjsproradio.com&identifier=CubanDjsPro%20Radio&info=https%3A%2F%2Fpublic-player-widget.webradiosite.com%2Fapp%2Fplayer%2Finfo%2F247079%3Fhash%3D7beeef43d3d82f9110c118b97c8e149829ddb4ad&locale=es-es"
                border="0" scrolling="no" frameborder="0" allow="autoplay; clipboard-write" allowtransparency="true"
                style="background-color: unset; width: 100%; border-radius:15px;" height="auto"></iframe>
            <iframe
                src="https://public-web-widget.webradiosite.com/app/widget/broadcaster/247079?hash=4b5dcf88092ec1ed77112bd9a980aeb8cdfa062e&theme=dark&color=1"
                style="width:100%; height:auto; border-radius:15px;" border="0" frameborder="0"
                allow="autoplay; clipboard-write" allowtransparency="true"></iframe>
        </div>
    </div>

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
