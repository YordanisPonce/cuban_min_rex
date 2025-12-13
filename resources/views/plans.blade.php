@extends('layouts.app')

@section('title', 'Página de Planes')

@push('styles')
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page.css') }}" />
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page-payment.css') }}" />
@endpush

@section('content')
    {{-- =========================
       PLANES
    ========================== --}}
    <section id="home-pricing" class="section-py bg-body landing-pricing mt-10">
        <div class="container mt-5">
            <div class="text-center mb-3">
                <span class="badge bg-label-primary">Planes de suscripción</span>
            </div>
            <h2 class="text-center fw-bold mb-2">Elige tu plan musical</h2>
            <p class="text-center text-body-secondary mb-6">
                Disfruta sin límites con beneficios a tu medida.
            </p>

            <div class="pricing-table">
                @foreach ($plans as $plan)
                    @include('partials.plans-card', ['plan'=>$plan])
                @endforeach
            </div>
        </div>
    </section>
@endsection
