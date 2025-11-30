@extends('layouts.app')
@php
    use Carbon\Carbon;
@endphp
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
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="nav-align-top">
                        <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-md-0 gap-2">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('profile.edit') }}"><i
                                        class="icon-base ti tabler-users icon-sm me-1_5"></i> Información de Usuario</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active text-black" href="{{ route('profile.billing') }}"><i
                                        class="icon-base ti tabler-bookmark icon-sm me-1_5"></i> Información de
                                    Facturación</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card mb-6">
                        <!-- Current Plan -->
                        <h5 class="card-header">Plan Actual</h5>
                        <div class="card-body">
                            <div class="row row-gap-6">
                                @if (Auth::user()->hasActivePlan() && Auth::user()->currentPlan)
                                    <div class="col-md-6 mb-1">
                                        <div class="mb-6">
                                            <h6 class="mb-1">Tu Plan Actual es {{ Auth::user()->currentPlan->name }}</h6>
                                            {!! Auth::user()->currentPlan->description !!}
                                        </div>
                                        <div class="mb-6">
                                            @php
                                                Carbon::setLocale('es');
                                                $expDate = Carbon::parse(
                                                    auth()->user()->plan_expires_at,
                                                )->translatedFormat('d \d\e F \d\e Y H:i');
                                            @endphp
                                            <h6 class="mb-1">Activo hasta el {{ $expDate }}</h6>
                                            <p>Te enviearemos una notificación cuando esté cerca de expirar</p>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">
                                                <span class="me-1">${{ Auth::user()->currentPlan->price }}/mes </span>
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
                                                    <span class="alert-icon rounded"><i
                                                            class="icon-base ti tabler-alert-triangle icon-md"></i></span>
                                                    <span>¡Necesitamos tu atención!</span>
                                                </h5>
                                                <span class="ms-11 ps-1">Tu plan requiere actualización</span>
                                            </div>
                                        @else
                                            <div class="alert alert-success mb-6" role="alert">
                                                <h5 class="alert-heading mb-1 d-flex align-items-center">
                                                    <span>¡Plan activo!</span>
                                                </h5>
                                                <span class="ps-1">Tu plan está activo y no requiere actualización, ahora
                                                    puedes disfrutar de todos los beneficios asociados</span>
                                            </div>
                                        @endif
                                        <div class="plan-statistics">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1">Días</h6>
                                                <h6 class="mb-1">
                                                    {{ Auth::user()->currentPlan->duration_months * 30 - Auth::user()->planExpirationDays()->days }}
                                                    de {{ Auth::user()->currentPlan->duration_months * 30 }} Días</h6>
                                            </div>
                                            <div class="progress rounded mb-1">
                                                @php
                                                    $percent =
                                                        Auth::user()->planExpirationDays()->days > 0
                                                            ? ((Auth::user()->currentPlan?->duration_months * 30 -
                                                                    Auth::user()->planExpirationDays()->days) /
                                                                    (Auth::user()->currentPlan?->duration_months *
                                                                        30)) *
                                                                100
                                                            : '100';
                                                @endphp
                                                <div class="progress-bar rounded" style="width: {{ $percent }}%"
                                                    role="progressbar"
                                                    aria-valuenow="{{ Auth::user()->currentPlan?->duration_months * 30 - Auth::user()->planExpirationDays()->days }}"
                                                    aria-valuemin="0"
                                                    aria-valuemax="{{ Auth::user()->currentPlan->duration_months * 30 }}">
                                                </div>
                                            </div>
                                            <small>{{ Auth::user()->planExpirationDays()->days }} días restantes hasta que
                                                tu plan requiera actualización</small>
                                        </div>
                                        <div class="plan-statistics">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1">Descargas Realizadas</h6>
                                                <h6 class="mb-1">
                                                    {{ Auth::user()->getCurrentMonthDownloads() }}
                                                    de {{ Auth::user()->currentPlan->downloads }} este mes.</h6>
                                            </div>
                                            {{--    <div class="progress rounded mb-1">
                                                @php
                                                    $percent =
                                                        Auth::user()->currentPlan->downloads > 0
                                                            ? (Auth::user()->getCurrentMonthDownloads() /
                                                                    Auth::user()->currentPlan->downloads) *
                                                                100
                                                            : '100';
                                                @endphp
                                                <div class="progress-bar rounded" style="width: {{ $percent }}%"
                                                    role="progressbar"
                                                    aria-valuenow="{{ Auth::user()->getCurrentMonthDownloads() }}"
                                                    aria-valuemin="0"
                                                    aria-valuemax="{{ Auth::user()->currentPlan->downloads }}">
                                                </div>
                                            </div>
                                            <small>{{ Auth::user()->currentPlan->downloads - Auth::user()->getCurrentMonthDownloads() }}
                                                días restantes este mes.</small> --}}
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex gap-2 flex-wrap">
                                        <button class="btn btn-primary me-2 text-black" data-bs-toggle="modal"
                                            data-bs-target="#pricingModal">Actualizar Plan</button>
                                        <a class="btn btn-dark me-2" href="{{ route('profile.billingLink') }}">Gestionar suscripción</a>
                                        <button class="btn btn-label-danger cancel-subscription"
                                            onclick="mostrarAdvertencia()">Cancelar Suscripción</button>
                                    </div>
                                @else
                                    @if (!Auth::user()->hasActivePlan())
                                        <div class="alert alert-warning mb-6 h-fit" role="alert">
                                            <h5 class="alert-heading mb-1 d-flex align-items-center">
                                                <span class="alert-icon rounded"><i
                                                        class="icon-base ti tabler-alert-triangle icon-md"></i></span>
                                                <span>¡Actualmente no posee ningun plan activo!</span>
                                            </h5>
                                            <span class="ms-11 ps-1">Por favor adquiera un plan para poder disfrutar de sus
                                                beneficios.</span>
                                        </div>
                                    @else
                                        <div class="alert alert-success mb-6" role="alert">
                                            <h5 class="alert-heading mb-1 d-flex align-items-center">
                                                <span>¡Membresía activa!</span>
                                            </h5>
                                            @php
                                                Carbon::setLocale('es');
                                                $expDate = Carbon::parse(
                                                    auth()->user()->plan_expires_at,
                                                )->translatedFormat('d \d\e F \d\e Y');
                                            @endphp
                                            <span class="ps-1">Aún puedes disfrutar de todos los beneficios asociados a tu
                                                antiguo plan durante <strong>{{ Auth::user()->planExpirationDays()->days }}
                                                    días</strong>, por favor considere renovarla antes de
                                                <strong>{{ $expDate }}</strong> o adquirir una nueva membresía.</span>
                                        </div>
                                    @endif
                                    <div class="col-12 d-flex gap-2 flex-wrap">
                                        <button class="btn btn-primary me-2 text-black" data-bs-toggle="modal"
                                            data-bs-target="#pricingModal">Adquirir Plan</button>
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
                                        <input type="text" class="form-control" id="billingAddress" name="address"
                                            placeholder="Dirección de Facturación"
                                            value="{{ Auth::user()->billing ? Auth::user()->billing->address : '' }}"
                                            required />
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="mobileNumber" class="form-label">Teléfono</label>
                                        <input class="form-control mobile-number" type="text" id="mobileNumber"
                                            name="phone"
                                            value="{{ Auth::user()->billing ? Auth::user()->billing->phone : '' }}"
                                            placeholder="+34 600 123 456" required />
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="country" class="form-label">País</label>
                                        <select id="country" class="form-select select2" name="country" required>
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
                                    <div class="col-sm-6">
                                        <label for="zipCode" class="form-label">Código Postal</label>
                                        <input type="text" class="form-control zip-code" id="zipCode"
                                            name="postal" placeholder="231465"
                                            value="{{ Auth::user()->billing ? Auth::user()->billing->postal : '' }}"
                                            maxlength="6" required />
                                    </div>
                                </div>
                                <div class="mt-6">
                                    <button type="submit" class="btn btn-primary me-3 text-black">Guardar cambios</button>
                                </div>
                            </form>
                        </div>
                        <!-- /Billing Address -->
                    </div>
                    <div class="card">
                        <!-- Billing History -->
                        <h5 class="card-header text-md-start text-center">Historial de facturas</h5>
                        <div class="card-datatable table-responsive border-top">
                            <table class="invoice-list-table table border-top">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Compra</th>
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
                                            <td>
                                                @if ($order->plan)
                                                    Plan: {{ $order->plan->name }}
                                                @else 
                                                    {{ count($order->order_items ?? [])}} Archivos: <br>
                                                    @foreach ($order->order_items as $key => $value)
                                                        {{ $value->file->name }} <br>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>{{ $order->amount }}</td>
                                            <td>{{ $order->status === 'paid' ? $order->paid_at : $order->created_at }}</td>
                                            <td>{{ $order->status === 'paid' ? 'Pagado' : ($order->status === 'pending' ? 'Pendiente' : 'Fallida') }}
                                            </td>
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
                    document.querySelector('#loader').style.display = 'flex';
                    window.location.href = '/payment/cancel-subscription';
                }
            });
        }

        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                document.querySelector('#loader').style.display = 'flex';
            });
        });
    </script>
@endpush
