<!DOCTYPE html>
<html lang="es" class="layout-navbar-fixed layout-wide" dir="ltr" data-skin="default" data-bs-theme="dark"
    data-assets-path="{{ asset('assets/') }}" data-template="front-pages">
    <head>
    @php
        use Illuminate\Support\Str;

        $siteName = config('app.name');

        // Cada vista puede sobrescribir con @section('title', '...'), etc.
        $title       = trim($__env->yieldContent('title'));
        $pageTitle   = $title !== '' ? "$title | $siteName" : "Restablecer Contraseña | $siteName";
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
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/pickr/pickr-themes.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- Vendor -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/form-validation.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>

    <!-- Template customizer -->
    <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>

    <!-- Config -->
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <!-- Scripts de Filament (necesarios para funcionalidad completa) -->
    @filamentStyles
    <style>
      :root{
          --bs-primary: #00FF9F !important; /* Color de Bottones */
          /*--bs-paper-bg: #00C8FF !important; /* Color de las Cards */

          --download-button: #FF2EC4 !important;
          --play-button: #00C8FF !important;
      }
    </style>
</head>

<body style="background-color: #000 !important">
    <!-- Content -->
    <div class="authentication-wrapper authentication-cover">
        <!-- Logo -->
        <a href="{{ url('/') }}" class="app-brand auth-cover-brand">
            <span class="app-brand-logo demo">
                <span class="text-primary">
                    <img src="{{ config('app.logo') }}" alt="{{ config('app.name') }}"
                                style="width: 50px; height: 50px; object-fit: contain; border-radius: 50%">
                </span>
            </span>
            <span class="app-brand-text demo text-heading fw-bold">Cuban Mex Rex</span>
        </a>
        <!-- /Logo -->

        <div class="authentication-inner row m-0">
            <!-- /Left Text -->
            <div class="d-none d-xl-flex col-xl-8 p-0">
                <div class="auth-cover-bg d-flex justify-content-center align-items-center">
                    <img src="{{ asset('assets/img/illustrations/auth-reset-password-illustration-light.png') }}" alt="auth-reset-password-cover" class="my-5 auth-illustration" data-app-light-img="illustrations/auth-reset-password-illustration-light.png" data-app-dark-img="illustrations/auth-reset-password-illustration-dark.png" />
                    <img src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}" alt="auth-reset-password-cover" class="platform-bg" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png" />
                </div>
            </div>
            <!-- /Left Text -->

            <!-- Reset Password -->
            <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-6 p-sm-12" style="background-color: #000 !important">
                <div class="w-px-400 mx-auto mt-12 pt-5">
                    <h4 class="mb-1">Reset Password 🔒</h4>
                    <p class="mb-6"><span class="fw-medium">Your new password must be different from previously used passwords</span></p>

                    <!-- Formulario de restablecimiento de contraseña -->
                    <form method="POST" action="{{ route('password.update') }}" class="mb-6">
                        @csrf

                        <!-- Token oculto -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">



                        <div class="mb-6 form-password-toggle form-control-validation">
                            <label class="form-label" for="password">New Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required autocomplete="new-password" />
                                <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-6 form-password-toggle form-control-validation">
                            <label class="form-label" for="password_confirmation">Confirm Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required autocomplete="new-password" />
                                <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary d-grid w-100 mb-6">Set new password</button>

                        <div class="text-center">
                            <a href="{{ route('login') }}" class="d-flex justify-content-center">
                                <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl me-1_5"></i>
                                Back to login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /Reset Password -->
        </div>
    </div>

    <!-- Core JS -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('assets/js/pages-auth.js') }}"></script>

    <!-- Scripts de Filament -->
    @filamentScripts

    <!-- Script para alternar visibilidad de contraseña -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            const togglePassword = document.querySelectorAll('.cursor-pointer');
            togglePassword.forEach(function(button) {
                button.addEventListener('click', function() {
                    const input = this.closest('.input-group').querySelector('input');
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);

                    // Toggle icon
                    this.querySelector('i').classList.toggle('tabler-eye');
                    this.querySelector('i').classList.toggle('tabler-eye-off');
                });
            });
        });
    </script>
</body>
</html>
