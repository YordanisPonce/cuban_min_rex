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

            <div class="row gy-4">
                @foreach ($plans as $plan)
                    @php
                        $isActive =
                            auth()->check() &&
                            auth()->user()->current_plan_id === $plan->id &&
                            auth()->user()->hasActivePlan();
                    @endphp
                    <div class="col-xl-4 col-lg-6">
                        <div class="card h-100">
                            <div class="card-header text-center">
                                <img src="{{ asset('storage/' . $plan->image) }}" alt="{{ $plan->name }}" class="mb-4"
                                    style="width:64px;height:64px;object-fit:contain;">
                                <h4 class="mb-1">{{ $plan->name }}</h4>
                                <div class="d-flex align-items-center justify-content-center">
                                    <span class="h2 text-primary fw-extrabold mb-0">€{{ $plan->price_formatted }}</span>
                                    <sub class="h6 text-body-secondary mb-n1 ms-1">/mes</sub>
                                </div>
                            </div>

                            <div class="card-body d-flex flex-column">
                                @if ($plan->description)
                                    <ul class="list-unstyled small text-body mb-4">
                                        <li class="mb-2 d-flex">
                                            <i class="ti tabler-check me-2"></i> {{ $plan->description }}
                                        </li>
                                    </ul>
                                @endif

                                <div class="mt-auto">
                                    @auth
                                        @if ($isActive)
                                            <button class="btn btn-secondary w-100" disabled>Ya lo tienes</button>
                                        @else
                                            <a href="{{ route('payment.form', $plan->id) }}"
                                                class="btn btn-label-primary w-100">
                                                Adquirir plan
                                            </a>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">
                                            Inicia sesión para comprar
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection