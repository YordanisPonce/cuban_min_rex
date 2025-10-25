@extends('layouts.app')
@php
    use Carbon\Carbon;
    Carbon::setLocale('es');
@endphp
@section('title', 'Página de Resultados de Busqueda')

@push('styles')
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page.css') }}" />
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page-payment.css') }}" />
@endpush

@section('content')
    <section id="musicSearch" class="section-py bg-body h-100">
        <div class="container" style="margin-top: 60px;">
            <div class="text-center mb-4">
                <span class="badge bg-label-primary">🎶 Archivos Disponibles</span>
            </div>
            <div class="card">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatables-basic table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Fecha</th>
                                <th>{{ isset($remixes) ? 'REMIXERS' :'Subido por'}}</th>
                                <th>Nombre</th>
                                <th>Álbum</th>
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
                                <tr>
                                    <td></td>
                                    <td>{{ $date }}</td>
                                    <td>{{ $file['user'] }}</td>
                                    <td>
                                        <span class="d-block w-100 text-nowrap overflow-hidden"
                                                style="text-overflow:ellipsis;">
                                            {{ $file['name'] }}
                                        </span>
                                        <spam><strong>
                                            BPM: {{ $file['bpm'] ?? 'No definido' }}
                                        </strong></spam>
                                    </td>
                                    <td>{{ $file['collection'] }}</td>
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
                                        <a id="{{$file['id']}}" style="display: flex; width: 20px" class="play-button cursor-pointer" data-url="{{$file['url']}}" data-state="pause" onclick="playAudio(this)"
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
    </section>
@endsection

@push('scripts')
<script>
    let currentAudio = null;

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
        let audio = document.createElement('audio');

        const rute = element.dataset.url;
        
        fetch(rute, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            track = data.filter(item => item.id === parseInt(element.id));
            console.log(track[0].url);
            if(track[0].url.endsWith('.mp3')){
                audio.src = track[0].url;
                if(element.dataset.state == "pause"){
                    stopCurrentAudio();
                    document.querySelectorAll('.play-button').forEach(button => {
                        if(button.dataset.state === "play" && button !== element){
                            button.innerHTML = '{{ svg('vaadin-play') }}';
                            button.dataset.state = "pause";
                        }
                    });
                    currentAudio = audio;
                    audio.play();
                    element.innerHTML = '{{ svg('vaadin-pause') }}';
                    element.dataset.state = "play";
                } else {
                    element.innerHTML = '{{ svg('vaadin-play') }}';
                    stopCurrentAudio();
                    element.dataset.state = "pause";
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
                document.getElementById('plyr-video-player').src = track[0].url;
                playVideo(track[0].title);
            }
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