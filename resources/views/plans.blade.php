@extends('layouts.app')

@php
    use Carbon\Carbon;
    use Carbon\CarbonInterface;

    Carbon::setLocale('es');
@endphp

@section('title', 'Planes y precios')
@section('description', 'Elige tu plan y accede a descargas de remixes y música exclusiva para DJs. Compara precios y beneficios.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/plans.css') }}" />
@endpush

@section('content')
    @include('partials.hero', [
        'baseDescription' => 'Más de 10,000 remixes exclusivos para DJs afrolatinos. Todo lo que necesitas para hacer estallar la pista.',
        'baseLinkText' => '+10k REMIXES EXCLUSIVOS',
        'baseLinkAddress' => route('remixes'),
    ])

    <!-- FEATURES -->
    <div class="features-row">
        <div class="feature-item">
            <div class="feature-icon"><i class="fas fa-download"></i></div>
            <h3>DESCARGA REMIXES<br>Y DJ <span class="gold">TOOLS</span></h3>
        </div>
        <div class="feature-item">
            <div class="feature-icon"><i class="fas fa-dollar-sign"></i></div>
            <h3>NUEVOS TRACKS<br><span class="gold">CADA SEMANA</span></h3>
        </div>
        <div class="feature-item">
            <div class="feature-icon"><i class="fas fa-lock"></i></div>
            <h3>ACCESO EXCLUSIVO<br>A <span class="gold">PLAYLISTS</span></h3>
        </div>
    </div>

    <!-- PRICING -->
    <section class="pricing-section">
        @if (auth()->check() && auth()->user()->hasActivePlan())
            <div class="contact-bar" style="margin-bottom: 30px">
                <div class="icon"><i class="fas fa-crown"></i></div>
                <div>
                    <h3>Ya posees un plan activo</h3>
                    <h2 class="text-primary" style="text-transform: uppercase">{{ $activePlan ? $activePlan->name : '' }}
                    </h2>
                    <p>Su suscripción vence <span
                            class="text-primary">{{ Carbon::parse(auth()->user()->plan_expires_at)->diffForHumans(now(), CarbonInterface::DIFF_RELATIVE_TO_NOW) }}</span>.
                    </p>
                </div>
            </div>
        @else
            <div class="pricing-grid">
                @foreach ($plans as $plan)
                    @include('partials.plans-card', ['item' => $plan])
                @endforeach
            </div>
        @endif
    </section>

    @if (auth()->check() && auth()->user()->hasActivePlan() && count($packs) > 0)
        <section class="packs-section">
            <div class="packs-head">
                <span class="packs-kicker"><i class="fas fa-layer-group"></i> Paquetes extra</span>
                <h2>PACKS DE <span>DESCARGAS</span> ADICIONALES</h2>
                <p>¿Te quedaste sin descargas este mes? Compra un pack extra y sigue bajando tracks sin cambiar de plan.</p>
            </div>

            <div class="packs-grid">
                @foreach ($packs as $pack)
                    @include('partials.extra-download-pack-card', ['item' => $pack, 'isFeatured' => false])
                @endforeach
            </div>

            <div
                style="margin: 20px auto; display: flex; justify-content: center; align-items: center; color: var(--fg-muted)">
                <p><i class="fas fa-info-circle"></i> Es necesario tener una suscripción activa para comprar y utilizar
                    packs extra.</p>
            </div>

            <div class="pack-note">
                <div><i class="fas fa-bolt"></i><span><strong>Activación</strong> inmediata</span></div>
                <div><i class="fas fa-rotate"></i><span><strong>No</strong> se renuevan solas</span></div>
                <div><i class="fas fa-shield-halved"></i><span><strong>Pago</strong> seguro y encriptado</span></div>
                <div><i class="fas fa-infinity"></i><span><strong>Se</strong> acumulan entre packs</span></div>
            </div>
        </section>
    @endif

    <!-- CONTACT -->
    <div class="contact-bar" style="margin-bottom: 30px">
        <div class="icon"><i class="fas fa-headset"></i></div>
        <div>
            <p>¿TIENES DUDAS?</p>
            <span>Contáctanos al soporte - <a href="mailto:{{ config('contact.email') }}">Respondemos rápido</a></span>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            let recomendedPlan = document.querySelector('.plan-card:nth-child(2)');
            recomendedPlan.classList.add('featured');
            let recomendedBadge = recomendedPlan.querySelector('.plan-badge');
            recomendedBadge.innerText = 'RECOMENDADO';
            recomendedBadge.style.display = 'inline-block';

            let premiunPlan = document.querySelector('.plan-card:nth-child(3) #icon');
            let icon = document.createElement('i');
            icon.className = "fas fa-gem diamond-icon";
            premiunPlan.appendChild(icon);

        });
    </script>
    <script>
        document.querySelectorAll('.pack-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                $id = btn.getAttribute('data-id');
                $rute = '';
                Swal.fire({
                    title: '¿Proceder con el pago?',
                    text: "Estás a punto de adquirir un pack de descargas, será redirigido para completar tu pago. Seleccione el método de pago:",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Paypal',
                    cancelButtonText: 'Targeta',
                    confirmButtonColor: "#477fe6",
                    cancelButtonColor: "#f5a623",
                    showCloseButton: true
                }).then((result) => {
                    document.querySelector('#wloader').style.display = 'flex';
                    if (result.isConfirmed) {
                        $rute = "{{ config('app.url') }}/paypal/process/pack/" + $id;
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        $rute = "{{ config('app.url') }}/stripe/process/pack/" + $id;
                    } else {
                        document.querySelector('#wloader').style.display = 'none';
                        return;
                    }
                    fetch($rute, {
                            method: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Access-Control-Allow-Origin': '*',
                            }
                        })
                        .then(async res => {
                            let data;

                            try {
                                data = await res.json();
                            } catch {
                                document.querySelector('#wloader').style.display =
                                    'none';
                                throw new Error("Respuesta inesperada del servidor");
                            }

                            if (res.ok && data.url) {
                                window.location.href = data.url;
                            } else {
                                document.querySelector('#wloader').style.display =
                                    'none';
                                Swal.fire("Error", data.error ??
                                    "No se pudo generar la sesión de pago",
                                    "error");
                            }
                        })
                        .catch(err => {
                            document.querySelector('#wloader').style.display = 'none';
                            Swal.fire("Error", err.message, "error");
                        });
                });
            });
        });
    </script>
@endpush
