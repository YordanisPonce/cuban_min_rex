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
                <a href="{{ route('home') }}" class="app-brand-link">
                    <span class="app-brand-logo demo">
                        <span class="text-primary">
                            <!-- Reemplaza el SVG con tu imagen PNG -->
                            <img src="{{ asset('assets/img/front-pages/icon/cubamix.svg') }}" alt="Cuban Mix Rex Logo"
                                style="width: 32px; height: 22px; object-fit: contain;">
                        </span>
                    </span>
                    <span class="app-brand-text demo menu-text fw-bold ms-2 ps-1 d-none d-md-inline">Cuban Mix
                        Rex</span>
                </a>
            </div>
            <!-- Menu logo wrapper: End -->
            <!-- Menu wrapper: Start -->
            <div class="collapse navbar-collapse landing-nav-menu" id="navbarSupportedContent">
                <button class="navbar-toggler border-0 text-heading position-absolute end-0 top-0 scaleX-n1-rtl p-2"
                    type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="icon-base ti tabler-x icon-lg"></i>
                </button>
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link fw-medium" aria-current="page" href="{{ route('home') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="/faq">Preguntas Frecuentes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="/contact">Contacto</a>
                    </li>
                    @isset($categories)
                        @foreach ($categories as $category)
                            <li class="nav-item">
                                <a class="nav-link fw-medium"
                                    href="{{ route('category.show', $category->id) }}">{{ $category->name }}</a>
                            </li>
                        @endforeach
                    @endisset
                </ul>
            </div>
            <div class="landing-menu-overlay d-lg-none"></div>
            <!-- Menu wrapper: End -->
            <!-- Toolbar: Start -->
            <ul class="navbar-nav flex-row align-items-center ms-auto gap-4">
                <li class="nav-item">
                    <form action="{{ route('search') }}" method="GET"
                        class="input-wrapper input-group input-group-merge position-relative mx-auto">
                        <span class="input-group-text" id="basic-addon1"><i
                                class="icon-base ti tabler-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Buscar"
                            aria-label="Search" aria-describedby="basic-addon1" />
                    </form>
                </li>

                <!-- navbar button: Start -->
                @auth
                    <li class="nav-item dropdown ms-2">
                        <a class="nav-link dropdown-toggle d-flex align-items-center hide-arrow"
                            href="javascript:void(0);" data-bs-toggle="dropdown">
                            <div class="avatar avatar-online">
                                <span
                                    class="avatar-initial rounded-circle bg-label-primary">{{ substr(Auth::user()->name, 0, 1) }}</span>
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
                            <li>
                                <a class="dropdown-item" href="/admin" target="_blank">
                                    <i class="icon-base ti tabler-dashboard me-2"></i>
                                    <span class="align-middle">Panel de Administración</span>
                                </a>
                            </li>
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
                        <a href="{{ route('login') }}" class="btn btn-primary"><span
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
