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
                        @foreach ($collections as $item)
                            @include('partials.pack-card', ['item' => $item])
                        @endforeach
                    </div>
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $collections->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
