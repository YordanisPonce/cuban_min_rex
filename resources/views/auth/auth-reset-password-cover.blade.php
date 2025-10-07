<!DOCTYPE html>
<html lang="en" class="layout-wide customizer-hide" dir="ltr" data-skin="default" data-bs-theme="dark">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>@yield('title', 'Cuban_Min_Rex - Reset Pass')</title>

    <meta name="description" content="{{ config('app.name') }} - Panel de administración" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

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
