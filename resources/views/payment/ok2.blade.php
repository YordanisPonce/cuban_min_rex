@extends('layouts.app')

@section('content')
<div class="container section-py text-center">
  <img src="{{ asset('/assets/img/front-pages/icon/ok.png') }}" alt="ok icon" class="mb-8 pb-2 w-25" />
  <h3>¡Gracias por su compra! 🎉</h3>
  <p>Revice su bandeja de entrada, se le envió un correo con el enlace de descarga del archivo.</p>
  <a href="{{ route('home') }}" class="btn btn-outline-secondary mt-3">Ir atrás</a>
</div>
@endsection
