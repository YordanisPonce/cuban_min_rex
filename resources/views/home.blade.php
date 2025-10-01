@extends('layouts.app')

@section('title', 'Home Page')

@section('content')

    @include('partials.collection', ['id' => 'collections-1'])
    @include('partials.collection', ['id' => 'collections-3'])
    <section id="landingPricing" class="section-py bg-body landing-pricing">
        <div class="container">
            <div class="text-center mb-4">
                <span class="badge bg-label-primary">🎶 Planes de Suscripción</span>
            </div>
            <h4 class="text-center mb-1">
                <span class="position-relative fw-extrabold z-1">
                    Elige tu plan musical ideal
                    {{-- <img src="https://cdn-icons-png.flaticon.com/512/727/727245.png"
             alt="nota musical"
             class="section-title-img position-absolute object-fit-contain bottom-0 z-n1"
             style="width:40px; left:50%; transform:translateX(-50%);"> --}}
                </span>
            </h4>
            <p class="text-center pb-2 mb-7">
                Disfruta de toda la música que amas, con beneficios que se adaptan a ti. <br>
                <!-- Paga mensual o ahorra con el plan anual 🎧 -->
            </p>

            <div class="row g-6 pt-lg-5">
                <!-- Plan Básico -->
                <div class="row gy-4">
                    @foreach ($plans as $plan)
                        @php
                            $isActive =
                                auth()->check() &&
                                auth()->user()->current_plan_id === $plan->id &&
                                auth()->user()->hasActivePlan();
                        @endphp
                        <div class="col-xl-4 col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <div class="text-center">
                                        <img src="{{ asset('storage/' . $plan->image) }}" alt="paper airplane icon"
                                            class="mb-8 pb-2 w-25" />
                                        <h4 class="mb-0">{{ $plan->name }}</h4>
                                        <div class="d-flex align-items-center justify-content-center">
                                            <span
                                                class="price-monthly h2 text-primary fw-extrabold mb-0">€{{ $plan->price_formatted }}</span>
                                            <!-- <span class="price-yearly h2 text-primary fw-extrabold mb-0 d-none">€{{ $plan->price_formatted * 0.75 }}</span> -->
                                            <sub class="h6 text-body-secondary mb-n1 ms-1">/mes</sub>
                                        </div>
                                        <!-- <div class="position-relative pt-2">
                                                        <div class="price-yearly text-body-secondary price-yearly-toggle d-none">€{{ $plan->price_formatted * 12 * 0.75 }} / año</div>
                                                      </div> -->
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if ($plan->description)
                                        <ul class="list-unstyled pricing-list">
                                            <li>
                                                <h6 class="d-flex align-items-center mb-3">
                                                    <span
                                                        class="badge badge-center rounded-pill bg-label-primary p-0 me-3"><i
                                                            class="icon-base ti tabler-check icon-12px"></i></span>
                                                    {{ $plan->description }}
                                                </h6>
                                            </li>
                                        </ul>
                                    @endif
                                    <div class="d-grid mt-8">
                                        @auth
                                            @if ($isActive)
                                                <button class="btn btn-secondary" disabled>Ya lo tienes</button>
                                            @else
                                                <a href="{{ route('payment.form', $plan->id) }}"
                                                    class="btn btn-label-primary">Adquirir Plan</a>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-outline-primary">
                                                Inicia sesión para comprar
                                            </a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                {{-- <div class="col-xl-4 col-lg-6">
        <div class="card">
          <div class="card-header text-center">
            <img src="https://cdn-icons-png.flaticon.com/512/727/727218.png" alt="icono básico" class="mb-4" style="width:60px;">
            <h4 class="mb-0">Plan Básico</h4>
            <div class="d-flex align-items-center justify-content-center">
              <span class="price-monthly h2 text-primary fw-extrabold mb-0 d-none">$5</span>
              <span class="price-yearly h2 text-primary fw-extrabold mb-0">$3.75</span>
              <sub class="h6 text-body-secondary mb-n1 ms-1">/mes</sub>
            </div>
            <div class="pt-2">
              <div class="price-yearly text-body-secondary price-yearly-toggle">$ 45 / año</div>
            </div>
          </div>
          <div class="card-body">
            <ul class="list-unstyled pricing-list">
              <li><h6><i class="fa-solid fa-check text-primary me-2"></i> 50 canciones/mes</h6></li>
              <li><h6><i class="fa-solid fa-check text-primary me-2"></i> Escucha online</h6></li>
              <li><h6><i class="fa-solid fa-check text-primary me-2"></i> Playlists básicas</h6></li>
            </ul>
            <div class="d-grid mt-4">
              <a href="#" class="btn btn-label-primary">Elegir Básico</a>
            </div>
          </div>
        </div>
      </div> --}}

                <!-- Plan Premium -->
                {{-- <div class="col-xl-4 col-lg-6">
        <div class="card border border-primary shadow-xl">
          <div class="card-header text-center">
            <img src="https://cdn-icons-png.flaticon.com/512/727/727245.png" alt="icono premium" class="mb-4" style="width:60px;">
            <h4 class="mb-0">Plan Premium</h4>
            <div class="d-flex align-items-center justify-content-center">
              <span class="price-monthly h2 text-primary fw-extrabold mb-0 d-none">$10</span>
              <span class="price-yearly h2 text-primary fw-extrabold mb-0">$7.5</span>
              <sub class="h6 text-body-secondary mb-n1 ms-1">/mes</sub>
            </div>
            <div class="pt-2">
              <div class="price-yearly text-body-secondary price-yearly-toggle">$ 90 / año</div>
            </div>
          </div>
          <div class="card-body">
            <ul class="list-unstyled pricing-list">
              <li><h6><i class="fa-solid fa-check text-primary me-2"></i> Canciones ilimitadas</h6></li>
              <li><h6><i class="fa-solid fa-check text-primary me-2"></i> Descargas offline</h6></li>
              <li><h6><i class="fa-solid fa-check text-primary me-2"></i> Playlists personalizadas</h6></li>
              <li><h6><i class="fa-solid fa-check text-primary me-2"></i> Soporte prioritario</h6></li>
            </ul>
            <div class="d-grid mt-4">
              <a href="#" class="btn btn-primary">Elegir Premium</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Plan Pro DJ -->
      <div class="col-xl-4 col-lg-6">
        <div class="card">
          <div class="card-header text-center">
            <img src="https://cdn-icons-png.flaticon.com/512/2910/2910768.png" alt="icono dj" class="mb-4" style="width:60px;">
            <h4 class="mb-0">Plan Pro DJ</h4>
            <div class="d-flex align-items-center justify-content-center">
              <span class="price-monthly h2 text-primary fw-extrabold mb-0 d-none">$20</span>
              <span class="price-yearly h2 text-primary fw-extrabold mb-0">$15</span>
              <sub class="h6 text-body-secondary mb-n1 ms-1">/mes</sub>
            </div>
            <div class="pt-2">
              <div class="price-yearly text-body-secondary price-yearly-toggle">$ 180 / año</div>
            </div>
          </div>
          <div class="card-body">
            <ul class="list-unstyled pricing-list">
              <li><h6><i class="fa-solid fa-check text-primary me-2"></i> Acceso a librería completa</h6></li>
              <li><h6><i class="fa-solid fa-check text-primary me-2"></i> Licencias comerciales</h6></li>
              <li><h6><i class="fa-solid fa-check text-primary me-2"></i> Master tracks en alta calidad</h6></li>
              <li><h6><i class="fa-solid fa-check text-primary me-2"></i> Integración con software DJ</h6></li>
            </ul>
            <div class="d-grid mt-4">
              <a href="#" class="btn btn-label-primary">Elegir Pro DJ</a>
            </div>
          </div>
        </div>
      </div> --}}
            </div>
        </div>
    </section>

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script><!-- Pricing plans: End -->
    @include('partials.collection', ['id' => 'collections-5'])
    <!-- Fun facts: Start -->
    <section id="landingMusicStats" class="section-py ">
        <div class="container">
            <div class="text-center mb-4">
                <span class="badge bg-label-primary">Top Música</span>
            </div>
            <h4 class="text-center mb-1">
                Estadísticas de tus
                <span class="position-relative fw-extrabold z-1">más escuchados
                    <img src="https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/front-pages/icons/section-title-icon.png"
                        alt="music icon" class="section-title-img position-absolute object-fit-contain bottom-0 z-n1">
                </span>
            </h4>
            <p class="text-center mb-12 pb-md-4">Descubre tus favoritos en un solo vistazo 🎶</p>

            <div class="row gy-4">
                <!-- Canción más escuchada -->
                <div class="col-md-6 col-lg-3">
                    <div class="card shadow-sm h-100 text-center p-3">
                        <div class="card-body">
                            <h5 class="fw-bold">🎵 Canción</h5>
                            <p class="mb-1 text-muted">Más escuchada</p>
                            <h6 class="text-primary">Shape of You</h6>
                            <p class="fw-bold fs-5">12,430 reproducciones</p>
                        </div>
                    </div>
                </div>

                <!-- Cantante más escuchado -->
                <div class="col-md-6 col-lg-3">
                    <div class="card shadow-sm h-100 text-center p-3">
                        <div class="card-body">
                            <h5 class="fw-bold">🎤 Cantante</h5>
                            <p class="mb-1 text-muted">Más escuchado</p>
                            <h6 class="text-success">Ed Sheeran</h6>
                            <p class="fw-bold fs-5">9,870 reproducciones</p>
                        </div>
                    </div>
                </div>

                <!-- Banda más escuchada -->
                <div class="col-md-6 col-lg-3">
                    <div class="card shadow-sm h-100 text-center p-3">
                        <div class="card-body">
                            <h5 class="fw-bold">🎸 Banda</h5>
                            <p class="mb-1 text-muted">Más escuchada</p>
                            <h6 class="text-danger">Coldplay</h6>
                            <p class="fw-bold fs-5">7,540 reproducciones</p>
                        </div>
                    </div>
                </div>

                <!-- Otro dato (ejemplo: género más escuchado) -->
                <div class="col-md-6 col-lg-3">
                    <div class="card shadow-sm h-100 text-center p-3">
                        <div class="card-body">
                            <h5 class="fw-bold">🎧 Género</h5>
                            <p class="mb-1 text-muted">Más escuchado</p>
                            <h6 class="text-warning">Pop</h6>
                            <p class="fw-bold fs-5">15,200 reproducciones</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- Fun facts: End -->
    @include('partials.collection', ['id' => 'collections-2', 'order' => 'reverse'])
    @include('partials.collection', ['id' => 'collections-4'])
    <!-- Contact Us: Start -->
    <section id="landingContact" class="section-py bg-body landing-contact">
        <div class="container">
            <div class="text-center mb-4">
                <span class="badge bg-label-primary">🎶 Contáctanos</span>
            </div>
            <h4 class="text-center mb-1">
                <span class="position-relative fw-extrabold z-1">¿Necesitas ayuda con tu música?
                    <img src="https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/front-pages/icons/section-title-icon.png"
                        alt="laptop charging" class="section-title-img position-absolute object-fit-contain bottom-0 z-n1">
                </span>

            </h4>
            <p class="text-center mb-12 pb-md-4">Estamos aquí para resolver tus dudas sobre canciones, playlists, compras o
                licencias 🎧</p>
            <div class="row g-6">
                <div class="col-lg-5">
                    <div class="contact-img-box position-relative border p-2 h-100">
                        <img src="https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/front-pages/icons/contact-border.png"
                            alt="contact border"
                            class="contact-border-img position-absolute d-none d-lg-block scaleX-n1-rtl">
                        <img src="https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/front-pages/landing-page/contact-customer-service.png"
                            alt="contact customer service" class="contact-img w-100 scaleX-n1-rtl">
                        <div class="p-4 pb-2">
                            <div class="row g-4">
                                <div class="col-md-6 col-lg-12 col-xl-6">
                                    <div class="d-flex align-items-center">
                                        <div class="badge bg-label-primary rounded p-1_5 me-3"><i
                                                class="icon-base ti tabler-mail icon-lg"></i></div>
                                        <div>
                                            <p class="mb-0">Correo</p>
                                            <h6 class="mb-0"><a href="mailto:example@gmail.com"
                                                    class="text-heading">soporte@cubanmix.com</a></h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-12 col-xl-6">
                                    <div class="d-flex align-items-center">
                                        <div class="badge bg-label-success rounded p-1_5 me-3"><i
                                                class="icon-base ti tabler-phone-call icon-lg"></i></div>
                                        <div>
                                            <p class="mb-0">Teléfono</p>
                                            <h6 class="mb-0"><a href="tel:+1234-568-963" class="text-heading">+1234 568
                                                    963</a></h6>
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
                                ¿Problemas con tu cuenta, compras de canciones o playlists personalizadas?<br
                                    class="d-none d-lg-block">
                                Déjanos tu mensaje y nuestro equipo musical te ayudará.
                            </p>
                            <form>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label" for="contact-form-fullname">Nombre</label>
                                        <input type="text" class="form-control" id="contact-form-fullname"
                                            placeholder="john">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="contact-form-email">Correo</label>
                                        <input type="text" id="contact-form-email" class="form-control"
                                            placeholder="johndoe@gmail.com">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="contact-form-message">Mensaje</label>
                                        <textarea id="contact-form-message" class="form-control" rows="7" placeholder="Write a message"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light">📩 Enviar
                                            consulta</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Contact Us: End -->
@endsection

@push('scripts')
    <script>
        // Scripts específicos para la página home
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar sliders, tooltips, etc.
            console.log('Home page loaded');
        });
    </script>
@endpush
