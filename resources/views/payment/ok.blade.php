@extends('layouts.app')

@section('content')
<div class="container payment_response text-center">
  <img src="{{ asset('/assets/img/front-pages/icon/ok.png') }}" alt="ok icon" class="mb-8 pb-2 w-25" />
  <h3 class="text-primary">¡Gracias por adquirir el plan! 🎉</h3>
  <p>Su pago se ha procesado correctamente. Se activará en breve. Tenga paciencia. Si tarda, contacte con nuestro soporte antes de realizar otra acción.</p>
  <a href="{{ route('home') }}" class="btn btn-outline"><i class="fas fa-angles-left"></i> Ir a Home</a>
</div>
@endsection
