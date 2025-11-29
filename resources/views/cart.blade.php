@extends('layouts.app')
@php
use Carbon\Carbon;
use App\Models\Cart;
Carbon::setLocale('es');
$success = session('success');
$error = session('error');
@endphp
@section('title', 'Carrito')

@section('content')
<div class="content-wrapper mt-10">
    <!-- Content -->
    @if (count(Cart::get_current_cart()->items ?? []) > 0)
    <div class="container-xxl flex-grow-1 container-p-y mt-10">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
            <div class="d-flex flex-column justify-content-center">
                
            </div>
            <div class="d-flex align-content-center flex-wrap gap-2">
                <a class="btn btn-label-danger" href="{{route('file.empty.cart')}}">Vacíar Carrito</a>
            </div>
        </div>

        <!-- Order Details Table -->

        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="card mb-6">
                    <div class="card-datatable">
                        <table class="datatables-order-details table mb-0">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th class="w-50">Archivo</th>
                                    <th class="w-25">Remixer</th>
                                    <th>Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cart as $item)
                                <tr>
                                    <td><a href="{{ route('file.remove.cart', $item->id )}}" style="width: 20px !important; display: block">{{ svg('vaadin-minus') }}</a></td>
                                    <td>{{ $item->name}}</td>
                                    <td>{{ $item->user->name}}</td>
                                    <td>$ {{ $item->price}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end align-items-center m-6 mb-2">
                            <div class="order-calculations">
                                <!-- <div class="d-flex justify-content-start mb-2">
                                    <span class="w-px-100 text-heading">Subtotal:</span>
                                    <h6 class="mb-0">$2093</h6>
                                </div>
                                <div class="d-flex justify-content-start mb-2">
                                    <span class="w-px-100 text-heading">Discount:</span>
                                    <h6 class="mb-0">$2</h6>
                                </div>
                                <div class="d-flex justify-content-start mb-2">
                                    <span class="w-px-100 text-heading">Tax:</span>
                                    <h6 class="mb-0">$28</h6>
                                </div> 
                                <div class="d-flex justify-content-start">
                                    <h6 class="w-px-100 mb-0">Total:</h6>
                                    <h6 class="mb-0">$ 0.00</h6>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="card-title m-0">Detalles</h5>
                    </div>
                    <div class="card-body">
                        @auth
                        <div class="d-flex justify-content-start align-items-center mb-6">
                            <div class="avatar me-3">
                                <img src="{{ Auth::user()->photo ? Auth::user()->photo : config('app.logo') }}" alt="Avatar" class="rounded-circle" />
                            </div>
                            <div class="d-flex flex-column">
                                <a href="app-user-view-account.html" class="text-body text-nowrap">
                                    <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                                </a>
                                <span>{{ Auth::user()->email}}</span>
                            </div>
                        </div>
                        @endauth
                        <div class="d-flex justify-content-between align-items-center mb-6">
                            <div class="d-flex justify-content-start align-items-center">
                                <span class="avatar rounded-circle bg-label-success me-3 d-flex align-items-center justify-content-center"><i class="icon-base ti tabler-shopping-cart icon-lg"></i></span>
                                <h6 class="text-nowrap mb-0">{{count(Cart::get_current_cart()->items ?? [])}} Archivo(s)</h6>
                            </div>
                            <div class="d-flex justify-content-start align-items-center">
                                <strong class="w-px-100 mb-0">Total:</strong>
                                <h6 class="mb-0">$ {{ Cart::get_current_cart()->get_cart_count()}}</h6>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end align-items-center">
                            <a class="btn btn-primary cursor-pointer" onclick="proccessPayment()">Procesar Pago</a>
                        </div>
                    </div>
                </div>
                @auth
                <div class="card mb-6">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="card-title m-0">Datos de Facturación</h5>
                        <h6 class="m-0"><a href=" javascript:void(0)" data-bs-toggle="modal" data-bs-target="#addNewAddress">Editar</a></h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>Dirección:</strong> {{ Auth::user()->billing ? Auth::user()->billing->address : 'Sin Dirección' }}</p>
                        <p class="mb-1"><strong>Telefono:</strong> {{ Auth::user()->billing ? Auth::user()->billing->phone : 'Sin Teléfono' }}</h5>
                        <p class="mb-1"><strong>País:</strong> {{ Auth::user()->billing ? Auth::user()->billing->country : 'Sin País' }}</h5>
                        <p class="mb-1"><strong>Código Postal:</strong> {{ Auth::user()->billing ? Auth::user()->billing->postal : 'Sin Código Postal' }}</h5>
                    </div>
                </div>
                @endauth
            </div>
        </div>
    </div>
    @else
    <div class="container-xxl flex-grow-1 container-p-y mt-10">
        <div class="row align-items-center g-5 mt-10">
            <div class="align-items-center justify-content-center text-center">
                <span class="badge bg-label-primary mb-3">Carrito Vacío</span>
                <h3 class="display-6 fw-bold mb-2">No hay elementos en tu carrito.</h3>
                <p class="text-body-secondary mb-4">
                    Explora nuestro repositorio y descubre música a tu gusto.
                </p>
                <div class="d-flex align-items-center justify-content-center gap-3 mt-4">
                    <a href="{{ route('remixes') }}" class="btn btn-primary">Explorar</a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@auth
<!-- Add New Address Modal -->
<div class="modal fade" id="addNewAddress" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple modal-add-new-address">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-6">
          <h4 class="address-title mb-2">Editar Datos de Facturación</h4>
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
                    <div class="col-sm-12">
                        <label for="zipCode" class="form-label">Código Postal</label>
                        <input type="text" class="form-control zip-code" id="zipCode"
                            name="postal" placeholder="231465"
                            value="{{ Auth::user()->billing ? Auth::user()->billing->postal : '' }}"
                            maxlength="6" required />
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="btn btn-primary me-3">Guardar cambios</button>
                </div>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ Add New Address Modal -->
@endauth
@endsection

@push('scripts')
<script>
    function proccessPayment() {
        const rute = "{{route('file.pay')}}";
        Swal.fire({
            title: '¿Proceder con el pago?',
            text: "Serás redirigido a Stripe para completar tu pago.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector('#loader').style.display = 'flex';
                fetch(rute)
                    .then(async res => {
                        let data;
                        
                        try {
                            data = await res.json();
                        } catch {
                            document.querySelector('#loader').style.display = 'none';
                            throw new Error("Respuesta inesperada del servidor");
                        }

                        if (res.ok && data.url) {
                            window.location.href = data.url;
                        } else {
                            document.querySelector('#loader').style.display = 'none';
                            Swal.fire("Error", data.error ?? "No se pudo generar la sesión de pago", "error");
                        }
                    })
                    .catch(err => {
                        document.querySelector('#loader').style.display = 'none';
                        Swal.fire("Error", err.message, "error");
                    });
            }
        });
    }
</script>
@isset($error)
<script>
    Swal.fire({
        title: 'Error',
        text: '{{ $error }}',
        icon: 'error'
    });
</script>
@endisset
@isset($success)
<script>
    Swal.fire({
        title: ' ',
        text: '{{ $success }}',
        icon: 'success'
    });
</script>
@endisset
@endpush