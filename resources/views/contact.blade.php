@extends('layouts.app')
@php
    $success = session('success');
    $error = session('error');
@endphp

@section('title', 'Página de Contacto')

@push('styles')
<link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page-payment.css') }}" />
@endpush

@section('content')

<!-- Contact Us: Start -->
<section id="landingContact" class="section-py bg-body landing-contact">
    <div class="container" style="margin-top: 60px;">
        <div class="text-center mb-4">
            <span class="badge bg-label-primary">🎶 Contáctanos</span>
        </div>
        <h4 class="text-center mb-1">
            <span class="position-relative fw-extrabold z-1">¿Necesitas ayuda con tu música?
                <img src="https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/front-pages/icons/section-title-icon.png" alt="laptop charging" class="section-title-img position-absolute object-fit-contain bottom-0 z-n1">
            </span>

        </h4>
        <p class="text-center mb-12 pb-md-4">Estamos aquí para resolver tus dudas sobre canciones, playlists, compras o licencias 🎧</p>
        <div class="row g-6">
            <div class="col-lg-5">
                <div class="contact-img-box position-relative border p-2 h-100">
                    <img src="https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/front-pages/icons/contact-border.png" alt="contact border" class="contact-border-img position-absolute d-none d-lg-block scaleX-n1-rtl">
                    <img src="{{ asset('assets/img/front-pages/landing-page/contact-form.jpeg') }}" alt="contact customer service" class="contact-img w-100 scaleX-n1-rtl">
                    <div class="p-4 pb-2">
                        <div class="row g-4">
                            <div class="col-md-6 col-lg-12 col-xl-6">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-label-primary rounded p-1_5 me-3"><i class="icon-base ti tabler-mail icon-lg"></i></div>
                                    <div>
                                        <p class="mb-0">Correo</p>
                                        <h6 class="mb-0"><a href="mailto:example@gmail.com" class="text-heading">soporte@cubanmix.com</a></h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-12 col-xl-6">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-label-success rounded p-1_5 me-3"><i class="icon-base ti tabler-phone-call icon-lg"></i></div>
                                    <div>
                                        <p class="mb-0">Teléfono</p>
                                        <h6 class="mb-0"><a href="tel:+1234-568-963" class="text-heading">+1234 568 963</a></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-body">
                        <h4 class="mb-2">🎵 Escríbenos un mensaje</h4>
                        <p class="mb-6">
                            ¿Problemas con tu cuenta, compras de canciones o playlists personalizadas?<br class="d-none d-lg-block">
                            Déjanos tu mensaje y nuestro equipo musical te ayudará.
                        </p>
                        <form action="{{ route('contact.form') }}" method="POST">
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label" for="contact-form-fullname">Nombre</label>
                                    <input type="text" class="form-control" id="contact-form-fullname" name="fullname" placeholder="john" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="contact-form-email">Correo</label>
                                    <input type="text" id="contact-form-email" class="form-control" name="email" placeholder="johndoe@gmail.com" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="contact-form-message">Mensaje</label>
                                    <textarea id="contact-form-message" class="form-control" rows="7" name="message" placeholder="Write a message" required></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">📩 Enviar consulta</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Contact Us: End -->
@endsection

@push('scripts')
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