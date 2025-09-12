@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
  <h3>Pago no completado</h3>
  <p>Lo sentimos, tu plan está pendiente de pago o fue cancelado.</p>
  <a href="{{ url()->previous() }}" class="btn btn-outline-secondary mt-3">Ir atrás</a>
</div>
@endsection
