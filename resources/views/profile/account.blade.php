@extends('layouts.app')
@php
    $success = session('success');
    $error = session('error');
@endphp

@section('title', 'Perfil de Usuario')

@push('styles')
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page.css') }}" />
@endpush

@section('content')
    <!-- Content wrapper -->
    <div class="content-wrapper pt-10 bg-body">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y mt-10">
            <div class="row">
                <div class="col-md-12">
                    <div class="nav-align-top">
                        <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-md-0 gap-2">
                            <li class="nav-item">
                                <a class="nav-link active" href="{{ route('profile.edit') }}"><i
                                        class="icon-base ti tabler-users icon-sm me-1_5"></i> Información de Usuario</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('profile.billing') }}"><i
                                        class="icon-base ti tabler-bookmark icon-sm me-1_5"></i> Información de
                                    Facturación</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card mb-6">
                        <!-- Account -->
                        <div class="card-body pt-4">
                            <form id="formUserSettings" method="POST" action="{{ route('profile.update') }}">
                                @csrf
                                <div class="row gy-4 gx-6 mb-6">
                                    <div class="col-md-6 form-control-validation">
                                        <label for="firstName" class="form-label">Nombre</label>
                                        <input class="form-control" type="text" id="name" name="name"
                                            value="{{ Auth::user()->name }}" autofocus />
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">E-mail</label>
                                        <input class="form-control" type="text" id="email" name="email"
                                            value="{{ Auth::user()->email }}" placeholder="john.doe@example.com" />
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary me-3">Guardar Cambios</button>
                                    <button type="reset" class="btn btn-label-secondary">Cancelar</button>
                                </div>
                            </form>
                        </div>
                        <!-- /Account -->
                    </div>
                    <!-- Change Password -->
                    <div class="card mb-6">
                        <h5 class="card-header">Cambiar Contraseña</h5>
                        <div class="card-body pt-1">
                            <form id="formAccountSettings" method="POST" action="{{ route('profile.changePassword') }}">
                                @csrf
                                <div class="row mb-sm-6 mb-2">
                                    <div class="col-md-6 form-password-toggle form-control-validation">
                                        <label class="form-label" for="currentPassword">Contraseña Actual</label>
                                        <div>
                                            <input class="form-control" type="password" name="currentPassword"
                                                id="currentPassword"
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                required />
                                            <!-- <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off icon-xs"></i></span> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="row gy-sm-6 gy-2 mb-sm-0 mb-2">
                                    <div class="mb-6 col-md-6 form-password-toggle form-control-validation">
                                        <label class="form-label" for="newPassword">Nueva Contraseña</label>
                                        <div>
                                            <input class="form-control" type="password" id="newPassword" name="newPassword"
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                required minlength="8" />
                                        </div>
                                    </div>

                                    <div class="mb-6 col-md-6 form-password-toggle form-control-validation">
                                        <label class="form-label" for="confirmPassword">Confirmar Nueva Contraseña</label>
                                        <div>
                                            <input class="form-control" type="password" name="confirmPassword"
                                                id="confirmPassword"
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                required minlength="8" />
                                        </div>
                                    </div>
                                </div>
                                <h6 class="text-body">Requisitos de Contraseña:</h6>
                                <ul class="ps-4 mb-0">
                                    <li class="mb-4">Mínimo 8 carácteres de longitud</li>
                                </ul>
                                <div class="mt-6">
                                    <button type="submit" class="btn btn-primary me-3">Cambiar Contraseña</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!--/ Change Password -->
                    <div class="card">
                        <h5 class="card-header">Eliminar Cuenta</h5>
                        <div class="card-body">
                            <div class="mb-6 col-12">
                                <div class="alert alert-warning">
                                    <h5 class="alert-heading mb-1">¿Estás seguro que quieres eliminar tu cuenta?</h5>
                                    <p class="mb-0">Una vez que elimines la cuenta, no podras recuperarla.</p>
                                </div>
                            </div>
                            <a id="deactivate-account" onClick="mostrarAdvertencia()"
                                class="btn btn-danger text-white">Eliminar Cuenta</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- / Content -->
    </div>
@endsection

@push('scripts')
    <script>
        function mostrarAdvertencia(e) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/profile/delete';
                }
            });
        }

        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                document.querySelector('#loader').style.display = 'flex';
            });
        });
    </script>
    @isset($error)
        <script>
            Swal.fire({
                title: 'Error al enviar el formulario',
                text: '{{ $error }}',
                icon: 'error'
            });
        </script>
    @endisset
    @isset($success)
        <script>
            Swal.fire({
                title: 'Completado',
                text: '{{ $success }}',
                icon: 'success'
            });
        </script>
    @endisset
@endpush
