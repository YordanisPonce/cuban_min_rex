@extends('layouts.app')

@section('title', 'Página de Inicio')

@push('styles')
<link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page-help-center.css') }}" />
@endpush

@section('content')
<!-- Hero: Start -->
<section id="hero-animation">
  <!-- <div id="landingHero" class="section-py landing-hero position-relative">
    <img src="{{ asset('assets/img/front-pages/backgrounds/hero-bg.png') }}" alt="hero background" class="position-absolute top-0 start-50 translate-middle-x object-fit-cover w-100 h-100" data-speed="1" />
    <div class="container">
      <div class="hero-text-box text-center position-relative">
        <h1 class="text-primary hero-title display-6 fw-extrabold">One dashboard to manage all your businesses</h1>
        <h2 class="hero-sub-title h6 mb-6">
          Production-ready & easy to use Admin Template<br class="d-none d-lg-block" />
          for Reliability and Customizability.
        </h2>
        <div class="landing-hero-btn d-inline-block position-relative">
          <span class="hero-btn-item position-absolute d-none d-md-flex fw-medium">Join community <img src="{{ asset('assets/img/front-pages/icon/Join-community-arrow.png') }}" alt="Join community arrow" class="scaleX-n1-rtl" /></span>
          <a href="#landingPricing" class="btn btn-primary btn-lg">Get early access</a>
        </div>
      </div>
      <div id="heroDashboardAnimation" class="hero-animation-img">
        <a href="https://demos.pixinvent.com/vuexy-html-admin-template/html/vertical-menu-template/app-ecommerce-dashboard.html" target="_blank">
          <div id="heroAnimationImg" class="position-relative hero-dashboard-img">
            <img src="{{ asset('assets/img/front-pages/landing-page/hero-dashboard-light.png')}}" alt="hero dashboard" class="animation-img" data-app-light-img="{{ asset('assets/front-pages/front-pages/landing-page/hero-dashboard-dark.png')}}" />
            <img src="{{ asset('assets/img/front-pages/landing-page/hero-elements-light.png')}}" alt="hero elements" class="position-absolute hero-elements-img animation-img top-0 start-0" data-app-light-img="front-pages/landing-page/hero-elements-light.png" data-app-dark-img="assets/img/front-pages/landing-page/hero-elements-dark.png" />
          </div>
        </a>
      </div>
    </div>
  </div>
  <div class="landing-hero-blank"></div> -->

  <div id="demo" class="carousel slide" data-bs-ride="carousel">

    <div class="carousel-indicators">
      <button type="button" data-bs-target="#demo" data-bs-slide-to="0" class="active"></button>
      <button type="button" data-bs-target="#demo" data-bs-slide-to="1"></button>
      <button type="button" data-bs-target="#demo" data-bs-slide-to="2"></button>
    </div>

    <div class="carousel-inner">
      <div class="carousel-item relative active">
        <img src="data:image/webp;base64,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" alt="imagine-dragons" class="d-block" style="width:100%">
        <div class="carousel-caption">
          <h3>Imagine Dragons</h3>
          <p><i class="icon-base ti tabler-star-filled"></i>
            <i class="icon-base ti tabler-star-filled"></i>
            <i class="icon-base ti tabler-star-filled"></i>
            <i class="icon-base ti tabler-star-filled"></i>
            <i class="icon-base ti tabler-star-filled"></i>
          </p>
        </div>
        <div class="dark-screen"></div>
      </div>
      <div class="carousel-item relative">
        <img src="https://th.bing.com/th/id/OIP.H6GK0lclpzeTtEwam6uGWwHaEK?w=207&h=150&c=6&o=7&dpr=1.3&pid=1.7&rm=3" alt="imagine-dragons" class="d-block" style="width:100%">
        <div class="carousel-caption">
          <h3>Imagine Dragons</h3>
          <p><i class="icon-base ti tabler-star-filled"></i>
            <i class="icon-base ti tabler-star-filled"></i>
            <i class="icon-base ti tabler-star-filled"></i>
            <i class="icon-base ti tabler-star-filled"></i>
            <i class="icon-base ti tabler-star-filled"></i>
          </p>
        </div>
        <div class="dark-screen"></div>
      </div>
      <div class="carousel-item relative">
        <img src="{{ asset('/assets/img/album/imagine-dragons.png') }}" alt="imagine-dragons" class="d-block" style="width:100%">
        <div class="carousel-caption">
          <h3>Imagine Dragons</h3>
          <p><i class="icon-base ti tabler-star-filled"></i>
            <i class="icon-base ti tabler-star-filled"></i>
            <i class="icon-base ti tabler-star-filled"></i>
            <i class="icon-base ti tabler-star-filled"></i>
            <i class="icon-base ti tabler-star-filled"></i>
          </p>
        </div>
        <div class="dark-screen"></div>
      </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#demo" data-bs-slide="prev">
      <span>
        <svg fill="#000000" version="1.1" baseProfile="tiny" id="Layer_1" xmlns:x="&ns_extend;" xmlns:i="&ns_ai;" xmlns:graph="&ns_graphs;"
          xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"
          width="40px" height="40px" viewBox="0 0 42 42" xml:space="preserve">
          <polygon fill-rule="evenodd" points="31,38.32 13.391,21 31,3.68 28.279,1 8,21.01 28.279,41 " />
        </svg>
      </span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#demo" data-bs-slide="next">
      <span>
        <svg fill="#000000" version="1.1" baseProfile="tiny" id="Layer_1" xmlns:x="&ns_extend;" xmlns:i="&ns_ai;" xmlns:graph="&ns_graphs;"
          xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"
          width="40px" height="40px" viewBox="0 0 42 42" xml:space="preserve">
          <polygon fill-rule="evenodd" points="11,38.32 28.609,21 11,3.68 13.72,1 34,21.01 13.72,41 " />
        </svg>
      </span>
    </button>
  </div>
