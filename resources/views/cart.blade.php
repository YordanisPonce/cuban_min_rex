@extends('layouts.app')
@php
    use Carbon\Carbon;
    use App\Models\Cart;
    Carbon::setLocale('es');
    $success = session('success');
    $error = session('error');
@endphp
@section('title', 'Carrito - ' . config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/cart.css') }}">
@endpush

@section('content')
    @if ($cart->cart_items()->count() > 0)
        <!-- MAIN -->
        <div class="main">
            <!-- LEFT: CART -->
            <div>
                <h1 class="section-title">TU CARRITO</h1>
                <div class="cart-box">
                    <div class="cart-box-title">TU CARRITO</div>

                    @foreach ($cart->cart_items as $item)
                        <div class="cart-item">
                            <img src="{{ $item->cover() }}" alt="{{ $item->name() }}">
                            <div class="cart-item-info">
                                <h4>{{ $item->name() }}</h4>
                                <p>{{ $item->dj() }}</p>
                            </div>
                            <div class="qty-control">
                                <a class="qty-btn" href="{{ $item->removeRoute() }}"><i class="fas fa-trash"></i></a>
                            </div>
                            <div class="cart-item-price">$ {{ number_format($item->price(), 2) }}</div>
                        </div>
                    @endforeach

                    {{-- <div class="discount-row">
                        <input class="discount-input" placeholder="Añadir código de descuento">
                        <button class="discount-btn">APLICAR</button>
                    </div> --}}
                </div>
            </div>

            <!-- RIGHT: PAYMENT -->
            <div>
                <h1 class="section-title text-primary">RESUMEN</h1>

                <!-- ORDER SUMMARY -->
                <div class="order-summary">
                    <div class="summary-title">RESUMEN DEL PEDIDO</div>
                    <div class="summary-row"><span>Subtotal</span><span>$ {{ number_format($cart->get_cart_count(), 2) }}</span></div>
                    <div class="summary-row muted"><span>Descuento</span><span>- $ 0.00</span></div>
                    <div class="summary-row total"><span>TOTAL</span><span>$ {{ number_format($cart->get_cart_count(), 2) }}</span></div>
                </div>

                <hr style="border:none;border-top:1px solid var(--border);margin:1.5rem 0;">

                <div class="add-info"><i class="fas fa-info-circle"></i> Al continuar estás aceptando nuestros <a href="{{ route('terms') }}"><strong>Términos y Condiciones de Uso</strong></a></div>.

                <button class="pay-btn" onclick="proccessPayment()">REALIZAR PAGO $ {{ number_format($cart->get_cart_count(), 2) }}</button>
                <div class="pay-secure"><i class="fas fa-lock"></i> Pago 100% seguro y encriptado</div>
            </div>
        </div>
    @else
        <div class="container" style="height: 100%; align-items: center; display: flex; justify-content: center; margin-top: 20px">
            <div style="text-align:center;padding:4rem 1rem;">
                <i class="fas fa-shopping-cart text-primary" style="font-size:3rem;margin-bottom:1rem;"></i>
                <div style="font-size:2rem;font-weight:600;margin-bottom:1rem;">Carrito Vacio</div>
                <div style="font-size:1rem;margin-bottom: 2rem">Agrega elementos al carrito para proceder con la compra.
                </div>
                <a class="btn btn-primary" href="{{ route('remixes') }}">EXPLORAR</a>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        function proccessPayment() {
            const rute = "{{ route('file.pay') }}";
            Swal.fire({
                title: '¿Proceder con el pago?',
                text: "Serás redirigido a Stripe para completar tu pago.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.querySelector('#wloader').style.display = 'flex';
                    fetch(rute)
                        .then(async res => {
                            let data;

                            try {
                                data = await res.json();
                            } catch {
                                document.querySelector('#wloader').style.display = 'none';
                                throw new Error("Respuesta inesperada del servidor");
                            }

                            if (res.ok && data.url) {
                                window.location.href = data.url;
                            } else {
                                document.querySelector('#wloader').style.display = 'none';
                                Swal.fire("Error", data.error ?? "No se pudo generar la sesión de pago",
                                    "error");
                            }
                        })
                        .catch(err => {
                            document.querySelector('#wloader').style.display = 'none';
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
