<script src="{{ asset('assets/vendor/js/dropdown-hover.js') }}"></script>
<script src="{{ asset('assets/vendor/js/mega-dropdown.js') }}"></script>

<nav class="layout-navbar shadow-none py-0">
  <div class="container">
    <div class="navbar navbar-expand-lg landing-navbar px-3 px-md-8">
      <!-- Menu logo wrapper: Start -->
      <div class="navbar-brand app-brand demo d-flex py-0 me-4 me-xl-8 ms-0">
        <!-- Mobile menu toggle: Start-->
        <button class="navbar-toggler border-0 px-0 me-4" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <i class="icon-base ti tabler-menu-2 icon-lg align-middle text-heading fw-medium"></i>
        </button>
        <!-- Mobile menu toggle: End-->
        <a href="{{ url('/') }}" class="app-brand-link">
          <span class="app-brand-logo demo">
            <span class="text-primary">
              <!-- Reemplaza el SVG con tu imagen PNG -->
              <img src="{{ asset('assets/img/front-pages/icon/cubamix.svg') }}" alt="Cuban Mix Rex Logo" style="width: 32px; height: 22px; object-fit: contain;">
            </span>
          </span>
          <span class="app-brand-text demo menu-text fw-bold ms-2 ps-1 d-none d-md-inline">Cuban Mix Rex</span>
        </a>
      </div>
      <!-- Menu logo wrapper: End -->
      <!-- Menu wrapper: Start -->
      <div class="collapse navbar-collapse landing-nav-menu" id="navbarSupportedContent">
        <button class="navbar-toggler border-0 text-heading position-absolute end-0 top-0 scaleX-n1-rtl p-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <i class="icon-base ti tabler-x icon-lg"></i>
        </button>
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link fw-medium" aria-current="page" href="#landingHero">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-medium" href="#landingFeatures">Features</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-medium" href="#landingTeam">Team</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-medium" href="#landingFAQ">FAQ</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-medium" href="#landingContact">Contact us</a>
          </li>
          <li class="nav-item mega-dropdown">
            <a href="javascript:void(0);" class="nav-link dropdown-toggle navbar-ex-14-mega-dropdown mega-dropdown fw-medium" aria-expanded="false" data-bs-toggle="mega-dropdown" data-trigger="hover">
              <span data-i18n="Pages">Pages</span>
            </a>
            <div class="dropdown-menu p-4 p-xl-8">
              <div class="row gy-4">
                <div class="col-12 col-lg">
                  <div class="h6 d-flex align-items-center mb-3 mb-lg-5">
                    <div class="avatar flex-shrink-0 me-3">
                      <span class="avatar-initial rounded bg-label-primary"><i class="icon-base ti tabler-layout-grid icon-lg"></i></span>
                    </div>
                    <span class="ps-1">Other</span>
                  </div>
                  <ul class="nav flex-column">
                    <li class="nav-item">
                      <a class="nav-link mega-dropdown-link" href="#">
                        <i class="icon-base ti tabler-circle me-1 icon-12px"></i>
                        <span data-i18n="Pricing">Pricing</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link mega-dropdown-link" href="#">
                        <i class="icon-base ti tabler-circle me-1 icon-12px"></i>
                        <span data-i18n="Payment">Payment</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link mega-dropdown-link" href="#">
                        <i class="icon-base ti tabler-circle me-1 icon-12px"></i>
                        <span data-i18n="Checkout">Checkout</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link mega-dropdown-link" href="#">
                        <i class="icon-base ti tabler-circle me-1 icon-12px"></i>
                        <span data-i18n="Help Center">Help Center</span>
                      </a>
                    </li>
                  </ul>
                </div>
                <div class="col-12 col-lg">
                  <div class="h6 d-flex align-items-center mb-3 mb-lg-5">
                    <div class="avatar flex-shrink-0 me-3">
                      <span class="avatar-initial rounded bg-label-primary"><i class="icon-base ti tabler-lock-open icon-lg"></i></span>
                    </div>
                    <span class="ps-1">Auth Demo</span>
                  </div>
                  <ul class="nav flex-column">
                    <li class="nav-item">
                      <a class="nav-link mega-dropdown-link" href="#">
                        <i class="icon-base ti tabler-circle me-1 icon-12px"></i>
                        Login (Basic)
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link mega-dropdown-link" href="#">
                        <i class="icon-base ti tabler-circle me-1 icon-12px"></i>
                        Login (Cover)
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link mega-dropdown-link" href="#">
                        <i class="icon-base ti tabler-circle me-1 icon-12px"></i>
                        Register (Basic)
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link mega-dropdown-link" href="#">
                        <i class="icon-base ti tabler-circle me-1 icon-12px"></i>
                        Register (Cover)
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link mega-dropdown-link" href="#">
                        <i class="icon-base ti tabler-circle me-1 icon-12px"></i>
                        Register (Multi-steps)
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link mega-dropdown-link" href="#">
                        <i class="icon-base ti tabler-circle me-1 icon-12px"></i>
                        Forgot Password (Basic)
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link mega-dropdown-link" href="#">
                        <i class="icon-base ti tabler-circle me-1 icon-12px"></i>
                        Forgot Password (Cover)
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link mega-dropdown-link" href="#">
                        <i class="icon-base ti tabler-circle me-1 icon-12px"></i>
                        Reset Password (Basic)
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link mega-dropdown-link" href="#">
                        <i class="icon-base ti tabler-circle me-1 icon-12px"></i>
                        Reset Password (Cover)
                      </a>
                    </li>
                  </ul>
                </div>
                <div class="col-12 col-lg">
                  <div class="h6 d-flex align-items-center mb-3 mb-lg-5">
                    <div class="avatar flex-shrink-0 me-3">
                      <span class="avatar-initial rounded bg-label-primary"><i class="icon-base ti tabler-file-analytics icon-lg"></i></span>
                    </div>
                    <span class="ps-1">Other</span>
                  </div>
                  <ul class="nav flex-column">
                    <li class="nav-item">
                      <a class="nav-link mega-dropdown-link" href="#">
                        <i class="icon-base ti tabler-circle me-1 icon-12px"></i>
                        Error
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link mega-dropdown-link" href="#">
                        <i class="icon-base ti tabler-circle me-1 icon-12px"></i>
                        Under Maintenance
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link mega-dropdown-link" href="#">
                        <i class="icon-base ti tabler-circle me-1 icon-12px"></i>
                        Coming Soon
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link mega-dropdown-link" href="#">
                        <i class="icon-base ti tabler-circle me-1 icon-12px"></i>
                        Not Authorized
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link mega-dropdown-link" href="#">
                        <i class="icon-base ti tabler-circle me-1 icon-12px"></i>
                        Verify Email (Basic)
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link mega-dropdown-link" href="#">
                        <i class="icon-base ti tabler-circle me-1 icon-12px"></i>
                        Verify Email (Cover)
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link mega-dropdown-link" href="#">
                        <i class="icon-base ti tabler-circle me-1 icon-12px"></i>
                        Two Steps (Basic)
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link mega-dropdown-link" href="#">
                        <i class="icon-base ti tabler-circle me-1 icon-12px"></i>
                        Two Steps (Cover)
                      </a>
                    </li>
                  </ul>
                </div>
                <div class="col-lg-4 d-none d-lg-block">
                  <div class="bg-body nav-img-col p-2">
                    <img src="{{ asset('assets/img/front-pages/misc/nav-item-col-img.png') }}" alt="nav item col image" class="w-100" />
                  </div>
                </div>
              </div>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-medium" href="#" target="_blank">Admin</a>
          </li>
        </ul>
      </div>
      <div class="landing-menu-overlay d-lg-none"></div>
      <!-- Menu wrapper: End -->
      <!-- Toolbar: Start -->
      <ul class="navbar-nav flex-row align-items-center ms-auto">

          <!-- Style Switcher -->
          {{-- <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-1">
            <a class="nav-link dropdown-toggle hide-arrow" id="nav-theme" href="javascript:void(0);" data-bs-toggle="dropdown">
              <i class="icon-base ti tabler-sun icon-lg theme-icon-active"></i>
              <span class="d-none ms-2" id="nav-theme-text">Toggle theme</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-theme-text">
              <li>
                <button type="button" class="dropdown-item align-items-center active" data-bs-theme-value="light" aria-pressed="false">
                  <span><i class="icon-base ti tabler-sun icon-md me-3" data-icon="sun"></i>Light</span>
                </button>
              </li>
              <li>
                <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="dark" aria-pressed="true">
                  <span><i class="icon-base ti tabler-moon-stars icon-md me-3" data-icon="moon-stars"></i>Dark</span>
                </button>
              </li>
              <li>
                <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="system" aria-pressed="false">
                  <span><i class="icon-base ti tabler-device-desktop-analytics icon-md me-3" data-icon="device-desktop-analytics"></i>System</span>
                </button>
              </li>
            </ul>
          </li> --}}
          <!-- / Style Switcher-->

        <!-- navbar button: Start -->
        @auth
          <li class="nav-item dropdown ms-2">
            <a class="nav-link dropdown-toggle d-flex align-items-center hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
              <div class="avatar avatar-online">
                <span class="avatar-initial rounded-circle bg-label-primary">{{ substr(Auth::user()->name, 0, 1) }}</span>
              </div>
              <span class="ms-2 d-none d-md-block">{{ Auth::user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              {{-- <li>
                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                  <i class="icon-base ti tabler-user me-2"></i>
                  <span class="align-middle">Perfil</span>
                </a>
              </li> --}}
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
            <a href="{{ route('login') }}" class="btn btn-primary" ><span class="tf-icons icon-base ti tabler-login scaleX-n1-rtl me-md-1"></span><span class="d-none d-md-block">Acceder / Registrar</span></a>
          </li>
        @endauth
        <!-- navbar button: End -->
      </ul>
      <!-- Toolbar: End -->
    </div>
  </div>
</nav>
