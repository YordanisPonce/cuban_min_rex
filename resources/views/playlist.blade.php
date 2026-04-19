@extends('layouts.app')

@php
    use App\Enums\NumEnum;

    $cover = $playlist->cover
        ? $playlist->getCoverUrl()
        : ($playlist->user?->photo
            ? $playlist->user?->photo
            : config('app.logo'));

    $success = session('success');
    $error = session('error');
@endphp

@section('title', $playlist->name . ' – ' . config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/playlist-details.css') }}" />
    <style>
        .filters-bar {
            max-width: 1400px;
        }
        .btn-next,
        .btn-prev{
            color: white;
            cursor: pointer;
            transition: all .2s
        }
        .btn-next:hover,.btn-prev:hover {
            transform: scale(1.2);
            color: var(--primary)
        }
        .track-title{
            justify-content: space-between;
            display: flex;
            gap: 20px;
        }
    </style>
@endpush

@section('content')
    <div class="main"> 
        <div class="playlist-header">
            <img class="playlist-cover" src="{{ $cover }}" alt="Club Latino Mix">
            <div class="playlist-meta">
                <div class="playlist-label"><span class="track-genre"><i class="fas fa-list"></i> {{ $playlist->folder?->name ?? 'Playlist' }}</span></div>
                <h1 class="playlist-title">{{ $playlist->name }}</h1>
                <p class="playlist-desc">{{ $playlist->description }}</p>
                <div class="playlist-info-row">
                    <a class="playlist-creator" href="{{ route('dj', str_replace(' ', '_', $playlist->user->name)) }}"><img src="{{ $playlist->user->photo ?? config('app.logo') }}"
                            alt="{{ $playlist->user->name }}"><span>{{ $playlist->user->name }}</span></a>
                    <span><i class="fas fa-music"></i> {{ $playlist->items->count() }} pistas</span>
                    <span><i class="fas fa-download"></i> {{ NumEnum::letter_format($playlist->downloads->count()) }}
                        descargas</span>
                </div>
                <div class="playlist-actions">
                    <button class="btn-play-all" onclick="playAllTracks()"><i class="fas fa-play"></i> Reproducir
                        Todo</button>
                    <button class="btn-secondary" onclick="playRandom()"><i class="fas fa-random"></i> Aleatorio</button>
                    @if ($playlist->canBeDownload())
                        <a class="btn-secondary" href="{{ route('playlist.download', $playlist->name) }}"><i
                                class="fas fa-cart-plus"></i> Descargar Completa</a>
                    @else 
                        <a class="btn-secondary" href="{{ route('playlist.add.cart', $playlist->name) }}"><i
                                class="fas fa-cart-plus"></i> Comprar Todo – ${{ $playlist->price }}</a>
                    @endif
                </div>
            </div>
        </div>

        <h2 class="section-title"><i class="fas fa-list-ol"></i> Canciones</h2>
        <div class="track-header">
            <span>#</span><span>Título</span><span>Descargas</span><span
                style="text-align:center;">Precio</span><span></span>
        </div>
        <div id="trackList"></div>

        <div style="margin-top:3rem;">
            <h2 class="section-title"><i class="fas fa-th-large"></i> Playlists Similares</h2>
            <div class="similar-grid" id="similarGrid">
            </div>
            @if ($similar->count() === 0)
                <div style="text-align: center; color: var(--fg-muted); width: 100%">Sin recomendaciones</div>
            @endif
        </div>
    </div>
    @include('partials.playlist-bottom-player')
@endsection

@push('scripts')
    <script>
        const tracks = @json($tracks);

        const tl = document.getElementById('trackList');
        tracks.forEach((t, i) => {
            tl.innerHTML += `<div class="track-row" id="${t.id}">
    <div style="position:relative;text-align:center;"><span class="track-num">${i + 1}</span><div class="track-play-icon" onclick="handleCardPlay(${t.id})"><i class="fas fa-play"></i></div></div>
    <div class="track-info"><img class="track-cover" src="${t.img}" alt="${t.title}"><div style="min-width:0;"><div class="track-title">${t.title}</div><div class="track-artist">${t.artist}</div></div></div>
    <div class="track-downloads"><i class="fas fa-download" style="margin-right:3px;font-size:.65rem;"></i>${t.downloads}</div>
    <div class="track-price">${t.price}</div><div><a class="btn-secondary add-to-cart" href="${t.addToCart}"><i class="fas fa-cart-plus"></i> <span>Añadir</span></a></div>
  </div>`;
        });

        const similar = @json($similar);

        const sg = document.getElementById('similarGrid');

        similar.forEach(s => {
            sg.innerHTML += `<div class="similar-card" onclick=" window.location = '${s.url}'">
                <img src="${s.img}" alt="${s.title}" loading="lazy">
                <div class="similar-card-info"><div class="similar-card-title">${s.title}</div><div class="similar-card-sub">${s.tracks} pistas</div></div>
            </div>`;
        });

        let currentTrack = null;
        let isPlaying = false;
        let currentTime = 0;
        let duration = 0;
        let timerInterval = null;
        let randomPlay = false;

        const audioPlayer = document.getElementById('plyr-audio-player');

        function closePlayer() {
            const player = document.getElementById('bottom-player');
            player.classList.remove('active');
            document.querySelectorAll('.track-row').forEach(card => {
                card.classList.remove('playing');
                card.querySelector('.track-play-icon i').className = 'fa-solid fa-play';
            });
            isPlaying = false;
            audioPlayer.pause();
            currentTrack = null;
        }

        function updatePlayerUI() {
            isPlaying ? audioPlayer.play() : audioPlayer.pause();
            const el = document.getElementById('bottom-player');
            if (!currentTrack) {
                el.classList.remove('active');
                return
            }
            el.classList.add('active');
            document.getElementById('player-img').src = currentTrack.img;
            document.getElementById('player-title').textContent = '#' + (getRemixIndex(currentTrack.id) + 1) + ' ' +
                currentTrack.title;
            document.getElementById('player-artist').textContent = currentTrack.artist;
            document.querySelectorAll('.track-row').forEach(card => {
                const id = card.id;
                const icon = card.querySelector('.track-play-icon i');
                if (id === currentTrack.id && isPlaying) {
                    icon.className = 'fa-solid fa-pause';
                    card.classList.add('playing');
                } else {
                    icon.className = 'fa-solid fa-play';
                    card.classList.remove('playing');
                }
            });
        }

        function setLoader(e) {
            document.querySelectorAll('.track-row').forEach(card => {
                const id = card.id;
                const btn = card.querySelector('.track-play-icon i');
                if (id === e) {
                    btn.className = 'fa fa-spinner fa-spin';
                    card.classList.add('playing');
                }
            });
        }

        function playTrack(track) {
            setLoader(track.id);

            audio = new Audio(track.url);

            audio.addEventListener("canplaythrough", () => {
                audioPlayer.src = audio.src;
                audioPlayer.play();
            });

            audioPlayer.addEventListener("play", () => {
                currentTrack = track;
                currentTime = 0;
                duration = track.duration;
                isPlaying = true;
                updatePlayerUI();
            });

            audioPlayer.addEventListener("pause", () => {
                isPlaying = false;
                updatePlayerUI();
            });

            audioPlayer.addEventListener("ended", () => {
                if (currentTrack != tracks[tracks.length - 1]) {
                    playNextTrack();
                } else {
                    isPlaying = false;
                    updatePlayerUI();
                }
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

        function handleCardPlay(id) {
            if (currentTrack && currentTrack.id === id) {
                togglePlay();
                return
            }
            const r = tracks.find(x => x.id == id);
            if (r) playTrack(r);
            else Swal.fire("Error", "Track not found", "error");
        }

        function playAllTracks() {
            if (tracks.length > 0) {
                playTrack(tracks[0]);
                randomPlay = false;
            }
        }

        function playRandom() {
            if (tracks.length > 0) {
                const randomIndex = Math.floor(Math.random() * tracks.length);
                playTrack(tracks[randomIndex]);
                randomPlay = true;
            }
        }

        function getRemixIndex(id) {
            return tracks.findIndex(x => x.id == id);
        }

        function playNextTrack() {
            if (!currentTrack) return;
            if (randomPlay) {
                playRandom();
            } else {
                const idx = getRemixIndex(currentTrack.id);
                if (idx >= 0 && idx < tracks.length - 1) {
                    playTrack(tracks[idx + 1]);
                } else {
                    playTrack(tracks[0]);
                }
            }
        }

        function playPreviousTrack() {
            if (!currentTrack) return;
            if (randomPlay) {
                playRandom();
            } else {
                const idx = getRemixIndex(currentTrack.id);
                if (idx > 0) {
                    playTrack(tracks[idx - 1]);
                } else {
                    playTrack(tracks[tracks.length - 1]);
                }
            }
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
