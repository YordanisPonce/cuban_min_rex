@extends('layouts.app')

@section('title', 'Página de FAQ')
@section('robots', 'noindex, follow')

@push('styles')
<link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page-payment.css') }}" />
@endpush

@section('content')
<!-- FAQ: Start -->
<section id="musicFAQ" class="section-py bg-body">
    <div class="container" style="margin-top: 60px;">
        <div class="text-center mb-4">
            <span class="badge bg-label-primary">Soporte</span>
        </div>
        <h4 class="text-center mb-1">
            Preguntas frecuentes en nuestra
            <span class="position-relative fw-extrabold z-1">Tienda de Música
                <img src="https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/front-pages/icons/section-title-icon.png"
                    alt="music icon"
                    class="section-title-img position-absolute object-fit-contain bottom-0 z-n1">
            </span>
        </h4>
        <p class="text-center mb-12 pb-md-4">Aquí encontrarás respuestas a las dudas más comunes de nuestros clientes 🎶</p>

        <div class="row gy-12 align-items-center">
            <div class="col-lg-5">
                <div class="text-center">
                    <img src="{{ asset('assets/img/album/image duda.png') }}"
                        alt="music shop illustration"
                        class="faq-image" style="max-width: 300px;">
                </div>
            </div>

            <div class="col-lg-7">
                <div class="accordion" id="accordionMusicStore">

                    <!-- FAQ 1 -->
                    <div class="card accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#accordionOne" aria-expanded="true" aria-controls="accordionOne">
                                ¿Cómo puedo comprar una canción o álbum?
                            </button>
                        </h2>
                        <div id="accordionOne" class="accordion-collapse collapse show" data-bs-parent="#accordionMusicStore">
                            <div class="accordion-body">
                                Simplemente añade tus canciones o álbumes favoritos al carrito, procede al checkout y realiza el pago de forma segura.
                                Una vez confirmado, tendrás acceso inmediato a tu música en tu cuenta.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="card accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#accordionTwo" aria-expanded="false" aria-controls="accordionTwo">
                                ¿En qué formato recibiré la música?
                            </button>
                        </h2>
                        <div id="accordionTwo" class="accordion-collapse collapse" data-bs-parent="#accordionMusicStore">
                            <div class="accordion-body">
                                Ofrecemos descargas en formato MP3 (320kbps) y FLAC para máxima calidad. Podrás elegir tu preferencia antes de confirmar la compra.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="card accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#accordionThree" aria-expanded="false" aria-controls="accordionThree">
                                ¿Puedo reproducir mi música en diferentes dispositivos?
                            </button>
                        </h2>
                        <div id="accordionThree" class="accordion-collapse collapse" data-bs-parent="#accordionMusicStore">
                            <div class="accordion-body">
                                Sí, una vez comprada la música puedes descargarla y transferirla a tu PC, móvil, tablet o cualquier dispositivo compatible.
                                También tendrás acceso a nuestra aplicación para escuchar en streaming.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="card accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                            <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#accordionFour" aria-expanded="false" aria-controls="accordionFour">
                                ¿Qué métodos de pago aceptan?
                            </button>
                        </h2>
                        <div id="accordionFour" class="accordion-collapse collapse" data-bs-parent="#accordionMusicStore">
                            <div class="accordion-body">
                                Aceptamos tarjetas de crédito/débito, PayPal y transferencias. También puedes usar tarjetas de regalo digitales disponibles en nuestra tienda.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 5 -->
                    <div class="card accordion-item">
                        <h2 class="accordion-header" id="headingFive">
                            <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#accordionFive" aria-expanded="false" aria-controls="accordionFive">
                                ¿Puedo pedir vinilos o CDs físicos?
                            </button>
                        </h2>
                        <div id="accordionFive" class="accordion-collapse collapse" data-bs-parent="#accordionMusicStore">
                            <div class="accordion-body">
                                Sí 🎵, contamos con ediciones físicas limitadas de algunos álbumes en vinilo y CD.
                                Estos productos se envían directamente a tu domicilio con garantía de calidad.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section><!-- FAQ: End -->
@endsection