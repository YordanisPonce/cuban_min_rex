<!doctype html>
<html lang="en" class="layout-wide customizer-hide" dir="ltr" data-bs-theme="dark">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Verifica tu correo | {{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />
</head>

<body style="background-color: #000 !important">
<div class="authentication-wrapper authentication-cover">
    <!-- Logo -->
    <a href="{{ url('/') }}" class="app-brand auth-cover-brand">
        <span class="app-brand-logo demo text-primary">
            <!-- Aquí tu logo -->
            <img src="{{ asset('assets/img/favicon/icon.PNG') }}" alt="{{ config('app.name') }}"
                                style="width: 50px; height: 50px; object-fit: contain; border-radius: 50%">
        </span>
        <span class="app-brand-text demo text-heading fw-bold">{{ config('app.name') }}</span>
    </a>
    <!-- /Logo -->

    <div class="authentication-inner row m-0">
        <!-- Imagen izquierda -->
        <div class="d-none d-xl-flex col-xl-8 p-0">
            <div class="auth-cover-bg d-flex justify-content-center align-items-center">
                <img src="{{ asset('assets/img/illustrations/auth-verify-email-illustration-light.png') }}"
                     alt="Verificar correo" class="my-5 auth-illustration" />
                <img src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}"
                     alt="Background shape" class="platform-bg" />
            </div>
        </div>

        <!-- Texto principal -->
        <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-6 p-sm-12" style="background-color: #000 !important">
            <div class="w-px-400 mx-auto mt-5">
                <h4 class="mb-1">Verifica tu correo ✉️</h4>
                <p class="text-start mb-0">
                    Hemos enviado un enlace de activación a tu correo:
                    <span class="fw-medium">{{ Auth::user()->email }}</span>.
                    Por favor revisa tu bandeja y sigue el enlace para continuar.
                </p>

                <!-- Botón para saltar -->
                <a class="btn btn-primary w-100 my-6" href="{{ route('dashboard') }}">
                    Saltar por ahora
                </a>

                <p class="text-center mb-0">
                    ¿No recibiste el correo?
                    <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">Reenviar</button>
                    </form>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
