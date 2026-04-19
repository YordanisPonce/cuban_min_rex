@extends('layouts.app')
@php
    use App\Models\Cart;
    $success = session('success');
    $error = session('error');
@endphp

@section('title', 'Inicio - ' . config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/home.css') }}" />
@endpush

@section('content')
    <!-- HERO -->
    <section class="hero container">
        <div class="hero-slides" id="heroSlides"></div>
        <div class="hero-gradient"></div>
        <div class="hero-gradient-b"></div>
        <div class="container">
            <h1 data-aos="fade-right" data-aos-delay="300">REMIXES<br>EXCLUSIVOS<br><span class="accent">PARA DJS
                    LATINOS</span></h1>
            <p data-aos="fade-right" data-aos-delay="500">Descarga edits, intros, mashups y más musical para hacer historia
                en la pista.</p>
            <a data-aos="zoom-right" data-aos-delay="700" class="btn-primary" style="padding:12px 28px;font-size:.9rem"
                href="{{ route('plans') }}"><i class="fas fa-crown"></i> REMIXES
                    EXCLUSIVOS</a>
            <div class="hero-stats">
                <span data-aos="fade-right" data-aos-delay="900"><span class="dot"></span> +1000 REMIXES
                    EXCLUSIVOS</span>
                <span data-aos="fade-right" data-aos-delay="1100">✓ ACTUALIZACIONES SEMANALES</span>
            </div>
        </div>
    </section>

    <!-- REMIXES NUEVOS -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <div class="section-title"><i class="fa-solid fa-fire"></i> REMIXES <span class="accent">RECIENTES</span>
                </div>
                <a href="{{ route('remixes') }}" class="btn-outline">VER TODOS LOS REMIXES »</a>
            </div>
            <div class="cards-grid" id="remixes-grid"></div>
        </div>
    </section>

    <!-- PLAYLISTS -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <div class="section-title"><i class="fa-solid fa-headphones"></i> PLAYLISTS <span class="accent">PARA
                        DJs</span></div>
                <a href="{{ route('playlist.index') }}" class="btn-outline">VER TODAS LAS PLAYLISTS »</a>
            </div>
            <div id="playlists-list"></div>
        </div>
    </section>

    <!-- TOP DESCARGADOS -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <div class="section-title"><i class="fa-solid fa-trophy"></i> TOP <span class="accent">DJS</span>
                </div><a href="{{ route('djs') }}" class="btn-outline">VER TODOS LOS DJS »</a>
            </div>
            <div class="cards-grid" id="top-djs-grid"></div>
        </div>
    </section>

    <!-- DJS DESTACADOS -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <div class="section-title"><i class="fa-solid fa-music"></i> SONIDOS <span class="accent">DESTACADOS</span>
                </div>
            </div>
            <div class="genres-scroll" id="genres-scroll"></div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta container">
        <div class="cta-bg">
            <img src="{{ asset('assets/img/hero-base.jpeg') }}" alt="cta-img">
            <div class="overlay"></div>
        </div>
        <div class="container">
            <h2 data-aos="zoom-in" data-aos-delay="300"><i class="fa-solid fa-bolt"></i> LLEVA TU DJ SET AL SIGUIENTE NIVEL
            </h2>
            <ul>
                <li data-aos="fade-right" data-aos-delay="1000"><i class="fa-solid fa-check"></i> Acceso a contenido
                    exclusivo de remixes premium</li>
                <li data-aos="fade-right" data-aos-delay="1100"><i class="fa-solid fa-check"></i> Recibe actualizaciones
                    semanales con nuevos tracks</li>
                <li data-aos="fade-right" data-aos-delay="1200"><i class="fa-solid fa-check"></i> Paga de forma parcial o
                    descarga sin límites</li>
                <li data-aos="fade-right" data-aos-delay="1300"><i class="fa-solid fa-check"></i> Pagos seguros y
                    encriptados</li>
            </ul>
        </div>
    </section>
    @include('partials.bottom-player')
@endsection

@push('scripts')
    <script>
        // ===== DATA =====

        const remixes = @json($newItems);

        const playlists = @json($playlists);

        const topDjs = @json($tops);

        const genres = @json($geners);

        // HERO SLIDESHOW
        const heroImages = @json($banners);
        
        const slidesEl = document.getElementById('heroSlides');
        let currentSlide = 0;
        heroImages.forEach((src, i) => {
            const slide = document.createElement('div');
            slide.className = 'hero-slide' + (i === 0 ? ' active' : '');
            slide.innerHTML = `<img src="${src}" alt="DJ hero ${i + 1}">`;
            slidesEl.appendChild(slide);
        });

        function goToSlide(n) {
            document.querySelectorAll('.hero-slide').forEach((s, i) => s.classList.toggle('active', i === n));
            currentSlide = n;
        }
        setInterval(() => goToSlide((currentSlide + 1) % heroImages.length), 5000);

        // ===== RENDER =====
        // Remixes
        let delay = 1;
        document.getElementById('remixes-grid').innerHTML = remixes.map(r => `
        <div class="remix-card" data-id="${r.id}"  data-aos="fade-up"  data-aos-delay="${ delay++*300 + 100}">
            <div class="thumb">
            <img src="${r.img}" alt="${r.title}" loading="lazy">
            ${r.isNew?'<span class="tag-new">NEW</span>':''}
            </div>
            <div class="info">
            <div class="title">${r.title}</div>
            <div class="artist">${r.artist}</div>
            <div class="genre">${r.genre}</div>
            <div class="mini-player">
                <button class="play-btn" onclick="handleCardPlay('${r.id}')"><i class="fa-solid fa-play"></i></button>
                <div class="waveform">
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                </div>
                <div class="mini-actions">
                    ${ r.canDownload ? '<a href="' + r.downloadLink + '" ><i class="fa-solid fa-download"></i></a>' : '<a href="' + r.addToCart +'" ><i class="ti tabler-shopping-cart-plus"></i></a>'}
                </div>
            </div>
            <div class="meta"><span>BPM · ${r.bpm}</span> <span>${ !r.canDownload ? '$ '+r.price : ''}</span></div>
            </div>
        </div>
        `).join('');

        // Playlists
        let prank = 0;
        delay = 1;
        document.getElementById('playlists-list').innerHTML = playlists.map(p => `
        <div class="playlist-row"   data-aos="fade-right"  data-aos-delay="${ delay++*300 + 100}">
            <div class="playlist-column">
                <span class="playlist-rank">#${++prank}</span>
                <div class="playlist-imgs">${p.imgs.map(i=>`<img src="${i}" alt="" loading="lazy">`).join('')}</div>
                <div class="playlist-info"><h3>${p.title}</h3><p>${p.sub}</p></div>
            </div>
            <div class="playlist-column">
                <div class="playlist-stats">
                    <span class="playlist-tag">${p.genre}</span>
                    <span class="playlist-genre"></span>
                    <span class="playlist-bpm"></span>
                    <span class="playlist-downloads"><i class="fa-solid fa-fire"></i> ${p.downloads}</span>
                </div>
                <a href="${p.route}" class="btn-primary">VER PLAYLIST</a>
            </div>
        </div>
        `).join('');

        // Top DJs
        delay = 1;
        document.getElementById('top-djs-grid').innerHTML = topDjs.map(d => `
        <div class="dj-card"  data-aos="fade-up"  data-aos-delay="${ delay++*300 + 100}">
            <div class="thumb"><img src="${d.img}" alt="${d.name}" loading="lazy"></div>
            <div class="info">
            <div class="name">${d.name}</div>
            <div class="genres">${d.genres}</div>
            <div class="actions">
                <a href="${d.route}" class="btn-primary" style="font-size:.75rem;padding:6px 14px">VER DJ</a>
                <p style="color:var(--fg-muted);cursor:pointer">${d.downloads} <i class="fa-solid fa-download"></i></p>
            </div>
            </div>
        </div>
        `).join('');

        // Genres
        delay = 0;
        document.getElementById('genres-scroll').innerHTML = genres.map(g => `
        <a href="${g.route}" class="genre-item"  data-aos="zoom-in"  data-aos-delay="${ delay++*100 + 100}">
            <span class="genre-icon"><i class="fa-solid ${g.icon}"></i></span>
            <span>${g.name}</span>
        </a>
        `).join('');

        // ===== PLAYER STATE =====
        let currentTrack = null;
        let isPlaying = false;
        let currentTime = 0;
        let duration = 0;
        let timerInterval = null;

        const audioPlayer = document.getElementById('plyr-audio-player');

        function closePlayer() {
            const player = document.getElementById('bottom-player');
            player.classList.remove('active');
            document.querySelectorAll('.remix-card').forEach(card => {
                const wf = card.querySelector('.waveform').classList.remove('playing');
                card.querySelector('.play-btn i').className = 'fa-solid fa-play';
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
                document.querySelectorAll('.remix-card').forEach(card => {
                    const wf = card.querySelector('.waveform');
                    wf.classList.remove('playing');
                });
                return
            }
            el.classList.add('active');
            document.getElementById('player-img').src = currentTrack.img;
            document.getElementById('player-title').textContent = currentTrack.title;
            document.getElementById('player-artist').textContent = currentTrack.artist;
            document.querySelectorAll('.remix-card').forEach(card => {
                const id = card.dataset.id;
                const icon = card.querySelector('.play-btn i');
                if (id === currentTrack.id && isPlaying) {
                    icon.className = 'fa-solid fa-pause'
                } else {
                    icon.className = 'fa-solid fa-play'
                }
                const wf = card.querySelector('.waveform');
                if (id === currentTrack.id && isPlaying) {
                    wf.classList.add('playing');
                } else {
                    wf.classList.remove('playing');
                }
            });
        }

        function setLoader(e) {
            document.querySelectorAll('.remix-card').forEach(card => {
                const id = card.dataset.id;
                const btn = card.querySelector('.play-btn i');
                if (id === e) {
                    btn.className = 'fa fa-spinner fa-spin';
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

        // ===== EVENT HANDLERS =====
        function handleCardPlay(id) {
            if (currentTrack && currentTrack.id === id) {
                togglePlay();
                return
            }
            const r = remixes.find(x => x.id === id);
            if (r) playTrack(r);
            else Swal.fire("Error", "Track not found", "error");
        }
    </script>
    @isset($error)
        <script>
            Swal.fire({
                title: 'Error al descargar el archivo',
                text: '{{ $error }}',
                icon: 'error'
            });
        </script>
    @endisset
@endpush
