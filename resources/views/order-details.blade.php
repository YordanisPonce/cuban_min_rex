@extends('layouts.app')

@php
    $orderId = $order->stripe_payment_intent ?? $order->paypal_order_id ?? $order->id;
@endphp

@section('title', 'Detalles Orden #' . $order->id . ' - ' . config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/order-details.css') }}">
@endpush

@section('content')
    <div class="main">

        @if($order->status === 'paid')
            <div class="success-header">
                <div class="success-icon"><i class="fas fa-check"></i></div>
                <div class="success-text">
                    <h1>¡Compra <span>completada!</span></h1>
                    <p>Tu pago fue procesado correctamente. {{count($purchaseItems) > 0 ? 'Los archivos están listos para descargar.' : ''}}</p>
                </div>
                <div class="success-meta">
                    <div class="label">Nº de orden</div>
                    <div class="val">#{{ $orderId }}</div>
                </div>
            </div>
        @else
            <div class="warning-header">
                <div class="warning-icon"><i class="fas fa-warning"></i></div>
                <div class="warning-text">
                    <h1>¡Compra <span>pendiente de pago!</span></h1>
                    <p>Tu pago está pendiente a confirmar.</p>
                </div>
                <div class="warning-meta">
                    <div class="label">Nº de orden</div>
                    <div class="val">#{{ $orderId }}</div>
                </div>
            </div>
        @endif

        <div class="grid">
            <div>
                <div class="section-title"><i class="fas fa-box-open"></i> Artículos comprados</div>
                <div class="card">
                    <div class="card-title">{{ $order->order_items->count() > 0 ? $order->order_items->count() : 1 }} Artículo(s)</div>

                    @if(count($purchaseItems) > 0)
                        @foreach($purchaseItems as $item)
                            <div class="purchase-item">
                                <img src="{{ $item['img'] }}" alt="{{ $item['name']}}">
                                <div class="purchase-item-info">
                                    <h4>{{ $item['name'] }}</h4>
                                    <p>{{ $item['artist'] }}</p>
                                    <div class="purchase-item-meta">
                                        <span class="pill"><i class="fas fa-headphones"></i> {{ strtoupper($item['extension']) }}</span>
                                    </div>
                                </div>
                                <div class="purchase-item-right">
                                    <div class="purchase-item-price">$ {{ number_format($item['price'], 2) }}</div>
                                    <div class="purchase-item-qty">x1</div>
                                </div>
                            </div>
                        @endforeach
                        <div class="dl-row">
                            <div class="dl-chip"><i class="fas fa-file-zipper"></i> {{count($purchaseItems)}} archivos · {{array_sum(array_column($purchaseItems, 'size'))}} MB</div>
                            @if($order->status === 'paid')
                                <a href="{{ route('order.download.all', $order->id)}}" class="dl-btn"><i class="fas fa-download"></i> Descargar todo</a>
                            @endif
                        </div>
                    @else
                        @if($order->plan)
                            <div class="purchase-item">
                                <div class="purchase-item-info">
                                    <h4>Suscripción: {{ $order->plan->name }}</h4>
                                </div>
                                <div class="purchase-item-right">
                                    <div class="purchase-item-price">$ {{ number_format($order->plan->price, 2) }}</div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <div>
                <div class="section-title"><i class="fas fa-receipt"></i> Resumen del pago</div>
                <div class="card">
                    <div class="card-title">Detalles del pago</div>
                    <div class="summary-row"><span>Subtotal</span><span>$ {{ number_format($order->amount, 2) }}</span></div>
                    <div class="summary-row muted"><span>Descuento</span><span>- $ 0.00</span></div>
                    <div class="summary-row total"><span>TOTAL PAGADO</span><span>$ {{ number_format($order->amount, 2) }}</span></div>

                    <div class="steps">
                        <div class="step done"><span class="dotc"><i class="fas fa-check"></i></span><span
                                class="lbl">Recibida</span></div>
                        @if($order->status === 'paid')
                        <div class="step done"><span class="dotc"><i class="fas fa-credit-card"></i></span><span
                                class="lbl">Pagada</span></div>
                        <div class="step active"><span class="dotc"><i class="fas fa-download"></i></span><span
                                class="lbl">Descargar</span></div>
                        @else
                        <div class="step active"><span class="dotc"><i class="fas fa-credit-card"></i></span><span
                                class="lbl">Pagada</span></div>
                        <div class="step"><span class="dotc"><i class="fas fa-download"></i></span><span
                                class="lbl">Descargar</span></div>
                        @endif
                    </div>
                </div>

                <div style="margin-top:1.5rem;">
                    <div class="section-title"><i class="fas fa-dollar-sign"></i> Método de pago</div>
                    @if($order->paypal_order_id)
                        <div class="pay-method">
                            <div class="pm-icon"><i class="fab fa-paypal"></i></div>
                            <div class="pm-info">
                                <h4>PAYPAL</h4>
                            </div>
                            <div class="pm-status"><span class="status-pill"><span class="dot"></span> Aprobado</span></div>
                        </div>
                    @else
                        <div class="pay-method">
                            <div class="pm-icon"><i class="fas fa-credit-card"></i></div>
                            <div class="pm-info">
                                <h4>TARJETA DE CRÉDITO</h4>
                            </div>
                            <div class="pm-status"><span class="status-pill"><span class="dot"></span> Aprobado</span></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
