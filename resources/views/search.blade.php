@extends('layouts.app')
@php
    use Carbon\Carbon;
    use App\Models\Cart;
    Carbon::setLocale('es');
    $success = session('success');
    $error = session('error');
@endphp
@section('title', 'Resultado de Busqueda - ' . config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/search.css') }}" />
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

    <div class="container" style="padding: 2rem 1rem">
        <div class="search-header">
            <h1>Resultado de Busqueda - {{ request()->get('name') ?? '' }}:</h1>
            <form style="width: 100%">
                <input class="search" name="name" value="{{ request()->get('name') ?? ''  }}">
            </form>
        </div>
        <div class="results">
            @foreach ($results as $r)
                <div class="result-card">
                    <div class="result-meta">
                        <img src="{{ $r['img'] }}" alt="{{ $r['name'] }}" class="result-cover">
                        <div class="result-data">
                            <a class="result-name" href="{{ $r['url'] }}">{{ $r['name'] }}</a>
                            <div class="result-artist">
                                <img src="{{ $r['dj_logo'] }}" class="avatar">
                                <a class="result-artist" href="{{ route('dj', str_replace(' ','_',$r['artist'])) }}">{{ $r['artist'] }}</a>
                            </div>
                        </div>
                    </div>
                    <a class="btn btn-outline" href="{{ $r['url'] }}">VER <i class="fas fa-angles-right"></i></a>
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
