@extends('layouts.app')

@section('title', 'Planes - '.config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/plans.css') }}" />
@endpush

@section('content')
    <!-- HERO -->
    <section class="hero container">
        <div class="hero-slides" id="heroSlides"></div>
        <div class="hero-gradient"></div>
        <div class="hero-gradient-b"></div>
        <div class="container">
            <h1 data-aos="fade-right" data-aos-delay="300">LLEVA <span class="accent">TU MÚSICA</span> AL
                <br>SIGUIENTE NIVEL
            </h1>
            <p data-aos="fade-right" data-aos-delay="500">Más de 10,000 remixes exclusivos para DJs afrolatinos. <br> Todo
                lo que necesitas para hacer estallar la pista.</p>
            <a data-aos="zoom-right" data-aos-delay="700" class="btn-primary" style="padding:12px 28px;font-size:.9rem"
                href="{{ route('remixes') }}">+ 11000 REMIXES EXCLUSIVOS</a>
            <div class="hero-stats">
                <span data-aos="fade-right" data-aos-delay="1000"><i class="fa-solid fa-check text-primary"></i> Acceso a
                    contenido exclusivo de remixes premium</span>
                <span data-aos="fade-right" data-aos-delay="1100"><i class="fa-solid fa-check text-primary"></i> Recibe
                    actualizaciones semanales con nuevos tracks</span>
                <span data-aos="fade-right" data-aos-delay="1200"><i class="fa-solid fa-check text-primary"></i> Paga de
                    forma parcial o descarga sin límites</span>
                <span data-aos="fade-right" data-aos-delay="1300"><i class="fa-solid fa-check text-primary"></i> Pagos
                    seguros y encriptados</span>
            </div>
        </div>
    </section>

    <!-- FEATURES -->
    <div class="features-row">
        <div class="feature-item">
            <div class="feature-icon"><i class="fas fa-download"></i></div>
            <h3>DESCARGA REMIXES<br>Y DJ <span class="gold">TOOLS</span></h3>
        </div>
        <div class="feature-item">
            <div class="feature-icon"><i class="fas fa-dollar-sign"></i></div>
            <h3>NUEVOS TRACKS<br><span class="gold">CADA SEMANA</span></h3>
        </div>
        <div class="feature-item">
            <div class="feature-icon"><i class="fas fa-lock"></i></div>
            <h3>ACCESO EXCLUSIVO<br>A <span class="gold">PLAYLISTS</span></h3>
        </div>
    </div>

    <!-- PRICING -->
    <section class="pricing-section">
        <div class="pricing-grid">
            @foreach ($plans as $plan)
                @include('partials.plans-card', ['item' => $plan])
            @endforeach
        </div>
    </section>

    <!-- BOTTOM NOTES -->
    <ul class="bottom-notes">
        <li><i class="fas fa-check"></i> Cancela en cualquier momento</li>
        <li><i class="fas fa-check"></i> Acceso instantáneo a todo el catálogo</li>
        <li><i class="fas fa-check"></i> Hecho por DJs cubanos, para <span class="gold">DJs reales</span></li>
        <li><i class="fas fa-check"></i> Pagos seguros y encriptados</li>
    </ul>

    <!-- CONTACT -->
    <div class="contact-bar" style="margin-bottom: 30px">
        <div class="icon"><i class="fas fa-headset"></i></div>
        <div>
            <p>¿TIENES DUDAS?</p>
            <span>Contáctanos al soporte - <a href="mailto:{{ config('contact.email') }}">Respondemos rápido</a></span>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', function(){
            let recomendedPlan = document.querySelector('.plan-card:nth-child(2)');
            recomendedPlan.classList.add('featured');
            let recomendedBadge = recomendedPlan.querySelector('.plan-badge');
            recomendedBadge.innerText = 'RECOMENDADO';
            recomendedBadge.style.display = 'inline-block';

            let premiunPlan = document.querySelector('.plan-card:nth-child(3) #icon');
            let icon = document.createElement('i');
            icon.className = "fas fa-gem diamond-icon";
            premiunPlan.appendChild(icon);
            
        });
    </script>
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