@extends('layouts.app')
@php
    use Carbon\Carbon;
    Carbon::setLocale('es');
@endphp
@section('title', 'Página de Resultados de Busqueda')

@push('styles')
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/plyr/plyr.css') }}" />
    <style>
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

        /* Card “Relacionadas” clickable + overlay + flecha de entrar */
        .card-relationed {
            position: relative;
            overflow: hidden;
            border-radius: 14px;
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .card-relationed:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, .08);
        }

        .card-relationed .dark-screen {
            position: absolute;
            inset: 0;
            background: linear-gradient(0deg, rgba(0, 0, 0, .35), rgba(0, 0, 0, .10));
            opacity: 0;
            transition: opacity .15s ease;
        }

        .card-relationed:hover .dark-screen {
            opacity: 1;
        }

        .card-relationed .enter-arrow {
            position: absolute;
            right: 10px;
            bottom: 10px;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(255, 255, 255, .9);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transform: translateY(6px);
            opacity: 0;
            transition: opacity .15s ease, transform .15s ease, background .15s ease;
            border: 1px solid rgba(0, 0, 0, .08);
        }

        .card-relationed:hover .enter-arrow {
            opacity: 1;
            transform: translateY(0);
        }

        .card-relationed .enter-arrow:hover {
            background: #fff;
        }

        /* Mejoras pequeñas en la lista de archivos */
        .file-list .list-group-item {
            border-left: 0;
            border-right: 0;
        }

        .file-list .list-group-item:first-child {
            border-top: 0;
        }

        .file-list .list-group-item:last-child {
            border-bottom: 0;
        }
    </style>
@endpush

