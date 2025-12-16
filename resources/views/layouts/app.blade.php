<!doctype html>

<html lang="en" class="layout-navbar-fixed layout-wide" dir="ltr" data-skin="default" data-bs-theme="dark"
    data-assets-path="{{ asset('assets/') }}" data-template="front-pages">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <!-- <meta name="robots" content="noindex, nofollow" /> -->
    <title>@yield('title', config('app.name'))</title>

    <meta name="description" content="{{ config('app.name') }}" />
    <!-- Canonical SEO -->
    <meta name="keywords" content="{{ config('app.name') }}, Remixes, Dj, music" />
    <meta name="author" content="{{ config('app.name') }}">
    <meta property="og:title" content="{{ config('app.name') }}" />
    <meta property="og:type" content="product" />
    <meta property="og:image" content="{{ config('app.logo') }}" />
    <meta property="og:description" content="{{ config('app.name') }}" />
    <meta property="og:site_name" content="{{ config('app.name') }}" />
    <meta property="og:url" content="{{ config('app.url') }}">

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
    <link rel="icon" href="{{ asset('favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/" />
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&amp;ampdisplay=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/pickr/pickr-themes.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/demo.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/front-page.css') }}" />

    <!-- Vendors CSS -->

    <!-- endbuild -->

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/nouislider/nouislider.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/swiper/swiper.css') }}" />

    <!-- Page CSS -->

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/front-page-landing.css') }}" />

    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page-payment.css') }}" />

    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/pricing.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="{{ asset('assets/js/front-config.js') }}"></script>

    <style>
        :root {
            --bs-primary: #00FF9F !important;
            /* Color de Bottones */
            /*--bs-paper-bg: #00C8FF !important; /* Color de las Cards */

            --download-button: #FF2EC4 !important;
            --play-button: #00C8FF !important;
        }

        /* Estilo para el loader */
        .loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
        }

        .carousel-item {
            max-height: 100vh;

            img {
                height: 100%;
            }
        }

        .dark-screen {
            width: 100%;
            height: 100vh;
            background-color: black;
            opacity: 0.6;
            position: absolute;
            top: 0;
            left: 0;
        }

        .categories h3 {
            cursor: pointer;
            color: currentColor;
        }

        .categories h3>small {
            color: #54b9c5;
            white-space: nowrap;
        }

        .categories .card {
            transition: all ease 1s;
            max-width: 100%;
        }

        .categories .card:hover {
            cursor: pointer;
        }

        .categories .card .dark-screen {
            height: 100% !important;
        }

        .categories .card:hover .dark-screen {
            display: block !important;
        }

        .swiper-button-prev:after,
        .swiper-button-next:after {
            font-size: 25px !important;
        }

        .card-relationed {
            overflow: hidden;
        }

        .card-relationed:hover .dark-screen {
            display: block !important;
        }

        .dropdown-item:hover {
            color: black;
        }

        .bg-body {
            background-color: transparent !important;
        }

        .play-button:hover, .btn-icon:hover{
            color: var(--bs-primary) !important;
        }

        body {
            /* The image used */
            background-image: linear-gradient(rgba(0, 0, 0, .7), rgba(0, 0, 0, .7)), url("{{ asset('assets/img/front-pages/backgrounds/remixes-bg.jpeg') }}");

            /* Create the parallax scrolling effect */
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        @media(max-width: 450px) {
            .carousel-item {
                height: 100vh;

                img {
                    height: 100%;
                }
            }

            body {
                background-image: linear-gradient(rgba(0, 0, 0, .5), rgba(0, 0, 0, .5)), url("{{ asset('assets/img/front-pages/backgrounds/remixes-bg-mobile.jpg') }}");
            }
        }

        @media(max-width: 767px) {
            .card-relationed img {
                max-height: 150px !important;
            }

            .navbar-djs .dropdown-menu{
                position: relative !important;
            }
        }

        @media(max-width: 992px) {
            .mt-xs-10 {
                margin-top: 3.6rem;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="d-flex flex-column bg-body " style="height: 100vh">
    @if (env('APP_ENV') == 'production')
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5J3LMKC" height="0" width="0"
                style="display: none; visibility: hidden"></iframe></noscript>
    @endif

    @include('partials.navbar')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    @if (env('APP_ENV') == 'production')
        <div class="buy-now">
            <a href="https://themeforest.net/item/vuexy-vuejs-html-laravel-admin-dashboard-template/23328599"
                target="_blank" class="btn btn-danger btn-buy-now">Buy Now</a>
        </div>
    @endif

    <div class="loader" id="loader">
        <div class="spinner-border" role="status">

        </div>
    </div>

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

    <!-- Main JS -->
    <script src="{{ asset('assets/js/front-main.js') }}"></script>

    <!-- Page JS -->
    {{--     <script src="{{ asset('assets/js/front-page-landing.js') }}"></script> --}}

    @stack('scripts')

</body>

</html>
