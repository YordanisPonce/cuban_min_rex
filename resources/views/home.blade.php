@extends('layouts.app')
@php
    $success = session('success');
    $error = session('error');
@endphp

@section('title', "Inicio – ".config('app.name'))

@push('styles')
<style> 
    body {
        /* The image used */
        background-image: linear-gradient(rgba(0,0,0,.9), rgba(0,0,0,.9)),url("{{ asset('assets/img/front-pages/backgrounds/bg-main.PNG') }}");

        /* Create the parallax scrolling effect */
        background-attachment: fixed;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
    }

    .player--dark {
        background-color: #12131C !important;
        color: #fff;
    }

    .bg-body{
        background-color: transparent !important;
    }

    @media (max-width: 500px) {
        body{
            background-image: linear-gradient(rgba(0,0,0,.75), rgba(0,0,0,.75)),url("{{ asset('assets/img/front-pages/backgrounds/bg-mobile.PNG') }}");
        }
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
    
    {{-- =========================
       PLANES
    ========================== --}}
    <section id="home-pricing" class="section-py landing-pricing mt-10">
        <div class="container">
            <div class="text-center mb-3">
                <span class="badge bg-label-primary">Planes de suscripción</span>
            </div>
            <h2 class="text-center fw-bold mb-2">Elige tu plan musical</h2>
            <p class="text-center text-body-secondary mb-6">
                Disfruta sin límites con beneficios a tu medida.
            </p>

            <div class="row gy-4 justify-content-center">
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
                                <img src="{{ $plan->image }}" alt="{{ $plan->name }}" class="mb-4"
                                    style="width:64px;height:64px;object-fit:contain;">
                                <h4 class="mb-1">{{ $plan->name }}</h4>
                                <div class="d-flex align-items-center justify-content-center">
                                    <span class="h2 text-primary fw-extrabold mb-0">${{ $plan->price_formatted }}</span>
                                    <sub class="h6 text-body-secondary mb-n1 ms-1">
                                        {{ $plan->duration_months === 1 ? '/mes' : '/' . $plan->duration_months . ' meses' }}
                                    </sub>
                                </div>
                            </div>

                            <div class="card-body d-flex flex-column">
                                @if ($plan->description)
                                    <div class="list-unstyled small text-body mb-4">
                                        {!! $plan->description ?? '' !!}
                                    </div>
                                @endif

                                @if ($plan->features)
                                    <ul class="list-unstyled small text-body mb-4">
                                        @foreach ($plan->features as $item)
                                            <li class="mb-2 d-flex">
                                                <i class="ti tabler-check me-2"></i>
                                                <span>
                                                    {{ $item['value'] }}
                                                </span>
                                            </li>
                                        @endforeach
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

            <div class="row mt-5 ">
                <div class="col-md-6 d-md-flex justify-content-around">
                    <div class="mb-6 mb-md-0"><h4><a href="mailto:{{ config('contact.email') }}" class="text-heading"><i class="icon-base ti tabler-mail icon-lg"></i> {{ config('contact.email') ?? 'Sin definir' }}</a></h4></div>
                    <div class="mb-6 mb-md-0"><h4><a href="tel:{{ config('contact.phone') }}" class="text-heading"><i class="icon-base ti tabler-phone-call icon-lg"></i> {{ config('contact.phone') ??  'Sin definir' }}</a></h4></div>
                    <div class="mb-6 mb-md-0"><h4><a href="https://www.instagram.com/{{config('contact.instagram')}}/" class="text-heading"><i class="icon-base ti tabler-brand-instagram icon-lg"></i> {{ '@'.config('contact.instagram') ??  'Sin definir' }}</a></h4></div>
                </div>
            </div>
        </div>
    </section>
    
    <hr class="m-0 mt-6 mt-md-12">

    {{-- =========================
       RECOMENDADO
    ========================== --}}
    @auth
        <section id="home-recommended" class="section-py">

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
                            <p class="text-body-secondary mb-0">Tu dosis diaria con packs y mixes según lo que más
                                escuchas.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="border rounded-4 p-4 p-md-5 text-center bg-body">
                        <h3 class="h5 fw-bold mb-2">Aún no tenemos recomendaciones</h3>
                        <p class="text-body-secondary mb-3">Escucha algunas canciones y vuelve para recibir sugerencias a tu
                            medida.</p>
                        <a href="{{ route('search') }}" class="btn btn-label-primary">Descubrir música</a>
                    </div>
                </div>
            @endif
        </section>
    @endauth

    {{-- =========================
       NUEVOS LANZAMIENTOS
    ========================== --}}
    <section id="home-new" class="section-py">

        @php $hasNew = isset($newItems) && count($newItems) > 0; @endphp

        @if ($hasNew)
            @include('partials.collection', [
                'id' => 'collections-new',
                'badge' => 'Novedades',
                'title' => 'Estrenos de la semana',
                'subtitle' => 'Actualizado con lanzamientos fresquitos para que no te pierdas nada.',
                'ctaText' => 'Ver estrenos',
                'ctaHref' => route('collection.news'),
            
                'newSingles' => $newItems ?? null,
            
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
       PACKS DE ARTISTAS
    ========================== --}}
    <section id="home-collections" class="section-py">

        @php $hasArtists = isset($artistCollections) && count($artistCollections) > 0; @endphp

        @if ($hasArtists)
            @include('partials.collection', [
                'id' => 'collections-artists',
                'badge' => 'Explorar',
                'title' => 'Packs de artistas',
                'subtitle' => 'Viaja por su historia musical: etapas, hits y mezclas imprescindibles.',
                'ctaText' => 'Explorar packs',
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


    {{-- =========================
       CONTACTO
    ========================== 
    <section id="landingContact" class="section-py bg-body landing-contact">
        <div class="container" style="margin-top: 60px;">
            <div class="text-center mb-4">
                <span class="badge bg-label-primary">🎶 Contáctanos</span>
            </div>
            <h4 class="text-center mb-1">
                <span class="position-relative fw-extrabold z-1">¿Necesitas ayuda con tu música?
                    <img src="https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/front-pages/icons/section-title-icon.png" alt="laptop charging" class="section-title-img position-absolute object-fit-contain bottom-0 z-n1">
                </span>

            </h4>
            <p class="text-center mb-12 pb-md-4">Estamos aquí para resolver tus dudas sobre canciones, playlists, compras o licencias 🎧</p>
            <div class="row g-6">
                <div class="col-lg-5">
                    <div class="contact-img-box position-relative border p-2 h-100">
                        <img src="https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/front-pages/icons/contact-border.png" alt="contact border" class="contact-border-img position-absolute d-none d-lg-block scaleX-n1-rtl">
                        <img src="{{ asset('assets/img/front-pages/landing-page/contact-form.jpeg') }}" alt="contact customer service" class="contact-img w-100 scaleX-n1-rtl">
                        <div class="p-4 pb-2">
                            <div class="row g-4">
                                <div class="col-md-6 col-lg-12 col-xl-6">
                                    <div class="d-flex align-items-center">
                                        <div class="badge bg-label-primary rounded p-1_5 me-3"><i class="icon-base ti tabler-mail icon-lg"></i></div>
                                        <div>
                                            <p class="mb-0">Correo</p>
                                            <h6 class="mb-0"><a href="mailto:{{ config('contact.email') }}" class="text-heading">{{ config('contact.email') }}</a></h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-12 col-xl-6">
                                    <div class="d-flex align-items-center">
                                        <div class="badge bg-label-success rounded p-1_5 me-3"><i class="icon-base ti tabler-phone-call icon-lg"></i></div>
                                        <div>
                                            <p class="mb-0">Teléfono</p>
                                            <h6 class="mb-0"><a href="tel:{{ config('contact.phone') }}" class="text-heading">{{ config('contact.phone') }}</a></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card h-100">
                        <div class="card-body">
                            <h4 class="mb-2">🎵 Escríbenos un mensaje</h4>
                            <p class="mb-6">
                                ¿Problemas con tu cuenta, compras de canciones o playlists personalizadas?<br class="d-none d-lg-block">
                                Déjanos tu mensaje y nuestro equipo musical te ayudará.
                            </p>
                            <form action="{{ route('contact.form') }}" method="POST">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label" for="contact-form-fullname">Nombre</label>
                                        <input type="text" class="form-control" id="contact-form-fullname" name="fullname" placeholder="john" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="contact-form-email">Correo</label>
                                        <input type="text" id="contact-form-email" class="form-control" name="email" placeholder="johndoe@gmail.com" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="contact-form-message">Mensaje</label>
                                        <textarea id="contact-form-message" class="form-control" rows="7" name="message" placeholder="Write a message" required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light">📩 Enviar consulta</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
@endsection

@push('scripts')
<script>
    window.addEventListener('DOMContentLoaded', function() {
        document.body.classList.remove('bg-body');
    })
</script>
@endpush