<footer>
    <div class="footer-top container">
      <div>
        <a href="{{ url('/') }}" class="footer-brand">
          <img class="app_logo" src="{{ config('app.logo') }}" alt="{{ config('app.name') }}"> {{ config('app.name') }}
        </a>
        <p class="footer-text">Música que conecta.<br> Djs que hacen historia.</p>
        @php
          $youtube = "https://www.youtube.com/@".config('contact.youtube')."/";
          $facebook = "https://www.facebook.com/@".config('contact.facebook')."/";
        @endphp
        
        <div class="footer-social">
          <p><a href="mailto:{{ config('contact.email') }}" class="text-heading" target="_blank"><i class="icon-base ti tabler-mail icon-lg"></i></a></p>
          <p><a href="tel:{{ config('contact.phone') }}" class="text-heading" target="_blank"><i class="icon-base ti tabler-phone-call icon-lg"></i></a></p>
          <p><a href="https://www.instagram.com/{{config('contact.instagram')}}/" class="text-heading" target="_blank"><i class="icon-base ti tabler-brand-instagram icon-lg"></i></a></p>
          <p><a href="{{$youtube}}" class="text-heading" target="_blank"><i class="icon-base ti tabler-brand-youtube icon-lg"></i></a></p>
          <p><a href="{{$facebook}}" class="text-heading" target="_blank"><i class="icon-base ti tabler-brand-facebook icon-lg"></i></a></p>
        </div>
      </div>
      <div>
        <h3 class="footer-title mb-6">NAVEGACIÓN</h3>
        <ul class="footer-nav">
          <li class="footer-link"><a href="{{ route('home') }}" class="d-block mb-2 text-heading">HOME</a></li>
          <li class="footer-link"><a href="{{ route('djs') }}" class="d-block mb-2 text-heading">DJS</a></li>
          <li class="footer-link"><a href="{{ route('remixes') }}" class="d-block mb-2 text-heading">REMIXES</a></li>
          <li class="footer-link"><a href="{{ route('videos') }}" class="d-block mb-2 text-heading">VIDEOS</a></li>
          <li class="footer-link"><a href="{{ route('playlist.index') }}" class="d-block mb-2 text-heading">PLAYLISTS</a></li>
          <li class="footer-link"><a href="{{ route('collection.index') }}" class="d-block mb-2 text-heading">PACKS</a></li>
          <li class="footer-link"><a href="{{ route('radio') }}" class="d-block mb-2 text-heading">EMISORA</a></li>
          <li class="footer-link"><a href="{{ route('plans') }}" class="d-block mb-2 text-heading">PLANES</a></li>
        </ul>
      </div>
      <div>
        <h3 class="footer-title mb-6">LEGAL</h3>
        <ul class="footer-nav">
          <li class="footer-link"><a href="{{ route('terms') }}" class="d-block mb-2 text-heading">Términos y Condiciones</a></li>
          <li class="footer-link"><a href="{{ route('privacy') }}" class="d-block mb-2 text-heading">Política de Privacidad</a></li>
          <li class="footer-link"><a href="{{ route('cookies') }}" class="d-block mb-2 text-heading">Cookies</a></li>
          <li class="footer-link"><a href="{{ route('legal') }}" class="d-block mb-2 text-heading">Aviso Legal</a></li>
        </ul>
      </div>
      <div>
        <h3 class="footer-title mb-6">SUSCRÍBETE</h3>
        <p class="footer-text">Descarga música exclusiva y contenido para DJs.</p>
        <div style="margin-top: 20px">
          <a href="{{ route('plans') }}" class="btn btn-primary footer-btn">
            <i class="fa-solid fa-crown me-2"></i>
            <span class="align-middle">Ver Planes</span>
          </a>
        </div>
      </div>
    </div> 
    <div class="footer-bottom container">
      <span><i class="fas fa-copyright"></i> {{ now()->year }}. Todos los derechos reservados.</span>
    </div>
</footer>
