@extends('layouts.app')

@section('title', 'Djs – ' . config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/djs.css') }}">
@endpush

@section('content')
    <!-- HERO -->
    @include('partials.hero', [
        'baseDescription' => 'Descubre a los mejores DJs disponibles para tus fiestas y eventos.',
        'baseLinkText' => 'REMIXES EXCLUSIVOS',
        'baseLinkAddress' => route('remixes'),
    ])

    <div class="djs-container">
        <p class="page-subtitle"></p>

        <!-- FILTERS -->
        <form class="filters">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Buscar DJs..." name="search" id="searchInput" value="{{ request('search') }}" onchange="this.form.submit()">
            </div>
        </form>

        <!-- DJ GRID -->
        <div class="dj-grid" id="djGrid">
            @foreach ($djs as $dj)
                @include('partials.dj-card', ['item' => $dj])
            @endforeach
        </div>

        <div>
            {{ $djs->onEachSide(1)->links() }}
        </div>
    </div>
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
@endpush