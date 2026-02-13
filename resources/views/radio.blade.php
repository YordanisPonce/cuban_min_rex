@extends('layouts.app')
@php
    use Carbon\Carbon;
    use App\Models\Cart;
    Carbon::setLocale('es');
    $success = session('success');
    $error = session('error');
@endphp

@section('title', 'Emisora - '.config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page.css') }}" />
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page-payment.css') }}" />
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/plyr/plyr.css') }}" />
    <style>
        :root{
            --azul: #0079FF;
            --rojo: #ff0000;

            --bs-primary: var(--red) !important;

            --bs-paper-bg: #000 !important;

            --download-button: var(--rojo) !important;
            --play-button: var(--azul) !important;
        }

        .swal2-confirm {
            background-color: var(--azul) !important;
        }

        .swal2-deny {
            background-color: var(--rojo) !important;
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

        .text-black{
            color: white !important;
        }

        .bg-body {
            background-color: black !important;
        }

        body{
            background-image: none !important;
        }

        #audioPlayer{
            position: sticky;
            bottom: 0;
            width: 100%;
            z-index: 10;
        }

        #musicSearch * {
            font-size: 18px !important;
            font-weight: 400;
        }

        td a:hover{
            transform: scale(0.9);
        }

        footer{
            z-index: 11;
        }

        .page-item .page-link
        {
            border-radius: 0 !important;
        }

        .show-xl{
            display: none;
        }

        .packs {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            margin-top: 40px;
            & .card {
                cursor: pointer;
                box-shadow: 0px 0px 6px 1px #ccc; 
                border-radius: 12px; 
                overflow: hidden; 
                min-width: 200px;
                max-width: 200px; 
                height: 250px;
                transform: translateY(0);
                transition: transform .3s ease-in;

                &>img{
                    width: 100%; 
                    height:100%; 
                    object-fit: cover; 
                    border-radius: 12px;
                }

                & .pack-meta{
                    position: absolute;
                    width: 100%;
                    height: 100%;
                    padding: 5px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    text-align: center;
                    background: rgba(0, 0, 0, 0.4);
                }

                & .pack-links, & .pack-links-top{
                    position: absolute;
                    width: 100%;
                    height: 50px;
                    padding: 5px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    text-align: center;
                    background: rgba(0, 0, 0, 0.6);
                    transition: transform .3s ease-in;
                }

                & .pack-links{
                    bottom: 0;
                    transform: translateY(50px);

                    &>div{
                        display: flex;
                        justify-content: center;
                        align-items: center;

                        & a{
                            background: black;
                            padding: 2px;
                            border-radius: 50%;
                            width: 30px;
                            height: 30px;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            transition: all .3s linear;
                            font-size: 8px !important;

                            &:hover{
                                background-color: var(--rojo);
                                /*width: 150px;
                                border-radius: 12px;*/
                                color: white !important;
                            }
                        }
                    }
                }

                & .pack-links-top{
                    top: 0;
                    transform: translateY(-50px);
                }
                &:hover{
                    box-shadow: 0px 0px 6px 2px #ccc;
                    transform: translateY(-10px);

                    & .pack-links, & .pack-links-top{
                        transform: translateY(0);
                    }
                }
            }
        }

        @media(max-width: 500px) {
            .hidden-mobile{
                display: none !important;
            }

            .name{
                max-width: 100px !important;
            }
        }
        @media(max-width: 1200px) {
            .hidden-xl{
                display: none !important;
            }

            .show-xl{
                display: table-cell;
            }
        }
    </style>
@endpush

