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
                    @php
                        $isActive =
                            auth()->check() &&
                            auth()->user()->current_plan_id === $plan->id &&
                            auth()->user()->hasActivePlan();
                    @endphp
                    <div class="pricing-card">
                        <spam class="type">{{$plan->name}}</spam>
                        <div class="price" data-content="${{$plan->price}}"><span>$</span>{{$plan->price}}</div>
                        <h5 class="plan">plan</h5>
                        <div class="details mb-5">
                            <p>Duración: {{$plan->duration_months}} {{$plan->duration_months > 1 ? 'meses' : 'mes'}}</p>
                            <p>Descargas por archivo: {{$plan->downloads}}</p>
                            @if ($plan->features)
                                @foreach ($plan->features as $item)
                                    <p>{{ $item['value'] }}</p>
                                @endforeach
                            @endif
                        </div>
                        @if ($isActive)
                        <div class="buy-button active">
                            <h3 class="btn"><a style="color: gray">Ya lo tienes</a></h3>
                        </div>
                        @else
                        <div class="buy-button">
                            <h3 class="btn"><a href="{{ route('payment.form', $plan->id) }}">Adquirir</a></h3>
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
