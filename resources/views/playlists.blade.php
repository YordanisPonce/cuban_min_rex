@extends('layouts.app')

@section('title', "PlayLists – ".config('app.name'))

@push('styles')
<link rel="stylesheet" href="{{ asset('/assets/vendor/libs/plyr/plyr.css') }}" />
<style>
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

    section#audioPlayer{
        transition: all 0.3s ease-in;
        transform: translateY(160px);
    }
    
    .landing-footer .footer-top {
        border-top-left-radius: 0rem !important;
        border-top-right-radius: 0rem !important;
    }

    .audio-player-controls:hover{
        transform: scale(1.5);
    }

    #audioPlayer{
        position: sticky;
        bottom: 0;
        width: 100%;
        z-index: 10;
    }

    footer{
        z-index: 11;
    }
</style>
@endpush

@section('content')
<section id="landingReviews" class="section-py bg-body">
    <div class="container mt-10">
        <div class="row align-items-center gx-0 gy-4 g-lg-5 mb-5 pb-md-5">
            @if ($playlists->isEmpty())
            <div class="container-xxl flex-grow-1 container-p-y mt-10">
                <div class="row align-items-center g-5 mt-10">
                    <div class="align-items-center justify-content-center text-center">
                        <span class="badge bg-label-primary mb-3">Sin Playlists</span>
                        <h3 class="display-6 fw-bold mb-2">No hay playlists disponibles.</h3>
                        <p class="text-body-secondary mb-4">
                            Explora nuestro repositorio y descubre música a tu gusto.
                        </p>
                        <div class="d-flex align-items-center justify-content-center gap-3 mt-4">
                            <a href="{{ route('remixes') }}" class="btn btn-primary text-black">Explorar Archivos</a>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="col-md-12 flex flex-column align-items-center">
                <div class="mb-4">
                    <span class="badge bg-label-primary">PlayLists</span>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <h2 class="display-6 fw-bold mb-2">Nuestras PlayLists</h2>
                <p class="text-body-secondary mb-4">
                    Explora nuestras playlists curadas para cada estado de ánimo y ocasión.
                </p>
            </div>
            <div class="col-md-6 mb-4">
                <form action="" method="GET" class="d-flex align-items-center gap-2">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control form-control-lg"
                            placeholder="Buscar" value="{{ request()->query('search') ?? '' }}">
                        <button type="submit" class="btn btn-primary btn-lg px-4 z-0">
                            <i class="ti tabler-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            <div class="categories col-md-12">
                <div class="row">
                    @foreach ($playlists as $item)
                        @include('partials.playlist-card', ['item' => $item])
                    @endforeach
                </div>
                <div class="mt-3 d-flex justify-content-center">
                    {{ $playlists->links() }}
                </div>
            </div>
            @endif
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
                        <span class="cursor-pointer audio-player-controls hidden-xl" onclick="playPrevAudio()"><i class="icon-base ti tabler-chevron-left icon-md scaleX-n1-rtl"></i></span>
                        <span id="plyr-audio-name" class="d-block w-100 text-nowrap overflow-hidden"
                            style="text-overflow:ellipsis; text-align:center">Audio</span>
                        <span class="cursor-pointer audio-player-controls hidden-xl" onclick="playNextAudio()"><i class="icon-base ti tabler-chevron-right icon-md scaleX-n1-rtl"></i></span>
                    </h5>
                    <div class="card-body">
                        <audio class="w-100" id="plyr-audio-player" type="audio/mp3" src="https://demos.pixinvent.com/vuexy-html-admin-template/assets/audio/Water_Lily.mp3" controls></audio>
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
    new Plyr("#plyr-audio-player");

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
            const name = names[0] === null ? 'Archivo sin nombre' : names[0];
            document.getElementById('plyr-audio-name').textContent = currentPlayList + ' - ' + name;
            showAudioPlayer();
        } else {
            playlist.innerHTML = '<i class="icon-base ti tabler-player-play-filled"></i>';
            playlist.dataset.status = 'pause';
            hiddenAudioPlayer();
        }
    }

    function playNextAudio() {
        const currentIndex = tracks.findIndex(track => track === audio.src);
        const nextIndex = (currentIndex + 1) % tracks.length;
        const name = names[nextIndex] === null ? 'Archivo sin nombre' : names[nextIndex];
        audio.src = tracks[nextIndex];
        document.getElementById('plyr-audio-name').textContent = currentPlayList + ' - ' + name;
        audio.play();
    }

    // Cuando termina un audio, cargar el siguiente
    audio.addEventListener("ended", () => {
        playNextAudio();
    });
</script>
@endpush