@extends('layouts.app')

@section('title', 'Login Page')

@section('styles')
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />
@endsection

@section('content')
<div class="authentication-wrapper authentication-cover">
    <!-- Logo -->
    <a href="{{ url('/') }}" class="app-brand auth-cover-brand">
        <span class="app-brand-logo demo">
            <span class="text-primary">
                <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z" fill="currentColor" />
                    <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z" fill="#161616" />
                    <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z" fill="#161616" />
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z" fill="currentColor" />
                </svg>
            </span>
        </span>
        <span class="app-brand-text demo text-heading fw-bold">Vuexy</span>
    </a>
    <!-- /Logo -->

    <div class="authentication-inner row m-0">
        <!-- Left side illustration -->
        <div class="d-none d-xl-flex col-xl-8 p-0">
            <div class="auth-cover-bg d-flex justify-content-center align-items-center">
                <img src="{{ asset('assets/img/illustrations/auth-login-illustration-light.png') }}" alt="auth-login-cover" class="my-5 auth-illustration" />
                <img src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}" alt="auth-login-cover" class="platform-bg" />
            </div>
        </div>

        <!-- Login form -->
        <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
            <div class="w-px-400 mx-auto mt-12 pt-5">
                <h4 class="mb-1">Welcome to Vuexy! 👋</h4>
                <p class="mb-6">Please sign-in to your account and start the adventure</p>

                <form id="formAuthentication" class="mb-6" action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-6 form-control-validation">
                        <label for="email" class="form-label">Email or Username</label>
                        <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Enter your email or username" value="{{ old('email') }}" autofocus />
                        @error('email')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="mb-6 form-password-toggle form-control-validation">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-group input-group-merge">
                            <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                            <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="my-8 d-flex justify-content-between">
                        <div class="form-check mb-0 ms-2">
                            <input class="form-check-input" type="checkbox" id="remember-me" name="remember" {{ old('remember') ? 'checked' : '' }} />
                            <label class="form-check-label" for="remember-me"> Remember Me </label>
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"><p class="mb-0">Forgot Password?</p></a>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary d-grid w-100">Sign in</button>
                </form>

                <p class="text-center mb-4">
                    <span>New on our platform?</span>
                    <a href="{{ route('register') }}"><span>Create an account</span></a>
                </p>

                <div class="divider my-6">
                    <div class="divider-text">or</div>
                </div>

                <div class="d-flex justify-content-center">
                    <!-- Social logins -->
                    <a href="{{ route('socialite.filament.admin.oauth.redirect', 'google') }}" class="btn btn-icon rounded-circle btn-text-google-plus me-1_5">
                        <i class="icon-base ti tabler-brand-google-filled icon-20px"></i>
                    </a>

                    <a href="{{ route('socialite.filament.admin.oauth.redirect', 'facebook') }}" class="btn btn-icon rounded-circle btn-text-facebook me-1_5">
                        <i class="icon-base ti tabler-brand-facebook-filled icon-20px"></i>
                    </a>

                    <a href="{{ route('socialite.filament.admin.oauth.redirect', 'github') }}" class="btn btn-icon rounded-circle btn-text-github me-1_5">
                        <i class="icon-base ti tabler-brand-github-filled icon-20px"></i>
                    </a>

                    <a href="{{ route('socialite.filament.admin.oauth.redirect', 'twitter') }}" class="btn btn-icon rounded-circle btn-text-twitter">
                        <i class="icon-base ti tabler-brand-twitter-filled icon-20px"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
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
@endsection
