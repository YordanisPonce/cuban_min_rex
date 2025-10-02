{{-- resources/views/partials/collection.blade.php --}}

<section class="section-py bg-body landing-reviews pb-0 swiper">
    <div class="container">
        <div class="row align-items-center gx-0 gy-4 g-lg-5 mb-5 pb-md-5 flex-lg-row-normal">

            {{-- Columna de textos y controles --}}
            <div class="col-md-6 col-lg-5 col-xl-3">
                <div class="mb-4 d-flex align-items-center justify-content-between">
                    <span class="badge bg-label-primary">Explorar</span>
                </div>

                <h4 class="mb-1">
                    <span class="position-relative fw-extrabold z-1">
                        Colecciones de artistas (DEMO)
                        <img src="https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/front-pages/icons/section-title-icon.png"
                            alt="icon" class="section-title-img position-absolute object-fit-contain bottom-0 z-n1">
                    </span>
                </h4>

                <p class="mb-5 mb-md-12">
                    Haz clic en cualquier card para reproducir su playlist (sin endpoints, todo en frontend).
                </p>

                <div class="landing-reviews-btns">
                    <button id="collections-demo-prev" class="btn btn-icon btn-label-primary reviews-btn me-3"
                        type="button" aria-label="Anterior">
                        <i class="icon-base ti tabler-chevron-left icon-md scaleX-n1-rtl"></i>
                    </button>
                    <button id="collections-demo-next" class="btn btn-icon btn-label-primary reviews-btn" type="button"
                        aria-label="Siguiente">
                        <i class="icon-base ti tabler-chevron-right icon-md scaleX-n1-rtl"></i>
                    </button>
                </div>
            </div>

            {{-- Columna carrusel --}}
            <div class="col-md-6 col-lg-7 col-xl-9">
                <div class="swiper-reviews-carousel overflow-hidden">
                    <div class="swiper swiper-horizontal js-swiper" id="collections-demo-swiper">
                        <div class="swiper-wrapper" aria-live="off">

                            {{-- CARD 1 --}}
                            <div class="swiper-slide" style="width: 254px; margin-right: 26px;">
                                <div class="card h-100 js-collection-card"
                                    data-tracks='[
                       "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3",
                       "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3",
                       "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3"
                     ]'
                                    role="button" tabindex="0" aria-label="Reproducir Synth Nights">
                                    <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                                        <div class="mb-4">
                                            <img src="https://images.unsplash.com/photo-1511379938547-c1f69419868d?q=80&w=800&auto=format&fit=crop"
                                                alt="Synth Nights" class="w-100 rounded">
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3 avatar-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-music-player" viewBox="0 0 16 16">
                                                    <path
                                                        d="M4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1zm1 0v3h6V3zm3 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2" />
                                                    <path
                                                        d="M11 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-3 2a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                                                    <path
                                                        d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z" />
                                                </svg>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 text-truncate">Synth Nights</h6>
                                                <p class="small text-body-secondary mb-0">Electrónica • 3 pistas</p>
                                            </div>
                                            <button class="btn btn-sm btn-label-primary ms-2" type="button"
                                                aria-label="Play Synth Nights">
                                                <i class="ti tabler-player-play"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- CARD 2 --}}
                            <div class="swiper-slide" style="width: 254px; margin-right: 26px;">
                                <div class="card h-100 js-collection-card"
                                    data-tracks='[
                       "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-4.mp3",
                       "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-5.mp3",
                       "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-6.mp3"
                     ]'
                                    role="button" tabindex="0" aria-label="Reproducir Guitar Vibes">
                                    <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                                        <div class="mb-4">
                                            <img src="https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?q=80&w=800&auto=format&fit=crop"
                                                alt="Guitar Vibes" class="w-100 rounded">
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3 avatar-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-music-player" viewBox="0 0 16 16">
                                                    <path
                                                        d="M4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1zm1 0v3h6V3zm3 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2" />
                                                    <path
                                                        d="M11 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-3 2a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                                                    <path
                                                        d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z" />
                                                </svg>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 text-truncate">Guitar Vibes</h6>
                                                <p class="small text-body-secondary mb-0">Rock • 3 pistas</p>
                                            </div>
                                            <button class="btn btn-sm btn-label-primary ms-2" type="button"
                                                aria-label="Play Guitar Vibes">
                                                <i class="ti tabler-player-play"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- CARD 3 --}}
                            <div class="swiper-slide" style="width: 254px; margin-right: 26px;">
                                <div class="card h-100 js-collection-card"
                                    data-tracks='[
                       "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-7.mp3",
                       "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-8.mp3",
                       "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-9.mp3"
                     ]'
                                    role="button" tabindex="0" aria-label="Reproducir Lo-Fi Focus">
                                    <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                                        <div class="mb-4">
                                            <img src="https://images.unsplash.com/photo-1459749411175-04bf5292ceea?q=80&w=800&auto=format&fit=crop"
                                                alt="Lo-Fi Focus" class="w-100 rounded">
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3 avatar-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-music-player"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1zm1 0v3h6V3zm3 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2" />
                                                    <path
                                                        d="M11 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-3 2a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                                                    <path
                                                        d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z" />
                                                </svg>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 text-truncate">Lo-Fi Focus</h6>
                                                <p class="small text-body-secondary mb-0">Lo-Fi • 3 pistas</p>
                                            </div>
                                            <button class="btn btn-sm btn-label-primary ms-2" type="button"
                                                aria-label="Play Lo-Fi Focus">
                                                <i class="ti tabler-player-play"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- CARD 4 --}}
                            <div class="swiper-slide" style="width: 254px; margin-right: 26px;">
                                <div class="card h-100 js-collection-card"
                                    data-tracks='[
                       "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-10.mp3",
                       "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-11.mp3",
                       "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-12.mp3"
                     ]'
                                    role="button" tabindex="0" aria-label="Reproducir Chill Mix">
                                    <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                                        <div class="mb-4">
                                            <img src="https://images.unsplash.com/photo-1507874457470-272b3c8d8ee2?q=80&w=800&auto=format&fit=crop"
                                                alt="Chill Mix" class="w-100 rounded">
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3 avatar-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-music-player"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1zm1 0v3h6V3zm3 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2" />
                                                    <path
                                                        d="M11 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-3 2a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                                                    <path
                                                        d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z" />
                                                </svg>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 text-truncate">Chill Mix</h6>
                                                <p class="small text-body-secondary mb-0">Ambient • 3 pistas</p>
                                            </div>
                                            <button class="btn btn-sm btn-label-primary ms-2" type="button"
                                                aria-label="Play Chill Mix">
                                                <i class="ti tabler-player-play"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- Flechas internas del swiper --}}
                        <div class="swiper-button-next" tabindex="0" role="button" aria-label="Siguiente"></div>
                        <div class="swiper-button-prev" tabindex="0" role="button" aria-label="Anterior"></div>
                        <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="m-0 mt-6 mt-md-12">
