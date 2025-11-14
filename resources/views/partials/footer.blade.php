<footer class="landing-footer bg-body footer-text mt-auto">
  <div class="footer-top position-relative overflow-hidden z-1 card">
    <!-- <img src="{{ asset('assets/img/front-pages/backgrounds/footer-bg.png') }}" alt="footer bg" class="footer-bg banner-bg-img z-n1" /> -->
    <div class="container">
      <div class="row gx-0 gy-6 g-lg-10">
        <div class="col-lg-5">
          <a href="{{ url('/') }}" class="app-brand-link mb-6">
            <span class="app-brand-logo demo">
              <span class="text-primary">
                <!-- Reemplaza el SVG con tu imagen PNG -->
                <img src="{{ config('app.logo') }}" alt="{{ config('app.name') }}"
                                style="width: 100px; height: 50px; object-fit: contain; border-radius: 50%">
              </span>
              </span>
            </span>
            <span class="app-brand-text demo footer-link fw-bold ms-2 ps-1">{{ config('app.name') }}</span>
          </a>
          <p class="footer-text footer-logo-description mb-6">Sube, descarga y compra: haz que cada momento suene.</p>
          <p><a href="mailto:{{ config('contact.email') }}" class="text-heading"><i class="icon-base ti tabler-mail icon-lg"></i> {{ config('contact.email') }}</a></p>
          <p><a href="tel:{{ config('contact.phone') }}" class="text-heading"><i class="icon-base ti tabler-phone-call icon-lg"></i> {{ config('contact.phone') }}</a></p>
          <p><a href="https://www.instagram.com/{{config('contact.instagram')}}/" class="text-heading"><i class="icon-base ti tabler-brand-instagram icon-lg"></i> {{ '@'.config('contact.instagram') ??  'Sin definir' }}</a></p>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
          <h6 class="footer-title mb-6">Categorías</h6>
          <ul class="list-unstyled">
            @isset($recentCategories)
              @foreach ($recentCategories as $category)
              <li class="mb-4">
                <a href="{{route('category.show', $category->id)}}" class="footer-link">{{$category->name}}</a>
              </li>
              @endforeach
            @endisset
          </ul>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
          <h6 class="footer-title mb-6">Packs</h6>
          <ul class="list-unstyled">
            @isset($recentCollections)
              @foreach ($recentCollections as $collection)
              <li class="mb-4">
                <a href="{{route('collection.show', $collection->id)}}" class="footer-link">{{$collection->name}}</a>
              </li>
              @endforeach
            @endisset
          </ul>
        </div>
        <div class="col-lg-3 col-md-4">
          @auth
            <a class="d-block btn btn-primary mb-4" href="{{ route('profile.edit') }}">
              <i class="icon-base ti tabler-user me-2"></i>
              <span class="align-middle">Perfil</span>
            </a>
            @if (Auth()->user()->role !== 'user')
            <a class="d-block btn btn-primary" href="/admin" target="_blank">
              <i class="icon-base ti tabler-dashboard me-2"></i>
              <span class="align-middle">Panel de Administración</span>
            </a>
            @endif
          @else
            <h6 class="footer-title mb-6">Únete a nosotros</h6>
            <a href="{{route('register')}}" class="d-block btn btn-primary mb-4"><span class="tf-icons icon-base ti tabler-user scaleX-n1-rtl me-md-1"></span>Registrarse</a>
            <a href="{{route('login')}}" class="d-block btn btn-primary"><span class="tf-icons icon-base ti tabler-login scaleX-n1-rtl me-md-1"></span>  Acceder</a>
          @endauth
        </div>
      </div>
    </div>
  </div>
</footer>
