@extends('layouts.app')
@php
use Carbon\Carbon;
Carbon::setLocale('es');
@endphp
@section('title', 'Página de Resultados de Busqueda')

@push('styles')

@endpush

@section('content')
<section id="landingReviews" class="section-py bg-body">
  <!-- What people say slider: Start -->
  <div class="container mt-10">
    <div class="row align-items-center gx-0 gy-4 g-lg-5 mb-5 pb-md-5">
      <div class="col-md-12 flex flex-column align-items-center">
        <div class="mb-4">
          <span class="badge bg-label-primary">Categoría</span>
        </div>
        <h4 class="mb-1">
          <span class="position-relative fw-extrabold z-1">{{$category->name}}
            <img src="{{ asset('assets/img/front-pages/icon/section-title-icon.png') }}" alt="laptop charging" class="section-title-img position-absolute object-fit-contain bottom-0 z-n1">
          </span>
        </h4>
      </div>
      <div class="categories col-md-12">
        <div class="row">
            @foreach ($category->collections as $collection)
            <div class="col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-6">
                <a class="card relative" href="{{ route('collection.show', $collection->id) }}" style="background-image: url('{{ $collection->image ? asset('storage/' . $collection->image) : asset('assets/img/front-pages/icon/collection.png') }}'); background-size: cover; height: 150px; background-color: rgba(0,0,0,.3); background-blend-mode: darken;">
                    <h4 class="bottom-0 w-100 text-center" style="position: absolute;">{{$collection->name}}</h4>
                    <div class="dark-screen d-none"></div>
                </a>
            </div>
            @endforeach
        </div>
      </div>
    </div>
  </div>
</section>
@endsection