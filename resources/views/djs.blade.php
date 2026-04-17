@extends('layouts.app')

@section('title', 'Djs – ' . config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/djs.css') }}">
@endpush

@section('content')
    <!-- HERO -->
    <section class="hero container">
        <div class="hero-slides" id="heroSlides"></div>
        <div class="hero-gradient"></div>
        <div class="hero-gradient-b"></div>
        <div class="container">
            <h1 data-aos="fade-right" data-aos-delay="300">DJS<br><span class="accent">DESTACADOS</span></h1>
            <p data-aos="fade-right" data-aos-delay="500">Descubre a los mejores DJs disponibles para tus fiestas y eventos.</p>
            <a data-aos="zoom-right" data-aos-delay="700" class="btn-primary" style="padding:12px 28px;font-size:.9rem"
                href="{{ route('plans') }}"><i class="fas fa-crown"></i> REMIXES
                    EXCLUSIVOS</a>
            <div class="hero-stats">
                <span data-aos="fade-right" data-aos-delay="900"><span class="dot"></span><strong>+1000 tracks</strong> actualizados semanalmente. </span>
                <span data-aos="fade-right" data-aos-delay="1100">✓ ACTUALIZACIONES SEMANALES</span>
            </div>
        </div>
    </section>

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
            slide.innerHTML = `<img src="${src}" alt="DJ hero ${i + 1}">`;
            slidesEl.appendChild(slide);
        });

        function goToSlide(n) {
            document.querySelectorAll('.hero-slide').forEach((s, i) => s.classList.toggle('active', i === n));
            currentSlide = n;
        }
        setInterval(() => goToSlide((currentSlide + 1) % heroImages.length), 5000);
    </script>
@endpush