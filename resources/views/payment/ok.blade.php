@extends('layouts.app')

@section('content')
<div class="container section-py text-center">
  <img src="{{ asset('/assets/img/front-pages/icon/ok.png') }}" alt="ok icon" class="mb-8 pb-2 w-25" />
  <h3>¡Gracias por adquirir el plan! 🎉</h3>
  <p>Tu suscripción está activa. Disfruta de los beneficios.</p>
  <a href="{{ url()->previous() }}" class="btn btn-outline-secondary mt-3">Ir atrás</a>
</div>
@endsection
