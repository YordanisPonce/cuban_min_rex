@extends('layouts.app')
@php
    use App\Models\Cart;
    $success = session('success');
    $error = session('error');
@endphp

@section('title', 'Inicio – ' . config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/plyr/plyr.css') }}" />
    <style>
        .player--dark {
            background-color: #12131C !important;
            color: #fff;
        }

        footer {
            z-index: 11;
        }

        section#audioPlayer {
            transition: all 0.3s ease-in;
            transform: translateY(160px);
        }

        #audioPlayer {
            position: sticky;
            bottom: 0;
            width: 100%;
            z-index: 10;
        }

        .bg-body {
            background-color: transparent !important;
        }

        .list-card:hover{
            transform: scale(.95);
            transition: transform 0.3s ease-in, background-color 0.3s ease-in;
            background-color: rgba(133,133,133,0.3) !important;
        }

        .btn-success{
            transform: scale(0) !important;
            bottom: 10px;
            transition: all 0.3s ease-in !important;
        }

        .list-card:hover .btn-success{
            transform: scale(1) !important;
        }

        @media (max-width: 400px) {
            .packs-link {
                bottom: -12px !important;
            }
        }
    </style>
@endpush

@section('content')

    {{-- =========================
       HEADER DE BUSQUEDA
    ========================== --}}
    <section class="section first-section-pt help-center-header position-relative overflow-hidden ">
        <div class="container">
            <div class="row align-items-center g-10">
                <div class="col-lg-3">
                    <div class="ratio ratio-4x3 rounded-4 overflow-hidden border border-dark-subtle">
                        <img src="{{ config('app.logo_alter') }}" alt="Arte destacado"
                            class="w-100 h-100 object-fit-cover">
                        <div class="dark-screen" style="opacity: 0.5;"></div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <span class="badge bg-label-primary mb-3">{{ config('app.name') }}</span>
                    <h1 class="display-6 fw-bold mb-2">Descubre tu música favorita</h1>
                    <p class="text-primary mb-4">
                        Encuentra el ritmo perfecto y descubre tu próxima pista favorita.
                    </p>
                    {{-- Formulario buscador --}}
                    <form action="{{ route('remixes') }}" method="GET" class="d-flex align-items-center gap-2">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control form-control-lg"
                                placeholder="Buscar" required>
                            <button type="submit" class="btn btn-primary btn-lg px-4 z-0">
                                <i class="ti tabler-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- =========================
       NUEVOS LANZAMIENTOS
    ========================== --}}
    <section id="home-new" class="section-py">

        @php $hasNew = isset($newItems) && count($newItems) > 0; @endphp

        @if ($hasNew)
            <div class="container">
                <div class="text-center mb-3">
                    <span class="badge bg-label-primary">Novedades</span>
                </div>
                <h2 class="text-center fw-bold mb-2">Estrenos de la semana</h2>
                <p class="text-center text-body-secondary mb-6">

                </p>
                <div class="row">
                    <div class="d-md-grid" style="grid-template-columns: repeat(2, 1fr); row-gap: 4px; column-gap: 24px;">
                        @foreach ($newItems as $item)
                            @include('partials.line-card', ['item' => $item])
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="container mb-3">
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <div class="text-center mb-3">
                            <span class="badge bg-label-primary">Novedades</span>
                        </div>
                        <h2 class="text-center fw-bold mb-2">Estrenos de la semana</h2>
                        <p class="text-body-secondary mb-0">Singles y packs recién salidos. Lo último de tus artistas
                            favoritos.</p>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="border rounded-4 p-4 p-md-5 text-center bg-body">
                    <h3 class="h5 fw-bold mb-2">No hay lanzamientos recientes</h3>
                    <p class="text-body-secondary mb-3">Vuelve pronto: actualizamos esta sección con nuevos estrenos.</p>
                    <a href="{{ route('remixes') }}" class="btn btn-outline-secondary">Explorar catálogos</a>
                </div>
            </div>
        @endif
    </section>

    <hr class="m-0 mt-6 mt-md-12">

    {{-- =========================
       TOPS LANZAMIENTOS
    ========================== --}}
    <section id="home-tops" class="section-py mt-10">

        @php $hasTops = isset($tops) && count($tops) > 0; @endphp

        @if ($hasTops)
            <div class="container">
                <div class="text-center mb-3">
                    <span class="badge bg-label-primary">TOPS</span>
                </div>
                <h2 class="text-center fw-bold mb-2">TOP REMIXES</h2>
                <div class="row">
                    @php
                        $pos = 1;
                        $column2 = $tops->slice(5);
                    @endphp
                    <div class="col-md-6">
                        @foreach ($tops as $item)
                            @if ($pos <= 5)
                                @include('partials.line-card', ['item' => $item, 'top' => $pos])
                                @php
                                    $pos = $pos + 1;
                                @endphp
                            @endif
                        @endforeach
                    </div>
                    <div class="col-md-6">
                        @foreach ($column2 as $item)
                            @include('partials.line-card', ['item' => $item, 'top' => $pos])
                            @php
                                $pos = $pos + 1;
                            @endphp
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="container mb-3">
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <span class="badge bg-label-primary mb-2">TOPS</span>
                        <h2 class="h3 fw-bold mb-1">TOP REMIXES</h2>
                        <p class="text-body-secondary mb-0"> </p>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="border rounded-4 p-4 p-md-5 text-center bg-body">
                    <h3 class="h5 fw-bold mb-2">No hay tops lanzamientos</h3>
                    <p class="text-body-secondary mb-3">Vuelve pronto: actualizamos esta sección con nuevos estrenos.</p>
                    <a href="{{ route('remixes') }}" class="btn btn-outline-secondary">Explorar catálogos</a>
                </div>
            </div>
        @endif
    </section>

    <hr class="m-0 mt-6 mt-md-12">

    {{-- =========================
        NUEVAS PLAYLIST
    ========================== --}}
    <section id="home-playlist" class="section-py mt-10">

        @php $hasPlsylidt = isset($recentPlaylist) && count($recentPlaylist) > 0; @endphp

        @if ($hasPlsylidt)
            <div class="container">
                <div class="text-center mb-3">
                    <span class="badge bg-label-primary">Explorar</span>
                </div>
                <div class="relative">
                    <h2 class="text-center fw-bold mb-6 mb-md-2">Playlist Recientes</h2>
                    <a href="{{ route('playlist.index') }}" class="position-absolute end-0 packs-link"
                        style="bottom: 15px">Ver todos →</a>
                </div>
                <div class="row">
                    @foreach ($recentPlaylist as $item)
                        @include('partials.playlist-card', ['item' => $item])
                    @endforeach
                </div>
            </div>
        @else
            <div class="container mb-3">
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <span class="badge bg-label-primary mb-2">Explorar</span>
                        <h2 class="h3 fw-bold mb-1">Playlist Recientess</h2>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="border rounded-4 p-4 p-md-5 text-center bg-body">
                    <h3 class="h5 fw-bold mb-2">Sin playlist por ahora</h3>
                    <p class="text-body-secondary mb-3">Estamos preparando nuevas playlist por artista y estilo.</p>
                    <a href="{{ route('remixes') }}" class="btn btn-outline-secondary">Buscar artistas</a>
                </div>
            </div>
        @endif
    </section>

    <hr class="m-0 mt-6 mt-md-12">

    {{-- =========================
       PACKS DE ARTISTAS
    ========================== --}}
    <section id="home-collections" class="section-py mt-10">

        @php $hasArtists = isset($artistCollections) && count($artistCollections) > 0; @endphp

        @if ($hasArtists)
            <div class="container">
                <div class="text-center mb-3">
                    <span class="badge bg-label-primary">Explorar</span>
                </div>
                <div class="relative">
                    <h2 class="text-center fw-bold mb-6 mb-md-2">Packs de artistas</h2>
                    <a href="{{ route('collection.index') }}" class="position-absolute end-0 packs-link"
                        style="bottom: 15px">Ver todos →</a>
                </div>
                <div class="row">
                    @foreach ($artistCollections as $item)
                        @include('partials.pack-card', ['item' => $item])
                    @endforeach
                </div>
            </div>
        @else
            <div class="container mb-3">
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <span class="badge bg-label-primary mb-2">Explorar</span>
                        <h2 class="h3 fw-bold mb-1">Packs de artistas</h2>
                        <p class="text-body-secondary mb-0">Discografías esenciales, playlists temáticas y selecciones por
                            mood.
                        </p>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="border rounded-4 p-4 p-md-5 text-center bg-body">
                    <h3 class="h5 fw-bold mb-2">Sin packs por ahora</h3>
                    <p class="text-body-secondary mb-3">Estamos preparando nuevas selecciones por artista y estilo.</p>
                    <a href="{{ route('remixes') }}" class="btn btn-outline-secondary">Buscar artistas</a>
                </div>
            </div>
        @endif
    </section>

    <hr class="m-0 mt-6 mt-md-12">

    {{-- =========================
       GÉNEROS POPULARES
    ========================== --}}
    <section id="home-genres" class="section-py">
        <div class="container">
            <div class="d-flex align-items-end justify-content-between mb-3">
                <div>
                    <span class="badge bg-label-primary mb-2">Explorar</span>
                    <h2 class="h3 fw-bold mb-1">Géneros populares</h2>
                    <p class="text-body-secondary mb-0">Elige un género y empieza a escuchar.</p>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                @foreach ($ctg as $genre)
                    <a href="{{ route('category.show', $genre->id) }}"
                        class="btn btn-sm btn-outline-secondary rounded-pill px-3">{{ $genre->name }}</a>
                @endforeach
            </div>
        </div>
    </section>

    <hr class="m-0 mt-6 mt-md-12">

    <section id="home-pricing" class="section-py bg-body landing-pricing mt-10">
        <div class="container mt-5">
            <div class="text-center mb-3">
                <span class="badge bg-label-primary">Planes de suscripción</span>
            </div>
            <h2 class="text-center fw-bold mb-2">Elige tu plan musical</h2>
            <p class="text-center text-body-secondary mb-10">
                Disfruta sin límites con beneficios a tu medida.
            </p>
            <div class="pricing-table">
                @foreach ($plans as $plan)
                    @include('partials.plans-card', ['plan' => $plan])
                @endforeach
            </div>
        </div>
        <div class="window-notice" id="video-player">
            <div class="content">
                <div class="container-xxl flex-grow-1 container-p-y w-100">
                    <div class="row gy-6">
                        <!-- Video Player -->
                        <div class="col-12">
                            <div class="card" style="position: relative; max-height: 100vh">
                                <h5 class="card-header d-block text-nowrap overflow-hidden"
                                    style="text-overflow:ellipsis; width: 90%" id="video-title">Nombre del Video</h5>
                                <spam style="position: absolute; top: 24px; right: 24px; cursor: pointer"
                                    onclick="stopVideo()">✖️</spam>
                                <div class="card-body w-100 h-100">
                                    <video class="w-100" style="max-height: 70dvh;" id="plyr-video-player"
                                        oncontextmenu="return false;" playsinline controls></video>
                                </div>
                            </div>
                        </div>
                        <!-- /Video Player -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="audioPlayer">
        <div class="container">
            <div class="row">
                <!-- Audio Player -->
                <div class="col-12">
                    <div class="card">
                        <h5 class="card-header d-flex justify-content-between">
                            <span id="plyr-audio-name" class="d-block w-100 text-nowrap overflow-hidden"
                                style="text-overflow:ellipsis; text-align:center">Audio</span>
                        </h5>
                        <div class="card-body">
                            <audio class="w-100" id="plyr-audio-player" type="audio/mp3"
                                src="https://demos.pixinvent.com/vuexy-html-admin-template/assets/audio/Water_Lily.mp3"
                                controls></audio>
                        </div>
                    </div>
                </div>
                <!-- /Audio Player -->
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('/assets/vendor/libs/plyr/plyr.js') }}"></script>
    <script>
        new Plyr("#plyr-video-player"), new Plyr("#plyr-audio-player");
    </script>
    <script>
        const audioExtensions = ['.mp3', '.wav', '.ogg', '.m4a'];
        const videoExtensions = ['.mp4', '.avi', '.mov', '.wmv', '.mkv'];

        window.addEventListener('DOMContentLoaded', function() {
            document.body.classList.remove('bg-body');
        })

        function stopVideo() {
            let video = document.getElementById('plyr-video-player');
            document.getElementById('video-player').style.display = 'none';
            video.pause();
        }

        function playAudio(element) {
            let audio = document.getElementById('plyr-audio-player');
            let video = document.getElementById('plyr-video-player');
            const rute = element.dataset.rute;
            const mode = element.dataset.status;
            video.pause();
            fetch(rute, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    data = data.filter(item => item.id === parseInt(element.id));
                    const extension = data[0].url.substring(data[0].url.lastIndexOf('.')).toLowerCase();
                    if (audioExtensions.includes(extension)) {
                        if (mode === "off") {
                            audio.src = data[0].url;
                            document.getElementById('audioPlayer').style.transform = 'translateY(0)';
                            document.getElementById('plyr-audio-name').innerText = data[0].title;
                            audio.play();
                            element.dataset.status = "on";
                            element.innerHTML = '<i class="icon-base ti tabler-player-pause-filled"></i>';
                        } else {
                            audio.pause();
                            document.getElementById('audioPlayer').style.transform = 'translateY(160px)';
                            element.dataset.status = "off";
                            element.innerHTML = '<i class="icon-base ti tabler-player-play-filled"></i>';
                        }
                    } else {
                        audio.pause();
                        document.getElementById('audioPlayer').style.transform = 'translateY(160px)';
                        document.querySelectorAll('.play-button').forEach(button=>{
                            button.dataset.status = "off";
                            button.innerHTML = '<i class="icon-base ti tabler-player-play-filled"></i>';
                        });
                        video.src = data[0].url;
                        document.getElementById('video-title').innerText = data[0].title;
                        document.getElementById('video-player').style.display = 'block';
                        video.play();
                    }
                })
                .catch(error => {
                    Swal.fire("Error", error.message, "error");
                });
        }

        const audioPlayer = document.getElementById('audioPlayer');
        const audio = document.getElementById('plyr-audio-player');
        var tracks = [];
        var names = [];
        var currentPlayList = "";

        if (tracks.length <= 1) {
            document.querySelectorAll('.audio-player-controls').forEach(control => {
                control.style.display="none";
            });
        }

        function cleanBtns(except){
            document.querySelectorAll('.btn-success').forEach(btn => {
                if (btn !== except) {
                    btn.innerHTML = '<i class="icon-base ti tabler-player-play-filled"></i>';
                    btn.dataset.status = 'pause';
                }
            });
        }

        function showAudioPlayer() {
            audioPlayer.style.transform = 'translateY(0)';
            audio.play();
        }

        function hiddenAudioPlayer() {
            audioPlayer.style.transform = 'translateY(160px)';
            audio.pause();
        }

        function playList(playlist, tracksList, namesList) {
            tracks = tracksList;
            names = namesList;
            currentPlayList = playlist.dataset.name;
            cleanBtns(playlist);
            if(playlist.dataset.status==='pause'){
                playlist.innerHTML = '<i class="icon-base ti tabler-player-pause-filled"></i>';
                playlist.dataset.status = 'play';
                audio.src = tracks[0];
                const name = names[0] === null ? "Track " + (0 + 1) : names[0];
                document.getElementById('plyr-audio-name').textContent = currentPlayList + ' - ' + name;
                showAudioPlayer();
            } else {
                playlist.innerHTML = '<i class="icon-base ti tabler-player-play-filled"></i>';
                playlist.dataset.status = 'pause';
                hiddenAudioPlayer();
            }
            

            // Cuando termina un audio, cargar el siguiente
            audio.addEventListener("ended", () => {
                playNextAudio();
            });
        }

        function playNextAudio() {
            const currentIndex = tracks.findIndex(track => track === audio.src);
            const nextIndex = (currentIndex + 1) % tracks.length;
            const name = names[nextIndex] === null ? "Track " + (nextIndex + 1) : names[nextIndex];
            audio.src = tracks[nextIndex];
            document.getElementById('plyr-audio-name').textContent = currentPlayList + ' - ' + name;
            audio.play();
        }
    </script>
    @isset($error)
        <script>
            Swal.fire({
                title: 'Error al descargar el archivo',
                text: '{{ $error }}',
                icon: 'error'
            });
        </script>
    @endisset
@endpush
