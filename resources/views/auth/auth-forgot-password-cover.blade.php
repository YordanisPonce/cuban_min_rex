<!doctype html>
<html lang="es" class="layout-navbar-fixed layout-wide" dir="ltr" data-skin="default" data-bs-theme="dark"
    data-assets-path="{{ asset('assets/') }}" data-template="front-pages">
<head>
    @php
        use Illuminate\Support\Str;

        $siteName = config('app.name');

        // Cada vista puede sobrescribir con @section('title', '...'), etc.
        $title       = trim($__env->yieldContent('title'));
        $pageTitle   = $title !== '' ? "$title | $siteName" : "Recuperar Contraseña | $siteName";
        $description = Str::limit(strip_tags(trim($__env->yieldContent('description')) ?: (string) config('app.description')), 160);
        $canonical   = trim($__env->yieldContent('canonical')) ?: url()->current();
        $ogType      = trim($__env->yieldContent('og_type')) ?: 'website';     
        $robots      = trim($__env->yieldContent('robots')) ?: 'noindex, nofollow';

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
    <link rel="icon" href="{{ config('app.logo') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#000000">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="{{ $siteName }}">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/pickr/pickr-themes.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/front-page.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/nouislider/nouislider.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/swiper/swiper.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/front-page-landing.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>

    <!-- Template customizer -->
    <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>

    <!-- Config: Mandatory theme config file -->
    <script src="{{ asset('assets/js/front-config.js') }}"></script>

    @stack('styles')
    <style>
        :root {
            --bs-primary: var(--primray) !important;
            /* Color de Bottones */
            /*--bs-paper-bg: #00C8FF !important; /* Color de las Cards */

            --download-button: #FF2EC4 !important;
            --play-button: #00C8FF !important;
        }

        body {
            background: url('{{ asset('assets/img/bg.png') }}') no-repeat center center fixed;
            background-color: rgba(20, 18, 16, .65);
            background-blend-mode: overlay;
            background-size: cover;
        }

        .btn-primary:hover {
            background-color: var(--primary) !important;
            color: #fff !important;
        }

        h1.text-center{
            display: flex;
            gap: 20px;
            align-items: center;
            justify-content: center;

            & img{
                width: 50px;
                height: 50px;
            }
        }
    </style>
</head>

<body>
    <!-- Content -->
    <div class="authentication-wrapper authentication-cover">

        <div class="authentication-inner row m-0 flex justify-content-center">
            <!-- Forgot Password -->
            <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg"
                style="background-color: rgba(0,0,0,.5)!important; border-radius: 18px; height: auto; padding: 2rem;">
                <div class="w-px-400 mx-auto">
                    <h1 class="text-center"><img class="app_logo" src="{{ config('app.logo') }}" alt="{{ config('app.name') }}">
                        {{ config('app.name') }}</h1>
                    <h4 class="mb-1">Forgot Password? 🔒</h4>
                    <p class="mb-6">Enter your email and we'll send you instructions to reset your password</p>

                    @if (session('status'))
                        <div class="alert alert-success mb-4" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form id="formAuthentication" class="mb-6" action="{{ route('password.email') }}" method="POST">
                        @csrf
                        <div class="mb-6 form-control-validation">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" placeholder="Enter your email"
                                value="{{ old('email') }}" autofocus />
                            @error('email')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary d-grid w-100 text-black">Send Reset Link</button>
                    </form>

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="d-flex justify-content-center">
                            <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl me-1_5"></i>
                            Back to login
                        </a>
                    </div>
                </div>
            </div>
            <!-- /Forgot Password -->
        </div>
    </div>
    <!-- / Content -->

    <!-- Scripts -->
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@algolia/autocomplete-js.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/pickr/pickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/nouislider/nouislider.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/swiper/swiper.js') }}"></script>
    <script src="{{ asset('assets/js/front-main.js') }}"></script>
    <script src="{{ asset('assets/js/front-page-landing.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('assets/js/pages-auth.js') }}"></script>

    @stack('scripts')
</body>

</html>
