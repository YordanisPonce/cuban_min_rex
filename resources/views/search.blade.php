@extends('layouts.app')

@section('title', 'Página de Resultados de Busqueda')

@push('styles')
<link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page-payment.css') }}" />
@endpush

@section('content')
<!-- FAQ: Start -->
<section id="musicSearch" class="section-py bg-body" style="height: 100vh;">
    <div class="container" style="margin-top: 60px;">
        <div class="text-center mb-4">
            <span class="badge bg-label-primary">🎶 Archivos Disponibles</span>
        </div>
        <div class="card">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatables-basic table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Nombre</th>
                            <th>Álbum</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $file)
                        <tr>
                            <td></td>
                            <td>{{ $file['name'] }}</td>
                            <td>{{ $file['collection'] }}</td>
                            <td>{{ $file['category'] }}</td>
                            <th>$ {{ $file['price'] }}</th>
                            <td></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if($results->isEmpty())
        <h4 class="text-center text-primary mt-2">Sin resultados</h4>
        @endif
    </div>
</section><!-- FAQ: End -->
@endsection