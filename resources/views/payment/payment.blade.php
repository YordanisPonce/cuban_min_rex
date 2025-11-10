@extends('layouts.app')

@section('title', 'Checkout')

@push('styles')
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page.css') }}" />
@endpush

@section('content')

    <!-- Sección de Checkout -->
    <section class="section-py bg-body mt-10 mt-md-1">
        <div class="container mt-5">
            <div class="card px-3">
                <div class="row">
                    <!-- Detalles de Facturación -->
                    <div class="col-lg-7 card-body border-end p-md-8">
                        <h4 class="mb-2">Finalizar compra</h4>
                        <p class="mb-2">
                            {{ isset($planId)
                                ? 'Todos los planes incluyen funciones avanzadas para impulsar tu música.
                                                        Escoge el plan que mejor se adapte a tus necesidades.'
                                : 'Antes de proceder con el pago revise bien los datos de facturación. Una vez realizado el pago se le enviará un correo con el enlace de descarga del archivo.' }}
                        </p>
                        <h4 class="mb-2">Detalles de facturación</h4>

                        <form id="checkoutForm" method="POST"
                            action="{{ isset($planId) ? route('payment.process') : route('file.payment.process') }}">
                            @csrf

                            <!-- Campos de facturación -->
                            <div class="row g-6">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre completo</label>
                                    <input type="text" name="name" class="form-control" placeholder="Juan Pérez"
                                        value="{{ Auth::user()->name }}" required />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Correo electrónico</label>
                                    <input type="email" name="email" class="form-control"
                                        placeholder="ejemplo@gmail.com" value="{{ Auth::user()->email }}" required />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Teléfono</label>
                                    <input type="tel" name="phone" class="form-control" placeholder="+34 600 123 456"
                                        value="{{ Auth::user()->billing ? Auth::user()->billing->phone : '' }}" required />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Dirección</label>
                                    <input type="text" name="address" class="form-control"
                                        placeholder="Dirección de Facturación"
                                        value="{{ Auth::user()->billing ? Auth::user()->billing->address : '' }}"
                                        required />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Código postal</label>
                                    <input type="text" name="postal" class="form-control" placeholder="28001"
                                        value="{{ Auth::user()->billing ? Auth::user()->billing->postal : '' }}" required />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">País</label>
                                    <select name="country" class="form-select" required>
                                        <option value="">Seleccionar</option>
                                        @php
                                            $countries = [
                                                'Alemania',
                                                'Andorra',
                                                'Argentina',
                                                'Armenia',
                                                'Australia',
                                                'Austria',
                                                'Bélgica',
                                                'Bolivia',
                                                'Bosnia y Herzegovina',
                                                'Brasil',
                                                'Canadá',
                                                'Chile',
                                                'China',
                                                'Colombia',
                                                'Costa Rica',
                                                'Cuba',
                                                'Dinamarca',
                                                'Ecuador',
                                                'Egipto',
                                                'El Salvador',
                                                'Emiratos Árabes Unidos',
                                                'España',
                                                'Estados Unidos',
                                                'Estonia',
                                                'Etiopía',
                                                'Filipinas',
                                                'Francia',
                                                'Ghana',
                                                'Grecia',
                                                'Guatemala',
                                                'Honduras',
                                                'India',
                                                'Indonesia',
                                                'Irak',
                                                'Irán',
                                                'Italia',
                                                'Japón',
                                                'Kenia',
                                                'Malasia',
                                                'Marruecos',
                                                'México',
                                                'Nicaragua',
                                                'Noruega',
                                                'Nigeria',
                                                'Panamá',
                                                'Paraguay',
                                                'Perú',
                                                'Polonia',
                                                'Portugal',
                                                'República Checa',
                                                'República Dominicana',
                                                'Rumanía',
                                                'Rusia',
                                                'Reino Unido',
                                                'Sudáfrica',
                                                'Suecia',
                                                'Suiza',
                                                'Tailandia',
                                                'Tanzania',
                                                'Uganda',
                                                'Uruguay',
                                                'Venezuela',
                                                'Vietnam',
                                            ];
                                        @endphp
                                        @foreach ($countries as $country)
                                            <option value="{{ $country }}"
                                                {{ Auth::user()->billing ? (Auth::user()->billing->country == $country ? 'selected' : '') : '' }}>
                                                {{ $country }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            @isset($planId)
                                <input type="hidden" name="plan_id" value="{{ $planId }}" />
                            @endisset

                            @isset($file)
                                <input type="hidden" name="file_id" value="{{ $file->id }}" />
                            @endisset

                        </form>
                    </div>

                    <!-- Resumen del Pedido -->
                    <div class="col-lg-5 card-body p-md-8">
                        <h4 class="mb-2">Resumen del pedido</h4>
                        <p class="mb-8">
                            Gestiona y controla tus pedidos antes, durante y después de la compra.
                        </p>

                        <div class="bg-lighter p-6 rounded">
                            @isset($planId)
                                @foreach ($plans as $plan)
                                    @if ($plan->id == $planId)
                                        <h3>{{ $plan->name }}</h3>
                                        @if ($plan->description)
                                            <p>{!! $plan->description !!}</p>
                                        @endif
                                        <p>Cantidad de descargas: {{ $plan->downloads }}</p>
                                        <div class="d-flex align-items-center mb-4">
                                            <h1 class="text-heading mb-0">$ {{ $plan->price }}</h1>
                                            <sub class="h6 text-body mb-n3">/mes</sub>
                                        </div>
                                    @endif
                                @endforeach
                            @endisset

                            @isset($file)
                                <p>Música a comprar: {{ $file->name }}</p>
                                <div class="d-flex align-items-center mb-4">
                                    <h1 class="text-heading mb-0">$ {{ $file->price }}</h1>
                                </div>
                            @endisset
                        </div>

                        <div class="mt-5">

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
            document.addEventListener("DOMContentLoaded", function() {
                const form = document.getElementById("checkoutForm");
                const button = document.getElementById("checkoutBtn");

                button.addEventListener("click", function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: '¿Proceder con el pago?',
                        text: "Serás redirigido a Stripe para completar tu pago.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, continuar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const requiredFields = form.querySelectorAll('[required]');
                            let allFilled = true;

                            requiredFields.forEach(field => {
                                if (!field.value.trim()) {
                                    allFilled = false;
                                }
                            });

                            if (allFilled) {
                                let formData = new FormData(form);
                                document.querySelector('#loader').style.display = 'flex';
                                fetch(form.action, {
                                        method: "POST",
                                        headers: {
                                            "X-CSRF-TOKEN": document.querySelector(
                                                'input[name="_token"]').value
                                        },
                                        body: formData
                                    })
                                    .then(async res => {
                                        let data;
                                        try {
                                            data = await res.json();
                                        } catch {
                                            document.querySelector('#loader').style.display =
                                                'none';
                                            throw new Error(
                                            "Respuesta inesperada del servidor");
                                        }

                                        if (res.ok && data.url) {
                                            window.location.href = data.url;
                                        } else {
                                            document.querySelector('#loader').style.display =
                                                'none';
                                            Swal.fire("Error", data.error ??
                                                "No se pudo generar la sesión de pago",
                                                "error");
                                        }
                                    })
                                    .catch(err => {
                                        document.querySelector('#loader').style.display = 'none';
                                        Swal.fire("Error", err.message, "error");
                                    });
                            } else {
                                Swal.fire("Error",
                                    "Por favor, rellene todos los campos del formulario de facturación.",
                                    "error");
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
@endpush
