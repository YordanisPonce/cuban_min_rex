@extends('layouts.app')
@php
    use App\Models\Cart;
    $success = session('success');
    $error = session('error');
@endphp

@section('title', "Inicio – ".config('app.name'))

@push('styles')
<link rel="stylesheet" href="{{ asset('/assets/vendor/libs/plyr/plyr.css') }}" />
<style>

    .player--dark {
        background-color: #12131C !important;
        color: #fff;
    }

    footer{
        z-index: 11;
    }
    section#audioPlayer{
        transition: all 0.3s ease-in;
        transform: translateY(160px);
    }

    #audioPlayer{
        position: sticky;
        bottom: 0;
        width: 100%;
        z-index: 10;
    }

    .bg-body{
        background-color: transparent !important;
    }
</style>
@endpush

@section('content')
    
    {{-- =========================
       NUEVOS LANZAMIENTOS
    ========================== --}}
    <section id="home-new" class="section-py mt-10">

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
                        <span class="badge bg-label-primary mb-2">Novedades</span>
                        <h2 class="h3 fw-bold mb-1">Estrenos de la semana</h2>
                        <p class="text-body-secondary mb-0">Singles y packs recién salidos. Lo último de tus artistas
                            favoritos.</p>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="border rounded-4 p-4 p-md-5 text-center bg-body">
                    <h3 class="h5 fw-bold mb-2">No hay lanzamientos recientes</h3>
                    <p class="text-body-secondary mb-3">Vuelve pronto: actualizamos esta sección con nuevos estrenos.</p>
                    <a href="{{ route('search') }}" class="btn btn-outline-secondary">Explorar catálogos</a>
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
                    <a href="{{ route('search') }}" class="btn btn-outline-secondary">Explorar catálogos</a>
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
                <h2 class="text-center fw-bold mb-2">Packs de artistas</h2>
                <div class="row">
                    <div class="d-md-grid" style="grid-template-columns: repeat(2, 1fr); row-gap: 4px; column-gap: 24px;">
                        @foreach ($artistCollections as $item)
                        <div class="card mb-1 mb-md-0 px-0">
                            <div class="row g-0">
                                <div style="width: 10%;">
                                    <img class="card-img card-img-left" src="{{ $item->image ? $item->imagen : ($item->user->photo ? $item->user->photo : config('app.logo')) }}" alt="{{ $item->name }}" />
                                </div>
                                <div style="width: 90%;">
                                    <div class="py-2 px-4">
                                        <h5 class="card-title mb-1 text-truncate">{{ $item->name}}</h5>
                                        <p class="card-text mb-1 text-truncate" style="color: var(--bs-primary)">{{ $item->user ? $item->user->name : 'Desconocido' }}</p>
                                        <p class="card-text d-flex gap-4">
                                            <small class="text-body-secondary">{{ $item->category ? $item->category->name : 'Sin Categoría' }}</small>
                                            <small class="text-body-secondary d-flex gap-2">
                                                <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8.50989 2.00001H15.49C15.7225 1.99995 15.9007 1.99991 16.0565 2.01515C17.1643 2.12352 18.0711 2.78958 18.4556 3.68678H5.54428C5.92879 2.78958 6.83555 2.12352 7.94337 2.01515C8.09917 1.99991 8.27741 1.99995 8.50989 2.00001Z" fill="currentColor"/>
                                                    <path d="M6.31052 4.72312C4.91989 4.72312 3.77963 5.56287 3.3991 6.67691C3.39117 6.70013 3.38356 6.72348 3.37629 6.74693C3.77444 6.62636 4.18881 6.54759 4.60827 6.49382C5.68865 6.35531 7.05399 6.35538 8.64002 6.35547H15.5321C17.1181 6.35538 18.4835 6.35531 19.5639 6.49382C19.9833 6.54759 20.3977 6.62636 20.7958 6.74693C20.7886 6.72348 20.781 6.70013 20.773 6.67691C20.3925 5.56287 19.2522 4.72312 17.8616 4.72312H6.31052Z" fill="currentColor"/>
                                                    <path d="M11.25 17C11.25 16.5858 10.9142 16.25 10.5 16.25C10.0858 16.25 9.75 16.5858 9.75 17C9.75 17.4142 10.0858 17.75 10.5 17.75C10.9142 17.75 11.25 17.4142 11.25 17Z" fill="currentColor"/>
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8.67239 7.54204H15.3276C18.7024 7.54204 20.3898 7.54204 21.3377 8.52887C22.2855 9.51569 22.0625 11.0404 21.6165 14.0896L21.1935 16.9811C20.8437 19.3724 20.6689 20.568 19.7717 21.284C18.8745 22 17.5512 22 14.9046 22H9.09534C6.4488 22 5.12553 22 4.22834 21.284C3.33115 20.568 3.15626 19.3724 2.80648 16.9811L2.38351 14.0896C1.93748 11.0403 1.71447 9.5157 2.66232 8.52887C3.61017 7.54204 5.29758 7.54204 8.67239 7.54204ZM12.75 10.5C12.75 10.0858 12.4142 9.75 12 9.75C11.5858 9.75 11.25 10.0858 11.25 10.5V14.878C11.0154 14.7951 10.763 14.75 10.5 14.75C9.25736 14.75 8.25 15.7574 8.25 17C8.25 18.2426 9.25736 19.25 10.5 19.25C11.7426 19.25 12.75 18.2426 12.75 17V13.3197C13.4202 13.8634 14.2617 14.25 15 14.25C15.4142 14.25 15.75 13.9142 15.75 13.5C15.75 13.0858 15.4142 12.75 15 12.75C14.6946 12.75 14.1145 12.5314 13.5835 12.0603C13.0654 11.6006 12.75 11.0386 12.75 10.5Z" fill="currentColor"/>
                                                </svg> 
                                            {{ $item->files->count() }}</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="container mb-3">
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <span class="badge bg-label-primary mb-2">Explorar</span>
                        <h2 class="h3 fw-bold mb-1">Packs de artistas</h2>
                        <p class="text-body-secondary mb-0">Discografías esenciales, playlists temáticas y selecciones por mood.
                        </p>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="border rounded-4 p-4 p-md-5 text-center bg-body">
                    <h3 class="h5 fw-bold mb-2">Sin packs por ahora</h3>
                    <p class="text-body-secondary mb-3">Estamos preparando nuevas selecciones por artista y estilo.</p>
                    <a href="{{ route('search') }}" class="btn btn-outline-secondary">Buscar artistas</a>
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
                    <a href="{{route('category.show', $genre->id)}}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">{{ $genre->name }}</a>
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
                    @php
                        $isActive =
                            auth()->check() &&
                            auth()->user()->current_plan_id === $plan->id &&
                            auth()->user()->hasActivePlan();
                    @endphp
                    <div class="pricing-card">
                        <spam class="type">{{$plan->name}}</spam>
                        <div class="price" data-content="${{$plan->price}}"><span>$</span>{{$plan->price}}</div>
                        <h5 class="plan">plan</h5>
                        <div class="details mb-5">
                            <p>Duración: {{$plan->duration_months}} {{$plan->duration_months > 1 ? 'meses' : 'mes'}}</p>
                            <p>Descargas por archivo: {{$plan->downloads}}</p>
                            @if ($plan->features)
                                @foreach ($plan->features as $item)
                                    <p>{{ $item['value'] }}</p>
                                @endforeach
                            @endif
                        </div>
                        @if ($isActive)
                        <div class="buy-button active">
                            <h3 class="btn"><a style="color: gray">Ya lo tienes</a></h3>
                        </div>
                        @else
                        <div class="buy-button">
                            <h3 class="btn"><a href="{{ route('payment.form', $plan->id) }}">Adquirir</a></h3>
                        </div>
                        @endif
                    </div>
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
                                <spam style="position: absolute; top: 24px; right: 24px; cursor: pointer" onclick="stopVideo()">✖️</spam>
                                <div class="card-body w-100 h-100">
                                    <video class="w-100" style="max-height: 70dvh;" id="plyr-video-player" oncontextmenu="return false;" playsinline controls></video>
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
    const audioExtensions = ['.mp3', '.wav', '.ogg', '.m4a'];
    const videoExtensions = ['.mp4', '.avi', '.mov', '.wmv', '.mkv'];

    window.addEventListener('DOMContentLoaded', function() {
        document.body.classList.remove('bg-body');
    })

    function stopVideo(){
        document.getElementById('video-player').style.display = 'none';
        video.pause();
    }

    function playAudio(element) {
        let audio = document.getElementById('plyr-audio-player');
        let video = document.getElementById('plyr-video-player');
        const rute = element.dataset.rute;
        const mode = element.dataset.status;
        console.log(rute);
        fetch(rute, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            data = data.filter( item => item.id === parseInt(element.id));
            const extension = data[0].url.substring(data[0].url.lastIndexOf('.')).toLowerCase();
            if(audioExtensions.includes(extension)){
                if(mode === "off"){
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