@section('content')
    <!-- Content wrapper -->
    <div class="content-wrapper bg-body">
        <!-- Content -->
        <div class="container flex-grow-1 container-p-y mt-10">
            <div class="row g-6 mt-10">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-6 gap-2">
                                <div class="me-1">
                                    <h5 class="mb-0">{{ $collection->name }}</h5>
                                    <p class="mb-0">Autor. <span
                                            class="fw-medium text-heading">{{ $collection->user->name }}</span></p>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    @auth
                                        @if (Auth::user()?->hasActivePlan())
                                            <a style="display:flex;width:20px" title="Descargar Álbum Completo"
                                                href="{{ route('collection.download', $collection->id) }}">
                                                {{ svg('entypo-download') }}
                                            </a>
                                        @endif
                                    @endauth
                                </div>
                            </div>

                            <div class="card academy-content shadow-none border">
                                <div class="p-2">
                                    <div class="cursor-pointer d-flex justify-content-center">
                                        <img class="w-75" style="max-height:400px"
                                            src="{{ $collection->image ? $collection->image : asset('assets/img/front-pages/icon/collection.png') }}" />
                                    </div>
                                </div>

                                <div class="card-body pt-4">
                                    <hr class="my-6" />
                                    <h5>Lista</h5>
                                    <div class="card mb-6">
                                        @foreach ($results as $file)
                                            <div class="row py-2">
                                                <div
                                                    class="{{ Auth::user()?->hasActivePlan() || !($file['price'] > 0) ? 'col-10 mb-3' : 'col-7 col-sm-8 mb-3' }}">
                                                    <span class="d-block w-100 text-nowrap overflow-hidden"
                                                        style="text-overflow:ellipsis;">
                                                        {{ $file['name'] }}
                                                    </span>
                                                    <spam><strong>
                                                            BPM: {{ $file['bpm'] ?? 'No definido' }}
                                                        </strong></spam>
                                                </div>
                                                @auth
                                                    @if (!Auth::user()?->hasActivePlan() && $file['price'] > 0)
                                                        <div class="col-3 col-sm-2">
                                                            <span class="d-block w-100 text-nowrap overflow-hidden"
                                                                style="text-overflow:ellipsis;">
                                                                $ {{ $file['price'] }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    <div class="col-1">
                                                        @if (Auth::user()?->hasActivePlan() || !($file['price'] > 0))
                                                            <a style="display: flex; width: 20px"
                                                                href="{{ route('file.download', $file['id']) }}">{{ svg('entypo-download') }}</a>
                                                        @else
                                                            <a style="display: flex; width: 20px; cursor: pointer"
                                                                data-url="{{ route('file.pay', $file['id']) }}"
                                                                onclick="proccessPayment(this.dataset.url)">{{ svg('vaadin-cart') }}</a>
                                                        @endif
                                                    </div>
                                                @else
                                                    @if ($file['price'] > 0)
                                                        <div class="col-3 col-sm-2">
                                                            <span class="d-block w-100 text-nowrap overflow-hidden"
                                                                style="text-overflow:ellipsis;">
                                                                $ {{ $file['price'] }}
                                                            </span>
                                                        </div>
                                                        <div class="col-1">
                                                            <a style="display: flex; width: 20px; cursor: pointer"
                                                                data-url="{{ route('file.pay', $file['id']) }}"
                                                                onclick="proccessPayment(this.dataset.url)">{{ svg('vaadin-cart') }}</a>
                                                        </div>
                                                    @else
                                                        <div class="col-1">
                                                            <a style="display: flex; width: 20px"
                                                                href="{{ route('file.download', $file['id']) }}">{{ svg('entypo-download') }}</a>
                                                        </div>
                                                    @endif
                                                @endauth
                                                <div class="col-1">
                                                    @if (!$file['isZip'])
                                                        <a id="{{ $file['id'] }}" style="display: flex; width: 20px"
                                                            class="play-button cursor-pointer"
                                                            data-name="{{ $file['name'] }}"
                                                            data-url="{{ $file['url'] }}" data-state="pause"
                                                            onclick="playAudio(this)">{{ svg('vaadin-play') }}</a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div> {{-- /card content --}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="pb-1 mb-6">Packs Relacionados</h5>
                            <hr class="my-6" />
                            <div class="row mb-12 g-6">
                                @foreach ($relationeds as $coll)
                                    <div class="col-12">
                                        {{-- Toda la card es enlace; el “play” se sustituyó por flecha de entrar --}}
                                        <a class="card card-relationed" href="{{ route('collection.show', $coll->id) }}">
                                            <div class="row g-0 align-items-center">
                                                <div class="col-md-4">
                                                    <img class="card-img card-img-left w-100"
                                                        style="max-height:80px;object-fit:cover"
                                                        src="{{ $coll->image ? $coll->image : asset('assets/img/front-pages/icon/collection.png') }}" />
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="card-body p-3">
                                                        <h6 class="card-title mb-1 text-truncate">{{ $coll->name }}</h6>
                                                        <p class="card-text mb-0">
                                                            <small class="text-body-secondary">Subido por
                                                                {{ $coll->user->name }}</small>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Overlay + flecha de entrar --}}
                                            <div class="dark-screen"></div>
                                            <div class="enter-arrow" aria-hidden="true" title="Entrar a la colección">
                                                <i class="ti tabler-arrow-right"></i>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="window-notice" id="video-player">
            <div class="content">
                <div class="container-xxl flex-grow-1 container-p-y w-100">
                    <div class="row gy-6">
                        <!-- Video Player -->
                        <div class="col-12">
                            <div class="card" style="position: relative">
                                <h5 class="card-header d-block text-nowrap overflow-hidden"
                                    style="text-overflow:ellipsis; width: 90%" id="video-title">Nombre del Video</h5>
                                <spam style="position: absolute; top: 24px; right: 24px; cursor: pointer"
                                    onclick="stopVideo()">✖️</spam>
                                <div class="card-body">
                                    <video class="w-100" id="plyr-video-player" oncontextmenu="return false;" playsinline>
                                    </video>
                                </div>
                            </div>
                        </div>
                        <!-- /Video Player -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section id="audioPlayer" class="bg-body">
        <div class="container">
            <div class="row">
                <!-- Audio Player -->
                <div class="col-12">
                    <div class="card">
                        <h5 class="card-header d-flex justify-content-between">
                            <span class="cursor-pointer audio-player-controls" onclick="playPrevAudio()"><i class="icon-base ti tabler-chevron-left icon-md scaleX-n1-rtl"></i></span>
                            <span id="plyr-audio-name" class="d-block w-100 text-nowrap overflow-hidden"
                                style="text-overflow:ellipsis; text-align:center">Audio</span>
                            <span class="cursor-pointer audio-player-controls" onclick="playNextAudio()"><i class="icon-base ti tabler-chevron-right icon-md scaleX-n1-rtl"></i></span>
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
    </script>
    <script>
        let currentAudio = null;
        let currentTrack = 0;

        function stopCurrentAudio() {
            if (currentAudio) {
                currentAudio.pause();
                currentAudio.currentTime = 0;
                currentAudio = null;
            }
        }

        function stopVideo() {
            document.getElementById('plyr-video-player').pause();
            document.getElementById('video-player').style.display = 'none';
        }

        function playVideo(title) {
            document.getElementById('video-player').style.display = 'flex';
            document.getElementById('video-title').innerHTML = title;
            document.getElementById('plyr-video-player').play();
        }

        function playAudio(element){
            let audio = document.getElementById('plyr-audio-player');

            const rute = "{{ route('collections.playlist', $collection->id)}}";

            fetch(rute, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                let index = 0;
                data.forEach(track => {
                    if (track.id === parseInt(element.id)) {
                        if(track.url.endsWith('.mp3')){
                            audio.src = track.url;
                            if(element.dataset.state == "pause"){
                                stopCurrentAudio();
                                document.querySelectorAll('.play-button').forEach(button => {
                                    if(button.dataset.state === "play" && button !== element){
                                        button.innerHTML = '{{ svg('vaadin-play') }}';
                                        button.dataset.state = "pause";
                                    }
                                });
                                document.getElementById('audioPlayer').style.transform = 'translateY(0)';
                                document.getElementById('plyr-audio-name').innerText = track.title;
                                currentAudio = audio;
                                currentTrack = index;
                                audio.play();
                                element.innerHTML = '{{ svg('vaadin-pause') }}';
                                element.dataset.state = "play";
                            } else {
                                element.innerHTML = '{{ svg('vaadin-play') }}';
                                stopCurrentAudio();
                                element.dataset.state = "pause";
                                document.getElementById('audioPlayer').style.transform = 'translateY(160px)';
                            }
                            
                            audio.addEventListener('ended', () => {
                                element.innerHTML = '{{ svg('vaadin-play') }}';
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
            })
            .catch(error => {
                Swal.fire("Error", error.message, "error");
            });
        }

        function playNextAudio(){
            let audio = document.getElementById('plyr-audio-player');

            const rute = "{{ route('collections.playlist', $collection->id)}}";

            fetch(rute, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                let index = 0;
                let loaded = false;
                data.forEach(track => {
                    if(!loaded){
                        if(currentTrack < data.length - 1){
                            if (index === currentTrack + 1) {
                                if(track.url.endsWith('.mp3')){
                                    
                                    element = document.getElementById(track.id);
                                    document.querySelectorAll('.play-button').forEach(button => {
                                        if(button.dataset.state === "play" && button !== element){
                                            button.innerHTML = '{{ svg('vaadin-play') }}';
                                            button.dataset.state = "pause";
                                        }
                                    });
                                    element.innerHTML = '{{ svg('vaadin-pause') }}';
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
                                if(track.url.endsWith('.mp3')){
                                    
                                    element = document.getElementById(track.id);
                                    document.querySelectorAll('.play-button').forEach(button => {
                                        if(button.dataset.state === "play" && button !== element){
                                            button.innerHTML = '{{ svg('vaadin-play') }}';
                                            button.dataset.state = "pause";
                                        }
                                    });
                                    element.innerHTML = '{{ svg('vaadin-pause') }}';
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
            })
            .catch(error => {
                Swal.fire("Error", error.message, "error");
            });
        }

        function playPrevAudio(){
            let audio = document.getElementById('plyr-audio-player');

            const rute = "{{ route('collections.playlist', $collection->id)}}";

            fetch(rute, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                let index = 0;
                let loaded = false;
                data.forEach(track => {
                    if(!loaded){
                        if(currentTrack > 0){
                            if (index === currentTrack - 1) {
                                if(track.url.endsWith('.mp3')){
                                    
                                    element = document.getElementById(track.id);
                                    document.querySelectorAll('.play-button').forEach(button => {
                                        if(button.dataset.state === "play" && button !== element){
                                            button.innerHTML = '{{ svg('vaadin-play') }}';
                                            button.dataset.state = "pause";
                                        }
                                    });
                                    element.innerHTML = '{{ svg('vaadin-pause') }}';
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
                            if (index === data.length - 1) {
                                if(track.url.endsWith('.mp3')){
                                    
                                    element = document.getElementById(track.id);
                                    document.querySelectorAll('.play-button').forEach(button => {
                                        if(button.dataset.state === "play" && button !== element){
                                            button.innerHTML = '{{ svg('vaadin-play') }}';
                                            button.dataset.state = "pause";
                                        }
                                    });
                                    element.innerHTML = '{{ svg('vaadin-pause') }}';
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
            })
            .catch(error => {
                Swal.fire("Error", error.message, "error");
            });
        }

        function proccessPayment(rute) {
            Swal.fire({
                title: '¿Proceder con el pago?',
                text: "Serás redirigido a Stripe para completar tu pago.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.querySelector('#loader').style.display = 'flex';
                    fetch(rute)
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
                                Swal.fire("Error", data.error ?? "No se pudo generar la sesión de pago",
                                    "error");
                            }
                        })
                        .catch(err => {
                            document.querySelector('#loader').style.display = 'none';
                            Swal.fire("Error", err.message, "error");
                        });
                }
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