@section('content')
    <section class="pt-10 mt-10">
        <div class="container">
            <div class="row align-items-center g-10 mt-5">
                <div class="col-lg-6">
                    <div class="ratio ratio-4x3 rounded-4 overflow-hidden border border-dark-subtle">
                        <img src="{{ asset('assets/img/emisora-portada.png') }}" alt="Arte destacado"
                            class="w-100 h-100 object-fit-cover">
                        <div class="dark-screen" style="opacity: 0.5;"></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <span class="badge bg-label-primary mb-3">Radio</span>
                    <h1 class="display-6 fw-bold mb-2">Nuestra Emisora</h1>
                    <p class="text-body-secondary mb-4">
                        Escucha música a tu gusto directamente desde nuestra emisora.
                    </p>
                    <iframe src="https://public-player-widget.webradiosite.com/?cover=1&current_track=1&schedules=1&link=1&popup=1&share=1&embed=0&auto_play=1&source=10382&theme=dark&color=4&link_to=cubandjsproradio.com&identifier=CubanDjsPro%20Radio&info=https%3A%2F%2Fpublic-player-widget.webradiosite.com%2Fapp%2Fplayer%2Finfo%2F247079%3Fhash%3D7beeef43d3d82f9110c118b97c8e149829ddb4ad&locale=es-es" border="0" scrolling="no" frameborder="0" allow="autoplay; clipboard-write" allowtransparency="true" style="background-color: unset; width: 100%; border-radius:15px; margin-bottom: 10px;" height="auto"></iframe>
                    <iframe src="https://public-web-widget.webradiosite.com/app/widget/broadcaster/247079?hash=4b5dcf88092ec1ed77112bd9a980aeb8cdfa062e&theme=dark&color=1" style="width:100%; height:auto; border-radius:15px;" border="0" frameborder="0" allow="autoplay; clipboard-write" allowtransparency="true"></iframe>
                </div>
            </div>
        </div>
    </section>
    <section id="musicSearch" class="section-py">
        <div class="container" style="margin-top: 60px;">
            <div class="text-center mb-4">
                <span class="badge bg-label-primary">🎶 Sesiones en Vivo</span>
            </div>
            @if (!$lives->isEmpty())
            <div class="card" style="background-color: transparent !important">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatables-basic table table-hover">
                        <thead>
                            <tr>
                                <th class="hidden-mobile"></th>
                                <th class="hidden-mobile">Fecha</th>
                                <th>REMIXERS</th>
                                <th>Nombre</th>
                                <th class="hidden-mobile">BPM</th>
                                <th class="hidden-mobile">Categorías</th>
                                <th class="hidden-xl">Precio</th>
                                <th class="hidden-xl">Moneda</th>
                                <th class="hidden-xl"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lives as $file)
                                @php
                                    $date = Carbon::parse($file['date'])->format('d/m/Y');
                                @endphp
                                <tr class="result" data-id="{{$file['id']}}" data-url="{{$file['url']}}">
                                    <td class="hidden-mobile">
                                        <div class="avatar overflow-hidden rounded-circle">
                                            <img src="{{ $file['logotipe'] ? $file['logotipe'] : config('app.logo') }}" alt="Avatar" class="rounded-circle" />
                                            <div class="dark-screen" style="background-color: rgba(0, 0, 0, 0.5);"></div>
                                        </div>
                                    </td>
                                    <td class="hidden-mobile">{{ $date }}</td>
                                    <td><span style="color: var(--bs-primary)">{{ $file['user'] }}</span></td>
                                    <td>
                                        <span class="d-block overflow-hidden name"
                                                style="text-overflow:ellipsis;max-width: 500px">
                                            {{ $file['name'] }} 
                                        </span>
                                    </td>
                                    <td class="hidden-mobile">{{ $file['bpm'] }}</td>
                                    <td class="hidden-mobile">
                                        @if(count($file['categories']) > 0)
                                            @foreach($file['categories'] as $cat)
                                                <small class="badge bg-label-primary">{{ $cat->name }}</small>
                                            @endforeach
                                        @else
                                            <small class="badge bg-label-primary">Sin Categoría</small>
                                        @endif
                                    </td>
                                    <td class="hidden-xl">
                                        <span class="d-block w-100 text-nowrap overflow-hidden"
                                                style="text-overflow:ellipsis;">
                                            {{ $file['price'] }}
                                        </span>
                                    </td>
                                    <td class="hidden-xl">
                                        {{ $file['currency'] ?? 'USD' }}
                                    </td>
                                    <td class="hidden-xl">
                                        <a style="display: flex; width: 25px; color: var(--download-button)" class="cursor-pointer" data-usd="{{ route('radio.file.pay', ['file' => $file['id']]) }}" data-cup="{{ route('payment.cup.form', ['file' => $file['id']]) }}" onclick="proccessPayment(this)">
                                            <i style="width: 20px;" class="icon-base ti tabler-shopping-cart-dollar"></i>
                                        </a>
                                    </td>
                                    <td class="hidden-xl">
                                        <a id="{{$file['id']}}" style="display: flex; width: 20px; color: var(--play-button)" class="play-button cursor-pointer" data-editable="true" data-url="{{$file['url']}}" data-name="{{$file['name']}}" data-state="pause" data-section="lives" onclick="playAudio(this)"
                                                ><i style="width: 20px;" class="icon-base ti tabler-player-play-filled"></i></a>
                                    </td>
                                    <td class="show-xl">
                                        <div class="dropdown d-flex justify-content-center">
                                            <button class="btn btn-text-secondary btn-icon rounded-pill text-body-secondary border-0" type="button" id="BulkOptions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="icon-base ti tabler-dots icon-22px text-body-secondary"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="BulkOptions">
                                                <p class="pt-2 text-center">Precio: {{ $file['price'] }} {{ $file['currency'] ?? 'USD' }}</p>
                                                <div class="dropdown-item d-flex gap-2 cursor-pointer align-items-center btn-play-xl" data-usd="{{ route('radio.file.pay', ['file' => $file['id']]) }}" data-cup="{{ route('payment.cup.form', ['file' => $file['id']]) }}" onclick="proccessPayment(this)">
                                                    <i style="width: 20px;" class="icon-base ti tabler-shopping-cart-dollar"></i> Comprar
                                                </div>
                                                <div class="dropdown-item d-flex gap-2 cursor-pointer align-items-center btn-play-xl" data-editable="false" id="{{$file['id']}}" data-url="{{$file['url']}}" data-name="{{$file['name']}}" data-state="pause" data-section="lives" onclick="playAudio(this)">
                                                    <i style="width: 20px;" class="icon-base ti tabler-player-play-filled"></i> Reproducir
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $lives->links() }}
                    </div>
                </div>
            </div>
            @else
                <h4 class="text-center text-primary mt-2">Sin Sesiones Disponibles</h4>
            @endif
        </div>
        <div class="container" style="margin-top: 60px;">
            <div class="text-center mb-4">
                <span class="badge bg-label-primary">🎶 MIX & REMIX</span>
            </div>
            @if (!$mixes->isEmpty())
            <div class="card" style="background-color: transparent !important">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatables-basic table table-hover">
                        <thead>
                            <tr>
                                <th class="hidden-mobile"></th>
                                <th class="hidden-mobile">Fecha</th>
                                <th>REMIXERS</th>
                                <th>Nombre</th>
                                <th class="hidden-mobile">BPM</th>
                                <th class="hidden-mobile">Categorías</th>
                                <th class="hidden-xl">PRECIO</th>
                                <th class="hidden-xl">MONEDA</th>
                                <th class="hidden-xl"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mixes as $file)
                                @php
                                    $date = Carbon::parse($file['date'])->format('d/m/Y');
                                @endphp
                                <tr class="result" data-id="{{$file['id']}}" data-url="{{$file['url']}}">
                                    <td class="hidden-mobile">
                                        <div class="avatar overflow-hidden rounded-circle">
                                            <img src="{{ $file['logotipe'] ? $file['logotipe'] : config('app.logo') }}" alt="Avatar" class="rounded-circle" />
                                            <div class="dark-screen" style="background-color: rgba(0, 0, 0, 0.5);"></div>
                                        </div>
                                    </td>
                                    <td class="hidden-mobile">{{ $date }}</td>
                                    <td><span style="color: var(--bs-primary)">{{ $file['user'] }}</span></td>
                                    <td>
                                        <span class="d-block overflow-hidden name"
                                                style="text-overflow:ellipsis;max-width: 500px">
                                            {{ $file['name'] }} 
                                        </span>
                                    </td>
                                    <td class="hidden-mobile">{{ $file['bpm'] }}</td>
                                    <td class="hidden-mobile">
                                        @if(count($file['categories']) > 0)
                                            @foreach($file['categories'] as $cat)
                                                <small class="badge bg-label-primary">{{ $cat->name }}</small>
                                            @endforeach
                                        @else
                                            <small class="badge bg-label-primary">Sin Categoría</small>
                                        @endif
                                    </td>
                                    <td class="hidden-xl">
                                        <span class="d-block w-100 text-nowrap overflow-hidden"
                                                style="text-overflow:ellipsis;">
                                            {{ $file['price'] }}
                                        </span>
                                    </td>
                                    <td class="hidden-xl">
                                        {{ $file['currency'] ?? 'USD' }}
                                    </td>
                                    <td class="hidden-xl">
                                        <a style="display: flex; width: 25px; color: var(--download-button)" class="cursor-pointer" data-usd="{{ route('radio.file.pay', ['file' => $file['id']]) }}" data-cup="{{ route('payment.cup.form', ['file' => $file['id']]) }}" onclick="proccessPayment(this)">
                                            <i style="width: 20px;" class="icon-base ti tabler-shopping-cart-dollar"></i>
                                        </a>
                                    </td>
                                    <td class="hidden-xl">
                                        <a id="{{$file['id']}}" style="display: flex; width: 20px; color: var(--play-button)" class="play-button cursor-pointer" data-editable="true" data-url="{{$file['url']}}" data-name="{{$file['name']}}" data-state="pause" data-section="mix" onclick="playAudio(this)"
                                                ><i style="width: 20px;" class="icon-base ti tabler-player-play-filled"></i></a>
                                    </td>
                                    <td class="show-xl">
                                        <div class="dropdown d-flex justify-content-center">
                                            <button class="btn btn-text-secondary btn-icon rounded-pill text-body-secondary border-0" type="button" id="BulkOptions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="icon-base ti tabler-dots icon-22px text-body-secondary"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="BulkOptions">
                                                <p class="pt-2 text-center">Precio: {{ $file['price'] }} {{ $file['currency'] ?? 'USD' }}</p>
                                                <div class="dropdown-item d-flex gap-2 cursor-pointer align-items-center" data-usd="{{ route('radio.file.pay', ['file' => $file['id']]) }}" data-cup="{{ route('payment.cup.form', ['file' => $file['id']]) }}" onclick="proccessPayment(this)">
                                                    <i style="width: 20px;" class="icon-base ti tabler-shopping-cart-dollar"></i> Comprar
                                                </div>
                                                <div class="dropdown-item d-flex gap-2 cursor-pointer align-items-center btn-play-xl" data-editable="false" id="{{$file['id']}}" data-url="{{$file['url']}}" data-name="{{$file['name']}}" data-state="pause" data-section="mix" onclick="playAudio(this)">
                                                    <i style="width: 20px;" class="icon-base ti tabler-player-play-filled"></i> Reproducir
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $mixes->links() }}
                    </div>
                </div>
            </div>
            @else
                <h4 class="text-center text-primary mt-2">Sin Mixes Disponibles</h4>
            @endif
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
                                <spam style="position: absolute; top: 24px; right: 24px; cursor: pointer" onclick="stopVideo()">✖️</spam>
                                <div class="card-body w-100 h-100">
                                    <video class="w-100" style="max-height: 80vh;" id="plyr-video-player" oncontextmenu="return false;" playsinline controls></video>
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
    new Plyr("#plyr-video-player"),new Plyr("#plyr-audio-player");

    window.addEventListener('DOMContentLoaded', function() {
        document.body.classList.remove('bg-body');
        document.querySelectorAll('.navbar-right li')[0].remove();
    })

    let currentAudio = null;
    let currentTrack = 0;
    const audioExtensions = ['.mp3', '.wav', '.ogg', '.m4a'];
    const videoExtensions = ['.mp4', '.avi', '.mov', '.wmv', '.mkv'];

    let tracks = @json($mixesPlayList);

    function stopCurrentAudio() {
        if (currentAudio) {
            currentAudio.pause();
            currentAudio.currentTime = 0;
            currentAudio = null;
        }
    }

    function stopVideo(){
        document.getElementById('plyr-video-player').pause();
        document.getElementById('video-player').style.display = 'none';
    }

    function playVideo(title){
        document.getElementById('video-player').style.display = 'flex';
        document.getElementById('video-title').innerHTML = title;
        document.getElementById('plyr-video-player').play();
    }

    function playAudio(element){
        let audio = document.getElementById('plyr-audio-player');
        let index = 0;
        let section = element.dataset.section;

        if (section==='mix') {
            tracks = @json($mixesPlayList);
        } else if (section==='lives') {
            tracks = @json($livesPlayList);
        }
    
        if (tracks.length === 1) {
            document.querySelectorAll('.audio-player-controls').forEach(control => {
                control.style.display="none";
            });
        }

        tracks.forEach(track => {
            const extension = track.url.substring(track.url.lastIndexOf('.')).toLowerCase();
            if (track.id === parseInt(element.id)) {
                if(audioExtensions.includes(extension)){
                    audio.src = track.url;
                    if(element.dataset.state == "pause"){
                        stopCurrentAudio();
                        if (element.dataset.editable === "true") {
                            document.querySelectorAll('.play-button').forEach(button => {
                                if(button.dataset.state === "play" && button !== element){
                                    button.innerHTML = '<i style="width: 20px;" class="icon-base ti tabler-player-play-filled"></i>';
                                    button.dataset.state = "pause";
                                }
                            });
                        } else {
                            document.querySelectorAll('.btn-play-xl').forEach(button => {
                                if(button.dataset.state === "play" && button !== element){
                                    button.innerHTML = '<i style="width: 20px;" class="icon-base ti tabler-player-play-filled"></i> Reproducir';
                                    button.dataset.state = "pause";
                                }
                            });
                        }
                        document.getElementById('audioPlayer').style.transform = 'translateY(0)';
                        document.getElementById('plyr-audio-name').innerText = track.title;
                        currentAudio = audio;
                        currentTrack = index;
                        audio.play();
                        if (element.dataset.editable === "true") {
                            element.innerHTML = '<i style="width: 20px;" class="icon-base ti tabler-player-pause-filled"></i>';
                        } else {
                            element.innerHTML = '<i style="width: 20px;" class="icon-base ti tabler-player-pause-filled"></i> Pausar';
                        }
                        element.dataset.state = "play";
                    } else {
                        if (element.dataset.editable === "true") {
                            element.innerHTML = '<i style="width: 20px;" class="icon-base ti tabler-player-play-filled"></i>';
                        } else {
                            element.innerHTML = '<i style="width: 20px;" class="icon-base ti tabler-player-play-filled"></i> Reproducir';
                        }
                        stopCurrentAudio();
                        element.dataset.state = "pause";
                        document.getElementById('audioPlayer').style.transform = 'translateY(160px)';
                    }
                    
                    audio.addEventListener('ended', () => {
                        element.innerHTML = '<i style="width: 20px;" class="icon-base ti tabler-player-play-filled"></i>';
                        element.dataset.state = "pause";
                    });
                } else {
                    stopVideo();
                    stopCurrentAudio();
                    document.querySelectorAll('.play-button').forEach(button => {
                        if(button.dataset.state === "play" && button !== element){
                            button.innerHTML = '{{ svg('vaadin-play') }}';
                            button.dataset.state = "pause";
                        }
                    });
                    document.getElementById('plyr-video-player').src = track.url;
                    playVideo(track.title);
                }
            }
            index++;
        });
    }

    function playNextAudio(){
        let audio = document.getElementById('plyr-audio-player');
        let index = 0;
        let loaded = false;
        tracks.forEach(track => {
            const extension = track.url.substring(track.url.lastIndexOf('.')).toLowerCase();
            if(!loaded){
                if(currentTrack < tracks.length - 1){
                    if (index === currentTrack + 1) {
                        if(audioExtensions.includes(extension)){
                            element = document.getElementById(track.id);
                            document.querySelectorAll('.play-button').forEach(button => {
                                if(button.dataset.state === "play" && button !== element){
                                    button.innerHTML = '<i style="width: 20px;" class="icon-base ti tabler-player-play-filled"></i>';
                                    button.dataset.state = "pause";
                                }
                            });
                            element.innerHTML = '<i style="width: 20px;" class="icon-base ti tabler-player-pause-filled"></i>';
                            element.dataset.state = "play";

                            audio.src = track.url;
                            stopCurrentAudio();
                            document.getElementById('plyr-audio-name').innerText = track.title;
                            currentAudio = audio;
                            currentTrack = index;
                            audio.play();

                            loaded = true;
                        }
                    }
                } else {
                    if (index === 0) {
                        if(audioExtensions.includes(extension)){
                            
                            element = document.getElementById(track.id);
                            document.querySelectorAll('.play-button').forEach(button => {
                                if(button.dataset.state === "play" && button !== element){
                                    button.innerHTML = '<i style="width: 20px;" class="icon-base ti tabler-player-play-filled"></i>';
                                    button.dataset.state = "pause";
                                }
                            });
                            element.innerHTML = '<i style="width: 20px;" class="icon-base ti tabler-player-pause-filled"></i>';
                            element.dataset.state = "play";

                            audio.src = track.url;
                            stopCurrentAudio();
                            document.getElementById('plyr-audio-name').innerText = track.title;
                            currentAudio = audio;
                            currentTrack = index;
                            audio.play();

                            loaded = true;
                        }
                    }
                }
            }
            index++;
        });
    }

    function playPrevAudio(){
        let audio = document.getElementById('plyr-audio-player');
        let index = 0;
        let loaded = false;
        tracks.forEach(track => {
            const extension = track.url.substring(track.url.lastIndexOf('.')).toLowerCase();
            if(!loaded){
                if(currentTrack > 0){
                    if (index === currentTrack - 1) {
                        if(audioExtensions.includes(extension)){
                            
                            element = document.getElementById(track.id);
                            document.querySelectorAll('.play-button').forEach(button => {
                                if(button.dataset.state === "play" && button !== element){
                                    button.innerHTML = '<i style="width: 20px;" class="icon-base ti tabler-player-play-filled"></i>';
                                    button.dataset.state = "pause";
                                }
                            });
                            element.innerHTML = '<i style="width: 20px;" class="icon-base ti tabler-player-pause-filled"></i>';
                            element.dataset.state = "play";

                            audio.src = track.url;
                            stopCurrentAudio();
                            document.getElementById('plyr-audio-name').innerText = track.title;
                            currentAudio = audio;
                            currentTrack = index;
                            audio.play();

                            loaded = true;
                        }
                    }
                } else {
                    if (index === tracks.length - 1) {
                        if(audioExtensions.includes(extension)){
                            
                            element = document.getElementById(track.id);
                            document.querySelectorAll('.play-button').forEach(button => {
                                if(button.dataset.state === "play" && button !== element){
                                    button.innerHTML = '<i style="width: 20px;" class="icon-base ti tabler-player-play-filled"></i>';
                                    button.dataset.state = "pause";
                                }
                            });
                            element.innerHTML = '<i style="width: 20px;" class="icon-base ti tabler-player-pause-filled"></i>';
                            element.dataset.state = "play";

                            audio.src = track.url;
                            stopCurrentAudio();
                            document.getElementById('plyr-audio-name').innerText = track.title;
                            currentAudio = audio;
                            currentTrack = index;
                            audio.play();

                            loaded = true;
                        }
                    }
                }
            }
            index++;
        });
    }

    function proccessPayment(element) {
        Swal.fire({ 
            title: '¿Proceder a comprar el archivo?', 
            text: 'Selecciona una de las opciones de moneda para continuar con el proceso de compra.', 
            icon: 'question', 
            showCancelButton: true, 
            showDenyButton: '{{ $activeCUPPayment }}', 
            confirmButtonText: 'USD', 
            denyButtonText: 'CUP', 
            cancelButtonText: 'Cancelar' 
        }).then((result) => { 
            if (result.isConfirmed) {
                document.querySelector('#loader').style.display = 'flex';
                fetch(element.dataset.usd)
                    .then(async res => {
                        let data;
                        
                        try {
                            data = await res.json();
                        } catch {
                            document.querySelector('#loader').style.display = 'none';
                            throw new Error("Respuesta inesperada del servidor");
                        }

                        if (res.ok && data.url) {
                            window.location.href = data.url;
                        } else {
                            document.querySelector('#loader').style.display = 'none';
                            Swal.fire("Error", data.error ?? "No se pudo generar la sesión de pago", "error");
                        }
                    })
                    .catch(err => {
                        document.querySelector('#loader').style.display = 'none';
                        Swal.fire("Error", err.message, "error");
                    });
            } else if (result.isDenied) { 
                window.location.href = element.dataset.cup;
            }
        });
    }
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