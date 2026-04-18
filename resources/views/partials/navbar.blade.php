@php
    use App\Models\Cart;
@endphp

<nav class="nav" id="navBar">
    <div class="container">
        <div class="nav-brand">
            <img class="app_logo" src="{{ config('app.logo') }}" alt="{{ config('app.name') }}">
            <span class="nav-brand-name">{{ config('app.name') }}</span>
        </div>
        <ul class="nav-links">
            <li><a href="{{ route('home') }}" class="{{$index===0 ? 'active' : ''}}">HOME</a></li>
            <li><a href="{{ route('djs') }}" class="{{$index===1 ? 'active' : ''}}">DJS</a></li>
            <li><a href="{{ route('remixes') }}" class="{{$index===2 ? 'active' : ''}}">REMIXES</a></li>
            <li><a href="{{ route('videos') }}" class="{{$index===3 ? 'active' : ''}}">VIDEOS</a></li>
            <li><a href="{{ route('playlist.index') }}" class="{{$index===4 ? 'active' : ''}}">PLAYLISTS</a></li>
            <li><a href="{{ route('collection.index') }}" class="{{$index===5 ? 'active' : ''}}">PACKS</a></li>
            <li><a href="{{ route('radio') }}" class="{{$index===6 ? 'active' : ''}}">EMISORA</a></li>
            <li><a href="{{ route('plans') }}" class="{{$index===7 ? 'active' : ''}}">PLANES</a></li>
        </ul>
        <div class="nav-right">
            <div id="google_translator"></div>
            <button class="icon-btn" onclick="document.getElementById('search').classList.toggle('active')"><i class="fa-solid fa-magnifying-glass" style="color:var(--fg-muted);cursor:pointer"></i></button>
            @auth
                <a href="{{ route('ntfs') }}" class="icon-btn">
                    <i class="fas fa-bell"></i>
                    <span class="badge-count">{{ auth()->user()->notifications()->where('was_readed', false)->count() }}</span>
                </a>
            @endauth
            <a href="{{ route('cart') }}" class="btn btn-primary btn-cart"><i class="ti tabler-shopping-cart"></i><span class="cart-badge">{{ Cart::get_current_cart()->cart_items->count() }}</span><span class="cart-amount">$ {{ number_format(Cart::get_current_cart()->get_cart_count(), 2) }}</span></a>
            @auth
                <a href="{{ route('profile.edit') }}" class="btn-primary"><i class="fa-solid fa-user"></i><span class="login-label"> PERFIL</a></a>
            @else  
                <a href="{{ route('login') }}" class="btn-primary"><i class="fa-solid fa-arrow-right-to-bracket"></i> <span class="login-label">ACCEDER</span></a>
            @endauth
            <button class="hamburger" id="navbarToggle"><i class="fa-solid fa-bars"></i></button>
        </div>
    </div>
    <div class="container">
        <ul class="mobile-nav-links">
            <li><a href="{{ route('home') }}" class="{{$index===0 ? 'active' : ''}}">HOME</a></li>
            <li><a href="{{ route('djs') }}" class="{{$index===1 ? 'active' : ''}}">DJS</a></li>
            <li><a href="{{ route('remixes') }}" class="{{$index===2 ? 'active' : ''}}">REMIXES</a></li>
            <li><a href="{{ route('videos') }}" class="{{$index===3 ? 'active' : ''}}">VIDEOS</a></li>
            <li><a href="{{ route('playlist.index') }}" class="{{$index===4 ? 'active' : ''}}">PLAYLISTS</a></li>
            <li><a href="{{ route('collection.index') }}" class="{{$index===5 ? 'active' : ''}}">PACKS</a></li>
            <li><a href="{{ route('radio') }}" class="{{$index===6 ? 'active' : ''}}">EMISORA</a></li>
            <li><a href="{{ route('plans') }}" class="{{$index===7 ? 'active' : ''}}">PLANES</a></li>
        </ul>
    </div>
</nav>

<div id="search" class="window-notice">
    <div class="content">
        <div class="search-card">
            <img src="{{ asset('assets/img/hero-base.jpeg') }}" alt="search-banner" />
            <div class="overlay"></div>
            <form action="{{ route('search') }}">
                <a id="search-modal-toggle" onclick="document.getElementById('search').classList.toggle('active')"><i class="fa-solid fa-close"></i></a>
                <div class="search-hero">
                    <h1 data-aos="fade-up" data-aos-delay="100"><i class="fa-solid fa-compact-disc text-primary"></i> {{ config('app.name') }}</h1>
                    <h3 data-aos="fade-right" data-aos-delay="400">REMIXES EXCLUSIVOS <span class="text-primary">PARA DJS LATINOS</span></h3>
                    <p data-aos="fade-right" data-aos-delay="600">Descarga edits, intro/outro, mashups y versiones listas para pista.</p>
                    <ul>
                        <li data-aos="fade-right" data-aos-delay="800"><strong>+1000 tracks</strong> actualizados semanalmente.</li>
                        <li data-aos="fade-right" data-aos-delay="1000"><strong>Formatos:</strong> Intro, Outro, Clean, Dirty, Acapella</li>
                    </ul>
                </div>
                <div class="input-group" data-aos="fade-up" data-aos-delay="1200">
                    <input type="text" name="name" placeholder="Bucar remix..."/>
                    <button type="submit" class="btn btn-primary"><span><i class="fa-solid fa-magnifying-glass"></i></span></button>
                </div>
            </form>
        </div>
    </div>
</div>
