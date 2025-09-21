@extends('layouts.app')

@section('title', 'Perfil de Usuario')

@push('styles')
<link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page-payment.css') }}" />
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
                            <a class="nav-link" href="{{ route('profile.edit') }}"><i class="icon-base ti tabler-users icon-sm me-1_5"></i> Información de Usuario</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('profile.billing') }}"><i class="icon-base ti tabler-bookmark icon-sm me-1_5"></i> Información de Facturación</a>
                        </li>
                    </ul>
                </div>
                <div class="card mb-6">
                    <!-- Current Plan -->
                    <h5 class="card-header">Plan Actual</h5>
                    <div class="card-body">
                        <div class="row row-gap-6">
                            @if (Auth::user()->hasActivePlan() === true)
                            <div class="col-md-6 mb-1">
                                <div class="mb-6">
                                    <h6 class="mb-1">Tu Plan Actual es {{Auth::user()->currentPlan->name}}</h6>
                                    <p>{{Auth::user()->currentPlan->description}}</p>
                                </div>
                                <div class="mb-6">
                                    @php
                                    setlocale(LC_TIME, 'Spanish_Spain.1252');
                                    $expDate = strftime('%d de %B de %Y', new DateTime(Auth::user()->plan_expires_at)->getTimestamp());
                                    @endphp
                                    <h6 class="mb-1">Activo hasta el {{ $expDate }}</h6>
                                    <p>Te enviearemos una notificación cuando esté cerca de expirar</p>
                                </div>
                                <div>
                                    <h6 class="mb-1">
                                        <span class="me-1">${{Auth::user()->currentPlan->price}}/mes </span>
                                        @if (Auth::user()->currentPlan->is_recomended)
                                        <span class="badge bg-label-primary rounded-pill">Recomendado</span>
                                        @endif
                                    </h6>
                                </div>
                            </div>
                            <div class="col-md-6">
                                @if (Auth::user()->planExpirationDays()->days <= 5)
                                <div class="alert alert-warning mb-6" role="alert">
                                    <h5 class="alert-heading mb-1 d-flex align-items-center">
                                        <span class="alert-icon rounded"><i class="icon-base ti tabler-alert-triangle icon-md"></i></span>
                                        <span>¡Necesitamos tu atención!</span>
                                    </h5>
                                    <span class="ms-11 ps-1">Tu plan requiere actualización</span>
                                </div>
                                @else
                                <div class="alert alert-success mb-6" role="alert">
                                    <h5 class="alert-heading mb-1 d-flex align-items-center">
                                        <span>¡Plan activo!</span>
                                    </h5>
                                    <span class="ps-1">Tu plan está activo y no requiere actualización, ahora puedes disfrutar de todos los beneficios asociados</span>
                                </div>
                                @endif
                                <div class="plan-statistics">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1">Días</h6>
                                        <h6 class="mb-1">{{Auth::user()->currentPlan->duration_months * 30 - Auth::user()->planExpirationDays()->days}} de {{Auth::user()->currentPlan->duration_months * 30}} Días</h6>
                                    </div>
                                    <div class="progress rounded mb-1">
                                        @php
                                        $percent = Auth::user()->planExpirationDays()->days > 0 ? ((Auth::user()->currentPlan->duration_months * 30 - Auth::user()->planExpirationDays()->days) / (Auth::user()->currentPlan->duration_months * 30) * 100) : '100';
                                        @endphp
                                        <div class="progress-bar rounded" style="width: {{ $percent }}%" role="progressbar" aria-valuenow="{{Auth::user()->currentPlan->duration_months * 30 - Auth::user()->planExpirationDays()->days}}" aria-valuemin="0" aria-valuemax="{{Auth::user()->currentPlan->duration_months * 30}}"></div>
                                    </div>
                                    <small>{{Auth::user()->planExpirationDays()->days}} días restantes hasta que tu plan requiera actualización</small>
                                </div>
                            </div>
                            <div class="col-12 d-flex gap-2 flex-wrap">
                                <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#pricingModal">Actualizar Plan</button>
                                <button class="btn btn-label-danger cancel-subscription" onclick="mostrarAdvertencia()">Cancelar Suscripción</button>
                            </div>
                            @else
                            <div class="alert alert-warning mb-6" role="alert">
                                <h5 class="alert-heading mb-1 d-flex align-items-center">
                                    <span class="alert-icon rounded"><i class="icon-base ti tabler-alert-triangle icon-md"></i></span>
                                    <span>¡Actualmente no posee ningun plan activo!</span>
                                </h5>
                                <span class="ms-11 ps-1">Por favor adquiera un plan para poder disfrutar de sus beneficios.</span>
                            </div>
                            <div class="col-12 d-flex gap-2 flex-wrap">
                                <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#pricingModal">Adquirir Plan</button>
                            </div>
                            @endif
                        </div>
                    </div>
                    <!-- /Current Plan -->
                </div>
                <div class="card mb-6">
                    <!-- Billing Address -->
                    <h5 class="card-header">Datos de Facturación</h5>
                    <div class="card-body">
                        <form id="formAccountSettings" method="POST" action="{{ route('profile.updateBilling') }}">
                            @csrf
                            <div class="row g-6">
                                <div class="col-12">
                                    <label for="billingAddress" class="form-label">Dirección de Facturación</label>
                                    <input type="text" class="form-control" id="billingAddress" name="billingAddress" placeholder="Dirección de Facturación"   value="{{ Auth::user()->billing ? Auth::user()->billing->address : '' }}" required/>
                                </div>
                                <div class="col-sm-6">
                                    <label for="mobileNumber" class="form-label">Teléfono</label>
                                    <input class="form-control mobile-number" type="text" id="mobileNumber" name="mobileNumber" value="{{ Auth::user()->billing ? Auth::user()->billing->phone : '' }}" placeholder="+34 600 123 456" required/>
                                </div>
                                <div class="col-sm-6">
                                    <label for="country" class="form-label">País</label>
                                    <select id="country" class="form-select select2" name="country" required>
                                        <option value="">Seleccionar</option>
                                        @php
                                            $countries = ['España', 'México', 'Argentina', 'Colombia'];
                                        @endphp
                                        @foreach ($countries as $country)
                                            <option value="{{ $country }}" {{ Auth::user()->billing ? (Auth::user()->billing->country == $country ? 'selected' : '') : '' }}>
                                                {{ $country }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label for="zipCode" class="form-label">Código Postal</label>
                                    <input type="text" class="form-control zip-code" id="zipCode" name="postal" placeholder="231465"  value="{{ Auth::user()->billing ? Auth::user()->billing->postal : '' }}" maxlength="6" required/>
                                </div>
                            </div>
                            <div class="mt-6">
                                <button type="submit" class="btn btn-primary me-3">Guardar cambios</button>
                                <button type="reset" class="btn btn-label-secondary">Descartar</button>
                            </div>
                        </form>
                    </div>
                    <!-- /Billing Address -->
                </div>
                <div class="card">
                    <!-- Billing History -->
                    <h5 class="card-header text-md-start text-center">Historial de facturas</h5>
                    <div class="card-datatable border-top">
                    <table class="invoice-list-table table border-top">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Plan</th>
                                <th>Cantidad Pagada</th>
                                <th class="text-truncate">Fecha de Pago</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td></td>
                                    <td>{{ $order->plan->name }}</td>
                                    <td>{{ $order->plan->price }}</td>
                                    <td>{{ $order->status === 'paid' ? $order->paid_at : $order->created_at }}</td>
                                    <td>{{ $order->status === 'paid' ? 'Pagado' : ($order->status === 'pending' ? 'Pendiente' : 'Fallida') }}</td>
                                    <td></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    <!--/ Billing History -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<!-- Pricing Modal -->
<div class="modal fade" id="pricingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-simple modal-pricing">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <!-- Pricing Plans -->
                <div class="rounded-top">
                    <div class="text-center mb-4">
                        <span class="badge bg-label-primary">🎶 Planes de Suscripción</span>
                    </div>
                    <h4 class="text-center mb-1">
                        <span class="position-relative fw-extrabold z-1">
                            Elige tu plan musical ideal
                        </span>
                    </h4>
                    <p class="text-center pb-2 mb-7">
                        Disfruta de toda la música que amas, con beneficios que se adaptan a ti. 🎧<br>
                        <!-- Paga mensual o ahorra con el plan anual  -->
                    </p>

                    <div class="row gy-6">
                        @foreach($plans as $plan)
                        @php
                        $isActive = auth()->check() && auth()->user()->current_plan_id === $plan->id && auth()->user()->hasActivePlan();
                        @endphp
                        <div class="col-xl-4 col-lg-6">
                            <div class="{{ $isActive ? 'card border border-primary shadow-xl' : 'card'}}">
                                <div class="card-header">
                                    <div class="text-center">
                                        <img src="{{ asset('storage/' . $plan->image) }}" alt="paper airplane icon" class="mb-8 pb-2 w-25" />
                                        <h4 class="mb-0">{{ $plan->name }}</h4>
                                        <div class="d-flex align-items-center justify-content-center">
                                            <span class="price-monthly h2 text-primary fw-extrabold mb-0">$ {{ $plan->price_formatted }}</span>
                                            <!-- <span class="price-yearly h2 text-primary fw-extrabold mb-0 d-none">$ {{ $plan->price_formatted*0.75 }}</span> -->
                                            <sub class="h6 text-body-secondary mb-n1 ms-1">/mes</sub>
                                        </div>
                                        <!-- <div class="position-relative pt-2">
                                            <div class="price-yearly text-body-secondary price-yearly-toggle d-none">$ {{ $plan->price_formatted*12*0.75 }} / año</div>
                                            </div> -->
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($plan->description)
                                    <ul class="list-unstyled pricing-list">
                                        <li>
                                            <h6 class="d-flex align-items-center mb-3">
                                                <span class="badge badge-center rounded-pill bg-label-primary p-0 me-3"><i class="icon-base ti tabler-check icon-12px"></i></span>
                                                {{ $plan->description }}
                                            </h6>
                                        </li>
                                    </ul>
                                    @endif
                                    <div class="d-grid mt-8">
                                        @auth
                                        @if($isActive)
                                        <button class="btn btn-secondary" disabled>Ya lo tienes</button>
                                        @else
                                        <a href="{{ route('payment.form', $plan->id) }}" class="btn btn-label-primary">Adquirir Plan</a>
                                        @endif
                                        @else
                                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
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
                <!--/ Pricing Plans -->
            </div>
        </div>
    </div>
</div>
<!--/ Pricing Modal -->
<script src="../../assets//js/pages-pricing.js"></script>
<!--/ Modal -->
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
                window.location.href = '/payment/cancel-subscription';
            }
        });
    }
</script>
@endpush