</section>
<!-- Hero: End -->

<!-- Our great team: Start -->
<section id="landingReviews" class="section-py bg-body">
  <!-- What people say slider: Start -->
  <div class="container">
    <div class="row align-items-center gx-0 gy-4 g-lg-5 mb-5 pb-md-5">
      <div class="col-md-6 col-lg-5 col-xl-3">
        <div class="mb-4">
          <span class="badge bg-label-primary">Artistas Destacados</span>
        </div>
        <h4 class="mb-1">
          <span class="position-relative fw-extrabold z-1">Canciones más populares
            <img src="https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/front-pages/icons/section-title-icon.png" alt="laptop charging" class="section-title-img position-absolute object-fit-contain bottom-0 z-n1">
          </span>
        </h4>
        <p class="mb-5 mb-md-12">Escucha a los artistas más sonados<br class="d-none d-xl-block">y descubre tu próxima canción favorita.</p>
        <div class="landing-reviews-btns">
          <button id="reviews-previous-btn" class="btn btn-icon btn-label-primary reviews-btn me-3 waves-effect" type="button">
            <i class="icon-base ti tabler-chevron-left icon-md scaleX-n1-rtl"></i>
          </button>
          <button id="reviews-next-btn" class="btn btn-icon btn-label-primary reviews-btn waves-effect" type="button">
            <i class="icon-base ti tabler-chevron-right icon-md scaleX-n1-rtl"></i>
          </button>
        </div>
      </div>
      <div class="col-md-6 col-lg-7 col-xl-9">
        <div class="swiper-reviews-carousel overflow-hidden">
          <div class="swiper swiper-initialized swiper-horizontal swiper-backface-hidden" id="swiper-reviews">
            <div class="swiper-wrapper" id="swiper-wrapper-d5f7ff1684355bde" aria-live="off" style="transition-duration: 0ms; transform: translate3d(-560px, 0px, 0px); transition-delay: 0ms;">

              <div class="swiper-slide" role="group" aria-label="2 / 6" style="width: 254px; margin-right: 26px;" data-swiper-slide-index="2">
                <div class="card h-100">
                  <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                    <div class="mb-4">
                      <img src="{{ asset('/assets/img/album/imagine-dragons.png') }}">
                    </div>
                    <div class="text-warning mb-8">
                      <i class="icon-base ti tabler-star-filled"></i>
                      <i class="icon-base ti tabler-star-filled"></i>
                      <i class="icon-base ti tabler-star-filled"></i>
                      <i class="icon-base ti tabler-star-filled"></i>
                      <i class="icon-base ti tabler-star-filled"></i>
                    </div>
                    <div class="d-flex align-items-center">
                      <div class="avatar me-3 avatar-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-music-player" viewBox="0 0 16 16">
                          <path d="M4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1zm1 0v3h6V3zm3 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2" />
                          <path d="M11 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-3 2a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                          <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z" />
                        </svg>
                      </div>
                      <div>
                        <h6 class="mb-0">Imagine Dragons</h6>
                        <p class="small text-body-secondary mb-0">Banda</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="swiper-slide" role="group" aria-label="3 / 6" style="width: 254px; margin-right: 26px;" data-swiper-slide-index="2">
                <div class="card h-100">
                  <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                    <div class="mb-4">
                      <img src="{{ asset('/assets/img/album/imagine-dragons.png') }}">
                    </div>
                    <div class="text-warning mb-8">
                      <i class="icon-base ti tabler-star-filled"></i>
                      <i class="icon-base ti tabler-star-filled"></i>
                      <i class="icon-base ti tabler-star-filled"></i>
                      <i class="icon-base ti tabler-star-filled"></i>
                      <i class="icon-base ti tabler-star-filled"></i>
                    </div>
                    <div class="d-flex align-items-center">
                      <div class="avatar me-3 avatar-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-music-player" viewBox="0 0 16 16">
                          <path d="M4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1zm1 0v3h6V3zm3 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2" />
                          <path d="M11 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-3 2a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                          <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z" />
                        </svg>
                      </div>
                      <div>
                        <h6 class="mb-0">Imagine Dragons</h6>
                        <p class="small text-body-secondary mb-0">Banda</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="swiper-slide" role="group" aria-label="4 / 6" style="width: 254px; margin-right: 26px;" data-swiper-slide-index="2">
                <div class="card h-100">
                  <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                    <div class="mb-4">
                      <img src="{{ asset('/assets/img/album/imagine-dragons.png') }}">
                    </div>
                    <div class="text-warning mb-8">
                      <i class="icon-base ti tabler-star-filled"></i>
                      <i class="icon-base ti tabler-star-filled"></i>
                      <i class="icon-base ti tabler-star-filled"></i>
                      <i class="icon-base ti tabler-star-filled"></i>
                      <i class="icon-base ti tabler-star-filled"></i>
                    </div>
                    <div class="d-flex align-items-center">
                      <div class="avatar me-3 avatar-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-music-player" viewBox="0 0 16 16">
                          <path d="M4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1zm1 0v3h6V3zm3 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2" />
                          <path d="M11 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-3 2a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                          <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z" />
                        </svg>
                      </div>
                      <div>
                        <h6 class="mb-0">Imagine Dragons</h6>
                        <p class="small text-body-secondary mb-0">Banda</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="swiper-slide" role="group" aria-label="5 / 6" style="width: 254px; margin-right: 26px;" data-swiper-slide-index="2">
                <div class="card h-100">
                  <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                    <div class="mb-4">
                      <img src="{{ asset('/assets/img/album/imagine-dragons.png') }}">
                    </div>
                    <div class="text-warning mb-8">
                      <i class="icon-base ti tabler-star-filled"></i>
                      <i class="icon-base ti tabler-star-filled"></i>
                      <i class="icon-base ti tabler-star-filled"></i>
                      <i class="icon-base ti tabler-star-filled"></i>
                      <i class="icon-base ti tabler-star-filled"></i>
                    </div>
                    <div class="d-flex align-items-center">
                      <div class="avatar me-3 avatar-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-music-player" viewBox="0 0 16 16">
                          <path d="M4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1zm1 0v3h6V3zm3 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2" />
                          <path d="M11 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-3 2a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                          <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z" />
                        </svg>
                      </div>
                      <div>
                        <h6 class="mb-0">Imagine Dragons</h6>
                        <p class="small text-body-secondary mb-0">Banda</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="swiper-slide" role="group" aria-label="6 / 6" style="width: 254px; margin-right: 26px;" data-swiper-slide-index="2">
                <div class="card h-100">
                  <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                    <div class="mb-4">
                      <img src="{{ asset('/assets/img/album/imagine-dragons.png') }}">
                    </div>
                    <div class="text-warning mb-8">
                      <i class="icon-base ti tabler-star-filled"></i>
                      <i class="icon-base ti tabler-star-filled"></i>
                      <i class="icon-base ti tabler-star-filled"></i>
                      <i class="icon-base ti tabler-star-filled"></i>
                      <i class="icon-base ti tabler-star-filled"></i>
                    </div>
                    <div class="d-flex align-items-center">
                      <div class="avatar me-3 avatar-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-music-player" viewBox="0 0 16 16">
                          <path d="M4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1zm1 0v3h6V3zm3 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2" />
                          <path d="M11 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-3 2a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                          <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z" />
                        </svg>
                      </div>
                      <div>
                        <h6 class="mb-0">Imagine Dragons</h6>
                        <p class="small text-body-secondary mb-0">Banda</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>
            <div class="swiper-button-next" tabindex="0" role="button" aria-label="Next slide" aria-controls="swiper-wrapper-d5f7ff1684355bde"></div>
            <div class="swiper-button-prev" tabindex="0" role="button" aria-label="Previous slide" aria-controls="swiper-wrapper-d5f7ff1684355bde"></div>
            <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- What people say slider: End -->
