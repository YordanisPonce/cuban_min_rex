@extends('layouts.app')

@section('title', 'Inicio – Cuban Mix Rex')

@section('content')
<section class="section-py bg-body mt-10">
    @php $hasDjs = isset($djs) && count($djs) > 0; @endphp

    @if ($hasDjs)
        @foreach($djs as $dj)
            @if ($dj->collections()->count() > 0)
                @include('partials.collection', [
                    'id' => $dj->id,
                    'badge' => 'DJ',
                    'title' => $dj->name,
                    'subtitle' => 'Algo sobre el DJ',
                    'ctaText' => 'Ver Más',
                    'ctaHref' => route('collection.dj', $dj->id),

                    'items' => $dj->collections()->take(10)->get(),

                ])
            @endif
        @endforeach
        <nav class="container mt-3" style="margin: auto;">
            <ul class="pagination" style="--bs-pagination-border-radius: 0%;">
                @if ($djs->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">←</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $djs->previousPageUrl() }}">←</a></li>
                @endif

                @for ($i = 1; $i <= $djs->lastPage(); $i++)
                    @if ($i == $djs->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $djs->url($i) }}">{{ $i }}</a></li>
                    @endif
                @endfor

                @if ($djs->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $djs->nextPageUrl() }}">→</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link">→</span></li>
                @endif
            </ul>
        </nav>
    @else
        <div class="container">
            <div class="border rounded-4 p-4 p-md-5 text-center bg-body">
                <h3 class="h5 fw-bold mb-2">Aún no tenemos Djs destacados</h3>
                <p class="text-body-secondary mb-3"> </p>
                <a href="{{ route('home') }}" class="btn btn-label-primary">Regresar</a>
            </div>
        </div>
    @endif
</section>
@endsection
