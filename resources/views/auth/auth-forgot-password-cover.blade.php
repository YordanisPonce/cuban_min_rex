<!doctype html>
<html lang="en" class="layout-navbar-fixed layout-wide" dir="ltr" data-skin="default" data-bs-theme="dark"
    data-assets-path="{{ asset('assets/') }}" data-template="front-pages">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <title>@yield('title', 'Recuperar contraseña - ' . config('app.name'))</title>

    <meta name="description" content="Reset your Cuban_Mix_Rex password." />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ config('app.logo') }}" />

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
