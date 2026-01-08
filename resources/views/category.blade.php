@extends('layouts.app')
@php
    use Carbon\Carbon;
    Carbon::setLocale('es');
    $success = session('success');
    $error = session('error');
@endphp
@section('title', 'Página de Resultados de Busqueda')

@push('styles')
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/plyr/plyr.css') }}" />
    <style>
        @isset($radio)
        :root{
            --azul: #0079FF;
            --rojo: #ff0000;

            --bs-primary: var(--red) !important;

            --bs-paper-bg: #000 !important;

            --download-button: var(--rojo) !important;
            --play-button: var(--azul) !important;
        }
        .text-black{
            color: white !important;
        }

        .bg-body {
            background-color: black !important;
        }
        @endisset
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

        @media (max-width: 400px) {
            .packs-link {
                bottom: -12px !important;
            }
        }
    </style>
@endpush

@section('content')
    <section id="landingReviews" class="section-py bg-body">
        <!-- What people say slider: Start -->
        <div class="container mt-10">
            <div class="row align-items-center gx-0 gy-4 g-lg-5 mb-5 pb-md-5">
                <div class="col-md-12 flex flex-column align-items-center">
                    @isset($ctgName)
                    <div class="mb-4">
                        <span class="badge bg-label-primary">Categoría</span>
                    </div>
                    <h4 class="mb-1">
                        <span class="position-relative fw-extrabold z-1">{{ $ctgName }}
                            <img src="{{ asset('assets/img/front-pages/icon/section-title-icon.png') }}"
                                alt="laptop charging"
                                class="section-title-img position-absolute object-fit-contain bottom-0 z-n1">
                        </span>
                    </h4>
                    @else
                    <div class="mb-4">
                        <span class="badge bg-label-primary">{{ $badge }}</span>
                    </div>
                    @endisset
                </div>
                @isset($radio)
                    <div class="packs">
                        @foreach ($collections as $pack)
                            @include('partials.radio-pack-card', ['pack' => $pack])
                        @endforeach
                        @if ($collections->isEmpty())
                            <h4 class="text-center text-black mt-2">Sin Packs</h4>
                        @endif
                    </div>
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $collections->links() }}
                    </div>
                @else
                <div class="categories col-md-12">
                    <div class="row">
                        @foreach ($collections as $item)
                            @include('partials.pack-card', ['item' => $item])
                        @endforeach
                    </div>
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $collections->links() }}
                    </div>
                </div>
                @endisset
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