</section>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ----- Player único y global -----
            window.__collectionPlayer = window.__collectionPlayer || {
                audio: new Audio(),
                queue: [],
                idx: 0,
                primed: false,
                title: '',
                async prime() {
                    if (this.primed) return;
                    try {
                        this.audio.muted = true;
                        this.audio.playsInline = true; // iOS
                        this.audio.crossOrigin = 'anonymous'; // CORS seguro
                        await this.audio.play().catch(() => {});
                        this.audio.pause();
                        this.audio.muted = false;
                        this.primed = true;
                    } catch (e) {}
                },
                setQueue(tracks, title = 'Colección') {
                    this.queue = (tracks || []).map(t => typeof t === 'string' ? ({
                        url: t
                    }) : t).filter(t => !!t.url);
                    this.idx = 0;
                    this.title = title;
                },
                _playCurrent() {
                    const t = this.queue[this.idx];
                    if (!t) return;
                    if (location.protocol === 'https:' && /^http:\/\//i.test(t.url)) {
                        notify('Contenido bloqueado', 'La pista es HTTP y la página es HTTPS. Usa URLs HTTPS.',
                            'warning');
                        return;
                    }
                    this.audio.src = t.url;
                    // Debug opcional:
                    // this.audio.controls = true; this.audio.style.position='fixed'; this.audio.style.bottom='12px'; this.audio.style.right='12px'; document.body.appendChild(this.audio);
                    this.audio.play().catch(err => {
                        console.error('Error al reproducir audio:', err);
                        notify('Error', 'No se pudo reproducir la pista. Revisa CORS/HTTPS.', 'error');
                    });
                },
                async playQueue(tracks, title = 'Colección') {
                    await this.prime();
                    this.setQueue(tracks, title);
                    if (!this.queue.length) return notify('Sin pistas',
                        'Esta colección no tiene canciones aún.', 'info');
                    this._playCurrent();
                }
            };
            const player = window.__collectionPlayer;

            // Avance automático al terminar cada pista
            player.audio.addEventListener('ended', () => {
                player.idx++;
                if (player.idx < player.queue.length) player._playCurrent();
                else notify('Finalizado', `Terminó: ${player.title}`, 'success');
            });

            function notify(title, text, icon = 'info') {
                if (window.Swal) return Swal.fire(title, text, icon);
                alert(`${title}\n${text}`);
            }

            // ----- Swiper + delegación de eventos -----
            const swiperEl = document.querySelector('#collections-demo-swiper');
            const hasWrapper = document.querySelector('#collections-demo-swiper .swiper-wrapper');
            if (hasWrapper) {
                const swiperReviews = new Swiper('#collections-demo-swiper', {
                    slidesPerView: 3,
                    spaceBetween: 26,
                    loop: true,
                    navigation: {
                        nextEl: '#collections-demo-swiper .swiper-button-next',
                        prevEl: '#collections-demo-swiper .swiper-button-prev',
                    },
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false
                    },
                    preventClicks: false,
                    preventClicksPropagation: false,
                    threshold: 5,
                    breakpoints: {
                        0: {
                            slidesPerView: 1.1,
                            spaceBetween: 16
                        },
                        576: {
                            slidesPerView: 2,
                            spaceBetween: 20
                        },
                        992: {
                            slidesPerView: 3,
                            spaceBetween: 26
                        },
                    }
                });

                // Botones externos
                const prevBtn = document.getElementById('collections-demo-prev');
                const nextBtn = document.getElementById('collections-demo-next');
                if (prevBtn && nextBtn) {
                    prevBtn.addEventListener('click', () => swiperReviews.slidePrev());
                    nextBtn.addEventListener('click', () => swiperReviews.slideNext());
                }

                // Delegación: clic sobre cualquier .js-collection-card (incluye slides clonados)
                swiperEl.addEventListener('click', async (evt) => {
                    const card = evt.target.closest('.js-collection-card');
                    if (!card || !swiperEl.contains(card)) return;

                    try {
                        await player.prime();
                        const raw = card.getAttribute('data-tracks');
                        const title = (card.querySelector('h6')?.textContent || 'Colección').trim();

                        let tracks = [];
                        if (raw) {
                            try {
                                const arr = JSON.parse(raw);
                                tracks = Array.isArray(arr) ? arr : [];
                            } catch {
                                tracks = [];
                            }
                        }

                        await player.playQueue(tracks, title);
                        notify('Reproduciendo', `Iniciando: ${title}`, 'success');
                    } catch (err) {
                        console.error(err);
                        notify('Error', 'No se pudo reproducir la colección.', 'error');
                    }
                }, {
                    passive: true
                });
            }
        });
    </script>
@endpush
