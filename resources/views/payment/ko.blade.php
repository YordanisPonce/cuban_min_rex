@extends('layouts.app')

@section('content')
<div class="container section-py text-center">
  <img src="{{ asset('/assets/img/front-pages/icon/ko.png') }}" alt="ko icon" class="mb-8 pb-2 w-25" />
  <h3>Pago no completado</h3>
  <p>Lo sentimos, tu plan está pendiente de pago o fue cancelado.</p>
  <a href="{{ url()->previous() }}" class="btn btn-outline-secondary mt-3">Ir atrás</a>
</div>
@endsection
