@extends('layouts.app')

@section('title', 'Djs – ' . config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/djs.css') }}">
@endpush

@section('content')<!-- HERO -->
    <section class="hero container">
        <div class="hero-bg">
            <img style="width: 100%" src="{{ asset('assets/img/hero-base.jpeg') }}" alt="hero-banner">
            <div class="overlay"></div>
        </div>
        <div class="container">
            <h1 data-aos="fade-right" data-aos-delay="300">DJS<br><span class="text-primary">DESTACADOS</span></h1>
            <p data-aos="fade-right" data-aos-delay="500">Descubre a los mejores DJs disponibles para tus fiestas y eventos.
            </p>
            <ul>
                <li data-aos="fade-right" data-aos-delay="700"><strong>+1000 tracks</strong> actualizados semanalmente.</li>
            </ul>
            <div class="hero-buttons">
                <a href="{{ route('plans') }}" data-aos="fade-right" data-aos-delay="1100" class="btn-primary"><i class="fas fa-crown"></i>
                    LOS MEJORES REMIXES</a>
                <a href="{{ route('plans') }}" data-aos="fade-right" data-aos-delay="1300" class="btn-outline"
                    style="display:inline-flex;align-items:center;gap:8px"><i class="fas fa-crown"></i>
                    VER PLANES</a>
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
