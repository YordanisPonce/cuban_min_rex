@extends('layouts.app')
@php
    use App\Enums\NumEnum;
    use App\Enums\SectionEnum;
@endphp

@section('title', $dj->name . ' – ' . config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/dj-profile.css') }}">
    <style>
        .btn-hero {
            border: 2px solid var(--primary);
            color: var(--primary);
            border-radius: 8px;
            padding: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .btn-hero.active,
        .btn-hero:hover {
            color: white;
            cursor: pointer;
            background: var(--primary);
        }

        .btn-hero.disabled {
            cursor: default;
            opacity: 0.5;
        }

        .btn-hero.disabled:hover {
            background: transparent;
            color: var(--primary);
        }

        .dj-hero-actions {
            display: flex;
            align-items: flex-end;
            gap: 0.5rem;
        }

        .stat-value{
            display: flex;
            gap: 8px;
            align-items: center;
            justify-content: center;
        }
    </style>
@endpush

@section('content')<!-- HERO -->
    <div class="container" style="max-width:1300px; margin: 80px auto;">
        <!-- HERO -->
        <div class="dj-hero">
            <img class="dj-hero-img" src="{{ $dj->cover ?? ($dj->photo ?? config('app.logo_alter')) }}" alt="DJ Cover">
            <div class="dj-hero-overlay"></div>
            <div class="dj-hero-content">
                <img class="dj-avatar" src="{{ $dj->photo ?? config('app.logo_alter') }}" alt="{{ $dj->name }}">
                <div class="dj-hero-info">
                    <h1 class="dj-hero-name">{{ $dj->name }}</h1>
                    <p class="dj-hero-tagline" style="color: var(--fg-muted)">
                        {{ $dj->bio ?? 'Productor musical especializado en remixes de música latina. Con experiencia creando beats que hacen vibrar las pistas de baile.' }}
                    </p>
                    <div class="dj-hero-badges">
                        @foreach ($dj->files()->with('categories')->get()->pluck('categories')->flatten()->pluck('name')->unique()->take(10)->toArray() as $genre)
                            <span class="genre-badge">{{ $genre }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="dj-hero-actions">
                    @auth
                        @if (auth()->user()->id !== $dj->id)
                            @if (auth()->user()->hasActivePlan())
                                <button id="follow" class="btn-hero {{ $isFollow ? 'active' : '' }}" onclick="follow()"><i
                                        class="fas fa-plus"></i> Seguir</button>
                                <button id="ntf" class="btn-hero {{ $isNtf ? 'active' : '' }}" onclick="ntf()"><i
                                        class="fas fa-bell"></i></button>
                            @else
                                <button class="btn-hero disabled" disabled><i class="fas fa-crown"></i> Seguir</button>
                                <button class="btn-hero disabled" disabled><i class="fas fa-crown"></i> <i
                                        class="fas fa-bell"></i></button>
                            @endif
                            <button class="btn-hero {{ $review ? 'active' : '' }}" onclick="document.getElementById('form-rating').classList.toggle('active')"><i class="fas fa-star"></i></button>
                        @endif
                    @else
                        <button class="btn-hero disabled" disabled><i class="fas fa-crown"></i> Seguir</button>
                        <button class="btn-hero disabled" disabled><i class="fas fa-crown"></i> <i
                                class="fas fa-bell"></i></button>
                    @endauth
                </div>
            </div>
        </div>

        <!-- STATS -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-value">{{ NumEnum::letter_format($dj->files()->count()) }}</div>
                <div class="stat-label">Remixes</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">
                    {{ NumEnum::letter_format($dj->files()->with('categories')->get()->pluck('categories')->flatten()->pluck('name')->unique()->count()) }}
                </div>
                <div class="stat-label">Géneros</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ NumEnum::letter_format($dj->playlists()->count()) }}</div>
                <div class="stat-label">Playlists</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><i class="fas fa-plus" style="font-size: 1rem"></i>
                    {{ NumEnum::letter_format($dj->files()->sum('download_count')) }}</div>
                <div class="stat-label">Descargas</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">
                    {{ NumEnum::letter_format($dj->followers->count()) }}</div>
                <div class="stat-label">Seguidores</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><i class="fas fa-star" style="font-size: 1rem"></i>
                    {{ $dj->reviews->count() > 0 ? NumEnum::letter_format($dj->reviews->sum('rating')/$dj->reviews->count()) : 0 }} ({{ $dj->reviews->count() }})</div>
                <div class="stat-label">Valoración</div>
            </div>
        </div>

        @if ($dj->socialLinks)
            <div class="info-block" style="margin-bottom:2rem;">
                <h3 class="section-title"><i class="fas fa-link"></i> Redes Sociales</h3>
                <div class="social-links">
                        @if ($dj->socialLinks->facebook) <a class="social-link" href="{{ $dj->socialLinks->facebook }}"><i class="fab fa-facebook"></i></a> @endif
                        @if ($dj->socialLinks->instagram) <a class="social-link" href="{{ $dj->socialLinks->instagram }}"><i class="fab fa-instagram"></i></a> @endif
                        @if ($dj->socialLinks->youtube) <a class="social-link" href="{{ $dj->socialLinks->youtube }}"><i class="fab fa-youtube"></i></a> @endif
                        @if ($dj->socialLinks->tiktok) <a class="social-link" href="{{ $dj->socialLinks->tiktok }}"><i class="fab fa-tiktok"></i></a> @endif
                        @if ($dj->socialLinks->spotify) <a class="social-link" href="{{ $dj->socialLinks->spotify }}"><i class="fab fa-spotify"></i></a> @endif
                        @if ($dj->socialLinks->twitter) <a class="social-link" href="{{ $dj->socialLinks->twitter }}"><i class="fab fa-x"></i></a> @endif
                        @if ($dj->socialLinks->site) <a class="social-link" href="{{ $dj->socialLinks->site }}"><i class="fas fa-globe"></i></a> @endif
                </div>
            </div>
        @endif

        <!-- TABS + REMIXES -->
        <div class="tabs">
            <button class="tab-btn active" data-tab="remixes">TOP REMIXES</button>
            <button class="tab-btn" data-tab="playlists">TOP PLAYLISTS</button>
        </div>

        <!-- REMIXES TAB -->
        <div id="tab-remixes" class="tab-content">
            <table class="remix-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Título</th>
                        <th>Género</th>
                        <th>BPM</th>
                        <th>Descargas</th>
                        <th>Precio</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="remixBody">
                    @php $i = 0; @endphp
                    @foreach ($dj->files()->section(SectionEnum::MAIN->value)->orderBy('download_count')->take(5)->get() as $file)
                        <tr class="remix-row" id="{{ $file->id }}" data-intro="{{ $file->intro() }}">
                            <td style="color:var(--muted);font-weight:600;">{{ ++$i }}</td>
                            <td>
                                <div class="remix-info">
                                    <button class="remix-play" onclick="handlePlay({{ $file->id }})"><i
                                            class="fas fa-play"></i></button>
                                    <img class="remix-cover" src="{{ $file->getPosterUrl() ?? ($dj->photo ?? config('app.name')) }}"
                                        alt="{{ $file->name }}">
                                    <div>
                                        <div class="remix-title">{{ $file->name }}</div>
                                        <div class="remix-artist">{{ $file->user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @foreach ($file->categories as $genre)
                                    <span class="remix-genre">{{ $genre->name }}</span>
                                @endforeach
                            </td>
                            <td style="color:var(--muted);">{{ $file->bpm }}</td>
                            <td class="remix-download" style="text-align: center"><i class="fas fa-download"
                                    style="margin-right:4px;"></i>{{ $file->download_count }}
                            </td>
                            <td class="remix-price">{{ $file->price }}</td>
                            <td>
                                @if (auth()->check() && auth()->user()->hasActivePlan())
                                    <a class="btn-add-cart" href="{{ route('file.download', $file->id) }}"><i
                                            class="fas fa-download"></i> <span>Descargar</span></a>
                                @else
                                    <a class="btn-add-cart" href="{{ route('file.add.cart', $file->id) }}"><i
                                            class="fas fa-cart-plus"></i> <span>Añadir</span></a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div>
                @if ($dj->files->count() == 0)
                    <div style="text-align:center;padding:4rem 1rem;color:var(--fg-muted);">
                        <i class="fas fa-face-sad-cry" style="font-size:3rem;margin-bottom:1rem;"></i>
                        <div style="font-size:1.25rem;font-weight:600;margin-bottom:1rem;">No hay remixes disponibles</div>
                        <div style="font-size:.9rem;">Parece que este DJ no ha creado ningún remix aún.</div>
                    </div>
                @else
                    <div style="margin-top:1rem;text-align:center;">
                        <a class="btn btn-primary"
                            href="{{ route('remixes', ['dj' => str_replace(' ', '_', $dj->name)]) }}">VER MAS</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- PLAYLISTS TAB -->
        <div id="tab-playlists" class="tab-content" style="display:none;">
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:1rem;"
                id="playlistGrid">
                @foreach ($dj->playlists()->take(5)->get() as $playlist)
                    <div style="background:var(--card); border:1px solid var(--border); border-radius:12px; overflow:hidden; cursor:pointer; transition:transform .2s;"
                        onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform=''">
                        <img src="{{ $playlist->cover ? $playlist->getCoverUrl() : config('app.logo') }}"
                            alt="{{ $playlist->name }}" style="width:100%; aspect-ratio: 2; object-fit:cover;">
                        <div style="padding:1rem;">
                            <div style="font-weight:700;font-size:.9rem;margin-bottom:.25rem;"><i
                                    class="fa-solid fa-fire text-primary"></i> {{ $playlist->name }}</div>
                            <div style="font-size:.75rem;color:var(--muted);">{{ $playlist->items->count() }} pistas</div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div>
                @if ($dj->playlists->count() == 0)
                    <div style="text-align:center;padding:4rem 1rem;color:var(--fg-muted);">
                        <i class="fas fa-face-sad-cry" style="font-size:3rem;margin-bottom:1rem;"></i>
                        <div style="font-size:1.25rem;font-weight:600;margin-bottom:1rem;">No hay playlists disponibles
                        </div>
                        <div style="font-size:.9rem;">Parece que este DJ no ha creado ninguna playlist aún.</div>
                    </div>
                @else
                    <div style="margin-top:1rem;text-align:center;">
                        <a class="btn btn-primary"
                            href="{{ route('playlist.list', ['dj' => str_replace(' ', '_', $dj->name)]) }}">VER MAS</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- BOTTOM PLAYER -->
    <div class="bottom-player container" id="bottom-player">
        <div class="player-inner">
            <div class="player-track">
                <img id="player-img" src="" alt="">
                <div class="track-info">
                    <div class="track-title" id="player-title">—</div>
                    <div class="track-artist" id="player-artist">—</div>
                </div>
            </div>
            <div class="player-controls">
                <div class="waveform">
                    @for ($i = 0; $i < 60; $i++)
                        <div class="bar"></div>
                    @endfor
                </div>
                <div class="controls">
                    <button class="disabled"><i class="fa-solid fa-backward-fast"></i></button>
                    <!-- <button><i class="fa-solid fa-backward-step"></i></button> -->
                    <button class="main-play" id="player-play-btn"><i class="fa-solid fa-play"></i></button>
                    <!-- <button><i class="fa-solid fa-forward-step"></i></button> -->
                    <button class="disabled"><i class="fa-solid fa-forward-fast"></i></button>
                    <div class="close">
                        <button onclick="closePlayer()"><i class="fa-solid fa-close"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="form-rating" class="window-notice">
        <div class="content" style="background: var(--bg); border:1px solid var(--border); border-radius:8px; padding:2rem; display:flex; flex-direction:column; align-items:center; gap:0.5rem; max-width:400px;">
            <h4 style="width: 100%;display: flex; align-items: center; justify-content: space-between"><span>Valorar a {{ $dj->name }}</span><button style="cursor: pointer;" onclick="document.getElementById('form-rating').classList.toggle('active')"><i class="fas fa-close"></i></button></h4>
            <form class="review-form" method="POST" action="{{ route('rating.dj', $dj->id) }}">
                @csrf
                <div class="form-rating">
                    <div class="rating" id="rating">
                        <i class="{{ $review ? ($review->rating < 1 && $review->rating >= 0.5 ? 'fa-solid fa-star-half-stroke' : ($review->rating >= 1 ? 'fa-solid fa-star' : 'fa-regular fa-star')) : 'fa-regular fa-star' }} {{ $review && $review->rating >= 0.5 ? 'filled' : '' }}"
                            data-index="1"></i>
                        <i class="{{ $review ? ($review->rating < 2 && $review->rating >= 1.5 ? 'fa-solid fa-star-half-stroke' : ($review->rating >= 2 ? 'fa-solid fa-star' : 'fa-regular fa-star')) : 'fa-regular fa-star' }} {{ $review && $review->rating >= 1.5 ? 'filled' : '' }}"
                            data-index="2"></i>
                        <i class="{{ $review ? ($review->rating < 3 && $review->rating >= 2.5 ? 'fa-solid fa-star-half-stroke' : ($review->rating >= 3 ? 'fa-solid fa-star' : 'fa-regular fa-star')) : 'fa-regular fa-star' }} {{ $review && $review->rating >= 2.5 ? 'filled' : '' }}"
                            data-index="3"></i>
                        <i class="{{ $review ? ($review->rating < 4 && $review->rating >= 3.5 ? 'fa-solid fa-star-half-stroke' : ($review->rating >= 4 ? 'fa-solid fa-star' : 'fa-regular fa-star')) : 'fa-regular fa-star' }} {{ $review && $review->rating >= 3.5 ? 'filled' : '' }}"
                            data-index="4"></i>
                        <i class="{{ $review ? ($review->rating < 5 && $review->rating >= 4.5 ? 'fa-solid fa-star-half-stroke' : ($review->rating >= 5 ? 'fa-solid fa-star' : 'fa-regular fa-star')) : 'fa-regular fa-star' }} {{ $review && $review->rating >= 4.5 ? 'filled' : '' }}"
                            data-index="5"></i>
                    </div>
                    <input class="valor" id="valor" type="number" name="rating" min="0"
                        value="{{ $review ? $review->rating : 0 }}" max="5" step="0.5" readonly required>
                </div>
                <textarea class="comment-box" placeholder="Escribe tu comentario aquí..." name="comment" required>{{ $review ? $review->comment : '' }}</textarea>
                <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Enviar Reseña</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const audioPlayer = document.createElement('audio');

        // ===== PLAYER STATE =====
        let currentTrack = null;
        let isPlaying = false;
        let currentTime = 0;
        let duration = 0;
        let timerInterval = null;

        function cleanCards() {
            document.querySelectorAll('.remix-row').forEach(card => {
                card.querySelector('.remix-play i').className = 'fa-solid fa-play';
            });
        }

        function closePlayer() {
            const player = document.getElementById('bottom-player');
            player.classList.remove('active');
            player.querySelector(".waveform").classList.remove('playing');
            cleanCards();
            isPlaying = false;
            audioPlayer.pause();
            currentTrack = null;
        }

        function updatePlayerUI() {
            isPlaying ? audioPlayer.play() : audioPlayer.pause();
            const el = document.getElementById('bottom-player');
            if (!currentTrack) {
                el.classList.remove('active');
                el.querySelector(".waveform").classList.remove('playing');
                document.querySelectorAll('.track-card').forEach(card => {
                    const wf = card.querySelector('.waveform');
                    wf.classList.remove('playing');
                });
                return
            }
            el.classList.add('active');
            let waves = el.querySelector(".waveform");
            isPlaying ? waves.classList.add('playing') : waves.classList.remove('playing');
            let trackData = document.getElementById(currentTrack);
            document.getElementById('player-img').src = trackData.querySelector('img').src;
            document.getElementById('player-title').textContent = trackData.querySelector('.remix-title').textContent;
            document.getElementById('player-artist').textContent = trackData.querySelector('.remix-artist').textContent;
            const icon = document.querySelector('#player-play-btn i');
            icon.className = isPlaying ? 'fa-solid fa-pause' : 'fa-solid fa-play';
            // Update mini-player buttons
            document.querySelectorAll('.remix-row').forEach(card => {
                const id = card.id;
                const icon = card.querySelector('.remix-play i');
                if (id == currentTrack && isPlaying) {
                    icon.className = 'fa-solid fa-pause'
                } else {
                    icon.className = 'fa-solid fa-play'
                }
            });
        }

        function setLoader(e) {
            document.querySelectorAll('.remix-row').forEach(card => {
                const id = card.id;
                const btn = card.querySelector('.remix-play i');
                if (id == e) {
                    btn.className = 'fa fa-spinner fa-spin';
                }
            });
        }

        function playTrack(id, url) {
            setLoader(id);

            audio = new Audio(url);

            audio.addEventListener("canplaythrough", () => {
                audioPlayer.src = audio.src;
                audioPlayer.play();
            });

            audioPlayer.addEventListener("play", () => {
                currentTrack = id;
                currentTime = 0;
                duration = 120;
                isPlaying = true;
                updatePlayerUI();
            });

            audioPlayer.addEventListener("pause", () => {
                isPlaying = false;
                updatePlayerUI();
            });

            audioPlayer.addEventListener("ended", () => {
                isPlaying = false;
                updatePlayerUI();
            });
        }

        function togglePlay() {
            if (!currentTrack) return;
            isPlaying = !isPlaying;
            if (isPlaying) {
                audioPlayer.play();
            } else {
                audioPlayer.pause();
            }
            updatePlayerUI();
        }

        function handlePlay(id) {
            if (currentTrack && currentTrack === id) {
                togglePlay();
                return
            }
            cleanCards();
            let url = document.getElementById(`${id}`).dataset.intro;
            playTrack(id, url);
        }

        document.getElementById('player-play-btn').addEventListener('click', togglePlay);

        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
                document.getElementById('tab-' + btn.dataset.tab).style.display = 'block';
            });
        });
    </script>
    <script>
        const stars = document.querySelectorAll('#rating i');
        const valor = document.getElementById('valor');
        let currentRating = valor.value;

        stars.forEach(star => {
            star.addEventListener('mousemove', (e) => {
                const index = parseInt(star.getAttribute('data-index'));
                const rect = star.getBoundingClientRect();
                const offsetX = e.clientX - rect.left;
                const half = offsetX < rect.width / 2; // mitad izquierda = media estrella
                let val = index - (half ? 0.5 : 0);
                fillStars(val + (half ? 0.5 : 0));
            });

            star.addEventListener('click', (e) => {
                const index = parseInt(star.getAttribute('data-index'));
                const rect = star.getBoundingClientRect();
                const offsetX = e.clientX - rect.left;
                const half = offsetX < rect.width / 2;
                currentRating = index - (half ? 0.5 : 0);
                valor.value = currentRating;
            });
        });

        document.getElementById('rating').addEventListener('mouseleave', () => {
            fillStars(currentRating);
        });

        function fillStars(val) {
            stars.forEach(star => {
                const index = parseInt(star.getAttribute('data-index'));
                star.className = "fa-regular fa-star"; // reset
                if (index <= val) {
                    star.className = "fa-solid fa-star filled";
                } else if (index - 0.5 === val) {
                    star.className = "fa-solid fa-star-half-stroke filled";
                }
            });
        }
    </script>
    @auth
        @if (auth()->user()->hasActivePlan())
            <script>
                function follow() {
                    $btn = document.getElementById('follow');
                    $btn.classList.add('disabled');
                    $btn.querySelector('i').className = 'fa fa-spinner fa-spin';
                    fetch("{{ route('follow', str_replace(' ', '_', $dj->name)) }}", {
                            method: 'GET',
                        })
                        .then(response => response.json())
                        .then(data => {
                            $btn.classList.remove('disabled');
                            $btn.querySelector('i').className = 'fa fa-plus';
                            if (data.success) {
                                location.reload();
                            } else {
                                alert(data.error || 'Error al procesar la solicitud');
                            }
                        });
                }

                function ntf() {
                    $btn = document.getElementById('ntf');
                    $btn.classList.add('disabled');
                    $btn.querySelector('i').className = 'fa fa-spinner fa-spin';
                    fetch("{{ route('ntf', str_replace(' ', '_', $dj->name)) }}", {
                            method: 'GET',
                        })
                        .then(response => response.json())
                        .then(data => {
                            $btn.classList.remove('disabled');
                            $btn.querySelector('i').className = 'fa fa-bell';
                            if (data.success) {
                                location.reload();
                            }
                        });
                }
            </script>
        @endif
    @endauth
@endpush
