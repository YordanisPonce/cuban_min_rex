@php
    $o = $order ?? 'normal';
@endphp

<!-- Our great team: Start -->
<section class="section-py bg-body landing-reviews pb-0 swiper">
    <div class="container">
        <div class="row align-items-center gx-0 gy-4 g-lg-5 mb-5 pb-md-5 flex-lg-row-{{ $o }}">
            <div class="col-md-6 col-lg-5 col-xl-3">
                <div class="mb-4">
                    <span class="badge bg-label-primary">
                        Colecciones de artistas
                    </span>
                </div>
                <h4 class="mb-1">
                    <span class="position-relative fw-extrabold z-1">Nestras colecciones
                        <img src="https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/front-pages/icons/section-title-icon.png"
                            alt="laptop charging"
                            class="section-title-img position-absolute object-fit-contain bottom-0 z-n1">
                    </span>
                </h4>
                <p class="mb-5 mb-md-12">Escucha a los artistas más sonados<br class="d-none d-xl-block">y descubre tu
                    próxima canción favorita.</p>
                <div class="landing-reviews-btns">
                    <button id="{{ $id }}-reviews-previous-btn"
                        class="btn btn-icon btn-label-primary reviews-btn me-3 waves-effect" type="button">
                        <i class="icon-base ti tabler-chevron-left icon-md scaleX-n1-rtl"></i>
                    </button>
                    <button id="{{ $id }}-reviews-next-btn"
                        class="btn btn-icon btn-label-primary reviews-btn waves-effect" type="button">
                        <i class="icon-base ti tabler-chevron-right icon-md scaleX-n1-rtl"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-6 col-lg-7 col-xl-9">
                <div class="swiper-reviews-carousel overflow-hidden">
                    <div class="swiper swiper-initialized swiper-horizontal swiper-backface-hidden"
                        id="{{ $id }}">
                        <div class="swiper-wrapper" id="swiper-wrapper-d5f7ff1684355bde" aria-live="off"
                            style="transition-duration: 0ms; transform: translate3d(-560px, 0px, 0px); transition-delay: 0ms;">

                            <div class="swiper-slide" role="group" aria-label="2 / 6"
                                style="width: 254px; margin-right: 26px;" data-swiper-slide-index="2">
                                <div class="card h-100">
                                    <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                                        <div class="mb-4">
                                            <img src="{{ asset('/assets/img/album/imagine-dragons.png') }}">
                                        </div>
                                         {{--     <div class="text-warning mb-8">
                                            <i class="icon-base ti tabler-star-filled"></i>
                                            <i class="icon-base ti tabler-star-filled"></i>
                                            <i class="icon-base ti tabler-star-filled"></i>
                                            <i class="icon-base ti tabler-star-filled"></i>
                                            <i class="icon-base ti tabler-star-filled"></i>
                                        </div> --}}
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
                                            <div>
                                                <h6 class="mb-0">Imagine Dragons</h6>
                                                <p class="small text-body-secondary mb-0">Banda</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="swiper-slide" role="group" aria-label="3 / 6"
                                style="width: 254px; margin-right: 26px;" data-swiper-slide-index="2">
                                <div class="card h-100">
                                    <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                                        <div class="mb-4">
                                            <img src="{{ asset('/assets/img/album/imagine-dragons.png') }}">
                                        </div>
                                       {{--     <div class="text-warning mb-8">
                                            <i class="icon-base ti tabler-star-filled"></i>
                                            <i class="icon-base ti tabler-star-filled"></i>
                                            <i class="icon-base ti tabler-star-filled"></i>
                                            <i class="icon-base ti tabler-star-filled"></i>
                                            <i class="icon-base ti tabler-star-filled"></i>
                                        </div> --}} 
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
                                            <div>
                                                <h6 class="mb-0">Imagine Dragons</h6>
                                                <p class="small text-body-secondary mb-0">Banda</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="swiper-slide" role="group" aria-label="4 / 6"
                                style="width: 254px; margin-right: 26px;" data-swiper-slide-index="2">
                                <div class="card h-100">
                                    <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                                        <div class="mb-4">
                                            <img src="{{ asset('/assets/img/album/imagine-dragons.png') }}">
                                        </div>
                                         {{--     <div class="text-warning mb-8">
                                            <i class="icon-base ti tabler-star-filled"></i>
                                            <i class="icon-base ti tabler-star-filled"></i>
                                            <i class="icon-base ti tabler-star-filled"></i>
                                            <i class="icon-base ti tabler-star-filled"></i>
                                            <i class="icon-base ti tabler-star-filled"></i>
                                        </div> --}}
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
                                            <div>
                                                <h6 class="mb-0">Imagine Dragons</h6>
                                                <p class="small text-body-secondary mb-0">Banda</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="swiper-slide" role="group" aria-label="5 / 6"
                                style="width: 254px; margin-right: 26px;" data-swiper-slide-index="2">
                                <div class="card h-100">
                                    <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                                        <div class="mb-4">
                                            <img src="{{ asset('/assets/img/album/imagine-dragons.png') }}">
                                        </div>
                                         {{--     <div class="text-warning mb-8">
                                            <i class="icon-base ti tabler-star-filled"></i>
                                            <i class="icon-base ti tabler-star-filled"></i>
                                            <i class="icon-base ti tabler-star-filled"></i>
                                            <i class="icon-base ti tabler-star-filled"></i>
                                            <i class="icon-base ti tabler-star-filled"></i>
                                        </div> --}}
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
                                            <div>
                                                <h6 class="mb-0">Imagine Dragons</h6>
                                                <p class="small text-body-secondary mb-0">Banda</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="swiper-slide" role="group" aria-label="6 / 6"
                                style="width: 254px; margin-right: 26px;" data-swiper-slide-index="2">
                                <div class="card h-100">
                                    <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                                        <div class="mb-4">
                                            <img src="{{ asset('/assets/img/album/imagine-dragons.png') }}">
                                        </div>
                                         {{--     <div class="text-warning mb-8">
                                            <i class="icon-base ti tabler-star-filled"></i>
                                            <i class="icon-base ti tabler-star-filled"></i>
                                            <i class="icon-base ti tabler-star-filled"></i>
                                            <i class="icon-base ti tabler-star-filled"></i>
                                            <i class="icon-base ti tabler-star-filled"></i>
                                        </div> --}}
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
                                            <div>
                                                <h6 class="mb-0">Imagine Dragons</h6>
                                                <p class="small text-body-secondary mb-0">Banda</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="swiper-button-next" tabindex="0" role="button" aria-label="Next slide"
                            aria-controls="swiper-wrapper-d5f7ff1684355bde"></div>
                        <div class="swiper-button-prev" tabindex="0" role="button" aria-label="Previous slide"
                            aria-controls="swiper-wrapper-d5f7ff1684355bde"></div>
                        <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- What people say slider: End -->
    <hr class="m-0 mt-6 mt-md-12">
    <!-- Logo slider: Start -->
</section>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const swiperReviews = new Swiper('#{{ $id }}', {
                slidesPerView: 3,
                spaceBetween: 26,
                loop: true,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                // Opcional: puedes añadir autoplay, paginación u otras configuraciones
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
            });

            // Control botones personalizados si los tienes aparte
            const prevBtn = document.getElementById('{{ $id }}-reviews-previous-btn');
            const nextBtn = document.getElementById('{{ $id }}-reviews-next-btn');

            if (prevBtn && nextBtn) {
                prevBtn.addEventListener('click', () => swiperReviews.slidePrev());
                nextBtn.addEventListener('click', () => swiperReviews.slideNext());
            }
        });
    </script>
@endpush
