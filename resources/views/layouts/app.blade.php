<!doctype html>

<html lang="es" class="layout-navbar-fixed layout-wide" dir="ltr" data-skin="default" data-bs-theme="dark"
    data-assets-path="{{ asset('assets/') }}" data-template="front-pages">

<head>
    @php
        use Illuminate\Support\Str;

        $siteName = config('app.name');

        // Cada vista puede sobrescribir con @section('title', '...'), etc.
        $title       = trim($__env->yieldContent('title'));
        $pageTitle   = $title !== '' ? "$title | $siteName" : "$siteName - Música, Remixes y Descargas";
        $description = Str::limit(strip_tags(trim($__env->yieldContent('description')) ?: (string) config('app.description')), 160);
        $canonical   = trim($__env->yieldContent('canonical')) ?: url()->current();
        $ogType      = trim($__env->yieldContent('og_type')) ?: 'website';     
        $robots      = trim($__env->yieldContent('robots')) ?: 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';

        $toAbsolute = fn ($path) => Str::startsWith($path, ['http://', 'https://']) ? $path : asset(ltrim($path, '/'));
        $logoUrl    = $toAbsolute((string) config('app.logo'));
        $image      = $toAbsolute(trim($__env->yieldContent('image')) ?: (string) config('app.og_image', config('app.logo')));
        $imageAlt   = trim($__env->yieldContent('image_alt')) ?: $siteName;

        $twitter  = ltrim((string) config('app.twitter'), '@');
        $ogLocale = config('app.og_locale', 'es_ES');

        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type'       => 'WebSite',
                    '@id'         => url('/') . '#website',
                    'url'         => url('/'),
                    'name'        => $siteName,
                    'description' => config('app.description'),
                    'inLanguage'  => 'es',
                ],
                [
                    '@type'  => 'Organization', // cámbialo a MusicGroup o Person si aplica
                    '@id'    => url('/') . '#organization',
                    'name'   => $siteName,
                    'url'    => url('/'),
                    'logo'   => $logoUrl,
                    'sameAs' => array_values(array_filter((array) config('app.social', []))),
                ],
            ],
        ];
    @endphp

    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="author" content="{{ $siteName }}">
    <meta name="robots" content="{{ $robots }}">
    <link rel="canonical" href="{{ $canonical }}">

    <!-- Open Graph (Facebook, WhatsApp, Instagram) -->
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:locale" content="{{ $ogLocale }}">
    <meta property="og:image" content="{{ $image }}">
    <meta property="og:image:secure_url" content="{{ $image }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $imageAlt }}">

    <!-- Twitter / X Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ $image }}">
    <meta name="twitter:image:alt" content="{{ $imageAlt }}">
    @if($twitter)
        <meta name="twitter:site" content="{{ '@' . $twitter }}">
        <meta name="twitter:creator" content="{{ '@' . $twitter }}">
    @endif

    <!-- Iconos, manifest y PWA -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ config('app.logo_32x32') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ config('app.logo_180x180') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#000000">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="{{ $siteName }}">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- Datos estructurados -->
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
    @stack('structured-data')

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

    @php
      use App\Models\Background;

      $background = Background::where('active', true)->first();
      
      $backgroundUrl = asset('assets/img/bg.png');

      if ($background) {
        $backgroundUrl = $background ? $background->image()."" : null;
        $backgroundUrl = str_replace('\\','\/', $backgroundUrl);
      }
    @endphp

    <style>
        :root{
            --maxw: 100%;
            --gutter: 10%;
        }
        
        body {
            background: url('{{ $backgroundUrl }}') no-repeat center center fixed;
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

        .hero {
            position: relative;
            overflow: hidden;
            padding: 0;
            border-bottom: 1px solid var(--border);
            background: var(--bg);
            min-height: 80dvh;
        }

        .hero-track {
            display: grid;
            min-height: clamp(20rem, 48vh, 34rem);
        }

        .hero-slide {
            grid-area: 1 / 1;
            position: absolute;
            display: flex;
            align-items: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity .9s ease, visibility 0s linear .9s;
        }

        .hero-slide.is-active {
            opacity: 1;
            visibility: visible;
            z-index: 1;
            transition: opacity .9s ease, visibility 0s;
        }

        .hero-bg {
            position: absolute;
            inset: 0;
            overflow: hidden;
        }

        .hero-bg img, .hero-bg video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(1.07);
            transition: transform 8s ease-out;
            aspect-ratio: auto;
            position: relative;
            max-width: 100%;
        }

        .hero-slide.is-active .hero-bg img, .hero-bg video {
            transform: scale(1);
        }

        .hero-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(15, 13, 11, .97) 0%, rgba(15, 13, 11, .85) 38%, rgba(15, 13, 11, .35) 70%, rgba(15, 13, 11, .55) 100%);
        }

        .hero-inner {
            position: relative;
            z-index: 2;
            max-width: var(--maxw);
            margin: 0 auto;
            padding: 2.6rem var(--gutter) 3.6rem;
            width: 100%;
        }

        .hero-inner>* {
            opacity: 0;
            transform: translateY(14px);
            transition: opacity .6s ease, transform .6s ease;
        }

        .hero-slide.is-active .hero-inner>* {
            opacity: 1;
            transform: none;
        }

        .hero-slide.is-active .hero-inner> :nth-child(1) {
            transition-delay: .15s;
        }

        .hero-slide.is-active .hero-inner> :nth-child(2) {
            transition-delay: .27s;
        }

        .hero-slide.is-active .hero-inner> :nth-child(3) {
            transition-delay: .39s;
        }

        .hero-slide.is-active .hero-inner> :nth-child(4) {
            transition-delay: .5s;
        }

        .hero-kicker {
            font-size: .74rem;
            font-weight: 800;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: var(--fg);
            margin-bottom: .8rem;
        }

        .hero-kicker em {
            color: var(--primary);
            font-style: normal;
        }

        .hero-title {
            font-size: clamp(2rem, 5.2vw + .4rem, 3.2rem);
            font-weight: 900;
            font-style: italic;
            text-transform: uppercase;
            line-height: 1.02;
            letter-spacing: -.01em;
            visibility: visible !important
        }

        .hero-title span {
            display: block;
            background: linear-gradient(90deg, var(--primary), #ffcf6e);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .hero-text {
            margin: .9rem 0 1.4rem;
            font-size: .9rem;
            color: #cfc4b2;
            max-width: 26rem;
            line-height: 1.55;
        }

        .btn-cta {
            display: inline-flex;
            align-items: center;
            gap: .6rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #14100b;
            padding: .8rem 1.7rem;
            border-radius: 8px;
            font-weight: 800;
            font-size: .82rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            border: none;
            cursor: pointer;
            font-family: inherit;
            transition: all .2s;
            box-shadow: 0 8px 24px rgba(245, 166, 35, .3);
        }

        .btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(245, 166, 35, .45);
        }

        .hero-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 5;
            width: 2.6rem;
            height: 2.6rem;
            border-radius: 50%;
            border: 1px solid rgba(240, 232, 216, .25);
            background: rgba(15, 13, 11, .55);
            backdrop-filter: blur(6px);
            color: var(--fg);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .85rem;
            opacity: 0;
            transition: opacity .25s, background .2s, border-color .2s;
        }

        .hero-arrow.prev {
            left: max(.8rem, calc((100% - var(--maxw)) / 2 + .6rem));
        }

        .hero-arrow.next {
            right: max(.8rem, calc((100% - var(--maxw)) / 2 + .6rem));
        }

        .hero:hover .hero-arrow,
        .hero-arrow:focus-visible {
            opacity: 1;
        }

        .hero-arrow:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: #14100b;
        }

        .hero-nav {
            position: absolute;
            bottom: 1.1rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: .55rem;
            z-index: 5;
        }

        .hero-dot {
            position: relative;
            width: 8px;
            height: 8px;
            padding: 0;
            border: none;
            border-radius: 99px;
            background: rgba(240, 232, 216, .32);
            cursor: pointer;
            transition: all .25s;
        }

        .hero-dot::after {
            content: '';
            position: absolute;
            inset: -10px -4px;
        }

        .hero-dot.active {
            background: var(--primary);
            width: 22px;
        }

        .hero-pause {
            margin-left: .4rem;
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 50%;
            border: 1px solid rgba(240, 232, 216, .3);
            background: rgba(15, 13, 11, .45);
            color: var(--fg);
            font-size: .55rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-pause:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .hero-progress {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 3px;
            background: rgba(240, 232, 216, .1);
            z-index: 5;
        }

        .hero-progress span {
            display: block;
            height: 100%;
            background: var(--primary);
            transform-origin: left;
            transform: scaleX(0);
        }

        .hero-progress span.run {
            animation: heroProgress var(--hero-interval, 6s) linear forwards;
        }

        @keyframes heroProgress {
            to {
                transform: scaleX(1);
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .hero-slide,
            .hero-slide.is-active {
                transition: none;
            }

            .hero-bg img {
                transition: none;
                transform: none;
            }

            .hero-inner>* {
                transition: none;
                transform: none;
            }
        }
    </style>

    @stack('styles')
</head>

<body style="height: 100vh">
    @if (env('APP_ENV') == 'production')
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5J3LMKC" height="0" width="0"
                style="display: none; visibility: hidden"></iframe></noscript>
    @endif

    @if (session()->has(\App\Http\Controllers\ImpersonationController::SESSION_KEY) && auth()->check())
        <div style="position: fixed; top: 0; left: 0; right: 0; z-index: 99999; background: #f59e0b; color: #000; padding: 6px 12px; display: flex; justify-content: center; align-items: center; gap: 12px; font-size: 14px;">
            <span>Estás viendo el sitio como <strong>{{ auth()->user()->name }}</strong></span>
            <form method="POST" action="{{ route('impersonate.leave') }}" style="margin: 0">
                @csrf
                <button type="submit" style="background: #000; color: #fff; border: 0; border-radius: 4px; padding: 3px 10px; cursor: pointer">Salir de la impersonación</button>
            </form>
        </div>
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
    
    @stack('scripts')
</body>

</html>
