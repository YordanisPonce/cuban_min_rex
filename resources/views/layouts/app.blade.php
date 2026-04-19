<!doctype html>

<html lang="es" class="layout-navbar-fixed layout-wide" dir="ltr" data-skin="default" data-bs-theme="dark"
    data-assets-path="{{ asset('assets/') }}" data-template="front-pages">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>@yield('title', config('app.name'))</title>

    <!-- Canonical SEO -->
    <link rel="canonical" href="{{ config('app.url') }}">
    <meta name="keywords" content="{{ config('app.keywords') }}">
    <meta name="description" content="{{ config('app.description') }}">
    <meta name="author" content="{{ config('app.name') }}">
    <meta name="robots" content="index, follow">
    <meta name="language" content="es">
    <meta name="distribution" content="global">
    <meta name="rating" content="general">
    <meta name="revisit-after" content="7 days">

    <!-- Open Graph (Facebook, WhatsApp, Instagram) -->
    <meta property="og:title" content="{{ config('app.name') }} - Música, Remixes y Descargas">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ config('app.logo') }}">
    <meta property="og:description" content="{{ config('app.description') }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:url" content="{{ config('app.url') }}">
    <meta property="og:locale" content="es_ES">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ config('app.name') }} - Música y Remixes">
    <meta name="twitter:description" content="Escucha y descarga música, remixes y contenido exclusivo para DJs.">
    <meta name="twitter:image" content="{{ config('app.logo') }}">
    <meta name="twitter:site" content="{{ '@' . config('app.name') }}">

    <!-- Mobile & PWA -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#000000">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">

    @if (env('APP_ENV') == 'production')
        <script>
            (function(w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({
                    'gtm.start': new Date().getTime(),
                    event: 'gtm.js'
                });
                var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s),
                    dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', 'GTM-5J3LMKC');
        </script>
    @endif

    <!-- Favicon -->
    <link rel="icon" href="{{ config('app.logo') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/" />
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&amp;ampdisplay=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/nouislider/nouislider.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/swiper/swiper.css') }}" />

    <!-- Page CSS -->

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/aos/aos.css') }}" />
    <link rel="stylesheet" href="../../assets/vendor/libs/plyr/plyr.css" />
    <script src="../../assets/vendor/libs/plyr/plyr.js"></script>
    <script src="{{ asset('assets/vendor/libs/aos/aos.js') }}"></script>

    <style>
        body {
            background: url('{{ asset('assets/img/bg.png') }}') no-repeat center center fixed;
            background-color: rgba(0, 0, 0, .75);
            background-blend-mode: overlay;
            background-size: cover;
        }

        .plyr {
            --plyr-color-main: var(--primary);
            --plyr-focus-visible-color: var(--primary-dark);
            --plyr-menu-background: var(--bg3);
            --plyr-video-control-color-hover: white;
            --plyr-audio-control-color-hover: white;
            --plyr-menu-color: white;
        }

        .plyr__menu, .plyr [data-plyr=pip]{
            display: none;
        }
    </style>

    @stack('styles')
</head>

<body style="height: 100vh">
    @if (env('APP_ENV') == 'production')
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5J3LMKC" height="0" width="0"
                style="display: none; visibility: hidden"></iframe></noscript>
    @endif

    @php
        $settings = \App\Models\Setting::first();
    @endphp

    @if ($settings && $settings->maintenance)
        <div class="h-100 d-flex flex-column align-items-center justify-content-center">
            <p><i class="icon-base ti tabler-mood-confuzed-filled" style="width: 6rem; height: 6rem"></i></p>
            <h1>MANTENIMIENTO</h1>
            <p>Lo sentimos, actualmente el sitio se encuentra en mantenimiento.</p>
        </div>
    @else
        @include('partials.navbar')
        {{-- <div class="bg-mobile">
            <img src="{{ asset('assets/img/front-pages/backgrounds/remixes-bg-mobile.jpg') }}">
            <div class="dark-screen"></div>    
        </div> --}}

        <main>
            @yield('content')
        </main>

        @include('partials.footer')
    @endif

    <div class="window-loader" id="wloader">
        <div>
            <i class="fa fa-spinner fa-spin"></i>
            <span>Cargando...</span>
        </div>
    </div>

    <script>
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                    pageLanguage: 'es',
                    includedLanguages: 'es,en'
                },
                'google_translator'
            );
        }
    </script>

    <script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/theme.js  -->

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>

    <script src="{{ asset('assets/vendor/libs/@algolia/autocomplete-js.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/pickr/pickr.js') }}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/nouislider/nouislider.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/swiper/swiper.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- <!-- Main JS -->
    <script src="{{ asset('assets/js/front-main.js') }}"></script>

    <!-- Page JS -->
         <script src="{{ asset('assets/js/front-page-landing.js') }}"></script> --}}

    <script>
        window.addEventListener('DOMContentLoaded', function() {
            document.getElementById('navbarToggle').addEventListener('click', function() {
                document.getElementById('navBar').classList.toggle('active');
            });

            document.getElementById('wloader').style.display = 'none';
        })
        AOS.init({
            duration: 1500, // duración en ms
            once: true // animar solo una vez
        });
    </script>

    @stack('scripts')
</body>

</html>
