@extends('layouts.app')

@php
    use Carbon\Carbon;
    use App\Enums\NotificationTypeEnum;
    Carbon::setLocale('es');
    $success = session('success');
    $error = session('error');
@endphp

@section('title', 'Notificaciones - ' . config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/notification-center.css') }}">
@endpush

@section('content')
    <div class="main">
        <div class="page-header">
            <h1><i class="fas fa-envelope" style="color: var(--primary); margin-right: 10px;"></i>Notificaciones de Email</h1>
            <p>Gestiona tus preferencias y revisa las notificaciones recibidas</p>
        </div>

        <div class="content-grid">

            <div class="sidebar" id="sidebar">
                <button class="sidebar-item active" data-view="all">
                    <i class="fas fa-inbox"></i> Todas
                    <span class="count muted">{{ $notifications->count() }}</span>
                </button>
                <button class="sidebar-item" data-view="remixes">
                    <i class="fas fa-music"></i> Remixes
                    <span class="count muted">{{ $notifications->where('type', 'remixes')->count() }}</span>
                </button>
                <button class="sidebar-item" data-view="social">
                    <i class="fas fa-users"></i> Social
                    <span class="count muted">{{ $notifications->where('type', 'social')->count() }}</span>
                </button>
                <button class="sidebar-item" data-view="buy">
                    <i class="fas fa-shopping-bag"></i> Compras
                    <span class="count muted">{{ $notifications->where('type', 'buy')->count() }}</span>
                </button>
                <button class="sidebar-item" data-view="system">
                    <i class="fas fa-cog"></i> Sistema
                    <span class="count muted">{{ $notifications->where('type', 'system')->count() }}</span>
                </button>
                <button class="sidebar-item" data-view="promotional">
                    <i class="fas fa-bullhorn"></i> Promociones
                    <span class="count muted">{{ $notifications->where('type', 'promotional')->count() }}</span>
                </button>
                <button class="sidebar-item" data-view="preferences">
                    <i class="fas fa-sliders-h"></i> Preferencias
                </button>
            </div>

            <div class="notifications-panel">

                <div id="notif-view">
                    <div class="panel-header">
                        <h2 id="panel-title">Todas las notificaciones</h2>
                        <div class="panel-actions">
                            <a href="{{ route('ntfs.read.all') }}"><i class="fas fa-check-double"></i> Marcar todo leído</a>
                            <a href="{{ route('ntfs.delete.all') }}"><i class="fas fa-trash-alt"></i> Limpiar</a>
                        </div>
                    </div>
                    <div class="filter-tabs">
                        <button class="filter-tab active" data-filter="all">Todas</button>
                        <button class="filter-tab" data-filter="unread">No leídas</button>
                        <button class="filter-tab" data-filter="read">Leídas</button>
                    </div>
                    <div class="notification-list" id="notification-list">
                        @foreach ($notifications as $n)
                            <div class="notification-item {{ !$n->was_readed ? 'unread' : '' }}"
                                data-read="{{ $n->was_readed }}" data-view="{{ $n->type }}">
                                <div class="notif-icon {{ $n->type }}">
                                    <i
                                        class="fas fa-{{ $n->type === 'remix' ? 'music' : ($n->type === 'social' ? 'user' : ($n->type === 'buy' ? 'shopping-bag' : ($n->type === 'system' ? 'shield-alt' : 'bullhorn'))) }}"></i>
                                </div>
                                <div class="notif-content">
                                    <h4>{{ $n->title }}</h4>
                                    <p>{{ $n->mesage }}</p>
                                    <div class="notif-meta">
                                        <span class="notif-time"><i class="far fa-clock"></i>
                                            {{ Carbon::parse($n->created_at)->diffForHumans() }}</span>
                                        <span
                                            class="notif-tag {{ $n->type }}">{{ NotificationTypeEnum::getTransformName($n->type) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="empty-state">
                            <i class="fas fa-bell-slash"></i>
                            <h3>Sin notificaciones</h3>
                            <p>No tienes notificaciones en esta categoría por el momento.</p>
                        </div>
                    </div>
                </div>

                <!-- Email preferences view -->
                <form class="email-prefs" id="prefs-view" method="POST" action="{{ route('ntfs.update') }}">
                    @csrf
                    <div class="panel-header" style="padding: 0 0 20px; margin-bottom: 1rem;">
                        <h2>Preferencias de Email</h2>
                        @if (auth()->user()->hasActivePlan())  
                            <input type="submit" class="btn btn-primary" style="cursor: pointer" value="Guardar cambios">
                        @endif
                    </div>

                    <div class="prefs-section">
                        <h3>Notificaciones de contenido</h3>
                        <p>Controla qué notificaciones de contenido recibes por email</p>
                        <div class="pref-item {{ !auth()->user()->hasActivePlan() ? 'disabled' : '' }}">
                            <div>
                                @if (!auth()->user()->hasActivePlan())
                                    <i class="fas fa-crown text-primary"></i>
                                @endif
                                <div class="pref-info">
                                    <h4><i class="fas fa-music" style="color: var(--primary); margin-right: 8px;"></i>Nuevos
                                        remixes</h4>
                                    <p>Cuando DJs que sigues suben nuevos remixes</p>
                                </div>
                            </div>
                            @if (auth()->user()->hasActivePlan())   
                                <label class="toggle-switch"><input type="checkbox" name="new_remixes"
                                        {{ $prefers->new_remixes ? 'checked' : '' }}><span
                                        class="toggle-slider"></span></label>
                            @else
                                <label class="toggle-switch"><input type="checkbox" disabled><span class="toggle-slider"></span></label>   
                            @endif
                        </div>
                        <div class="pref-item {{ !auth()->user()->hasActivePlan() ? 'disabled' : '' }}">
                            <div>
                                @if (!auth()->user()->hasActivePlan())
                                    <i class="fas fa-crown text-primary"></i>
                                @endif
                                <div class="pref-info">
                                    <h4><i class="fas fa-list" style="color: var(--info); margin-right: 8px;"></i>Nuevas
                                        playlists</h4>
                                    <p>Cuando DJs que sigues suben nuevos playlist</p>
                                </div>
                            </div>
                            @if (auth()->user()->hasActivePlan())
                                <label class="toggle-switch"><input type="checkbox" name="new_playlist"
                                        {{ $prefers->new_playlist ? 'checked' : '' }}><span
                                        class="toggle-slider"></span></label>
                            @else
                                <label class="toggle-switch"><input type="checkbox" disabled><span class="toggle-slider"></span></label>   
                            @endif
                        </div>
                    </div>

                    <div class="prefs-section">
                        <h3>Notificaciones sociales</h3>
                        <p>Actividad social y de comunidad</p>
                        <div class="pref-item {{ !auth()->user()->hasActivePlan() ? 'disabled' : '' }}">
                            <div>
                                @if (!auth()->user()->hasActivePlan())
                                    <i class="fas fa-crown text-primary"></i>
                                @endif
                                <div class="pref-info">
                                    <h4><i class="fas fa-user-plus"
                                            style="color: var(--info); margin-right: 8px;"></i>Nuevos
                                        seguidores
                                    </h4>
                                    <p>Cuando alguien comienza a seguirte</p>
                                </div>
                            </div>
                            @if (auth()->user()->hasActivePlan())  
                                <label class="toggle-switch"><input type="checkbox" name="new_followers"
                                        {{ $prefers->new_followers ? 'checked' : '' }}><span
                                        class="toggle-slider"></span></label>
                            @else
                                <label class="toggle-switch"><input type="checkbox" disabled><span class="toggle-slider"></span></label>
                            @endif
                        </div>
                    </div>

                    <div class="prefs-section">
                        <h3>Notificaciones de compras</h3>
                        <p>Transacciones y facturación</p>
                        <div class="pref-item {{ !auth()->user()->hasActivePlan() ? 'disabled' : '' }}">
                            <div>
                                @if (!auth()->user()->hasActivePlan())
                                    <i class="fas fa-crown text-primary"></i>
                                @endif
                                <div class="pref-info">
                                    <h4><i class="fas fa-tag"
                                            style="color: var(--warning); margin-right: 8px;"></i>Ofertas y
                                        descuentos
                                    </h4>
                                    <p>Promociones exclusivas y descuentos especiales</p>
                                </div>
                            </div>
                            @if (auth()->user()->hasActivePlan())
                                <label class="toggle-switch"><input type="checkbox" name="promos"
                                    {{ $prefers->promos ? 'checked' : '' }}><span class="toggle-slider"></span></label> 
                            @else
                                <label class="toggle-switch"><input type="checkbox" disabled><span class="toggle-slider"></span></label>   
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="toast" id="toast"></div>
@endsection

@push('scripts')
    <script>
        function showToast(msg) {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 3000);
        }

        const notifications = document.querySelectorAll('.notification-item');
        const filterBtns = document.querySelectorAll('.filter-tab');
        const viewBtns = document.querySelectorAll('.sidebar-item');
        const emptyState = document.querySelector('.empty-state');

        let currentView = null;

        if (notifications.length === 0) {
            emptyState.style.display = 'flex';
        }

        viewBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                emptyState.style.display = 'none';
                viewBtns.forEach(b => {
                    b.classList.remove('active');
                });
                btn.classList.add('active');
                if (btn.dataset.view === 'preferences') {
                    document.getElementById('notif-view').style.display = 'none';
                    document.getElementById('prefs-view').style.display = 'block';
                } else {
                    document.getElementById('prefs-view').style.display = 'none';
                    document.getElementById('notif-view').style.display = 'block';
                    if (btn.dataset.view === 'all') {
                        currentView = null;
                    } else {
                        currentView = btn.dataset.view;
                    }
                    let nts = 0;
                    notifications.forEach(n => {
                        if (currentView != null) {
                            if (n.dataset.view === currentView) {
                                n.style.display = 'flex';
                                nts++;
                            } else {
                                n.style.display = 'none';
                            }
                        } else {
                            n.style.display = 'flex';
                            nts++;
                        }
                    });
                    if (nts === 0) {
                        emptyState.style.display = 'flex';
                    }
                }
            })
        })

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                emptyState.style.display = 'none';
                filterBtns.forEach(b => {
                    b.classList.remove('active');
                });
                btn.classList.add('active');
                let nts = 0;
                switch (btn.dataset.filter) {
                    case 'unread':
                        notifications.forEach(n => {
                            if (currentView != null) {
                                if (n.dataset.view === currentView) {
                                    if (n.dataset.read == 1) {
                                        n.style.display = 'none';
                                    } else {
                                        n.style.display = 'flex';
                                        nts++;
                                    }
                                } else {
                                    n.style.display = 'none';
                                }
                            } else {
                                if (n.dataset.read == 1) {
                                    n.style.display = 'none';
                                } else {
                                    n.style.display = 'flex';
                                    nts++;
                                }
                            }
                        });
                        break;

                    case 'read':
                        notifications.forEach(n => {
                            if (currentView != null) {
                                if (n.dataset.view === currentView) {
                                    if (n.dataset.read == 0) {
                                        n.style.display = 'none';
                                    } else {
                                        n.style.display = 'flex';
                                        nts++;
                                    }
                                } else {
                                    n.style.display = 'none';
                                }
                            } else {
                                if (n.dataset.read == 0) {
                                    n.style.display = 'none';
                                } else {
                                    n.style.display = 'flex';
                                    nts++;
                                }
                            }
                        });
                        break;

                    default:
                        notifications.forEach(n => {
                            n.style.display = 'flex';
                            nts++;
                        });
                        break;
                }
                if (nts === 0) {
                    emptyState.style.display = 'flex';
                }
            })
        })
    </script>
    @isset($success)
        <script>
            showToast("{{ $success }}");
        </script>
    @endisset
    @isset($error)
        <script>
            showToast("{{ $error }}");
        </script>
    @endisset
@endpush
