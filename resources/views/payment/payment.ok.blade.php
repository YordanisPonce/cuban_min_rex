@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
  <h3>¡Gracias por adquirir el plan! 🎉</h3>
  <p>Tu suscripción está activa. Disfruta de los beneficios.</p>
  <a href="{{ url()->previous() }}" class="btn btn-outline-secondary mt-3">Ir atrás</a>
</div>
@endsection
