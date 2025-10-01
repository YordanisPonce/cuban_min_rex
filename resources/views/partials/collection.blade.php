@php
    $o = $order ?? 'normal';

    // Copys dinámicos
    $badge = $badge ?? 'Explorar';
    $title = $title ?? 'Colecciones de artistas';
    $subtitle = $subtitle ?? 'Escucha a los que más suenan y descubre tu próximo favorito.';

    /**
     * $items (opcional): si lo pasas, controla el estado vacío.
     * Estructura sugerida por ítem:
     * [
     *   'image' => url, 'title' => 'Imagine Dragons', 'meta' => 'Banda', 'href' => '#'
     * ]
     */
    $items = $items ?? null;

    // Para reproducción completa
    // Si pasas ambos, se muestra el botón de play y se usa el endpoint para obtener la playlist.
    $collectionId = $collectionId ?? null; // e.g., $collection->id
    $playlistEndpoint = $playlistEndpoint ?? null; // e.g., route('collections.playlist', $collectionId)
@endphp

<section class="section-py bg-body landing-reviews pb-0 swiper">
    <div class="container">
        <div class="row align-items-center gx-0 gy-4 g-lg-5 mb-5 pb-md-5 flex-lg-row-{{ $o }}">

            {{-- Columna de textos y controles --}}
            <div class="col-md-6 col-lg-5 col-xl-3">
                <div class="mb-4 d-flex align-items-center justify-content-between">
                    <span class="badge bg-label-primary">{{ $badge }}</span>

                    @if ($collectionId && $playlistEndpoint)
                        <button id="{{ $id }}-play-btn" class="btn btn-sm btn-label-primary" type="button"
                            data-endpoint="{{ $playlistEndpoint }}" data-collection-id="{{ $collectionId }}">
                            <i class="ti tabler-player-play me-1"></i> Reproducir colección
                        </button>
                    @endif
                </div>

                <h4 class="mb-1">
                    <span class="position-relative fw-extrabold z-1">{{ $title }}
                        <img src="https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/front-pages/icons/section-title-icon.png"
                            alt="laptop charging"
                            class="section-title-img position-absolute object-fit-contain bottom-0 z-n1">
                    </span>
                </h4>

                <p class="mb-5 mb-md-12">{!! nl2br(e($subtitle)) !!}</p>

                {{-- Controles prev/next: se ocultan si $items está definido y vacío --}}
                @php $isEmpty = is_array($items) && count($items) === 0; @endphp

                @unless ($isEmpty)
                    <div class="landing-reviews-btns">
                        <button id="{{ $id }}-reviews-previous-btn"
                            class="btn btn-icon btn-label-primary reviews-btn me-3 waves-effect" type="button"
                            aria-label="Anterior">
                            <i class="icon-base ti tabler-chevron-left icon-md scaleX-n1-rtl"></i>
                        </button>
                        <button id="{{ $id }}-reviews-next-btn"
                            class="btn btn-icon btn-label-primary reviews-btn waves-effect" type="button"
                            aria-label="Siguiente">
                            <i class="icon-base ti tabler-chevron-right icon-md scaleX-n1-rtl"></i>
                        </button>
                    </div>
                @endunless

            </div>

            {{-- Columna carrusel / estado vacío --}}
            <div class="col-md-6 col-lg-7 col-xl-9">
                @if ($isEmpty)
                    <div class="border rounded-4 p-4 p-md-5 text-center bg-body">
                        <h3 class="h5 fw-bold mb-2">Aún no hay elementos aquí</h3>
                        <p class="text-body-secondary mb-0">Estamos preparando nuevas selecciones para esta sección.</p>
                    </div>
                @else
                    <div class="swiper-reviews-carousel overflow-hidden">
                        <div class="swiper swiper-initialized swiper-horizontal swiper-backface-hidden"
                            id="{{ $id }}">
                            <div class="swiper-wrapper" aria-live="off">
                                {{-- Si pasas $items, dibújalos; si no, usa tus slides actuales (de ejemplo) --}}
                                @if (is_array($items))
                                    @foreach ($items as $card)
                                        <div class="swiper-slide" style="width: 254px; margin-right: 26px;">
                                            <div class="card h-100">
                                                <div
                                                    class="card-body text-body d-flex flex-column justify-content-between h-100">
                                                    <div class="mb-4">
                                                        @if (!empty($card['image']))
                                                            <img src="{{ $card['image'] }}"
                                                                alt="{{ $card['title'] ?? 'Colección' }}">
                                                        @else
                                                            <div
                                                                class="w-100 ratio ratio-1x1 bg-body-secondary rounded">
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar me-3 avatar-sm">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                height="16" fill="currentColor"
                                                                class="bi bi-music-player" viewBox="0 0 16 16">
                                                                <path
                                                                    d="M4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1zm1 0v3h6V3zm3 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2" />
                                                                <path
                                                                    d="M11 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-3 2a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                                                                <path
                                                                    d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z" />
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0 text-truncate">
                                                                {{ $card['title'] ?? 'Colección' }}</h6>
                                                            @if (!empty($card['meta']))
                                                                <p class="small text-body-secondary mb-0">
                                                                    {{ $card['meta'] }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    {{-- Tus slides de ejemplo (como los tenías) --}}
                                    @include('partials.collection-slides-demo')
                                @endif
                            </div>

                            {{-- Flechas Swiper internas (las mantengo para compatibilidad) --}}
                            <div class="swiper-button-next" tabindex="0" role="button" aria-label="Next slide"></div>
                            <div class="swiper-button-prev" tabindex="0" role="button" aria-label="Previous slide">
                            </div>
                            <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <hr class="m-0 mt-6 mt-md-12">
</section>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Swiper solo si no está vacío
            const wrapper = document.querySelector('#{{ $id }} .swiper-wrapper');
            const hasSlides = !!wrapper;
            if (hasSlides) {
                const swiperReviews = new Swiper('#{{ $id }}', {
                    slidesPerView: 3,
                    spaceBetween: 26,
                    loop: true,
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                });

                const prevBtn = document.getElementById('{{ $id }}-reviews-previous-btn');
                const nextBtn = document.getElementById('{{ $id }}-reviews-next-btn');
                if (prevBtn && nextBtn) {
                    prevBtn.addEventListener('click', () => swiperReviews.slidePrev());
                    nextBtn.addEventListener('click', () => swiperReviews.slideNext());
                }
            }

            // Play colección
            const playBtn = document.getElementById('{{ $id }}-play-btn');
            if (playBtn) {
                playBtn.addEventListener('click', async () => {
                    const endpoint = playBtn.dataset.endpoint;
                    try {
                        const res = await fetch(endpoint, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        if (!res.ok) throw new Error('No se pudo obtener la playlist');
                        const list = await res.json();

                        // Normaliza formatos: [{url: '...'}, ...] o ['...']
                        const tracks = Array.isArray(list) ? list.map(x => (typeof x === 'string' ? {
                            url: x
                        } : x)) : [];
                        if (!tracks.length) {
                            return window.Swal ? Swal.fire('Sin pistas',
                                'Esta colección no tiene canciones aún.', 'info') : alert(
                                'Esta colección no tiene canciones aún.');
                        }

                        // Reproductor simple en serie
                        const audio = new Audio();
                        let i = 0;

                        const playIndex = (idx) => {
                            audio.src = tracks[idx].url;
                            audio.play().catch(err => {
                                console.error(err);
                                if (window.Swal) Swal.fire('Error',
                                    'No se pudo reproducir la pista.', 'error');
                            });
                        };

                        audio.addEventListener('ended', () => {
                            i++;
                            if (i < tracks.length) playIndex(i);
                        });

                        playIndex(i);
                    } catch (e) {
                        console.error(e);
                        return window.Swal ? Swal.fire('Error', 'No se pudo cargar la colección.',
                            'error') : alert('No se pudo cargar la colección.');
                    }
                });
            }
        });
    </script>
@endpush
