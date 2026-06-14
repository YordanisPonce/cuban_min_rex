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
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/plyr/plyr.css') }}" />
    <script src="{{ asset('assets/vendor/libs/plyr/plyr.js') }}"></script>
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

        .plyr__menu,
        .plyr [data-plyr=pip] {
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
        <style>
            <style>* {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            :root {
                --bg: #0f0d0b;
                --card: #1a1714;
                --primary: #f5a623;
                --fg: #f0e8d8;
                --muted: #8a7a66;
                --border: #2a2520;
                --tag-new: #e04040;
            }

            body {
                font-family: 'Inter', sans-serif;
                background: var(--bg);
                color: var(--fg);
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 2rem;
                position: relative;
                overflow: hidden;
            }

            a {
                text-decoration: none;
                color: inherit;
            }

            .bg-fx {
                position: absolute;
                inset: 0;
                pointer-events: none;
            }

            .bg-fx::before {
                content: "";
                position: absolute;
                top: 20%;
                left: 20%;
                width: 400px;
                height: 400px;
                background: radial-gradient(circle, rgba(245, 166, 35, .12) 0%, transparent 70%);
                border-radius: 50%;
                filter: blur(50px);
                animation: float 8s ease-in-out infinite;
            }

            .bg-fx::after {
                content: "";
                position: absolute;
                bottom: 20%;
                right: 20%;
                width: 400px;
                height: 400px;
                background: radial-gradient(circle, rgba(245, 166, 35, .08) 0%, transparent 70%);
                border-radius: 50%;
                filter: blur(50px);
                animation: float 10s ease-in-out infinite reverse;
            }

            @keyframes float {
                50% {
                    transform: translate(30px, -30px);
                }
            }

            .brand {
                position: absolute;
                top: 1.5rem;
                left: 2rem;
                display: flex;
                align-items: center;
                gap: .5rem;
                font-weight: 800;
                font-size: 1.1rem;
                z-index: 2;
            }

            .brand i {
                color: var(--primary);
            }

            .card {
                position: relative;
                z-index: 2;
                max-width: 600px;
                text-align: center;
            }

            .gear-stack {
                position: relative;
                width: 160px;
                height: 160px;
                margin: 0 auto 2rem;
            }

            .gear {
                position: absolute;
                color: var(--primary);
            }

            .gear-1 {
                top: 0;
                left: 0;
                font-size: 6rem;
                animation: spin 8s linear infinite;
            }

            .gear-2 {
                bottom: 0;
                right: 0;
                font-size: 4rem;
                color: var(--muted);
                animation: spin 5s linear infinite reverse;
            }

            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }

            .code {
                font-size: .8rem;
                font-weight: 700;
                color: var(--primary);
                letter-spacing: .3em;
                margin-bottom: .75rem;
                text-transform: uppercase;
            }

            .title {
                font-size: 2.5rem;
                font-weight: 900;
                margin-bottom: 1rem;
                line-height: 1.1;
                text-transform: uppercase;
            }

            .title span {
                color: var(--primary);
                font-style: italic;
            }

            .desc {
                color: var(--muted);
                font-size: 1rem;
                line-height: 1.6;
                margin-bottom: 2.5rem;
                max-width: 460px;
                margin: 0 auto 2.5rem;
            }

            /* progress */
            .progress-card {
                background: var(--card);
                border: 1px solid var(--border);
                border-radius: 12px;
                padding: 1.25rem;
                max-width: 440px;
                margin: 0 auto 2rem;
            }

            .progress-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: .75rem;
                font-size: .8rem;
            }

            .progress-label {
                color: var(--muted);
                text-transform: uppercase;
                font-weight: 700;
                letter-spacing: .05em;
            }

            .progress-pct {
                color: var(--primary);
                font-weight: 800;
            }

            .progress-bar {
                width: 100%;
                height: 6px;
                background: var(--border);
                border-radius: 3px;
                overflow: hidden;
            }

            .progress-fill {
                height: 100%;
                background: linear-gradient(90deg, var(--primary), #ff6b35);
                border-radius: 3px;
                width: 0;
                animation: fill 4s ease-in-out infinite;
                box-shadow: 0 0 10px rgba(245, 166, 35, .5);
            }

            @keyframes fill {
                0% {
                    width: 5%;
                }

                50% {
                    width: 75%;
                }

                100% {
                    width: 5%;
                }
            }

            .eta {
                display: flex;
                justify-content: center;
                gap: 2rem;
                margin-bottom: 2rem;
                font-size: .85rem;
            }

            .eta-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: .25rem;
            }

            .eta-item .num {
                font-size: 1.5rem;
                font-weight: 900;
                color: var(--primary);
                font-variant-numeric: tabular-nums;
            }

            .eta-item .lbl {
                font-size: .7rem;
                color: var(--muted);
                text-transform: uppercase;
                letter-spacing: .1em;
            }

            .socials {
                display: flex;
                justify-content: center;
                gap: 1rem;
                margin-bottom: 1rem;
            }

            .socials a {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: var(--card);
                border: 1px solid var(--border);
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--muted);
                transition: all .2s;
            }

            .socials a:hover {
                border-color: var(--primary);
                color: var(--primary);
                transform: translateY(-2px);
            }

            .note {
                font-size: .75rem;
                color: var(--muted);
            }

            @media(max-width:640px) {
                .title {
                    font-size: 1.6rem;
                }

                .gear-stack {
                    width: 130px;
                    height: 130px;
                }

                .gear-1 {
                    font-size: 5rem;
                }
            }
        </style>
        <div class="bg-fx"></div>
        <a href="#" class="brand">
            <img class="app_logo" src="{{ config('app.logo') }}" alt="{{ config('app.name') }}"> {{ config('app.name') }}
        </a>

        <div class="card">
            <div class="gear-stack">
                <i class="fa-solid fa-gear gear gear-1"></i>
                <i class="fa-solid fa-gear gear gear-2"></i>
            </div>
            <div class="code">— Error 503 · Servicio no disponible —</div>
            <h1 class="title">Estamos puliendo <span>la consola</span></h1>
            <p class="desc">CubanPool está en mantenimiento programado para mejorar tu experiencia. Volveremos en
                breve con nuevas funciones y mejor rendimiento.</p>

            <div class="progress-card">
                <div class="progress-header">
                    <span class="progress-label"><i class="fa-solid fa-wrench"></i> Trabajando en ello...</span>
                    <span class="progress-pct" id="pct"> </span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill"></div>
                </div>
            </div>

            <div class="socials">
                @php
                    $youtube = "https://www.youtube.com/@".config('contact.youtube')."/";
                    $facebook = "https://www.facebook.com/@".config('contact.facebook')."/";
                @endphp
                <a href="https://www.instagram.com/{{config('contact.instagram')}}/" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
                <a href="{{$youtube}}" title="Youtube"><i class="fa-brands fa-youtube"></i></a>
                <a href="{{$facebook}}" title="Facebook"><i class="fa-brands fa-facebook"></i></a>
                <a href="mailto:{{ config('contact.email') }}" title="Email"><i class="fa-solid fa-envelope"></i></a>
            </div>
            <p class="note">Síguenos para actualizaciones en tiempo real</p>
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
    @endif

    <div class="window-loader" id="wloader">
        <div>
            <i class="fa fa-spinner fa-spin"></i>
            <span>Cargando...</span>
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', function() {
            document.getElementById('wloader').style.display = 'none';
        })
    </script>

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
</body>

</html>
