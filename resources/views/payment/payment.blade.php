@extends('layouts.app')

@section('title', 'Página de Inicio')

@push('styles')
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page.css') }}" />
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page-payment.css') }}" />
@endpush

@section('content')

    <!-- Sección de Checkout -->
    <section class="section-py bg-body first-section-pt">
        <div class="container">
            <div class="card px-3">
                <div class="row">
                    <!-- Detalles de Facturación -->
                    <div class="col-lg-7 card-body border-end p-md-8">
                        <h4 class="mb-2">Finalizar compra</h4>
                        <p class="mb-0">
                            Todos los planes incluyen más de 40 herramientas y funciones avanzadas para impulsar tu música. <br />
                            Escoge el plan que mejor se adapte a tus necesidades.
                        </p>
                        <h4 class="mb-6">Detalles de facturación</h4>

                        <form id="checkoutForm" method="POST" action="{{ route('payment.process') }}">
                            @csrf

                            <!-- Campos de facturación -->
                            <div class="row g-6">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre completo</label>
                                    <input type="text" name="name" class="form-control" placeholder="Juan Pérez" value="{{ Auth::user()->name }}" required />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Correo electrónico</label>
                                    <input type="email" name="email" class="form-control" placeholder="ejemplo@gmail.com" value="{{ Auth::user()->email }}" required />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Teléfono</label>
                                    <input type="tel" name="phone" class="form-control" placeholder="+34 600 123 456"  value="{{ Auth::user()->billing->phone }}" required/>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Dirección</label>
                                    <input type="text" name="address" class="form-control" placeholder="Calle Falsa 123"  value="{{ Auth::user()->billing->address }}" required />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Código postal</label>
                                    <input type="text" name="postal" class="form-control" placeholder="28001"  value="{{ Auth::user()->billing->postal }}" required />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">País</label>
                                    <select name="country" class="form-select" required>
                                        <option value="">Seleccionar</option>
                                        @php
                                            $countries = ['España', 'México', 'Argentina', 'Colombia'];
                                        @endphp
                                        @foreach ($countries as $country)
                                            <option value="{{ $country }}" {{ Auth::user()->billing->country == $country ? 'selected' : '' }}>
                                                {{ $country }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Hidden con el plan -->
                            <input type="hidden" name="plan_id" value="{{ $planId }}" />


                        </form>
                    </div>

                    <!-- Resumen del Pedido -->
                    <div class="col-lg-5 card-body p-md-8">
                        <h4 class="mb-2">Resumen del pedido</h4>
                        <p class="mb-8">
                            Gestiona y controla tus pedidos antes, <br />
                            durante y después de la compra.
                        </p>

                        <div class="bg-lighter p-6 rounded">
                            <p>{{$plan->description}}</p>
                            <div class="d-flex align-items-center mb-4">
                                @foreach($plans as $plan)
                                    @if($plan->id == $planId)
                                        <h1 class="text-heading mb-0">{{$plan->price}} €</h1>
                                        <sub class="h6 text-body mb-n3">/mes</sub>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-5">
                            <!-- <div class="d-flex justify-content-between align-items-center">
                                <p class="mb-0">Subtotal</p>
                                <h6 class="mb-0">85,99 €</h6>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <p class="mb-0">Impuestos</p>
                                <h6 class="mb-0">4,99 €</h6>
                            </div>
                            <hr />
                            <div class="d-flex justify-content-between align-items-center mt-4 pb-1">
                                <p class="mb-0">Total</p>
                                <h6 class="mb-0">90,98 €</h6>
                            </div> -->

                            <div class="d-grid mt-5">
                                <button id="checkoutBtn" class="btn btn-success" form="checkoutForm">
                                    <span class="me-2">Proceder con el pago</span>
                                    <i class="icon-base ti tabler-arrow-right scaleX-n1-rtl"></i>
                                </button>
                            </div>

                            <p class="mt-8">
                                Al continuar, aceptas nuestros Términos de Servicio y la Política de Privacidad.
                                Ten en cuenta que los pagos no son reembolsables.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ Sección de Checkout -->

    <!-- Modal de Planes -->
    {{-- <div class="modal fade" id="pricingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-simple modal-pricing">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>

                    <h4 class="text-center mb-2">Planes de Suscripción</h4>
                    <p class="text-center mb-0">
                        Todos los planes incluyen beneficios adaptados a ti. <br>
                        Escoge el que mejor se ajuste a tu música 🎶
                    </p>

                    <!-- Toggle Mensual / Anual -->
                    <div class="d-flex align-items-center justify-content-center flex-wrap gap-2 pt-12 pb-4">
                        <label class="switch switch-sm ms-sm-12 ps-sm-12 me-0">
                            <span class="switch-label fs-6 text-body">Mensual</span>
                            <input type="checkbox" class="switch-input price-duration-toggler" checked />
                            <span class="switch-toggle-slider">
                                <span class="switch-on"></span>
                                <span class="switch-off"></span>
                            </span>
                            <span class="switch-label fs-6 text-body">Anual</span>
                        </label>
                        <div class="mt-n5 ms-n10 ml-2 mb-12 d-none d-sm-flex align-items-center gap-1">
                            <i class="icon-base ti tabler-corner-left-down icon-lg text-body-secondary scaleX-n1-rtl"></i>
                            <span class="badge badge-sm bg-label-primary rounded-1 mb-2">Ahorra hasta 25%</span>
                        </div>
                    </div>

                    <!-- Planes dinámicos -->
                    <div class="row gy-6">
                        @foreach($plans as $plan)
                            @php
                                $isActive = auth()->check() && auth()->user()->current_plan_id === $plan->id && auth()->user()->hasActivePlan();
                            @endphp

                            <div class="col-xl-4 col-lg-6">
                                <div class="card border {{ $isActive ? 'border-success' : 'rounded' }} shadow-sm h-100">
                                    <div class="card-body text-center position-relative pt-5 p-4">

                                        @if($plan->is_recommended)
                                            <span class="badge bg-label-primary position-absolute top-0 end-0 m-3">Recomendado</span>
                                        @endif
                                        @if($isActive)
                                            <span class="badge bg-success position-absolute top-0 start-0 m-3">Tu plan</span>
                                        @endif

                                                                                <img src="{{ asset('assets/img/illustrations/page-pricing-basic.png') }}"
                                            alt="plan {{ $plan->name }}" height="100" class="mb-3" />


                                        <h5 class="fw-bold mb-1">{{ $plan->name }}</h5>
                                        <p class="text-muted mb-4">{{ $plan->description }}</p>


                                        <h2 class="text-primary mb-0">€{{ $plan->price_formatted }}</h2>
                                        <sub class="h6 text-body">/mes</sub>


                                        <div class="mt-4">
                                            @auth
                                                @if($isActive)
                                                    <button class="btn btn-secondary w-100" disabled>Ya lo tienes</button>
                                                @else
                                                    <a href="{{ route('payment.form', $plan->id) }}" class="btn btn-primary w-100">
                                                        Comprar plan
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
                    <!--/ Planes dinámicos -->
                </div>
            </div>
        </div>
    </div> --}}
    <!--/ Modal de Planes -->

@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/js/dropdown-hover.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/mega-dropdown.js') }}"></script>
    <script src="{{ asset('assets/js/pages-pricing.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @push('scripts')
    <script src="{{ asset('assets/vendor/js/dropdown-hover.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/mega-dropdown.js') }}"></script>
    <script src="{{ asset('assets/js/pages-pricing.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById("checkoutForm");
            const button = document.getElementById("checkoutBtn");

            button.addEventListener("click", function (e) {
                e.preventDefault();

                Swal.fire({
                    title: '¿Proceder con el pago?',
                    text: "Serás redirigido a Stripe para completar tu suscripción.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, continuar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData(form);

                        fetch(form.action, {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                            },
                            body: formData
                        })
                        .then(async res => {
                            let data;
                            try {
                                data = await res.json();
                            } catch {
                                throw new Error("Respuesta inesperada del servidor");
                            }

                            if (res.ok && data.url) {
                                window.location.href = data.url;
                            } else {
                                Swal.fire("Error", data.error ?? "No se pudo generar la sesión de pago", "error");
                            }
                        })
                        .catch(err => {
                            Swal.fire("Error", err.message, "error");
                        });
                    }
                });
            });
        });
    </script>
@endpush
@endpush