</section>

<!-- Fun facts: Start -->
<section id="landingMusicStats" class="section-py">
  <div class="container">
    <div class="text-center mb-4">
      <span class="badge bg-label-primary">Top Música</span>
    </div>
    <h4 class="text-center mb-1">
      Estadísticas de tus
      <span class="position-relative fw-extrabold z-1">más escuchados
        <img src="https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/front-pages/icons/section-title-icon.png"
          alt="music icon"
          class="section-title-img position-absolute object-fit-contain bottom-0 z-n1">
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

<section id="landingPricing" class="section-py bg-body">
  <div class="container">
    <div class="text-center mb-4">
      <span class="badge bg-label-primary">🎶 Planes de Suscripción</span>
    </div>
    <h4 class="text-center mb-1">
      <span class="position-relative fw-extrabold z-1">
        Elige tu plan musical ideal
      </span>
    </h4>
    <p class="text-center pb-2 mb-7">
      Disfruta de toda la música que amas, con beneficios que se adaptan a ti. <br>
      <!-- Paga mensual o ahorra con el plan anual 🎧 -->
    </p>

    <!-- <div class="text-center mb-12">
      <div class="position-relative d-inline-block pt-3 pt-md-0">
        <label class="switch switch-sm switch-primary me-0">
          <span class="switch-label fs-6 text-body me-3">Mensual</span>
          <input type="checkbox" class="switch-input price-duration-toggler">
          <span class="switch-toggle-slider">
            <span class="switch-on"></span>
            <span class="switch-off"></span>
          </span>
          <span class="switch-label fs-6 text-body ms-3">Anual</span>
        </label>
        <div class="pricing-plans-item position-absolute d-flex">
          <img src="https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/front-pages/icons/pricing-plans-arrow.png" alt="pricing plans arrow" class="scaleX-n1-rtl">
          <span class="fw-medium mt-2 ms-1"> Ahorra 25%</span>
        </div>
      </div>
    </div> -->

    <div class="row g-6 pt-lg-5">
      <div class="row gy-4">
        @foreach($plans as $plan)
        @php
        $isActive = auth()->check() && auth()->user()->current_plan_id === $plan->id && auth()->user()->hasActivePlan();
        @endphp
        <div class="col-xl-4 col-lg-6">
          <div class="{{ $isActive ? 'card border border-primary shadow-xl' : 'card'}}">
            <div class="card-header">
              <div class="text-center">
                <img src="{{ asset('storage/' . $plan->image) }}" alt="paper airplane icon" class="mb-8 pb-2 w-25" />
                <h4 class="mb-0">{{ $plan->name }}</h4>
                <div class="d-flex align-items-center justify-content-center">
                  <span class="price-monthly h2 text-primary fw-extrabold mb-0">€{{ $plan->price_formatted }}</span>
                  <!-- <span class="price-yearly h2 text-primary fw-extrabold mb-0 d-none">€{{ $plan->price_formatted*0.75 }}</span> -->
                  <sub class="h6 text-body-secondary mb-n1 ms-1">/mes</sub>
                </div>
                <!-- <div class="position-relative pt-2">
                      <div class="price-yearly text-body-secondary price-yearly-toggle d-none">€{{ $plan->price_formatted*12*0.75 }} / año</div>
                    </div> -->
              </div>
            </div>
            <div class="card-body">
              @if($plan->description)
              <ul class="list-unstyled pricing-list">
                <li>
                  <h6 class="d-flex align-items-center mb-3">
                    <span class="badge badge-center rounded-pill bg-label-primary p-0 me-3"><i class="icon-base ti tabler-check icon-12px"></i></span>
                    {{ $plan->description }}
                  </h6>
                </li>
              </ul>
              @endif
              <div class="d-grid mt-8">
                @auth
                @if($isActive)
                <button class="btn btn-secondary" disabled>Ya lo tienes</button>
                @else
                <a href="{{ route('payment.form', $plan->id) }}" class="btn btn-label-primary">Adquirir Plan</a>
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
    </div>
  </div>
</section>

<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script><!-- Pricing plans: End -->



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