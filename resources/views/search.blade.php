@extends('layouts.app')
@php
    use Carbon\Carbon;
    use App\Models\Cart;
    Carbon::setLocale('es');
    $success = session('success');
    $error = session('error');
@endphp

@section('title', 'Resultado de Busqueda ')
@section('robots', 'noindex, nofollow')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/search.css') }}" />
@endpush

@section('content')
    @include('partials.hero')

    <div class="container" style="padding: 2rem 1rem">
        <div class="search-header">
            <h1>Resultado de Busqueda - {{ request()->get('name') ?? '' }}:</h1>
            <form style="width: 100%">
                <input class="search" name="name" value="{{ request()->get('name') ?? ''  }}">
            </form>
        </div>
        <div class="results">
            @foreach ($results as $r)
                <div class="result-card" id="{{ $r['id'] }}" data-intro="{{ $r['intro'] }}">
                    <div class="result-meta">
                        <div class="result-thumb">
                            <img src="{{ $r['img'] }}" alt="{{ $r['name'] }}" class="result-cover">
                            @if ($r['intro'])
                                <div class="overlay" data-id="{{ $r['id'] }}" onclick="handlePlay(this.dataset.id)"><i class="fa fa-play"></i></div>
                            @endif
                        </div>
                        <div class="result-data">
                            <a class="result-name" href="{{ $r['url'] }}">{{ $r['name'] }}</a>
                            <div class="result-artist">
                                <img src="{{ $r['dj_logo'] }}" class="avatar">
                                <a class="result-artist" href="{{ route('dj', str_replace(' ','_',$r['artist'] ?? 'CubanPool')) }}">{{ $r['artist'] ?? 'CubanPool' }}</a>
                            </div>
                        </div>
                    </div>
                    <div>
                        @if($r['action'])
                            @if($r['canDownload'])
                                <a class="btn btn-outline" href="{{ $r['download'] }}"><i class="fas fa-download"></i></a>
                            @else
                                <a class="btn btn-outline" href="{{ $r['buy'] }}">$ {{ $r['price'] }} <i class="fas fa-shopping-cart"></i></a>
                            @endif
                        @else
                            <a class="btn btn-outline" href="{{ $r['url'] }}">VER <i class="fas fa-angles-right"></i></a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div style="padding: 1rem">
            @if ($results->count()===0)
                Sin Resultados de Busqueda
            @endif
            {{ $results->onEachSide(1)->links() }}
        </div>
    </div>

    @include('partials.bottom-player')
@endsection

@push('scripts')
    <script>
        // HERO SLIDESHOW
        const heroImages = @json($banners);

        const slidesEl = document.getElementById('heroSlides');
        let currentSlide = 0;
        heroImages.forEach((src, i) => {
            const slide = document.createElement('div');
            slide.className = 'hero-slide' + (i === 0 ? ' active' : '');
            slide.innerHTML = src.isVideo ? `<video src="${src.image}" alt="DJ hero ${i + 1}" autoplay muted loop></video>` : `<img src="${src.image}" alt="DJ hero ${i + 1}">`;
            slidesEl.appendChild(slide);
        });

        function goToSlide(n) {
            document.querySelectorAll('.hero-slide').forEach((s, i) => s.classList.toggle('active', i === n));
            currentSlide = n;
        }
        setInterval(() => goToSlide((currentSlide + 1) % heroImages.length), 5000);
    </script>
    <script>
        
        const audioPlayer = document.getElementById('plyr-audio-player');
        let currentTrack = null;
        let isPlaying = false;

        function cleanCards() {
            document.querySelectorAll('.result-card').forEach(card => {
                card.classList.remove('playing');
                card.querySelector('.overlay i').className = 'fa-solid fa-play';
            });
        }

        function closePlayer() {
            const player = document.getElementById('bottom-player');
            player.classList.remove('active');
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
                cleanCards();
                return
            }
            el.classList.add('active');
            let trackData = document.getElementById(currentTrack);
            document.getElementById('player-img').src = trackData.querySelector('img').src;
            document.getElementById('player-title').textContent = trackData.querySelector('.result-name').textContent;
            document.getElementById('player-artist').textContent = trackData.querySelector('.result-artist').textContent;
            // Update mini-player buttons
            document.querySelectorAll('.result-card').forEach(card => {
                const id = card.id;
                const icon = card.querySelector('.overlay i');
                if (id == currentTrack && isPlaying && icon) {
                    icon.className = 'fa-solid fa-pause';
                    card.classList.add('playing');
                } else {
                    icon.className = 'fa-solid fa-play';
                    card.classList.remove('playing');
                }
            });
        }

        function setLoader(e) {
            document.querySelectorAll('.result-card').forEach(card => {
                const id = card.id;
                const btn = card.querySelector('.overlay i');
                if (btn && id == e) {
                    btn.className = 'fa fa-spinner fa-spin';
                    card.classList.add('playing');
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
