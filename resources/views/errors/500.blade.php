@extends('layouts.app')

@section('title', 'Error del servidor - ' . config('app.name'))

@push('styles')
<style>
:root{
  --bg:#0f0d0b;--card:#1a1714;--card-hover:#221f1a;--primary:#f5a623;--primary-dark:#d48e1a;
  --fg:#f0e8d8;--muted:#8a7a66;--border:#2a2520;--surface:#1e1b17;--tag-new:#e04040;
}
.error-wrap{flex:1;display:flex;align-items:center;justify-content:center;padding:6rem 2rem 3rem;position:relative;overflow:hidden;}
.error-bg{position:absolute;inset:0;background:radial-gradient(ellipse at center, rgba(224,64,64,.1) 0%, transparent 60%);pointer-events:none;}
.error-bg::before{content:"";position:absolute;top:30%;left:50%;transform:translateX(-50%);width:500px;height:500px;background:radial-gradient(circle, rgba(224,64,64,.12) 0%, transparent 70%);border-radius:50%;filter:blur(60px);}

.error-card{position:relative;z-index:2;max-width:680px;text-align:center;}
.error-icon-wrap{position:relative;display:inline-block;margin-bottom:2rem;}
.error-code{font-size:11rem;font-weight:900;line-height:1;background:linear-gradient(135deg,var(--tag-new) 0%,#ff6b35 50%,var(--primary) 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;letter-spacing:-.05em;}
.error-icon-overlay{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:2.5rem;color:#fff;background:var(--tag-new);width:80px;height:80px;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 0 40px rgba(224,64,64,.5);animation:shake 3s ease-in-out infinite;}
@keyframes shake{0%,100%{transform:translate(-50%,-50%) rotate(0);}25%{transform:translate(-50%,-50%) rotate(-8deg);}75%{transform:translate(-50%,-50%) rotate(8deg);}}

.error-title{font-size:2rem;font-weight:800;margin-bottom:.75rem;text-transform:uppercase;}
.error-title span{color:var(--primary);font-style:italic;}
.error-desc{color:var(--muted);font-size:1rem;line-height:1.6;margin-bottom:2rem;max-width:480px;margin:0 auto 2rem;}

.error-actions{display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;margin-bottom:2rem;}
.btn-cta{background:var(--primary);color:var(--bg);padding:.85rem 1.75rem;border-radius:8px;font-weight:800;font-size:.85rem;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:.5rem;text-transform:uppercase;transition:transform .2s,box-shadow .2s;}
.btn-cta:hover{transform:translateY(-2px);box-shadow:0 10px 24px rgba(245,166,35,.3);}
.btn-secondary{background:transparent;color:var(--fg);padding:.85rem 1.75rem;border-radius:8px;font-weight:700;font-size:.85rem;border:1px solid var(--border);cursor:pointer;display:inline-flex;align-items:center;gap:.5rem;text-transform:uppercase;transition:all .2s;}
.btn-secondary:hover{border-color:var(--primary);color:var(--primary);}

.error-details{background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.25rem;text-align:left;margin-top:1.5rem;font-family:'SF Mono',Consolas,monospace;font-size:.75rem;color:var(--muted);}
.error-details .row{display:flex;justify-content:space-between;padding:.35rem 0;border-bottom:1px dashed var(--border);}
.error-details .row:last-child{border:none;}
.error-details .label{color:var(--muted);}
.error-details .val{color:var(--fg);font-weight:600;}
.status-badge{display:inline-flex;align-items:center;gap:.4rem;background:rgba(224,64,64,.1);color:var(--tag-new);padding:.25rem .6rem;border-radius:4px;font-size:.7rem;font-weight:700;}
.status-badge .dot{width:6px;height:6px;border-radius:50%;background:var(--tag-new);animation:blink 1.5s infinite;}
@keyframes blink{50%{opacity:.3;}}

@media(max-width:640px){
  .error-code{font-size:7rem;}
  .error-icon-overlay{width:60px;height:60px;font-size:1.8rem;}
  .error-title{font-size:1.4rem;}
}
</style>
@endpush

@section('content')
<section class="error-wrap">
  <div class="error-bg"></div>
  <div class="error-card">
    <div class="error-icon-wrap">
      <div class="error-code">500</div>
      <div class="error-icon-overlay"><i class="fa-solid fa-triangle-exclamation"></i></div>
    </div>
    <h1 class="error-title">Paso algo <span>inesperado</span> de nuestro lado</h1>
    <p class="error-desc">Nuestro servidor está pasando por un mal momento. Nuestro equipo ya fue notificado y trabaja en restablecer el ritmo.</p>

    <div class="error-actions">
      <button class="btn-cta" onclick="location.reload()"><i class="fa-solid fa-rotate-right"></i> Reintentar</button>
      <a href="{{ route('home') }}" class="btn-secondary"><i class="fa-solid fa-house"></i> Ir al inicio</a>
    </div>

    <div class="error-details">
      <div class="row"><span class="label">Estado</span><span class="val"><span class="status-badge"><span class="dot"></span> Investigando</span></span></div>
      <div class="row"><span class="label">Código de error</span><span class="val">CP_500_INTERNAL</span></div>
      <div class="row"><span class="label">Hora</span><span class="val" id="ts"></span></div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
document.getElementById('ts').textContent = new Date().toLocaleString('es-ES');
</script>
@endpush