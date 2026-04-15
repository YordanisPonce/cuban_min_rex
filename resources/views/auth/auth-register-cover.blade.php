<!doctype html>
<html lang="en" class="layout-navbar-fixed layout-wide" dir="ltr" data-skin="default" data-bs-theme="dark"
    data-assets-path="{{ asset('assets/') }}" data-template="front-pages">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <title>@yield('title', 'Registrarse - '.config('app.name'))</title>

    <meta name="description" content="Cuban Mix Rex - Plataforma de gestión de contenido" />
    <!-- Canonical SEO -->
    <meta name="keywords" content="Cuban Mix Rex, gestión de contenido, dashboard" />
    <meta property="og:title" content="Cuban Mix Rex" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url('/') }}" />
    <meta property="og:image" content="{{ asset('assets/img/hero-image.png') }}" />
    <meta property="og:description" content="Cuban Mix Rex - Plataforma de gestión de contenido" />
    <link rel="canonical" href="{{ url('/') }}" />

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
    <link rel="icon" type="image/x-icon" href="{{ config('app.logo') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/" />
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Vendors CSS -->
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

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/js/front-config.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}" />

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
    @if (env('APP_ENV') == 'production')
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5J3LMKC" height="0" width="0"
                style="display: none; visibility: hidden"></iframe></noscript>
    @endif

    <div class="authentication-wrapper authentication-cover">

        <div class="authentication-inner row m-0 flex justify-content-center">

            <!-- Register form -->
            <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg m-5"
                style="background-color: rgba(0,0,0,.5)!important; border-radius: 18px">
                <div class="w-px-400 mx-auto mt-12">
                    <h1 class="text-center"><img class="app_logo" src="{{ config('app.logo') }}" alt="{{ config('app.name') }}"> {{ config('app.name') }}</h1>
                    <h4 class="mb-1">¡La aventura comienza aquí! 🚀</h4>
                    <p class="mb-6">Haga que la gestión de su aplicación sea fácil y divertida.</p>

                    <form id="formAuthentication" class="mb-6" action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="mb-6 form-control-validation">
                            <label for="name" class="form-label">Nombre de usuario</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" name="name" placeholder="Ingrese su nombre de usuario"
                                value="{{ old('name') }}" autofocus />
                            @error('name')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="mb-6 form-control-validation">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" placeholder="Ingrese su email"
                                value="{{ old('email') }}" />
                            @error('email')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="mb-6 form-password-toggle form-control-validation">
                            <label class="form-label" for="password">Contraseña</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password" />
                                <span class="input-group-text cursor-pointer"><i
                                        class="icon-base ti tabler-eye-off"></i></span>
                            </div>
                            @error('password')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="mb-6 form-password-toggle form-control-validation">
                            <label class="form-label" for="password_confirmation">Confirmar Contraseña</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password_confirmation" class="form-control"
                                    name="password_confirmation"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password_confirmation" />
                                <span class="input-group-text cursor-pointer"><i
                                        class="icon-base ti tabler-eye-off"></i></span>
                            </div>
                        </div>
                        <div class="mb-6 mt-8">
                            <div class="form-check mb-8 ms-2 form-control-validation">
                                <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox"
                                    id="terms-conditions" name="terms" {{ old('terms') ? 'checked' : '' }} />
                                <label class="form-check-label" for="terms-conditions">
                                    Acepto los
                                    <a href="javascript:void(0);">términos y condiciones</a>
                                </label>
                                @error('terms')
                                    <span class="invalid-feedback"
                                        role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary d-grid w-100 text-black">Registrarse</button>
                    </form>

                    <p class="text-center mb-4">
                        <span>¿Ya tienes una cuenta?</span>
                        <a href="{{ route('login') }}"><span class="text-primary">Inicia sesión aquí</span></a>
                    </p>

                    {{-- <div class="divider my-6">
                        <div class="divider-text">o</div>
                    </div>

                    <div class="d-flex justify-content-center"> --}}
                    <!-- Social logins -->
                    {{-- <a href="{{ route('#') }}" class="btn btn-icon rounded-circle btn-text-google-plus me-1_5">
                            <i class="icon-base ti tabler-brand-google-filled icon-20px"></i>
                        </a> --}}

                    {{-- <a href="{{ route('#', 'facebook') }}" class="btn btn-icon rounded-circle btn-text-facebook me-1_5">
                            <i class="icon-base ti tabler-brand-facebook-filled icon-20px"></i>
                        </a>

                        <a href="{{ route('#', 'github') }}" class="btn btn-icon rounded-circle btn-text-github me-1_5">
                            <i class="icon-base ti tabler-brand-github-filled icon-20px"></i>
                        </a>

                        <a href="{{ route('#', 'twitter') }}" class="btn btn-icon rounded-circle btn-text-twitter">
                            <i class="icon-base ti tabler-brand-twitter-filled icon-20px"></i>
                        </a> --}}
                </div>
            </div>
        </div>
    </div>
    </div>

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@algolia/autocomplete-js.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/pickr/pickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/nouislider/nouislider.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/swiper/swiper.js') }}"></script>
    <script src="{{ asset('assets/js/front-main.js') }}"></script>
    <script src="{{ asset('assets/js/front-page-landing.js') }}"></script>
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
