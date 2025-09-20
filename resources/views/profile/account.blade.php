@extends('layouts.app')

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
                            <a class="nav-link active" href="{{ route('profile.edit') }}"><i class="icon-base ti tabler-users icon-sm me-1_5"></i> Información de Usuario</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('profile.billing') }}"><i class="icon-base ti tabler-bookmark icon-sm me-1_5"></i> Información de Facturación</a>
                        </li>
                    </ul>
                </div>
                <div class="card mb-6">
                    <!-- Account -->
                    <div class="card-body pt-4">
                        <form id="formAccountSettings" method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            <div class="row gy-4 gx-6 mb-6">
                                <div class="col-md-6 form-control-validation">
                                    <label for="firstName" class="form-label">Nombre</label>
                                    <input class="form-control" type="text" id="name" name="name" value="{{ Auth::user()->name }}" autofocus />
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input class="form-control" type="text" id="email" name="email" value="{{ Auth::user()->email }}" placeholder="john.doe@example.com" />
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
                <div class="card">
                    <h5 class="card-header">Eliminar Cuenta</h5>
                    <div class="card-body">
                        <div class="mb-6 col-12">
                            <div class="alert alert-warning">
                                <h5 class="alert-heading mb-1">¿Estás seguro que quieres eliminar tu cuenta?</h5>
                                <p class="mb-0">Una vez que elimines la cuenta, no podras recuperarla.</p>
                            </div>
                        </div>
                        <a id="deactivate-account" onClick="mostrarAdvertencia()" class="btn btn-danger text-white">Eliminar Cuenta</a>
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
</script>
@endpush