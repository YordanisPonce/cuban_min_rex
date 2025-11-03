@extends('layouts.app')

@section('title', 'Emisora - '.config('app.name'))

@push('styles')
<link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page-payment.css') }}" />
@endpush

@section('content')
    <section class="section-py">
        <div class="container">
            <div class="row align-items-center g-10 mt-5">
                <div class="col-lg-6">
                    <div class="ratio ratio-4x3 rounded-4 overflow-hidden border border-dark-subtle">
                        <img src="{{ asset('assets/img/front-pages/landing-page/radio.jpeg') }}" alt="Arte destacado"
                            class="w-100 h-100 object-fit-cover">
                        <div class="dark-screen" style="opacity: 0.5;"></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <span class="badge bg-label-primary mb-3">Radio</span>
                    <h1 class="display-6 fw-bold mb-2">Nuestra Emisora</h1>
                    <p class="text-body-secondary mb-4">
                        Escucha música a tu gusto directamente desde nuestra emisora.
                    </p>
                    <iframe src="https://public-player-widget.webradiosite.com/?cover=1&current_track=1&schedules=1&link=1&popup=1&share=1&embed=0&auto_play=1&source=10382&theme=dark&color=4&link_to=cubandjsproradio.com&identifier=CubanDjsPro%20Radio&info=https%3A%2F%2Fpublic-player-widget.webradiosite.com%2Fapp%2Fplayer%2Finfo%2F247079%3Fhash%3D7beeef43d3d82f9110c118b97c8e149829ddb4ad&locale=es-es" border="0" scrolling="no" frameborder="0" allow="autoplay; clipboard-write" allowtransparency="true" style="background-color: unset; width: 100%;" height="auto"></iframe>
                </div>
            </div>
        </div>
    </section>
@endsection