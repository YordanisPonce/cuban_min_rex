@extends('layouts.app')
@php
    $success = session('success');
    $error = session('error');

    use Carbon\Carbon;
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
@section('title', 'Editar Perfil - ' . config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/edit-profile.css') }}" />
    <style>
        :root {
            --bg: #000;
            --bg-card: #000;
            --bg-elevated: #221f1a;
            --bg-input: #2a2520;
            --primary: #f5a623;
            --primary-hover: #e09010;
            --text: #ece5d8;
            --text-muted: #8a7e6b;
            --border: #2e2a24;
            --danger: #e74c3c;
            --success: #27ae60;
        }
    </style>
@endpush

@section('content')
    <div class="page-container" style="margin-top: 50px;">
        <!-- Header -->
        <div class="page-header">
            <div>
                <a class="back-btn" href="{{ route('profile.edit') }}"><i class="fas fa-arrow-left"></i></a>
                <h1>Editar Perfil</h1>
            </div>
            <div>
                <button class="btn btn-primary" onclick="saveChanges()">Guardar cambios</button>
            </div>
        </div>

        <!-- Avatar -->
        <div class="avatar-section">
            <div class="avatar-cover">
                <img id="cover_preview" src="{{ $user->cover ?? config('app.logo_alter') }}" alt="avatar">
                <input id="cover_input" type="file" name="cover" />
                <div class="overlay"></div>
            </div>
            <div class="avatar-editor">
                <img id="photo_preview" src="{{ $user->photo ?? config('app.logo_alter') }}" alt="avatar">
                <input id="photo_input" type="file" name="photo" style="width: 0px" />
                <div class="edit-overlay" onclick="changeAvatar()"><i class="fas fa-pencil"></i></div>
            </div>
            <div class="avatar-info">
                <h3>{{ $user->name }}</h3>
                <p>Miembro desde {{ Carbon::parse($user->created_at)->format('M \d\e Y') }}</p>
                <div class="avatar-actions">
                    <button class="btn btn-primary" onclick="changeCover()"><i class="fas fa-image"></i> Cambiar
                        Portada</button>
                    <a class="btn btn-outline" href="{{ route('profile.restorePhoto') }}"
                        onclick="return confirm('¿Estás seguro de que quieres eliminar tu foto?');">Eliminar</a>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs-container">
            <div class="tabs-nav">
                <button class="tab-btn active" data-tab="personal">
                    <i class="fas fa-user"></i>&nbsp; Personal
                </button>
                <button class="tab-btn" data-tab="social">
                    <i class="fas fa-share-alt"></i>&nbsp; Redes Sociales
                </button>
                <button class="tab-btn" data-tab="seguridad">
                    <i class="fas fa-shield-alt"></i>&nbsp; Seguridad
                </button>
            </div>
        </div>

        <!-- Tab: Personal -->
        <div class="tab-content active" id="tab-personal">
            <div class="form-section">
                <div class="form-section-title">Información Personal</div>
                <div class="form-section-desc">Esta información será visible en tu perfil público.</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nombre <span class="required">*</span></label>
                        <input type="text" value="{{ $user->name }}" placeholder="Tu nombre" name="name">
                    </div>
                    <div class="form-group">
                        <label>Email <span class="required">*</span></label>
                        <input type="email" value="{{ $user->email }}" placeholder="tu@email.com" name="email">
                    </div>
                    <div class="form-group">
                        <label>Email PayPal</label>
                        <input type="email" value="{{ $user->paypal_email }}" placeholder="alternativo@email.com"
                            name="paypal_email">
                        <span class="hint"><i class="fas fa-info-circle"></i> Importante para monetizar</span>
                    </div>
                    <div class="form-group full">
                        <label>Biografía</label>
                        <textarea placeholder="Cuéntanos sobre ti..." name="bio">{{ $user->bio }}</textarea>
                        <span class="hint"><i class="fas fa-info-circle"></i> Máximo 500 caracteres</span>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title">Facturación</div>
                <div class="form-section-desc">Información de contacto privada usada para facturación, no se mostrará
                    públicamente.</div>
                <div class="form-grid">
                    <div class="form-group full">
                        <label>Dirección</label>
                        <input type="text" placeholder="Calle, número, municipio..."
                            value="{{ $user->billing?->address }}" name="address">
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="tel" value="{{ $user->billing?->phone }}" placeholder="+53 5 XXXXXXX"
                            name="phone">
                    </div>
                    <div class="form-group">
                        <label>País</label>
                        <select name="country">
                            @foreach ($countries as $country)
                                <option value="{{ $country }}"
                                    {{ $user->billing?->country === $country ? 'selected' : '' }}>{{ $country }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Código Postal</label>
                        <input type="text" value="{{ $user->billing?->postal }}" placeholder="Código postal"
                            name="postal">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Social -->
        <div class="tab-content" id="tab-social">
            <div class="form-section">
                <div class="form-section-title">Redes Sociales</div>
                <div class="form-section-desc">Conecta tus redes sociales para que tus seguidores te encuentren.</div>
                <div class="social-row"><i class="fab fa-instagram"></i><input type="url"
                        value="{{ $user->socialLinks?->instagram }}" placeholder="https://instagram.com/..."
                        name="instagram"></div>
                <div class="social-row"><i class="fab fa-tiktok"></i><input type="url"
                        value="{{ $user->socialLinks?->tiktok }}" placeholder="https://tiktok.com/@..." name="tiktok">
                </div>
                <div class="social-row"><i class="fab fa-youtube"></i><input type="url"
                        value="{{ $user->socialLinks?->youtube }}" placeholder="https://youtube.com/@..."
                        name="youtube">
                </div>
                <div class="social-row"><i class="fab fa-spotify"></i><input type="url"
                        value="{{ $user->socialLinks?->spotify }}" placeholder="https://open.spotify.com/artist/..."
                        name="spotify"></div>
                <div class="social-row"><i class="fab fa-facebook"></i><input type="url"
                        value="{{ $user->socialLinks?->facebook }}" placeholder="https://facebook.com/..."
                        name="facebook">
                </div>
                <div class="social-row"><i class="fab fa-x"></i><input type="url"
                        value="{{ $user->socialLinks?->twitter }}" placeholder="https://x.com/..." name="twitter"></div>
                <div class="social-row"><i class="fas fa-globe"></i><input type="url"
                        value="{{ $user->socialLinks?->site }}" placeholder="https://tusitio.com" name="site"></div>
            </div>
        </div>

        <!-- Tab: Seguridad -->
        <div class="tab-content" id="tab-seguridad">
            <div class="form-section">
                <div class="form-section-title">Cambiar Contraseña</div>
                <div class="form-section-desc">Asegúrate de usar una contraseña segura y única.</div>
                <div class="form-grid">
                    <div class="form-group full">
                        <label>Contraseña actual <span class="required">*</span></label>
                        <input type="password" placeholder="••••••••" name="current_password">
                    </div>
                    <div class="form-group">
                        <label>Nueva contraseña <span class="required">*</span></label>
                        <input type="password" placeholder="Mínimo 8 caracteres" name="new_password">
                    </div>
                    <div class="form-group">
                        <label>Confirmar contraseña <span class="required">*</span></label>
                        <input type="password" placeholder="Repite la contraseña" name="confirm_password">
                    </div>
                </div>
            </div>
            {{-- 
            <div class="form-section danger-zone">
                <div class="form-section-title"><i class="fas fa-exclamation-triangle" style="margin-right:8px"></i>Zona
                    de
                    peligro</div>
                <div class="form-section-desc">Acciones irreversibles sobre tu cuenta.</div>
                <div class="toggle-row">
                    <div class="toggle-info">
                        <h4>Eliminar cuenta permanentemente</h4>
                        <p>Se borrarán todos tus datos. Esta acción no se puede deshacer.</p>
                    </div>
                    <button class="btn-sm btn-outline-sm">Eliminar</button>
                </div>
            </div>
            --}}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Tabs
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
            });
        });

        function collectData() {
            const data = {
                name: document.querySelector('input[name="name"]').value,
                email: document.querySelector('input[name="email"]').value,
                paypal_email: document.querySelector('input[name="paypal_email"]').value,
                bio: document.querySelector('textarea[name="bio"]').value,
                address: document.querySelector('input[name="address"]').value,
                phone: document.querySelector('input[name="phone"]').value,
                country: document.querySelector('select[name="country"]').value,
                postal: document.querySelector('input[name="postal"]').value,
                instagram: document.querySelector('input[name="instagram"]').value,
                tiktok: document.querySelector('input[name="tiktok"]').value,
                youtube: document.querySelector('input[name="youtube"]').value,
                spotify: document.querySelector('input[name="spotify"]').value,
                facebook: document.querySelector('input[name="facebook"]').value,
                twitter: document.querySelector('input[name="twitter"]').value,
                site: document.querySelector('input[name="site"]').value,
                current_password: document.querySelector('input[name="current_password"]').value,
                new_password: document.querySelector('input[name="new_password"]').value,
                confirm_password: document.querySelector('input[name="confirm_password"]').value,
            };
            return data;
        }

        function saveChanges() {
            const formData = new FormData();

            const data = collectData();
            for (const key in data) {
                formData.append(key, data[key]);
            }

            const photoInput = document.querySelector('input[name="photo"]');
            const coverInput = document.querySelector('input[name="cover"]');

            if (photoInput.files.length > 0) {
                formData.append('photo', photoInput.files[0]);
            }

            if (coverInput.files.length > 0) {
                formData.append('cover', coverInput.files[0]);
            }

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'POST');

            fetch('{{ route('profile.update') }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function changeAvatar() {
            document.getElementById('photo_input').click();
        }

        document.getElementById('photo_input').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('photo_preview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        function changeCover() {
            document.getElementById('cover_input').click();
        }

        document.getElementById('cover_input').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('cover_preview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
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
