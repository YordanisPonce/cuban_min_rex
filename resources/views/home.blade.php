@extends('layouts.app')

@section('title', 'Inicio – Cuban Mix Rex')

@section('content')

    {{-- =========================
       HERO compacto
    ========================== --}}
    <section id="hero" class="py-6 py-lg-7 bg-body" style="margin-top: 125px;">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="badge bg-label-primary mb-3">Descubre música</span>
                    <h1 class="display-6 fw-bold mb-2">Tu próxima canción favorita, a un clic</h1>
                    <p class="text-body-secondary mb-4">
                        Explora artistas, colecciones y lanzamientos hechos para ti.
                    </p>

                    <form class="input-group input-group-lg" action="{{ route('search') }}" method="GET">
                        <span class="input-group-text"><i class="ti tabler-search"></i></span>
                        <input type="search" class="form-control" name="q"
                            placeholder="Busca artistas, canciones o colecciones…">
                    </form>

                    <div class="d-flex align-items-center gap-3 mt-4">
                        <a href="#home-recommended" class="btn btn-primary">Reproducir ahora</a>
                        <a href="#home-collections" class="btn btn-outline-secondary">Ver colecciones</a>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="ratio ratio-4x3 rounded-4 overflow-hidden border border-dark-subtle">
                        <img src="{{ asset('assets/img/dj-portada.png') }}" alt="Arte destacado"
                            class="w-100 h-100 object-fit-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- =========================
       RECOMENDADO
    ========================== --}}
    @auth
    <section id="home-recommended" class="section-py bg-body">

        @php $hasRecommended = isset($recommendedItems) && count($recommendedItems) > 0; @endphp

        @if ($hasRecommended)
            @include('partials.collection', [
                'id' => 'collections-recommended',
                'badge' => 'Para ti',
                'title' => 'Hecho para ti',
                'subtitle' => 'Listas personalizadas y selecciones que encajan con tu historial.',
                'ctaText' => 'Ver recomendaciones',
                'ctaHref' => route('collection.recommended'),

                // contenido para las cards del carrusel (si tu partial las usa)
                'items' => $recommendedItems ?? null,

                // reproducción de colección completa (opcional)
                'collectionId' => optional($recommendedCollectionToPlay ?? null)->id,
                'playlistEndpoint' => isset($recommendedCollectionToPlay)
                    ? route('collections.playlist', $recommendedCollectionToPlay->id)
                    : null,
            ])
        @else
            <div class="container mb-3">
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <span class="badge bg-label-primary mb-2">Para ti</span>
                        <h2 class="h3 fw-bold mb-1">Hecho para ti</h2>
                        <p class="text-body-secondary mb-0">Tu dosis diaria con colecciones y mixes según lo que más escuchas.
                        </p>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="border rounded-4 p-4 p-md-5 text-center bg-body">
                    <h3 class="h5 fw-bold mb-2">Aún no tenemos recomendaciones</h3>
                    <p class="text-body-secondary mb-3">Escucha algunas canciones y vuelve para recibir sugerencias a tu medida.</p>
                    <a href="{{ route('search') }}" class="btn btn-label-primary">Descubrir música</a>
                </div>
            </div>
        @endif
    </section>
    @endauth

    {{-- =========================
       NUEVOS LANZAMIENTOS
    ========================== --}}
    <section id="home-new" class="section-py bg-body">

        @php $hasNew = isset($newItems) && count($newItems) > 0; @endphp

        @if ($hasNew)
            @include('partials.collection', [
                'id' => 'collections-new',
                'badge' => 'Novedades',
                'title' => 'Estrenos de la semana',
                'subtitle' => 'Actualizado con lanzamientos fresquitos para que no te pierdas nada.',
                'ctaText' => 'Ver estrenos',
                'ctaHref' => route('collection.news'),
            
                'items' => $newItems ?? null,
            
                'collectionId' => optional($newCollectionToPlay ?? null)->id,
                'playlistEndpoint' => isset($newCollectionToPlay)
                    ? route('collections.playlist', $newCollectionToPlay->id)
                    : null,
            ])
        @else
            <div class="container mb-3">
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <span class="badge bg-label-primary mb-2">Novedades</span>
                        <h2 class="h3 fw-bold mb-1">Estrenos de la semana</h2>
                        <p class="text-body-secondary mb-0">Singles y álbumes recién salidos. Lo último de tus artistas
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
       COLECCIONES DE ARTISTAS
    ========================== --}}
    <section id="home-collections" class="section-py bg-body">

        @php $hasArtists = isset($artistCollections) && count($artistCollections) > 0; @endphp

        @if ($hasArtists)
            @include('partials.collection', [
                'id' => 'collections-artists',
                'badge' => 'Explorar',
                'title' => 'Colecciones de artistas',
                'subtitle' => 'Viaja por su historia musical: etapas, hits y mezclas imprescindibles.',
                'ctaText' => 'Explorar colecciones',
                'ctaHref' => route('collection.index'),
            
                'items' => $artistCollections ?? null,
            
                'collectionId' => optional($artistCollectionToPlay ?? null)->id,
                'playlistEndpoint' => isset($artistCollectionToPlay)
                    ? route('collections.playlist', $artistCollectionToPlay->id)
                    : null,
            ])
        @else
            <div class="container mb-3">
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <span class="badge bg-label-primary mb-2">Explorar</span>
                        <h2 class="h3 fw-bold mb-1">Colecciones de artistas</h2>
                        <p class="text-body-secondary mb-0">Discografías esenciales, playlists temáticas y selecciones por mood.
                        </p>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="border rounded-4 p-4 p-md-5 text-center bg-body">
                    <h3 class="h5 fw-bold mb-2">Sin colecciones por ahora</h3>
                    <p class="text-body-secondary mb-3">Estamos preparando nuevas selecciones por artista y estilo.</p>
                    <a href="{{ route('search') }}" class="btn btn-outline-secondary">Buscar artistas</a>
                </div>
            </div>
        @endif
    </section>

    {{-- =========================
       GÉNEROS POPULARES
    ========================== --}}
    <section id="home-genres" class="section-py bg-body">
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

    {{-- =========================
       PLANES
    ========================== --}}
    <section id="home-pricing" class="section-py bg-body landing-pricing">
        <div class="container">
            <div class="text-center mb-3">
                <span class="badge bg-label-primary">Planes de suscripción</span>
            </div>
            <h2 class="text-center fw-bold mb-2">Elige tu plan musical</h2>
            <p class="text-center text-body-secondary mb-6">
                Disfruta sin límites con beneficios a tu medida.
            </p>

            <div class="row gy-4">
                @foreach ($plans as $plan)
                    @php
                        $isActive =
                            auth()->check() &&
                            auth()->user()->current_plan_id === $plan->id &&
                            auth()->user()->hasActivePlan();
                    @endphp
                    <div class="col-xl-4 col-lg-6">
                        <div class="card h-100">
                            <div class="card-header text-center">
                                <img src="{{ asset('storage/' . $plan->image) }}" alt="{{ $plan->name }}" class="mb-4"
                                    style="width:64px;height:64px;object-fit:contain;">
                                <h4 class="mb-1">{{ $plan->name }}</h4>
                                <div class="d-flex align-items-center justify-content-center">
                                    <span class="h2 text-primary fw-extrabold mb-0">€{{ $plan->price_formatted }}</span>
                                    <sub class="h6 text-body-secondary mb-n1 ms-1">/mes</sub>
                                </div>
                            </div>

                            <div class="card-body d-flex flex-column">
                                @if ($plan->description)
                                    <ul class="list-unstyled small text-body mb-4">
                                        <li class="mb-2 d-flex">
                                            <i class="ti tabler-check me-2"></i> {{ $plan->description }}
                                        </li>
                                    </ul>
                                @endif

                                <div class="mt-auto">
                                    @auth
                                        @if ($isActive)
                                            <button class="btn btn-secondary w-100" disabled>Ya lo tienes</button>
                                        @else
                                            <a href="{{ route('payment.form', $plan->id) }}"
                                                class="btn btn-label-primary w-100">
                                                Adquirir plan
                                            </a>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">
                                            Inicia sesión para comprar
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
