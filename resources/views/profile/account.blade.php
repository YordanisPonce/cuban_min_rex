@extends('layouts.app')
@php
    $success = session('success');
    $error = session('error');

    use Carbon\Carbon;
@endphp

@section('title', 'Perfil - ' . config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('/assets/css/profile.css') }}" />
@endpush

@section('content')
    <div class="page-wrapper container">
        <!-- MAIN -->
        <div class="main-content">

            <!-- COVER / PROFILE HEADER -->
            <div class="profile-cover">
                <div class="profile-cover-overlay" style="
    background: linear-gradient(135deg, rgba(15, 13, 11, .5), rgba(15, 13, 11, .3)), url('{{ $user->cover ?? $user->photo ?? config('app.logo_alter') }}') center/cover;"></div>
                <div class="profile-header">
                    <div class="avatar-wrap">
                        <img src="{{ $user->photo ?? config('app.logo_alter') }}" alt="Avatar">
                    </div>
                    <div class="profile-info">
                        <div class="profile-level">
                            @if ($user->role == 'worker')
                                <i class="fas fa-music"></i> DJ
                            @elseif ($user->role == 'developer')
                                <i class="fas fa-tv"></i> DESARROLLO
                            @else
                                @if ($user->hasActivePlan())
                                    <i class="fas fa-crown"></i> VIP
                                @else
                                    <i class="fas fa-star"></i> MIEMBRO
                                @endif
                            @endif
                        </div>
                        <h1>{{ $user->name }}</h1>
                        <div class="handle"> </div>
                        <div class="profile-meta">
                            <span><i class="fas fa-calendar"></i>Miembro desde {{ Carbon::parse($user->created_at)->format('M \d\e\ Y') }}</span>
                        </div>
                    </div>
                </div>
                <a class="edit-btn" href="{{ route('profile.billing') }}"><i class="fas fa-pen"></i> <span>Editar Perfil</span></a>
                @if($user->role != 'user') <a class="panel-btn" href="{{ route('filament.admin.pages.dashboard') }}"><i class="fas fa-dashboard"></i> <span>Panel de Control</span></a> @endif
                <a class="logout-btn" href="{{ route('logout-user') }}"><i class="fas fa-right-from-bracket"></i> <span>Cerrar Sesión</span></a>
            </div>

            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-download"></i></div>
                    <div>
                        <div class="stat-value">{{ $user->downloads->count() }}</div>
                        <div class="stat-label">Descargadas Realizadas</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
                    <div>
                        <div class="stat-value">{{ $user->sales->count() }}</div>
                        <div class="stat-label">Compras Directas</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-crown"></i></div>
                    <div>
                        <div class="stat-value">{{ $subs }}</div>
                        <div class="stat-label">Suscripciones</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-crown"></i></div>
                    <div>
                        <div class="stat-value">{{ $currentPlan ?? 'Sin Plan Activo' }}</div>
                        <div class="stat-label">PLAN ACTUAL</div>
                    </div>
                </div>
            </div>

            <div class="two-col">
                <div class="section-card">
                    <div class="section-header">
                        <h3>Información Personal</h3>
                    </div>
                    <div class="info-list">
                        <div class="info-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <div class="info-label">Email</div>
                                <div class="info-value">{{ $user->email }} @if($user->email_verified_at) <span class="verified">✓ Verificado</span> @endif</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fa-brands fa-paypal"></i>
                            <div>
                                <div class="info-label">PayPal</div>
                                <div class="info-value">{{ $user->phone ?? 'Sin Establecer' }} <i class="fas fa-info-circle" style="font-size: 0.7rem; cursor: pointer" title="Necesario para monetizar"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-header">
                        <h3>Datos de Facturación    </h3>
                    </div>
                    <div class="info-list">
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <div class="info-label">Teléfono</div>
                                <div class="info-value">{{ $user->billing->phone ?? 'Sin Establecer' }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-location-dot"></i>
                            <div>
                                <div class="info-label">Dirección</div>
                                <div class="info-value">{{ $user->billing->address ?? 'Sin Establecer' }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-globe"></i>
                            <div>
                                <div class="info-label">País</div>
                                <div class="info-value">{{ $user->billing->country ?? 'Sin Establecer' }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-globe"></i>
                            <div>
                                <div class="info-label">Código postal</div>
                                <div class="info-value">{{ $user->billing->postal ?? 'Sin Establecer' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-header">
                        <h3>Redes Sociales</h3>
                    </div>
                    <div class="info-list">
                        <div class="info-item">
                            <i class="fab fa-facebook"></i>
                            <div>
                                <div class="info-value">{{ $user->socialLinks?->facebook ?? 'Sin Establecer' }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fab fa-instagram"></i>
                            <div>
                                <div class="info-value">{{ $user->socialLinks?->instagram ?? 'Sin Establecer' }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fab fa-youtube"></i>
                            <div>
                                <div class="info-value">{{ $user->socialLinks?->youtube ?? 'Sin Establecer' }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fab fa-tiktok"></i>
                            <div>
                                <div class="info-value">{{ $user->socialLinks?->tiktok ?? 'Sin Establecer' }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fab fa-spotify"></i>
                            <div>
                                <div class="info-value">{{ $user->socialLinks?->spotify ?? 'Sin Establecer' }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fab fa-x"></i>
                            <div>
                                <div class="info-value">{{ $user->socialLinks?->twitter ?? 'Sin Establecer' }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-globe"></i>
                            <div>
                                <div class="info-value">{{ $user->socialLinks?->site ?? 'Sin Establecer' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="two-col" style="grid-template-columns: 100%">
                <div class="section-card">
                    <div class="section-header">
                        <h3>Actividad Reciente</h3>
                    </div>
                    <div class="activity-list">
                        @foreach ($recentActivity as $activity)
                            <div class="activity-item">
                                <div>
                                    <div class="activity-icon">
                                        @if ($activity['type'] === 1) 
                                            <i class="fas fa-crown"></i>
                                        @else
                                            <i class="fas fa-download"></i>
                                        @endif
                                    </div>
                                    <div class="act-text">{{ $activity['title'] }}<br><strong>{{ $activity['description'] }}</strong></div>
                                </div>
                                <div class="act-price">
                                    <div class="act-amount"><i class="fas fa-dollar-sign"></i> {{ number_format($activity['amount'], 2) }}</div>
                                    <div class="act-status {{ $activity['status'] === 'paid' ? 'success' : ( $activity['status'] === 'pending' ? '' : 'danger' ) }}"> {{ $activity['status'] === 'paid' ? 'Completada' : ( $activity['status'] === 'pending' ? 'Pendiente' : 'Fallida' ) }}</div>
                                    <div class="act-time">{{ $activity['date'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')

    @isset($error)
        <script>
            Swal.fire({
                title: 'Error al enviar el formulario',
                text: '{{ $error }}',
                icon: 'error'
            });
        </script>
    @endisset
    @isset($success)
        <script>
            Swal.fire({
                title: 'Completado',
                text: '{{ $success }}',
                icon: 'success'
            });
        </script>
    @endisset
@endpush
