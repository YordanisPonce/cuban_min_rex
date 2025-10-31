@extends('layouts.app')
@php
    use Carbon\Carbon;
    Carbon::setLocale('es');
    $success = session('success');
    $error = session('error');
@endphp
@section('title', isset($remixes) ? 'Remixes - '.config('app.name') : 'Página de Resultados de Búsqueda')

@push('styles')
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page.css') }}" />
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page-payment.css') }}" />
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
    <section id="musicSearch" class="section-py bg-body">
        <div class="container" style="margin-top: 60px;">
            <div class="text-center mb-4">
                <span class="badge bg-label-primary">🎶 Archivos Disponibles{{isset($category) ? ' de la categoría '.$category->name : ''}}</span>
            </div>
            <div class="card">
                <div class="card-datatable table-responsive pt-0">
                    <form id="filter-form" class="row mx-3 my-0 justify-content-between" action="">
                        <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
                            <div class="me-2 my-6">Filtros: </div>
                            @isset($category)
                            @else
                            <div class="dt-length me-md-4 my-6">
                                <select class="form-select" id="dt-filter-1" name="categories" onchange="filter()">
                                    <option value="" selected>Todas las categorías</option>
                                    @isset($allCategories)
                                        @foreach ($allCategories as $category)
                                            <option value="{{ $category->name }}"
                                                {{ request()->query('categories') ? (request()->query('categories')===$category->name ? 'selected' : '' ) : ''}}
                                            >{{ $category->name }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            @endisset
                            <div class="dt-length my-6">
                                <select class="form-select" name="remixers" id="dt-filter-2" onchange="filter()">
                                    <option value="" selected>Todos los Remixers</option>
                                    @isset($allRemixers)
                                        @foreach ($allRemixers as $remixer)
                                            <option value="{{ $remixer->name }}"
                                            {{ request()->query('remixers') ? (request()->query('remixers')===$remixer->name ? 'selected' : '' ) : ''}}
                                            >{{ $remixer->name }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                         </div>
                        <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0">
                            <div class="dt-search my-6">
                                <input type="search" name="search" class="form-control" id="dt-search" placeholder="Buscar canción" onchange="filter()"
                                value="{{ request()->query('search') ?? '' }}">
                            </div>
                        </div>
                    </form>
                    <table class="datatables-basic table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Fecha</th>
                                <th>{{ isset($remixes) ? 'REMIXERS' :'Subido por'}}</th>
                                <th>Nombre</th>
                                <th>BPM</th>
                                <th>Categoría</th>
                                @auth
                                    @if (!Auth::user()->hasActivePlan())
                                        <th></th>
                                    @endif
                                @else
                                    <th></th>
                                @endauth
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($results as $file)
                                @php
                                    $date = Carbon::parse($file['date'])->translatedFormat('d \d\e F \d\e Y H:i');
                                    $visible = true;
                                    if( isset($file['ext']) && $file['ext'] !== 'mp3') $visible = false;
                                @endphp
                                @if ($visible)
                                <tr class="result" data-remixer="{{ $file['user'] }}" data-category="{{ $file['category'] }}" data-name="{{ $file['name'] }}">
                                    <td></td>
                                    <td>{{ $date }}</td>
                                    <td>{{ $file['user'] }}</td>
                                    <td>
                                        <span class="d-block w-100 text-nowrap overflow-hidden"
                                                style="text-overflow:ellipsis;">
                                            {{ $file['name'] }}
                                        </span>
                                    </td>
                                    <td>{{ $file['bpm'] }}</td>
                                    <td>{{ $file['category'] }}</td>
                                    @auth
                                        @if (!Auth::user()->hasActivePlan())
                                            @if ($file['price'] > 0)
                                            <td><span class="d-block w-100 text-nowrap overflow-hidden"
                                                style="text-overflow:ellipsis;">
                                                $ {{ $file['price'] }}
                                            </span></td>
                                            @else
                                            <td></td>
                                            @endif
                                        @endif
                                        <td>
                                            @if (Auth::user()->hasActivePlan() || !($file['price'] > 0))
                                                <a style="display: flex; width: 20px"
                                                    href="{{ route('file.download', $file['id'])}}">{{ svg('entypo-download') }}</a>
                                            @else
                                                <a style="display: flex; width: 20px" data-url="{{route('file.pay', $file['id']) }}"  onclick="proccessPayment(this.dataset.url)">{{ svg('vaadin-cart') }}</a>
                                            @endif
                                        </td>
                                    @else
                                        @if ($file['price'] > 0)
                                            <td><span class="d-block w-100 text-nowrap overflow-hidden"
                                                style="text-overflow:ellipsis;">
                                                $ {{ $file['price'] }}
                                            </span></td>
                                            <td>
                                                <a style="display: flex; width: 20px" data-url="{{route('file.pay', $file['id']) }}"  onclick="proccessPayment(this.dataset.url)">{{ svg('vaadin-cart') }}</a>
                                            </td>
                                        @else
                                            <td></td>
                                            <td>
                                                <a style="display: flex; width: 20px"
                                                    href="{{ route('file.download', $file['id'])}}">{{ svg('entypo-download') }}</a>
                                            </td>
                                        @endif
                                    @endauth
                                    <td>
                                        @if (!$file['isZip'])
                                        <a id="{{$file['id']}}" style="display: flex; width: 20px" class="play-button cursor-pointer" data-url="{{$file['url']}}" data-name="{{$file['name']}}" data-state="pause" onclick="playAudio(this)"
                                                >{{ svg('vaadin-play') }}</a>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                    <nav class="mt-3">
                        <ul class="pagination justify-content-center" style="--bs-pagination-border-radius: 0%;">
                            @if ($results->onFirstPage())
                                <li class="page-item disabled"><span class="page-link">Anterior</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $results->previousPageUrl() }}">Anterior</a></li>
                            @endif

                            @for ($i = 1; $i <= $results->lastPage(); $i++)
                                @if ($i == $results->currentPage())
                                    <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $results->url($i) }}">{{ $i }}</a></li>
                                @endif
                            @endfor

                            @if ($results->hasMorePages())
                                <li class="page-item"><a class="page-link" href="{{ $results->nextPageUrl() }}">Siguiente</a></li>
                            @else
                                <li class="page-item disabled"><span class="page-link">Siguiente</span></li>
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
            @if ($results->isEmpty())
                <h4 class="text-center text-primary mt-2">Sin resultados</h4>
            @endif
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
                                <spam style="position: absolute; top: 24px; right: 24px; cursor: pointer" onclick="stopVideo()">✖️</spam>
                                <div class="card-body">
                                    <video class="w-100" id="plyr-video-player" oncontextmenu="return false;" playsinline controls></video>
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
    const audioExtensions = ['.mp3', '.wav', '.ogg', '.m4a'];
    const videoExtensions = ['.mp4', '.avi', '.mov', '.wmv', '.mkv'];

    const tracks = [];
    let music;
    
    @isset($playList[0])
    @foreach ($playList[0] as $track)
        music = {
            'id' : {{$track['id']}},
            'url' : "{{ str_replace('\\', '/', $track['url']) }}",
            'title' : "{{$track['title']}}",
        }
        if(document.getElementById("{{$track['id']}}")!= null){
            tracks.push(music);
        }
    @endforeach
    @endisset
    
    if (tracks.length === 1) {
        document.querySelectorAll('.audio-player-controls').forEach(control => {
            control.style.display="none";
        });
    }

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
        tracks.forEach(track => {
            const extension = track.url.substring(track.url.lastIndexOf('.')).toLowerCase();
            if (track.id === parseInt(element.id)) {
                if(audioExtensions.includes(extension)){
                    console.log(track.url);
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
                        element.innerHTML = '{{ svg('vaadin-close') }}';
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
    }
    

    function playPrevAudio(){
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
                                    button.innerHTML = '{{ svg('vaadin-play') }}';
                                    button.dataset.state = "pause";
                                }
                            });
                            element.innerHTML = '{{ svg('vaadin-close') }}';
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
                                    button.innerHTML = '{{ svg('vaadin-play') }}';
                                    button.dataset.state = "pause";
                                }
                            });
                            element.innerHTML = '{{ svg('vaadin-close') }}';
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

    function playNextAudio(){
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
                                    button.innerHTML = '{{ svg('vaadin-play') }}';
                                    button.dataset.state = "pause";
                                }
                            });
                            element.innerHTML = '{{ svg('vaadin-close') }}';
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
                                    button.innerHTML = '{{ svg('vaadin-play') }}';
                                    button.dataset.state = "pause";
                                }
                            });
                            element.innerHTML = '{{ svg('vaadin-close') }}';
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
                            Swal.fire("Error", data.error ?? "No se pudo generar la sesión de pago", "error");
                        }
                    })
                    .catch(err => {
                        document.querySelector('#loader').style.display = 'none';
                        Swal.fire("Error", err.message, "error");
                    });
            }
        });
    }

    function filter() {
        document.getElementById('filter-form').submit();
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