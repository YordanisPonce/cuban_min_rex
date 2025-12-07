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
       HERO compacto
    ========================== 
    <section id="hero" class="py-6 py-lg-7" style="margin-top: 125px;">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="badge bg-label-primary mb-3">Descubre música</span>
                    <h1 class="display-6 fw-bold mb-2">Tu próxima canción favorita, a un clic</h1>
                    <p class="text-body-secondary mb-4">
                        Explora artistas y lanzamientos hechos para ti.
                    </p>

                    <form class="input-group input-group-lg" action="{{ route('search') }}" method="GET">
                        <span class="input-group-text"><i class="ti tabler-search"></i></span>
                        <input type="search" class="form-control" name="search"
                            placeholder="Busca artistas o canciones…">
                    </form>

                    <div class="d-flex align-items-center gap-3 mt-4">
                        <a href="#home-recommended" class="btn btn-primary">Reproducir ahora</a>
                        <a href="#home-collections" class="btn btn-outline-secondary">Ver packs</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="ratio ratio-4x3 rounded-4 overflow-hidden border border-dark-subtle">
                        <img src="{{ asset('assets/img/front-pages/backgrounds/bg-main.PNG') }}" alt="Arte destacado"
                            class="w-100 h-100 object-fit-cover">
                        <div class="dark-screen" style="opacity: 0.5;"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <hr class="m-0 mt-6 mt-md-12">--}}

    <div class="container d-md-flex gap-4">
    
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
                    @foreach ($newItems as $item)
                    {{--<div class="col-md-2 mb-4">
                        <div class="relative card h-100">
                            <div class="relative overflow-hidden">
                                <img class="card-img-top" style="height: 200px" src="{{ $item->poster ? $item->poster : ($item->user->photo ? $item->user->photo : config('app.logo')) }}" alt="{{ $item->name }}" />
                                <div class="dark-screen" style="background-color: rgba(0,0,0,.6);"></div>
                            </div>
                            <spam style="position: absolute; right: 0; top: 0; background-color: var(--bs-primary);color:#12131C; font-weight:500; width: 40%; text-align: center; padding: 4px 0;">{{ $item->category ? $item->category->name : 'Sin Categoría' }}</spam>
                            <div class="card-body">
                                <h5 class="card-title">{{ $item->name }}</h5>
                                <div class="d-flex justify-content-between">
                                    <p class="card-text">{{ $item->user ? $item->user->name : 'Desconocido' }}</p>
                                </div>
                                <div class="d-flex gap-4 align-items-center" style="top: 170px; position: absolute">
                                    <a id="{{$item->id}}" style="display: flex; width: 20px;" class="play-button cursor-pointer" data-rute="{{ route('file.play', [$item->collection ?? 'none', $item->id])}}" onclick="playAudio(this)"
                                        >{{ svg('vaadin-play') }}</a>
                                    @if ((Auth::user() && Auth::user()->hasActivePlan()) || !($item->price > 0))
                                        <a style="display: flex; width: 20px"
                                            href="{{ route('file.download', $item->id)}}">{{ svg('entypo-download') }}</a>
                                    @else
                                        @if (in_array($item->id,Cart::get_current_cart()->items ?? []))
                                        <a style="display: flex; width: 25px; color: red" class="cursor-pointer" href="{{route('file.remove.cart', $item->id) }}">
                                            <svg fill="currentColor" width="auto" height="auto" viewBox="0 0 56 56" xmlns="http://www.w3.org/2000/svg"><path d="M 45.4157 28.7296 C 51.2174 28.7296 56 23.9677 56 18.1659 C 56 12.3642 51.2174 7.6022 45.4157 7.6022 C 39.6349 7.6022 34.8519 12.3642 34.8519 18.1659 C 34.8519 23.9677 39.6349 28.7296 45.4157 28.7296 Z M 16.9061 42.0175 L 41.1736 42.0175 C 41.9844 42.0175 42.6914 41.3520 42.6914 40.4579 C 42.6914 39.5637 41.9844 38.8982 41.1736 38.8982 L 17.2596 38.8982 C 16.0743 38.8982 15.3673 38.0665 15.1593 36.7980 L 14.8266 34.6146 L 41.2153 34.6146 C 43.3779 34.6146 44.7918 33.6788 45.5196 32.0152 L 45.6861 31.5785 C 37.9919 31.5577 32.0031 25.6312 32.0031 18.1659 C 32.0031 17.5421 32.0446 16.9182 32.1278 16.2944 L 12.1649 16.2944 L 11.7698 13.6535 C 11.5203 12.0523 10.9796 11.2413 8.8586 11.2413 L 1.5388 11.2413 C .7070 11.2413 0 11.9691 0 12.8009 C 0 13.6535 .7070 14.3813 1.5388 14.3813 L 8.5674 14.3813 L 11.8946 37.2139 C 12.3312 40.1668 13.8909 42.0175 16.9061 42.0175 Z M 40.0923 19.4344 C 39.3853 19.4344 38.8027 18.8314 38.8027 18.1659 C 38.8027 17.4797 39.3853 16.8975 40.0923 16.8975 L 50.7805 16.8975 C 51.4670 16.8975 52.0492 17.4797 52.0492 18.1659 C 52.0492 18.8314 51.4670 19.4344 50.7805 19.4344 Z M 15.1801 48.7549 C 15.1801 50.6473 16.6565 52.1237 18.5489 52.1237 C 20.4204 52.1237 21.9176 50.6473 21.9176 48.7549 C 21.9176 46.8834 20.4204 45.3862 18.5489 45.3862 C 16.6565 45.3862 15.1801 46.8834 15.1801 48.7549 Z M 34.6024 48.7549 C 34.6024 50.6473 36.1204 52.1237 38.0127 52.1237 C 39.8844 52.1237 41.3814 50.6473 41.3814 48.7549 C 41.3814 46.8834 39.8844 45.3862 38.0127 45.3862 C 36.1204 45.3862 34.6024 46.8834 34.6024 48.7549 Z"/></svg>
                                        </a>
                                        @else
                                        <a style="display: flex; width: 25px;" class="cursor-pointer" href="{{route('file.add.cart', $item->id) }}">
                                            <svg fill="currentColor" width="auto" height="auto" viewBox="0 0 56 56" xmlns="http://www.w3.org/2000/svg"><path d="M 45.4157 28.7296 C 51.1548 28.7296 56 23.9261 56 18.1659 C 56 12.3642 51.2174 7.6022 45.4157 7.6022 C 39.6349 7.6022 34.8519 12.3642 34.8519 18.1659 C 34.8519 23.9677 39.6349 28.7296 45.4157 28.7296 Z M 16.9061 42.0175 L 41.1736 42.0175 C 41.9844 42.0175 42.6914 41.3520 42.6914 40.4579 C 42.6914 39.5637 41.9844 38.8982 41.1736 38.8982 L 17.2596 38.8982 C 16.0743 38.8982 15.3673 38.0665 15.1593 36.7980 L 14.8266 34.6146 L 41.2153 34.6146 C 43.3779 34.6146 44.7918 33.6788 45.5196 32.0152 L 45.6861 31.5785 C 37.9919 31.5577 32.0031 25.6312 32.0031 18.1659 C 32.0031 17.5421 32.0446 16.9182 32.1278 16.2944 L 12.1649 16.2944 L 11.7698 13.6535 C 11.5203 12.0523 10.9796 11.2413 8.8586 11.2413 L 1.5388 11.2413 C .7070 11.2413 0 11.9691 0 12.8009 C 0 13.6535 .7070 14.3813 1.5388 14.3813 L 8.5674 14.3813 L 11.8946 37.2139 C 12.3312 40.1668 13.8909 42.0175 16.9061 42.0175 Z M 45.4366 25.0282 C 44.7088 25.0282 44.0640 24.5291 44.0640 23.7389 L 44.0640 19.4344 L 40.0923 19.4344 C 39.3853 19.4344 38.8236 18.8521 38.8236 18.1659 C 38.8236 17.4589 39.3853 16.8767 40.0923 16.8767 L 44.0640 16.8767 L 44.0640 12.5721 C 44.0640 11.7820 44.7088 11.3037 45.4366 11.3037 C 46.1644 11.3037 46.7879 11.7820 46.7879 12.5721 L 46.7879 16.8767 L 50.7600 16.8767 C 51.4670 16.8767 52.0492 17.4589 52.0492 18.1659 C 52.0492 18.8521 51.4670 19.4344 50.7600 19.4344 L 46.7879 19.4344 L 46.7879 23.7389 C 46.7879 24.5291 46.1644 25.0282 45.4366 25.0282 Z M 15.1801 48.7549 C 15.1801 50.6473 16.6565 52.1237 18.5489 52.1237 C 20.4204 52.1237 21.9176 50.6473 21.9176 48.7549 C 21.9176 46.8834 20.4204 45.3862 18.5489 45.3862 C 16.6565 45.3862 15.1801 46.8834 15.1801 48.7549 Z M 34.6024 48.7549 C 34.6024 50.6473 36.1204 52.1237 38.0127 52.1237 C 39.8844 52.1237 41.3814 50.6473 41.3814 48.7549 C 41.3814 46.8834 39.8844 45.3862 38.0127 45.3862 C 36.1204 45.3862 34.6024 46.8834 34.6024 48.7549 Z"/></svg>
                                        </a>
                                        @endif
                                        <spam>$ {{ $item->price }}</spam>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>--}}
                    <div class="card mb-1 px-0">
                        <div class="row g-0">
                            <div style="width: 10%;">
                                <img class="card-img card-img-left" src="{{ $item->poster ? $item->poster : ($item->user->photo ? $item->user->photo : config('app.logo')) }}" alt="{{ $item->name }}" />
                            </div>
                            <div style="width: 80%;">
                                <div class="py-2 px-4">
                                    <h5 class="card-title mb-1 text-truncate">{{ $item->name}}</h5>
                                    <p class="card-text mb-1 text-truncate" style="color: var(--bs-primary)">{{ $item->user ? $item->user->name : 'Desconocido' }}</p>
                                    <p class="card-text d-flex gap-4">
                                        <small class="text-body-secondary" style="height: 20px;">{{ $item->category ? $item->category->name : 'Sin Categoría' }}</small>
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex flex-column justify-content-center align-items-center" style="width: 10%; border-left: 2px solid black">
                                <div class="w-100 d-flex justify-content-center align-items-center" style="height: 38px;">
                                    <a id="{{$item->id}}" style="display: flex; width: 20px" class="play-button cursor-pointer" data-rute="{{ route('file.play', [$item->collection ?? 'none', $item->id])}}" onclick="playAudio(this)"
                                        >{{ svg('vaadin-play') }}</a>
                                </div>
                                <div class="dropdown d-flex justify-content-center">
                                    <button class="btn btn-text-secondary btn-icon rounded-pill text-body-secondary border-0" type="button" id="BulkOptions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="icon-base ti tabler-dots icon-22px text-body-secondary"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="BulkOptions">
                                        @if ((Auth::user() && Auth::user()->hasActivePlan()) || !($item->price > 0))
                                        <a class="dropdown-item d-flex gap-2" href="{{ route('file.download', $item->id)}}">
                                            <i style="width: 20px">{{ svg('entypo-download') }}</i>
                                            Descargar
                                        </a>
                                        @else
                                        <p class="pt-2 text-center">Precio: $ {{ $item->price }}</p>
                                            @if (!in_array($item->id,Cart::get_current_cart()->items ?? []))
                                            <a class="dropdown-item d-flex gap-2" href="{{ route('file.add.cart', $item->id)}}">
                                                <i style="width: 20px">
                                                    <svg fill="currentColor" width="auto" height="auto" viewBox="0 0 56 56" xmlns="http://www.w3.org/2000/svg"><path d="M 45.4157 28.7296 C 51.1548 28.7296 56 23.9261 56 18.1659 C 56 12.3642 51.2174 7.6022 45.4157 7.6022 C 39.6349 7.6022 34.8519 12.3642 34.8519 18.1659 C 34.8519 23.9677 39.6349 28.7296 45.4157 28.7296 Z M 16.9061 42.0175 L 41.1736 42.0175 C 41.9844 42.0175 42.6914 41.3520 42.6914 40.4579 C 42.6914 39.5637 41.9844 38.8982 41.1736 38.8982 L 17.2596 38.8982 C 16.0743 38.8982 15.3673 38.0665 15.1593 36.7980 L 14.8266 34.6146 L 41.2153 34.6146 C 43.3779 34.6146 44.7918 33.6788 45.5196 32.0152 L 45.6861 31.5785 C 37.9919 31.5577 32.0031 25.6312 32.0031 18.1659 C 32.0031 17.5421 32.0446 16.9182 32.1278 16.2944 L 12.1649 16.2944 L 11.7698 13.6535 C 11.5203 12.0523 10.9796 11.2413 8.8586 11.2413 L 1.5388 11.2413 C .7070 11.2413 0 11.9691 0 12.8009 C 0 13.6535 .7070 14.3813 1.5388 14.3813 L 8.5674 14.3813 L 11.8946 37.2139 C 12.3312 40.1668 13.8909 42.0175 16.9061 42.0175 Z M 45.4366 25.0282 C 44.7088 25.0282 44.0640 24.5291 44.0640 23.7389 L 44.0640 19.4344 L 40.0923 19.4344 C 39.3853 19.4344 38.8236 18.8521 38.8236 18.1659 C 38.8236 17.4589 39.3853 16.8767 40.0923 16.8767 L 44.0640 16.8767 L 44.0640 12.5721 C 44.0640 11.7820 44.7088 11.3037 45.4366 11.3037 C 46.1644 11.3037 46.7879 11.7820 46.7879 12.5721 L 46.7879 16.8767 L 50.7600 16.8767 C 51.4670 16.8767 52.0492 17.4589 52.0492 18.1659 C 52.0492 18.8521 51.4670 19.4344 50.7600 19.4344 L 46.7879 19.4344 L 46.7879 23.7389 C 46.7879 24.5291 46.1644 25.0282 45.4366 25.0282 Z M 15.1801 48.7549 C 15.1801 50.6473 16.6565 52.1237 18.5489 52.1237 C 20.4204 52.1237 21.9176 50.6473 21.9176 48.7549 C 21.9176 46.8834 20.4204 45.3862 18.5489 45.3862 C 16.6565 45.3862 15.1801 46.8834 15.1801 48.7549 Z M 34.6024 48.7549 C 34.6024 50.6473 36.1204 52.1237 38.0127 52.1237 C 39.8844 52.1237 41.3814 50.6473 41.3814 48.7549 C 41.3814 46.8834 39.8844 45.3862 38.0127 45.3862 C 36.1204 45.3862 34.6024 46.8834 34.6024 48.7549 Z"/></svg>
                                                </i>
                                                Añadir al Carrito
                                            </a>
                                            @else
                                            <a class="dropdown-item d-flex gap-2" href="{{ route('file.remove.cart', $item->id)}}">
                                                <i style="width: 20px">
                                                    <svg fill="currentColor" width="auto" height="auto" viewBox="0 0 56 56" xmlns="http://www.w3.org/2000/svg"><path d="M 45.4157 28.7296 C 51.2174 28.7296 56 23.9677 56 18.1659 C 56 12.3642 51.2174 7.6022 45.4157 7.6022 C 39.6349 7.6022 34.8519 12.3642 34.8519 18.1659 C 34.8519 23.9677 39.6349 28.7296 45.4157 28.7296 Z M 16.9061 42.0175 L 41.1736 42.0175 C 41.9844 42.0175 42.6914 41.3520 42.6914 40.4579 C 42.6914 39.5637 41.9844 38.8982 41.1736 38.8982 L 17.2596 38.8982 C 16.0743 38.8982 15.3673 38.0665 15.1593 36.7980 L 14.8266 34.6146 L 41.2153 34.6146 C 43.3779 34.6146 44.7918 33.6788 45.5196 32.0152 L 45.6861 31.5785 C 37.9919 31.5577 32.0031 25.6312 32.0031 18.1659 C 32.0031 17.5421 32.0446 16.9182 32.1278 16.2944 L 12.1649 16.2944 L 11.7698 13.6535 C 11.5203 12.0523 10.9796 11.2413 8.8586 11.2413 L 1.5388 11.2413 C .7070 11.2413 0 11.9691 0 12.8009 C 0 13.6535 .7070 14.3813 1.5388 14.3813 L 8.5674 14.3813 L 11.8946 37.2139 C 12.3312 40.1668 13.8909 42.0175 16.9061 42.0175 Z M 40.0923 19.4344 C 39.3853 19.4344 38.8027 18.8314 38.8027 18.1659 C 38.8027 17.4797 39.3853 16.8975 40.0923 16.8975 L 50.7805 16.8975 C 51.4670 16.8975 52.0492 17.4797 52.0492 18.1659 C 52.0492 18.8314 51.4670 19.4344 50.7805 19.4344 Z M 15.1801 48.7549 C 15.1801 50.6473 16.6565 52.1237 18.5489 52.1237 C 20.4204 52.1237 21.9176 50.6473 21.9176 48.7549 C 21.9176 46.8834 20.4204 45.3862 18.5489 45.3862 C 16.6565 45.3862 15.1801 46.8834 15.1801 48.7549 Z M 34.6024 48.7549 C 34.6024 50.6473 36.1204 52.1237 38.0127 52.1237 C 39.8844 52.1237 41.3814 50.6473 41.3814 48.7549 C 41.3814 46.8834 39.8844 45.3862 38.0127 45.3862 C 36.1204 45.3862 34.6024 46.8834 34.6024 48.7549 Z"/></svg>
                                                </i>
                                                Quitar del Carrito
                                            </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
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

    {{-- =========================
       TOPS LANZAMIENTOS
    ========================== --}}
    <section id="home-new" class="section-py mt-10">

        @php $hasTops = isset($tops) && count($tops) > 0; @endphp

        @if ($hasTops)
            <div class="container">
                <div class="text-center mb-3">
                    <span class="badge bg-label-primary">TOPS</span>
                </div>
                <h2 class="text-center fw-bold mb-2">TOP REMIXES</h2>
                <p class="text-center text-body-secondary mb-6">
                     
                </p>
                <div class="row">
                    @php
                        $top = 1;
                    @endphp
                    @foreach ($tops as $item)
                    {{-- <div class="col-md-2 mb-4">
                        <div class="relative card h-100">
                            <div class="relative overflow-hidden">
                                <img class="card-img-top" style="height: 200px" src="{{ $item->poster ? $item->poster : ($item->user->photo ? $item->user->photo : config('app.logo')) }}" alt="{{ $item->name }}" />
                                <div class="dark-screen" style="background-color: rgba(0,0,0,.6);"></div>
                            </div>
                            <spam style="position: absolute; right: 0; top: 0; background-color: var(--bs-primary);color:#12131C; font-weight:500; width: 40%; text-align: center; padding: 4px 0;">{{ $item->category ? $item->category->name : 'Sin Categoría' }}</spam>
                            <div class="card-body">
                                <h5 class="card-title mb-5">{{ $item->name }}</h5>
                                <div class="d-flex gap-4" style="position: absolute; bottom: 0; margin-top: auto;">
                                    <p class="card-text">{{ $item->user ? $item->user->name : 'Desconocido' }}</p>
                                    <p class="card-text d-flex gap-2">
                                        <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3 15C3 17.8284 3 19.2426 3.87868 20.1213C4.75736 21 6.17157 21 9 21H15C17.8284 21 19.2426 21 20.1213 20.1213C21 19.2426 21 17.8284 21 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M12 3V16M12 16L16 11.625M12 16L8 11.625" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg> 
                                        {{ $item->download_count }}</p>
                                </div>
                                <div class="d-flex gap-4 align-items-center" style="top: 170px; position: absolute">
                                    <a id="{{$item->id}}" style="display: flex; width: 20px;" class="play-button cursor-pointer" data-rute="{{ route('file.play', [$item->collection ?? 'none', $item->id])}}" onclick="playAudio(this)"
                                        >{{ svg('vaadin-play') }}</a>
                                    @if ((Auth::user() && Auth::user()->hasActivePlan()) || !($item->price > 0))
                                        <a style="display: flex; width: 20px"
                                            href="{{ route('file.download', $item->id)}}">{{ svg('entypo-download') }}</a>
                                    @else
                                        @if (in_array($item->id,Cart::get_current_cart()->items ?? []))
                                        <a style="display: flex; width: 25px; color: red" class="cursor-pointer" href="{{route('file.remove.cart', $item->id) }}">
                                            <svg fill="currentColor" width="auto" height="auto" viewBox="0 0 56 56" xmlns="http://www.w3.org/2000/svg"><path d="M 45.4157 28.7296 C 51.2174 28.7296 56 23.9677 56 18.1659 C 56 12.3642 51.2174 7.6022 45.4157 7.6022 C 39.6349 7.6022 34.8519 12.3642 34.8519 18.1659 C 34.8519 23.9677 39.6349 28.7296 45.4157 28.7296 Z M 16.9061 42.0175 L 41.1736 42.0175 C 41.9844 42.0175 42.6914 41.3520 42.6914 40.4579 C 42.6914 39.5637 41.9844 38.8982 41.1736 38.8982 L 17.2596 38.8982 C 16.0743 38.8982 15.3673 38.0665 15.1593 36.7980 L 14.8266 34.6146 L 41.2153 34.6146 C 43.3779 34.6146 44.7918 33.6788 45.5196 32.0152 L 45.6861 31.5785 C 37.9919 31.5577 32.0031 25.6312 32.0031 18.1659 C 32.0031 17.5421 32.0446 16.9182 32.1278 16.2944 L 12.1649 16.2944 L 11.7698 13.6535 C 11.5203 12.0523 10.9796 11.2413 8.8586 11.2413 L 1.5388 11.2413 C .7070 11.2413 0 11.9691 0 12.8009 C 0 13.6535 .7070 14.3813 1.5388 14.3813 L 8.5674 14.3813 L 11.8946 37.2139 C 12.3312 40.1668 13.8909 42.0175 16.9061 42.0175 Z M 40.0923 19.4344 C 39.3853 19.4344 38.8027 18.8314 38.8027 18.1659 C 38.8027 17.4797 39.3853 16.8975 40.0923 16.8975 L 50.7805 16.8975 C 51.4670 16.8975 52.0492 17.4797 52.0492 18.1659 C 52.0492 18.8314 51.4670 19.4344 50.7805 19.4344 Z M 15.1801 48.7549 C 15.1801 50.6473 16.6565 52.1237 18.5489 52.1237 C 20.4204 52.1237 21.9176 50.6473 21.9176 48.7549 C 21.9176 46.8834 20.4204 45.3862 18.5489 45.3862 C 16.6565 45.3862 15.1801 46.8834 15.1801 48.7549 Z M 34.6024 48.7549 C 34.6024 50.6473 36.1204 52.1237 38.0127 52.1237 C 39.8844 52.1237 41.3814 50.6473 41.3814 48.7549 C 41.3814 46.8834 39.8844 45.3862 38.0127 45.3862 C 36.1204 45.3862 34.6024 46.8834 34.6024 48.7549 Z"/></svg>
                                        </a>
                                        @else
                                        <a style="display: flex; width: 25px;" class="cursor-pointer" href="{{route('file.add.cart', $item->id) }}">
                                            <svg fill="currentColor" width="auto" height="auto" viewBox="0 0 56 56" xmlns="http://www.w3.org/2000/svg"><path d="M 45.4157 28.7296 C 51.1548 28.7296 56 23.9261 56 18.1659 C 56 12.3642 51.2174 7.6022 45.4157 7.6022 C 39.6349 7.6022 34.8519 12.3642 34.8519 18.1659 C 34.8519 23.9677 39.6349 28.7296 45.4157 28.7296 Z M 16.9061 42.0175 L 41.1736 42.0175 C 41.9844 42.0175 42.6914 41.3520 42.6914 40.4579 C 42.6914 39.5637 41.9844 38.8982 41.1736 38.8982 L 17.2596 38.8982 C 16.0743 38.8982 15.3673 38.0665 15.1593 36.7980 L 14.8266 34.6146 L 41.2153 34.6146 C 43.3779 34.6146 44.7918 33.6788 45.5196 32.0152 L 45.6861 31.5785 C 37.9919 31.5577 32.0031 25.6312 32.0031 18.1659 C 32.0031 17.5421 32.0446 16.9182 32.1278 16.2944 L 12.1649 16.2944 L 11.7698 13.6535 C 11.5203 12.0523 10.9796 11.2413 8.8586 11.2413 L 1.5388 11.2413 C .7070 11.2413 0 11.9691 0 12.8009 C 0 13.6535 .7070 14.3813 1.5388 14.3813 L 8.5674 14.3813 L 11.8946 37.2139 C 12.3312 40.1668 13.8909 42.0175 16.9061 42.0175 Z M 45.4366 25.0282 C 44.7088 25.0282 44.0640 24.5291 44.0640 23.7389 L 44.0640 19.4344 L 40.0923 19.4344 C 39.3853 19.4344 38.8236 18.8521 38.8236 18.1659 C 38.8236 17.4589 39.3853 16.8767 40.0923 16.8767 L 44.0640 16.8767 L 44.0640 12.5721 C 44.0640 11.7820 44.7088 11.3037 45.4366 11.3037 C 46.1644 11.3037 46.7879 11.7820 46.7879 12.5721 L 46.7879 16.8767 L 50.7600 16.8767 C 51.4670 16.8767 52.0492 17.4589 52.0492 18.1659 C 52.0492 18.8521 51.4670 19.4344 50.7600 19.4344 L 46.7879 19.4344 L 46.7879 23.7389 C 46.7879 24.5291 46.1644 25.0282 45.4366 25.0282 Z M 15.1801 48.7549 C 15.1801 50.6473 16.6565 52.1237 18.5489 52.1237 C 20.4204 52.1237 21.9176 50.6473 21.9176 48.7549 C 21.9176 46.8834 20.4204 45.3862 18.5489 45.3862 C 16.6565 45.3862 15.1801 46.8834 15.1801 48.7549 Z M 34.6024 48.7549 C 34.6024 50.6473 36.1204 52.1237 38.0127 52.1237 C 39.8844 52.1237 41.3814 50.6473 41.3814 48.7549 C 41.3814 46.8834 39.8844 45.3862 38.0127 45.3862 C 36.1204 45.3862 34.6024 46.8834 34.6024 48.7549 Z"/></svg>
                                        </a>
                                        @endif
                                        <spam>$ {{ $item->price }}</spam>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <div class="card mb-1 px-0">
                        <div class="row g-0">
                            <div style="width: 10%;">
                                <img class="card-img card-img-left" src="{{ $item->poster ? $item->poster : ($item->user->photo ? $item->user->photo : config('app.logo')) }}" alt="{{ $item->name }}" />
                            </div>
                            <div style="width: 10%;">
                                <div class="d-flex justify-content-center align-items-center h-100">
                                    <p class="text-secondary" style="font-weight: 800; font-size:xx-large">{{ $top }}</p>
                                </div>
                            </div>
                            <div style="width: 70%;">
                                <div class="p-2">
                                    <h5 class="card-title mb-1 text-truncate">{{ $item->name}}</h5>
                                    <p class="card-text mb-1 text-truncate" style="color: var(--bs-primary)">{{ $item->user ? $item->user->name : 'Desconocido' }}</p>
                                    <p class="card-text d-flex gap-4">
                                        <small class="text-body-secondary">{{ $item->category ? $item->category->name : 'Sin Categoría' }}</small>
                                        <small class="text-body-secondary d-flex gap-2">
                                            <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M3 15C3 17.8284 3 19.2426 3.87868 20.1213C4.75736 21 6.17157 21 9 21H15C17.8284 21 19.2426 21 20.1213 20.1213C21 19.2426 21 17.8284 21 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M12 3V16M12 16L16 11.625M12 16L8 11.625" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg> 
                                        {{ $item->download_count }}</small>
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex flex-column justify-content-center align-items-center" style="width: 10%; border-left: 2px solid black">
                                <div class="w-100 d-flex justify-content-center align-items-center" style="height: 38px;">
                                    <a id="{{$item->id}}" style="display: flex; width: 20px" class="play-button cursor-pointer" data-rute="{{ route('file.play', [$item->collection ?? 'none', $item->id])}}" onclick="playAudio(this)"
                                        >{{ svg('vaadin-play') }}</a>
                                </div>
                                <div class="dropdown d-flex justify-content-center">
                                    <button class="btn btn-text-secondary btn-icon rounded-pill text-body-secondary border-0" type="button" id="BulkOptions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="icon-base ti tabler-dots icon-22px text-body-secondary"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="BulkOptions">
                                        @if ((Auth::user() && Auth::user()->hasActivePlan()) || !($item->price > 0))
                                        <a class="dropdown-item d-flex gap-2" href="{{ route('file.download', $item->id)}}">
                                            <i style="width: 20px">{{ svg('entypo-download') }}</i>
                                            Descargar
                                        </a>
                                        @else
                                        <p class="pt-2 text-center">Precio: $ {{ $item->price }}</p>
                                            @if (!in_array($item->id,Cart::get_current_cart()->items ?? []))
                                            <a class="dropdown-item d-flex gap-2" href="{{ route('file.add.cart', $item->id)}}">
                                                <i style="width: 20px">
                                                    <svg fill="currentColor" width="auto" height="auto" viewBox="0 0 56 56" xmlns="http://www.w3.org/2000/svg"><path d="M 45.4157 28.7296 C 51.1548 28.7296 56 23.9261 56 18.1659 C 56 12.3642 51.2174 7.6022 45.4157 7.6022 C 39.6349 7.6022 34.8519 12.3642 34.8519 18.1659 C 34.8519 23.9677 39.6349 28.7296 45.4157 28.7296 Z M 16.9061 42.0175 L 41.1736 42.0175 C 41.9844 42.0175 42.6914 41.3520 42.6914 40.4579 C 42.6914 39.5637 41.9844 38.8982 41.1736 38.8982 L 17.2596 38.8982 C 16.0743 38.8982 15.3673 38.0665 15.1593 36.7980 L 14.8266 34.6146 L 41.2153 34.6146 C 43.3779 34.6146 44.7918 33.6788 45.5196 32.0152 L 45.6861 31.5785 C 37.9919 31.5577 32.0031 25.6312 32.0031 18.1659 C 32.0031 17.5421 32.0446 16.9182 32.1278 16.2944 L 12.1649 16.2944 L 11.7698 13.6535 C 11.5203 12.0523 10.9796 11.2413 8.8586 11.2413 L 1.5388 11.2413 C .7070 11.2413 0 11.9691 0 12.8009 C 0 13.6535 .7070 14.3813 1.5388 14.3813 L 8.5674 14.3813 L 11.8946 37.2139 C 12.3312 40.1668 13.8909 42.0175 16.9061 42.0175 Z M 45.4366 25.0282 C 44.7088 25.0282 44.0640 24.5291 44.0640 23.7389 L 44.0640 19.4344 L 40.0923 19.4344 C 39.3853 19.4344 38.8236 18.8521 38.8236 18.1659 C 38.8236 17.4589 39.3853 16.8767 40.0923 16.8767 L 44.0640 16.8767 L 44.0640 12.5721 C 44.0640 11.7820 44.7088 11.3037 45.4366 11.3037 C 46.1644 11.3037 46.7879 11.7820 46.7879 12.5721 L 46.7879 16.8767 L 50.7600 16.8767 C 51.4670 16.8767 52.0492 17.4589 52.0492 18.1659 C 52.0492 18.8521 51.4670 19.4344 50.7600 19.4344 L 46.7879 19.4344 L 46.7879 23.7389 C 46.7879 24.5291 46.1644 25.0282 45.4366 25.0282 Z M 15.1801 48.7549 C 15.1801 50.6473 16.6565 52.1237 18.5489 52.1237 C 20.4204 52.1237 21.9176 50.6473 21.9176 48.7549 C 21.9176 46.8834 20.4204 45.3862 18.5489 45.3862 C 16.6565 45.3862 15.1801 46.8834 15.1801 48.7549 Z M 34.6024 48.7549 C 34.6024 50.6473 36.1204 52.1237 38.0127 52.1237 C 39.8844 52.1237 41.3814 50.6473 41.3814 48.7549 C 41.3814 46.8834 39.8844 45.3862 38.0127 45.3862 C 36.1204 45.3862 34.6024 46.8834 34.6024 48.7549 Z"/></svg>
                                                </i>
                                                Añadir al Carrito
                                            </a>
                                            @else
                                            <a class="dropdown-item d-flex gap-2" href="{{ route('file.remove.cart', $item->id)}}">
                                                <i style="width: 20px">
                                                    <svg fill="currentColor" width="auto" height="auto" viewBox="0 0 56 56" xmlns="http://www.w3.org/2000/svg"><path d="M 45.4157 28.7296 C 51.2174 28.7296 56 23.9677 56 18.1659 C 56 12.3642 51.2174 7.6022 45.4157 7.6022 C 39.6349 7.6022 34.8519 12.3642 34.8519 18.1659 C 34.8519 23.9677 39.6349 28.7296 45.4157 28.7296 Z M 16.9061 42.0175 L 41.1736 42.0175 C 41.9844 42.0175 42.6914 41.3520 42.6914 40.4579 C 42.6914 39.5637 41.9844 38.8982 41.1736 38.8982 L 17.2596 38.8982 C 16.0743 38.8982 15.3673 38.0665 15.1593 36.7980 L 14.8266 34.6146 L 41.2153 34.6146 C 43.3779 34.6146 44.7918 33.6788 45.5196 32.0152 L 45.6861 31.5785 C 37.9919 31.5577 32.0031 25.6312 32.0031 18.1659 C 32.0031 17.5421 32.0446 16.9182 32.1278 16.2944 L 12.1649 16.2944 L 11.7698 13.6535 C 11.5203 12.0523 10.9796 11.2413 8.8586 11.2413 L 1.5388 11.2413 C .7070 11.2413 0 11.9691 0 12.8009 C 0 13.6535 .7070 14.3813 1.5388 14.3813 L 8.5674 14.3813 L 11.8946 37.2139 C 12.3312 40.1668 13.8909 42.0175 16.9061 42.0175 Z M 40.0923 19.4344 C 39.3853 19.4344 38.8027 18.8314 38.8027 18.1659 C 38.8027 17.4797 39.3853 16.8975 40.0923 16.8975 L 50.7805 16.8975 C 51.4670 16.8975 52.0492 17.4797 52.0492 18.1659 C 52.0492 18.8314 51.4670 19.4344 50.7805 19.4344 Z M 15.1801 48.7549 C 15.1801 50.6473 16.6565 52.1237 18.5489 52.1237 C 20.4204 52.1237 21.9176 50.6473 21.9176 48.7549 C 21.9176 46.8834 20.4204 45.3862 18.5489 45.3862 C 16.6565 45.3862 15.1801 46.8834 15.1801 48.7549 Z M 34.6024 48.7549 C 34.6024 50.6473 36.1204 52.1237 38.0127 52.1237 C 39.8844 52.1237 41.3814 50.6473 41.3814 48.7549 C 41.3814 46.8834 39.8844 45.3862 38.0127 45.3862 C 36.1204 45.3862 34.6024 46.8834 34.6024 48.7549 Z"/></svg>
                                                </i>
                                                Quitar del Carrito
                                            </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @php
                        $top = $top + 1;
                    @endphp
                    @endforeach
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
                <p class="text-center text-body-secondary mb-6">
                    
                </p>
                <div class="row">
                    @foreach ($artistCollections as $item)
                    {{--<div class="col-md-2 mb-4">
                        <div class="relative card h-100">
                            <div class="relative overflow-hidden">
                                <img class="card-img-top" style="height: 200px" src="{{ $item->poster ? $item->poster : ($item->user->photo ? $item->user->photo : config('app.logo')) }}" alt="{{ $item->name }}" />
                                <div class="dark-screen" style="background-color: rgba(0,0,0,.6);"></div>
                            </div>
                            <spam style="position: absolute; right: 0; top: 0; color: #12131C; font-weight: 500;background-color: var(--bs-primary); width: 40%; text-align: center; padding: 4px 0;">{{ $item->category ? $item->category->name : 'Sin Categoría' }}</spam>
                            <div class="card-body">
                                <h5 class="card-title mb-6"> <a href="{{route('collection.show', $item->id)}}">{{ $item->name }}</a></h5>
                                <div class="d-flex gap-4" style="position: absolute; bottom: 0; margin-top: auto;">
                                    <p class="card-text">{{ $item->user ? $item->user->name : 'Desconocido' }}</p>
                                    <p class="card-text d-flex gap-2">
                                        <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8.50989 2.00001H15.49C15.7225 1.99995 15.9007 1.99991 16.0565 2.01515C17.1643 2.12352 18.0711 2.78958 18.4556 3.68678H5.54428C5.92879 2.78958 6.83555 2.12352 7.94337 2.01515C8.09917 1.99991 8.27741 1.99995 8.50989 2.00001Z" fill="currentColor"/>
                                            <path d="M6.31052 4.72312C4.91989 4.72312 3.77963 5.56287 3.3991 6.67691C3.39117 6.70013 3.38356 6.72348 3.37629 6.74693C3.77444 6.62636 4.18881 6.54759 4.60827 6.49382C5.68865 6.35531 7.05399 6.35538 8.64002 6.35547H15.5321C17.1181 6.35538 18.4835 6.35531 19.5639 6.49382C19.9833 6.54759 20.3977 6.62636 20.7958 6.74693C20.7886 6.72348 20.781 6.70013 20.773 6.67691C20.3925 5.56287 19.2522 4.72312 17.8616 4.72312H6.31052Z" fill="currentColor"/>
                                            <path d="M11.25 17C11.25 16.5858 10.9142 16.25 10.5 16.25C10.0858 16.25 9.75 16.5858 9.75 17C9.75 17.4142 10.0858 17.75 10.5 17.75C10.9142 17.75 11.25 17.4142 11.25 17Z" fill="currentColor"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.67239 7.54204H15.3276C18.7024 7.54204 20.3898 7.54204 21.3377 8.52887C22.2855 9.51569 22.0625 11.0404 21.6165 14.0896L21.1935 16.9811C20.8437 19.3724 20.6689 20.568 19.7717 21.284C18.8745 22 17.5512 22 14.9046 22H9.09534C6.4488 22 5.12553 22 4.22834 21.284C3.33115 20.568 3.15626 19.3724 2.80648 16.9811L2.38351 14.0896C1.93748 11.0403 1.71447 9.5157 2.66232 8.52887C3.61017 7.54204 5.29758 7.54204 8.67239 7.54204ZM12.75 10.5C12.75 10.0858 12.4142 9.75 12 9.75C11.5858 9.75 11.25 10.0858 11.25 10.5V14.878C11.0154 14.7951 10.763 14.75 10.5 14.75C9.25736 14.75 8.25 15.7574 8.25 17C8.25 18.2426 9.25736 19.25 10.5 19.25C11.7426 19.25 12.75 18.2426 12.75 17V13.3197C13.4202 13.8634 14.2617 14.25 15 14.25C15.4142 14.25 15.75 13.9142 15.75 13.5C15.75 13.0858 15.4142 12.75 15 12.75C14.6946 12.75 14.1145 12.5314 13.5835 12.0603C13.0654 11.6006 12.75 11.0386 12.75 10.5Z" fill="currentColor"/>
                                        </svg> 
                                        {{ $item->files->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>--}}
                    <div class="card mb-1 px-0">
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
    
    </div>

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
    window.addEventListener('DOMContentLoaded', function() {
        document.body.classList.remove('bg-body');
    })

    function playAudio(element) {
        let audio = document.getElementById('plyr-audio-player');
        const rute = element.dataset.rute;
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
            audio.src = data[0].url;    
            document.getElementById('audioPlayer').style.transform = 'translateY(0)';
            document.getElementById('plyr-audio-name').innerText = data[0].title;
            audio.play();
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