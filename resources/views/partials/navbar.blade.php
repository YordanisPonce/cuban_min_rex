@php
    use App\Models\Cart;
@endphp

<script src="{{ asset('assets/vendor/js/dropdown-hover.js') }}"></script>
<script src="{{ asset('assets/vendor/js/mega-dropdown.js') }}"></script>

<nav class="layout-navbar shadow-none py-0">
    <div class="container">
        <div class="navbar navbar-expand-lg landing-navbar px-3 px-md-8">
            <!-- Menu logo wrapper: Start -->
            <div class="navbar-brand app-brand demo d-flex py-0 me-4 me-xl-8 ms-0">
                <!-- Mobile menu toggle: Start-->
                <button class="navbar-toggler border-0 px-0 me-4" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <i class="icon-base ti tabler-menu-2 icon-lg align-middle text-heading fw-medium"></i>
                </button>
                <!-- Mobile menu toggle: End-->
                <a href="{{ route('home') }}" class="app-brand-link d-none d-md-flex">
                    <span class="app-brand-logo demo">
                        <span class="text-primary">
                            <!-- Reemplaza el SVG con tu imagen PNG -->
                            <img src="{{ config('app.logo') }}" alt="{{ config('app.name') }}"
                                style="width: 100px; height: 50px; object-fit: contain; border-radius: 50%">
                        </span>
                    </span>
                    <span class="app-brand-text demo menu-text fw-bold ms-2 ps-1 d-none d-lg-inline">{{ config('app.name') }}</span>
                </a>
            </div>
            <!-- Menu logo wrapper: End -->
            <!-- Menu wrapper: Start -->
            <div class="collapse navbar-collapse landing-nav-menu" id="navbarSupportedContent">
                <button class="navbar-toggler border-0 text-heading position-absolute start-0 top-0 scaleX-n1-rtl p-2"
                    type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="app-brand-logo demo">
                        <span class="text-primary">
                            <!-- Reemplaza el SVG con tu imagen PNG -->
                            <img src="{{ config('app.logo') }}" alt="{{ config('app.name') }}"
                                style="width: 100px; height: 50px; object-fit: contain; border-radius: 50%">
                        </span>
                    </span>
                    <span class="app-brand-text demo menu-text fw-bold ms-2 ps-1 d-inline">{{ config('app.name') }}</span>
                </button>
                <button class="navbar-toggler border-0 text-heading position-absolute end-0 top-0 scaleX-n1-rtl p-2"
                    type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="icon-base ti tabler-x icon-lg"></i>
                </button>
                <ul class="navbar-nav me-auto mt-xs-10">
                    <!-- <li class="nav-item">
                        <a class="nav-link fw-medium" aria-current="page" href="{{ route('home') }}">Inicio</a>
                    </li> -->
                    <!-- <li class="nav-item">
                        <a class="nav-link fw-medium" href="{{ route('plans') }}">Membresias</a>
                    </li> -->
                    <!-- <li class="nav-item">
                        <a class="nav-link fw-medium" href="{{ route('collection.index') }}">Colecciones</a>
                    </li> -->
                    @if (isset($djs) && count($djs)>0)
                    <li class="nav-item dropdown ms-2">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="javascript:void(0);"
                            data-bs-toggle="dropdown">
                            <span class="ms-2 d-none d-md-block">DJ'S</span>
                        </a>
                        <ul class="dropdown-menu">
                            @foreach ($djs as $dj)
                            <li>
                                <a class="dropdown-item" href="{{ route('dj', $dj->id)}}">
                                    <span class="align-middle">{{ $dj->name }}</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="{{ route('remixes') }}">Remixes</a>
                    </li>
                    @isset($categories)
                        @foreach ($categories as $category)
                            <li class="nav-item">
                                <a class="nav-link fw-medium"
                                    href="{{ route('category.show', $category->id) }}">{{ $category->name }}</a>
                            </li>
                        @endforeach
                    @endisset
                    <!-- <li class="nav-item">
                        <a class="nav-link fw-medium" href="/faq">FAQ</a>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="/radio">Emisora</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link fw-medium" href="/contact">Contacto</a>
                    </li> -->
                </ul>
            </div>
            <div class="landing-menu-overlay d-lg-none"></div>
            <!-- Menu wrapper: End -->
            <!-- Toolbar: Start -->
            <ul class="navbar-nav flex-row align-items-center ms-auto gap-4 navbar-right">
             {{--    <li class="nav-item">
                    <form action="{{ route('search') }}" method="GET"
                        class="input-wrapper input-group input-group-merge position-relative mx-auto">
                        <span class="input-group-text" id="basic-addon1"><i
                                class="icon-base ti tabler-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Buscar"
                            aria-label="Search" aria-describedby="basic-addon1" />
                    </form>
                </li> --}}

                
                <li>
                    <a class="btn btn-primary align-center text-black" href="{{ route('cart') }}">
                        <span class="icon-base me-md-1" style="width: 18px !important;">{{ svg('vaadin-cart') }}</span>
                        <span class="d-none d-md-block">$ {{ number_format(Cart::get_current_cart()->get_cart_count(), 2, '.', '')}}</span>
                    </a>
                </li>
                @auth
                    <!-- navbar button: Start -->
                    <li class="nav-item dropdown ms-2">
                        <a class="nav-link dropdown-toggle d-flex align-items-center hide-arrow" href="javascript:void(0);"
                            data-bs-toggle="dropdown">
                            <div class="avatar overflow-hidden rounded-circle">
                                <img src="{{ Auth::user()->photo ? Auth::user()->photo : config('app.logo') }}" alt="Avatar" class="rounded-circle" />
                                <div class="dark-screen" style="background-color: rgba(0, 0, 0, 0.5);"></div>
                            </div>
                            <span class="ms-2 d-none d-md-block">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="icon-base ti tabler-user me-2"></i>
                                    <span class="align-middle">Perfil</span>
                                </a>
                            </li>
                            @if (Auth()->user()->role !== 'user')
                            <li>
                                <a class="dropdown-item" href="/admin" target="_blank">
                                    <i class="icon-base ti tabler-dashboard me-2"></i>
                                    <span class="align-middle">Panel de Administración</span>
                                </a>
                            </li>
                            @endif
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="icon-base ti tabler-logout me-2"></i>
                                        <span class="align-middle">Cerrar sesión</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li>
                        <a href="{{ route('login') }}" class="btn btn-primary text-black"><span
                                class="tf-icons icon-base ti tabler-login scaleX-n1-rtl me-md-1"></span><span
                                class="d-none d-md-block">Acceder / Registrar</span></a>
                    </li>
                @endauth
                <!-- navbar button: End -->
            </ul>
            <!-- Toolbar: End -->
        </div>
    </div>
</nav>
