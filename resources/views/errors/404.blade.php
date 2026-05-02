@extends('layouts.app')

@section('title', 'Página no Encontrada - ' . config('app.name'))

@push('styles')
<style>
:root{
  --bg:#0f0d0b;--card:#1a1714;--card-hover:#221f1a;--primary:#f5a623;--primary-dark:#d48e1a;
  --fg:#f0e8d8;--muted:#8a7a66;--border:#2a2520;--surface:#1e1b17;--tag-new:#e04040;
}

/* ERROR LAYOUT */
.error-wrap{flex:1;display:flex;align-items:center;justify-content:center;padding:6rem 2rem 3rem;position:relative;overflow:hidden;}
.error-bg{position:absolute;inset:0;background:radial-gradient(ellipse at center, rgba(245,166,35,.08) 0%, transparent 60%);pointer-events:none;}
.error-bg::before{content:"";position:absolute;top:20%;left:10%;width:300px;height:300px;background:radial-gradient(circle, rgba(245,166,35,.15) 0%, transparent 70%);border-radius:50%;filter:blur(40px);}
.error-bg::after{content:"";position:absolute;bottom:10%;right:10%;width:400px;height:400px;background:radial-gradient(circle, rgba(224,64,64,.1) 0%, transparent 70%);border-radius:50%;filter:blur(60px);}

.error-card{position:relative;z-index:2;max-width:680px;text-align:center;}
.error-icon-wrap{position:relative;display:inline-block;margin-bottom:2rem;}
.error-code{font-size:11rem;font-weight:900;line-height:1;background:linear-gradient(135deg,var(--primary) 0%,#ff6b35 50%,var(--tag-new) 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;letter-spacing:-.05em;text-shadow:0 0 80px rgba(245,166,35,.3);}
.error-icon-overlay{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:3rem;color:var(--bg);background:var(--primary);width:80px;height:80px;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 0 40px rgba(245,166,35,.6);animation:pulse 2s ease-in-out infinite;}
@keyframes pulse{0%,100%{transform:translate(-50%,-50%) scale(1);}50%{transform:translate(-50%,-50%) scale(1.08);}}

.error-title{font-size:2rem;font-weight:800;margin-bottom:.75rem;text-transform:uppercase;letter-spacing:-.02em;}
.error-title span{color:var(--primary);font-style:italic;}
.error-desc{color:var(--muted);font-size:1rem;line-height:1.6;margin-bottom:2rem;max-width:480px;margin-left:auto;margin-right:auto;}

.error-actions{display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;margin-bottom:3rem;}
.btn-cta{background:var(--primary);color:var(--bg);padding:.85rem 1.75rem;border-radius:8px;font-weight:800;font-size:.85rem;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:.5rem;text-transform:uppercase;transition:transform .2s,box-shadow .2s;}
.btn-cta:hover{transform:translateY(-2px);box-shadow:0 10px 24px rgba(245,166,35,.3);}
.btn-secondary{background:transparent;color:var(--fg);padding:.85rem 1.75rem;border-radius:8px;font-weight:700;font-size:.85rem;border:1px solid var(--border);cursor:pointer;display:inline-flex;align-items:center;gap:.5rem;text-transform:uppercase;transition:border-color .2s,color .2s;}
.btn-secondary:hover{border-color:var(--primary);color:var(--primary);}

/* SUGGESTIONS */
.suggestions{margin-top:2rem;}
.suggestions-title{font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--muted);letter-spacing:.1em;margin-bottom:1rem;}
.suggestions-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:.75rem;max-width:560px;margin:0 auto;}
.suggestion-link{background:var(--card);border:1px solid var(--border);padding:1rem;border-radius:8px;display:flex;align-items:center;gap:.75rem;font-size:.85rem;font-weight:600;transition:all .2s;cursor:pointer;}
.suggestion-link:hover{background:var(--card-hover);border-color:var(--primary);transform:translateY(-2px);}
.suggestion-link i{color:var(--primary);font-size:1rem;}

/* SEARCH */
.error-search{max-width:480px;margin:2rem auto 0;position:relative;}
.error-search input{width:100%;background:var(--card);border:1px solid var(--border);color:var(--fg);padding:.85rem 1rem .85rem 2.75rem;border-radius:8px;font-size:.9rem;font-family:inherit;outline:none;transition:border-color .2s;}
.error-search input:focus{border-color:var(--primary);}
.error-search i{position:absolute;left:1rem;top:50%;transform:translateY(-50%);color:var(--muted);}

@media(max-width:640px){
  .error-code{font-size:7rem;}
  .error-icon-overlay{width:60px;height:60px;font-size:2rem;}
  .error-title{font-size:1.4rem;}
}
</style>
@endpush

@section('content')
<section class="error-wrap">
  <div class="error-bg"></div>
  <div class="error-card">
    <div class="error-icon-wrap">
      <div class="error-code">404</div>
      <div class="error-icon-overlay"><i class="fa-solid fa-compact-disc fa-spin" style="animation-duration:6s;"></i></div>
    </div>
    <h1 class="error-title">Página <span>no encontrada</span></h1>
    <p class="error-desc">La página que buscas no existe, fue movida o el enlace está roto. Pero no te preocupes, tenemos miles de remixes esperándote.</p>

    <div class="error-actions">
      <a href="{{ route('home') }}" class="btn-cta"><i class="fa-solid fa-house"></i> Ir al inicio</a>
      <button class="btn-secondary" onclick="history.back()"><i class="fa-solid fa-arrow-left"></i> Volver atrás</button>
    </div>

    <form class="error-search" action="{{ route('search') }}">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" name="name" placeholder="Buscar remixes, DJs o playlists...">
    </form>
  </div>
</section>
@endsection

@push('scripts')
@endpush