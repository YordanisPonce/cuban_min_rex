@extends('layouts.app')
@php
    use Carbon\Carbon;
    Carbon::setLocale('es');
@endphp
@section('title', 'Página de Resultados de Busqueda')

@push('styles')
@endpush

@section('content')
    <section id="landingReviews" class="section-py bg-body mt-10">
        <!-- What people say slider: Start -->
        <div class="container mt-10">
            <div class="row align-items-center gx-0 gy-4 g-lg-5 mb-5 pb-md-5">
                <div class="col-md-12 flex flex-column align-items-center">
                    @isset($ctgName)
                    <div class="mb-4">
                        <span class="badge bg-label-primary">Categoría</span>
                    </div>
                    <h4 class="mb-1">
                        <span class="position-relative fw-extrabold z-1">{{ $ctgName }}
                            <img src="{{ asset('assets/img/front-pages/icon/section-title-icon.png') }}"
                                alt="laptop charging"
                                class="section-title-img position-absolute object-fit-contain bottom-0 z-n1">
                        </span>
                    </h4>
                    @else
                    <div class="mb-4">
                        <span class="badge bg-label-primary">{{ $badge }}</span>
                    </div>
                    @endisset
                </div>
                <div class="categories col-md-12">
                    <div class="row">
                        @foreach ($collections as $collection)
                            @if ($collection->files()->count() > 0)
                                <div class="col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-6">
                                    <a class="card relative overflow-hidden" href="{{ route('collection.show', $collection->id) }}"
                                        style="background-image: url('{{ $collection->image ? $collection->image : asset('assets/img/front-pages/icon/collection.png') }}'); background-size: cover; height: 150px; background-color: rgba(0,0,0,.5); background-blend-mode: darken;">
                                        <h4 class="bottom-0 w-100 text-center" style="position: absolute;">
                                            {{ $collection->name }}</h4>
                                        <div class="dark-screen d-none"></div>
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <nav class="container mt-3" style="margin: auto;">
                        <ul class="pagination" style="--bs-pagination-border-radius: 0%;">
                            @if ($collections->onFirstPage())
                                <li class="page-item disabled"><span class="page-link">←</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $collections->previousPageUrl() }}">←</a></li>
                            @endif

                            @for ($i = 1; $i <= $collections->lastPage(); $i++)
                                @if ($i == $collections->currentPage())
                                    <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $collections->url($i) }}">{{ $i }}</a></li>
                                @endif
                            @endfor

                            @if ($collections->hasMorePages())
                                <li class="page-item"><a class="page-link" href="{{ $collections->nextPageUrl() }}">→</a></li>
                            @else
                                <li class="page-item disabled"><span class="page-link">→</span></li>
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </section>
@endsection
