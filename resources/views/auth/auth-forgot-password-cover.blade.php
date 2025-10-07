<!doctype html>
<html lang="en" class="layout-navbar-fixed layout-wide" dir="ltr" data-skin="default" data-bs-theme="dark" data-assets-path="{{ asset('assets/') }}" data-template="front-pages">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <title>Forgot Password - Cuban_Mix_Rex</title>

    <meta name="description" content="Reset your Cuban_Mix_Rex password." />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap" rel="stylesheet" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/pickr/pickr-themes.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/front-page.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/nouislider/nouislider.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/swiper/swiper.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/front-page-landing.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>

    <!-- Template customizer -->
    <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>

    <!-- Config: Mandatory theme config file -->
    <script src="{{ asset('assets/js/front-config.js') }}"></script>

    @stack('styles')
  </head>

  <body style="background-color: #000 !important">
    <!-- Content -->
    <div class="authentication-wrapper authentication-cover">
      <!-- Logo -->
      <a href="{{ url('/') }}" class="app-brand auth-cover-brand">
        <span class="app-brand-logo demo">
          <span class="text-primary">
            <img src="{{ asset('assets/img/favicon/icon.jpeg') }}" alt="Cuban Mix Rex Logo"
                                style="width: 50px; height: 50px; object-fit: contain; border-radius: 50%">
          </span>
        </span>
        <span class="app-brand-text demo text-heading fw-bold">Cuban Mix Rex</span>
      </a>
      <!-- /Logo -->

      <div class="authentication-inner row m-0">
        <!-- Left side illustration -->
        <div class="d-none d-xl-flex col-xl-8 p-0">
          <div class="auth-cover-bg d-flex justify-content-center align-items-center">
            <img src="{{ asset('assets/img/illustrations/auth-forgot-password-illustration-light.png') }}" alt="auth-forgot-password-cover" class="my-5 auth-illustration" data-light-img="{{ asset('assets/img/illustrations/auth-forgot-password-illustration-light.png') }}" data-dark-img="{{ asset('assets/img/illustrations/auth-forgot-password-illustration-dark.png') }}" />
            <img src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}" alt="auth-forgot-password-cover" class="platform-bg" data-light-img="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}" data-dark-img="{{ asset('assets/img/illustrations/bg-shape-image-dark.png') }}" />
          </div>
        </div>
        <!-- /Left Text -->

        <!-- Forgot Password -->
        <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-sm-12 p-6"  style="background-color: #000 !important">
          <div class="w-px-400 mx-auto mt-12 mt-5">
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
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Enter your email" value="{{ old('email') }}" autofocus />
                @error('email')
                  <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>
              <button type="submit" class="btn btn-primary d-grid w-100">Send Reset Link</button>
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
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>
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
