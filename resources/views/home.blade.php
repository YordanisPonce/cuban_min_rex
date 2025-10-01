@extends('layouts.app')

@section('title', 'Inicio – Cuban Mix Rex')

@section('content')

    {{-- =========================
       HERO compacto
  ========================== --}}
    <section id="hero" class="py-6 py-lg-7 bg-body" style="margin-top: 85px;">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="badge bg-label-primary mb-3">Descubre música</span>
                    <h1 class="display-6 fw-bold mb-2">Tu próxima canción favorita, a un click</h1>
                    <p class="text-body-secondary mb-4">
                        Explora colecciones, artistas y lanzamientos seleccionados para ti.
                    </p>

                    <form class="input-group input-group-lg" action="{{ route('search') }}" method="GET">
                        <span class="input-group-text"><i class="ti tabler-search"></i></span>
                        <input type="search" class="form-control" placeholder="Buscar artistas, canciones o colecciones…">
                    </form>

                    <div class="d-flex align-items-center gap-3 mt-4">
                        <a href="#home-recommended" class="btn btn-primary">Escuchar ahora</a>
                        <a href="#home-collections" class="btn btn-outline-secondary">Ver colecciones</a>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="ratio ratio-4x3 rounded-4 overflow-hidden border border-dark-subtle">
                        <img src="{{ asset('assets/img/album/imagine-dragons.png') }}" alt="Arte destacado"
                            class="w-100 h-100 object-fit-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- =========================
       RECOMENDADO (reusa tu partial)
  ========================== --}}
    <section id="home-recommended" class="section-py bg-body">
        <div class="container mb-3">
            <div class="d-flex align-items-end justify-content-between">
                <div>
                    <span class="badge bg-label-primary mb-2">Para ti</span>
                    <h2 class="h3 fw-bold mb-1">Recomendado</h2>
                    <p class="text-body-secondary mb-0">Selección curada según tus gustos y tendencias.</p>
                </div>
                <a href="#" class="link-underline">Ver todas →</a>
            </div>
        </div>

        {{-- Carrusel/cards (tu componente actual) --}}
        @include('partials.collection', ['id' => 'collections-recommended'])
    </section>

    {{-- =========================
       NUEVOS LANZAMIENTOS (reusa partial)
  ========================== --}}
    <section id="home-new" class="section-py bg-body">
        <div class="container mb-3">
            <div class="d-flex align-items-end justify-content-between">
                <div>
                    <span class="badge bg-label-primary mb-2">Novedades</span>
                    <h2 class="h3 fw-bold mb-1">Nuevos lanzamientos</h2>
                    <p class="text-body-secondary mb-0">Lo último de tus artistas y bandas favoritas.</p>
                </div>
                <a href="#" class="link-underline">Ver todas →</a>
            </div>
        </div>

        @include('partials.collection', ['id' => 'collections-new'])
    </section>

    {{-- =========================
       COLECCIONES DE ARTISTAS (reusa partial)
  ========================== --}}
    <section id="home-collections" class="section-py bg-body">
        <div class="container mb-3">
            <div class="d-flex align-items-end justify-content-between">
                <div>
                    <span class="badge bg-label-primary mb-2">Explorar</span>
                    <h2 class="h3 fw-bold mb-1">Colecciones de artistas</h2>
                    <p class="text-body-secondary mb-0">Descubre discografías y playlists temáticas.</p>
                </div>
                <a href="#" class="link-underline">Ver todas →</a>
            </div>
        </div>

        @include('partials.collection', ['id' => 'collections-artists'])
    </section>

    {{-- =========================
       GÉNEROS POPULARES (opcional, chips simples)
  ========================== --}}
    <section id="home-genres" class="section-py bg-body">
        <div class="container">
            <div class="d-flex align-items-end justify-content-between mb-3">
                <div>
                    <span class="badge bg-label-primary mb-2">Explorar</span>
                    <h2 class="h3 fw-bold mb-1">Géneros populares</h2>
                    <p class="text-body-secondary mb-0">Elige un género para iniciar tu viaje.</p>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                @foreach (['Pop', 'Rock', 'Reggaetón', 'Hip-Hop', 'Electrónica', 'Indie', 'Latino', 'Baladas'] as $genre)
                    <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill px-3">{{ $genre }}</a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =========================
       PLANES (reusa tu sección actual con pequeños ajustes de copy)
  ========================== --}}
    <section id="home-pricing" class="section-py bg-body landing-pricing">
        <div class="container">
            <div class="text-center mb-3">
                <span class="badge bg-label-primary">Planes de suscripción</span>
            </div>
            <h2 class="text-center fw-bold mb-2">Elige tu plan musical ideal</h2>
            <p class="text-center text-body-secondary mb-6">
                Disfruta de toda la música que amas con beneficios a tu medida.
            </p>

            {{-- Tu grid de planes tal como lo tienes --}}
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
                                        <li class="mb-2 d-flex"><i class="ti tabler-check me-2"></i>
                                            {{ $plan->description }}</li>
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

    {{-- =========================
       CONTACTO
  ========================== --}}
    <section id="home-contact" class="section-py bg-body">
        <div class="container">
            <div class="text-center mb-3">
                <span class="badge bg-label-primary">Contacto</span>
            </div>
            <h2 class="text-center fw-bold mb-2">¿Necesitas ayuda?</h2>
            <p class="text-center text-body-secondary mb-6">
                Escríbenos y te respondemos cuanto antes.
            </p>

            <div class="row g-5">
                <div class="col-lg-5">
                    <div class="p-4 border rounded-4 h-100">
                        <div class="d-flex align-items-center mb-3">
                            <div class="badge bg-label-primary rounded p-2 me-3"><i class="ti tabler-mail"></i></div>
                            <div>
                                <p class="mb-0 small text-body-secondary">Correo</p>
                                <h6 class="mb-0"><a href="mailto:soporte@cubanmix.com"
                                        class="text-reset">soporte@cubanmix.com</a></h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="badge bg-label-success rounded p-2 me-3"><i class="ti tabler-phone-call"></i>
                            </div>
                            <div>
                                <p class="mb-0 small text-body-secondary">Teléfono</p>
                                <h6 class="mb-0"><a href="tel:+1234568963" class="text-reset">+1 234 568 963</a></h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card h-100">
                        <div class="card-body">
                            <h4 class="mb-2">Escríbenos un mensaje</h4>
                            <form>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label" for="contact-name">Nombre</label>
                                        <input type="text" id="contact-name" class="form-control"
                                            placeholder="Tu nombre">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="contact-email">Correo</label>
                                        <input type="email" id="contact-email" class="form-control"
                                            placeholder="tucorreo@ejemplo.com">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="contact-message">Mensaje</label>
                                        <textarea id="contact-message" class="form-control" rows="6" placeholder="Cuéntanos en qué te ayudamos"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Enviar consulta</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection
