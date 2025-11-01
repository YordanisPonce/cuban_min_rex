{{-- resources/views/partials/collection.blade.php --}}

<section class="swiper pt-5" id="{{ $id }}">
    <div class="container" style="position: relative;">
        <a href="{{ $ctaHref }}" class="link-underline d-none d-md-block" style="position: absolute; right: 25px; top: 16px; z-index: 9999">{{ $ctaText }} →</a>
        <div class="row align-items-center gx-0 gy-4 g-lg-5 flex-lg-row-normal">
            {{-- Columna de textos y controles --}}
            <div class="col-md-6 col-lg-5 col-xl-3">
                <div class="mb-4 d-flex align-items-center justify-content-between">
                    <span class="badge bg-label-primary">{{ $badge }}</span>
                    <a href="{{ $ctaHref }}" class="link-underline float-end d-md-none">{{ $ctaText }} →</a>
                </div>

                <h4 class="mb-1">
                    <a class="position-relative fw-extrabold z-1" href="{{ $ctaHref }}">
                        {{ $title }}
                        <img src="https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/front-pages/icons/section-title-icon.png"
                            alt="icon" class="section-title-img position-absolute object-fit-contain bottom-0 z-n1">
                    </a>
                </h4>

                <p class="mb-5 mb-md-12">
                    {{ $subtitle }}
                </p>

                <div class="landing-reviews-btns">
                    <button id="collections-{{ $id }}-prev" class="btn btn-icon btn-label-primary reviews-btn me-3"
                        type="button" aria-label="Anterior">
                        <i class="icon-base ti tabler-chevron-left icon-md scaleX-n1-rtl"></i>
                    </button>
                    <button id="collections-{{ $id }}-next" class="btn btn-icon btn-label-primary reviews-btn" type="button"
                        aria-label="Siguiente">
                        <i class="icon-base ti tabler-chevron-right icon-md scaleX-n1-rtl"></i>
                    </button>
                </div>
            </div>

            <div class="col-md-6 col-lg-7 col-xl-9">
                <div class="swiper-reviews-carousel overflow-hidden">
                    <div class="swiper swiper-horizontal js-swiper" id="collections-{{ $id }}-swiper">
                        <div class="swiper-wrapper mt-5 mb-5" aria-live="off">
                            @foreach ($packs as $item)
                                <div class="swiper-slide" style="width: 254px; margin-right: 26px;">
                                    <div class="card h-100 js-collection-card" role="button" tabindex="0"
                                        aria-label="Reproducir Synth Nights">
                                        <div
                                            class="card-body text-body d-flex flex-column justify-content-between h-100">
                                            <a class="mb-4" href="{{ route('collection.show', $item->id)}}">
                                                <img src="{{ $item->image ? $item->image : asset('assets/img/favicon/icon.PNG') }}"
                                                    alt="Synth Nights" class="w-100 rounded" style="max-height: 150px">
                                            </a>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-3 avatar-sm">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                        height="16" fill="currentColor" class="bi bi-music-player"
                                                        viewBox="0 0 16 16">
                                                        <path
                                                            d="M4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1zm1 0v3h6V3zm3 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2" />
                                                        <path
                                                            d="M11 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-3 2a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                                                        <path
                                                            d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z" />
                                                    </svg>
                                                </div>
                                                <div class="flex-grow-1 text-truncate">
                                                    <h6 class="mb-0 text-truncate">{{ $item->name }}</h6>
                                                    <p class="small text-body-secondary mb-0">
                                                        {{ $item->category?->name ?? 'Sin Categoría'}} • {{ $item->files()->count() }} pistas </p>
                                                </div>
                                                <button class="btn btn-sm btn-label-primary ms-2 btn-play-collection" type="button" data-rute="{{ route('collections.playlist', $item)}}" data-state="paused" onclick="playList(this)">
                                                    <i class="ti tabler-player-play"></i>    
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            @foreach ($singles as $item)
                                @if (!$item->collection)
                                <div class="swiper-slide" style="width: 254px; margin-right: 26px;">
                                    <div class="card h-100 js-collection-card" role="button" tabindex="0"
                                        aria-label="Reproducir Synth Nights">
                                        <div
                                            class="card-body text-body d-flex flex-column justify-content-between h-100">
                                            <a class="mb-4">
                                                <img src="{{ $item->image ? $item->image : asset('assets/img/favicon/icon.PNG') }}"
                                                    alt="Synth Nights" class="w-100 rounded" style="max-height: 150px">
                                            </a>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-3 avatar-sm">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                        height="16" fill="currentColor" class="bi bi-music-player"
                                                        viewBox="0 0 16 16">
                                                        <path
                                                            d="M4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1zm1 0v3h6V3zm3 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2" />
                                                        <path
                                                            d="M11 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-3 2a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                                                        <path
                                                            d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z" />
                                                    </svg>
                                                </div>
                                                <div class="flex-grow-1 text-truncate">
                                                    <h6 class="mb-0 text-truncate">{{ $item->name }}</h6>
                                                    <p class="small text-body-secondary mb-0">
                                                        {{ $item->category?->name ?? 'Sin Categoría' }}</p>
                                                </div>
                                                <button id="{{ $item->id }}" class="btn btn-sm btn-label-primary ms-2 btn-play-collection" type="button" data-rute="{{ route('file.play', [ 'none' , $item->id])}}" data-state="paused" onclick="playAudio(this)">
                                                    <i class="ti tabler-player-play"></i>    
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
    <script>
        let tracks = [];
        
        const audioExtensions = ['.mp3', '.wav', '.ogg', '.m4a'];
        const videoExtensions = ['.mp4', '.avi', '.mov', '.wmv', '.mkv'];

        let currentAudio = null;
        let currentTrack = 0;

        function stopCurrentAudio() {
            if (currentAudio) {
                currentAudio.pause();
                currentAudio.currentTime = 0;
                currentAudio = null;
            }
        }

        function playNextTrack(currentTrackIndex, tracks, audioPlayer, audioSource) {
            if (currentTrackIndex < tracks.length) {
                audioSource.src = tracks[currentTrackIndex].url;
                audioPlayer.load();
                audioPlayer.play();
                currentTrackIndex++;
            } else {
                currentTrackIndex = 0;
            }
        }

        function playCollection() {
            let audioPlayer = document.createElement('audio');
            let audioSource = document.createElement('source');
            audioSource.type = 'audio/mpeg';
            audioPlayer.appendChild(audioSource);
            let currentTrackIndex = 0;
            currentAudio = audioPlayer;

            playNextTrack(currentTrackIndex, tracks, audioPlayer, audioSource);

            audioPlayer.addEventListener('ended', () => {
                playNextTrack(currentTrackIndex, tracks, audioPlayer, audioSource);
            });
        }

        function pauseCollection() {  
            currentAudio.pause();
        }

        function playList(element){
            const rute = element.dataset.rute;

            document.querySelectorAll('.btn-play-collection').forEach( button => {
                if(button.dataset.state === 'played' && button !== element){
                    pauseCollection();
                    button.dataset.state = 'paused';
                    button.innerHTML = '<i class="ti tabler-player-play"></i>';
                }
            });

            fetch(rute, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                tracks = data.filter(item => audioExtensions.includes(item.url.substring(item.url.lastIndexOf('.')).toLowerCase()));
                if(element.dataset.state === 'paused'){
                    playCollection();
                    element.dataset.state = 'played';
                    element.innerHTML = '<i class="ti tabler-player-pause"></i>';
                } else {
                    pauseCollection();
                    element.dataset.state = 'paused';
                    element.innerHTML = '<i class="ti tabler-player-play"></i>';
                }
            })
            .catch(error => {
                Swal.fire("Error", error.message, "error");
            });
        }

        function playAudio(element){
            let audio = document.createElement('audio');

            const rute = element.dataset.rute;

            document.querySelectorAll('.btn-play-collection').forEach( button => {
                if(button.dataset.state === 'played' && button !== element){
                    pauseCollection();
                    button.dataset.state = 'paused';
                    button.innerHTML = '<i class="ti tabler-player-play"></i>';
                }
            });

            fetch(rute, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                data = data.filter(item => item.id === parseInt(element.id));
                if(audioExtensions.includes(data[0].url.substring(data[0].url.lastIndexOf('.')).toLowerCase())){
                    if(element.dataset.state === 'paused'){
                        stopCurrentAudio();
                        audio.src = data[0].url;
                        audio.play();
                        currentAudio = audio;
                        element.dataset.state = 'played';
                        element.innerHTML = '<i class="ti tabler-player-pause"></i>';
                    } else {
                        stopCurrentAudio();
                        element.dataset.state = 'paused';
                        element.innerHTML = '<i class="ti tabler-player-play"></i>';
                    }
                }
            })
            .catch(error => {
                Swal.fire("Error", error.message, "error");
            });
        }
    </script>
    <script>
        var swiper = new Swiper("#collections-{{ $id }}-swiper", {
            slidesPerView: 1,
            spaceBetween: 20,

            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },

            breakpoints: {

                480: {
                    slidesPerView: 2,
                    spaceBetween: 20
                },

                1200: {
                    slidesPerView: 3,
                    spaceBetween: 20
                },

                1400: {
                    slidesPerView: 4,
                    spaceBetween: 20
                },
            },

            navigation: {
                nextEl: '#collections-{{ $id }}-next',
                prevEl: '#collections-{{ $id }}-prev',
            },

            pagination: {
                el: '.swiper-pagination',
                type: 'bullets',
            },
        });
    </script>
@endpush