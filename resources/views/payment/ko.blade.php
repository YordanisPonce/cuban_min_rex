@extends('layouts.app')

@section('content')
<div class="container payment_response text-center">
  <img src="{{ asset('/assets/img/front-pages/icon/ko.png') }}" alt="ko icon" class="mb-8 pb-2 w-25" />
  <h3 class="text-primary">Pago no completado</h3>
  <p>Lo sentimos, tu pago está pendiente o fue cancelado.</p>
  <a href="{{ route('home') }}" class="btn btn-outline"><i class="fas fa-angles-left"></i> Ir a Home</a>
</div>
@endsection
