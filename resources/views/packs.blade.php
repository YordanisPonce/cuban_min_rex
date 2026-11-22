@extends('layouts.app')
@php
    use Carbon\Carbon;
    Carbon::setLocale('es');
    $success = session('success');
    $error = session('error');
@endphp
@section('title', 'SAMPLES/FX')
@section('description', 'Descarga SAMPLES/FX de remixes y música para DJs en un solo paquete. Colecciones listas para tus sets al mejor precio.')

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
    @include('partials.hero', [
        'baseDescription' => 'Descarga edits, intros, mashups y más musical para hacer historia en la pista.',
        'baseLinkText' => 'REMIXES EXCLUSIVOS',
        'baseLinkAddress' => route('plans'),
    ])

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
    @include('partials.bottom-player')
@endsection

@push('scripts')
    <script>

        const audioPlayer = document.getElementById('plyr-audio-player');

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
                cleanCards();
                return
            }
            el.classList.add('active');
            let trackData = document.getElementById(currentTrack);
            document.getElementById('player-img').src = trackData.querySelector('img').src;
            document.getElementById('player-title').textContent = trackData.querySelector('.pack-name').textContent;
            document.getElementById('player-artist').textContent = trackData.querySelector('.dj-name').textContent;
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
    </script>
@endpush
