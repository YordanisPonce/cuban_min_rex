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
                            <th>Fecha</th>
                            <th>Subido por</th>
                            <th>Nombre</th>
                            <th>Álbum</th>
                            <th>Categoría</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $file)
                        @php
                        setlocale(LC_TIME, 'Spanish_Spain.1252');
                        $date = strftime('%d de %B de %Y', new DateTime($file['date'])->getTimestamp());
                        @endphp
                        <tr>
                            <td></td>
                            <td>{{ $date }}</td>
                            <td>{{ $file['user'] }}</td>
                            <td>{{ $file['name'] }}</td>
                            <td>{{ $file['collection'] }}</td>
                            <td>{{ $file['category'] }}</td>
                            @if (!Auth::user()->hasActivePlan())
                            <td>$ {{ $file['price'] }}</td>
                            @endif
                            <td>
                                @if (Auth::user()->hasActivePlan())
                                    <a style="display: flex; width: 20px" href="">{{ svg('entypo-download') }}</a>
                                @else
                                    <a style="display: flex; width: 20px" href="">{{ svg('vaadin-cart') }}</a>
                                @endif
                            </td>
                            <th></th>
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