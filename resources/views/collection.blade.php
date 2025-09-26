@extends('layouts.app')
@php
    use Carbon\Carbon;
    Carbon::setLocale('es');
@endphp
@section('title', 'Página de Resultados de Busqueda')

@push('styles')
@endpush

@section('content')
    <!-- Content wrapper -->
    <div class="content-wrapper bg-body">
        <!-- Content -->
        <div class="container flex-grow-1 container-p-y mt-5">
            <div class="row g-6 mt-10">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-6 gap-2">
                                <div class="me-1">
                                    <h5 class="mb-0">{{ $collection->name }}</h5>
                                    <p class="mb-0">Autor. <span class="fw-medium text-heading">
                                            {{ $collection->user->name }} </span></p>
                                </div>
                                <div class="d-flex align-items-center">
                                    @auth
                                        @if (Auth::user()->hasActivePlan())
                                            <a style="display: flex; width: 20px" title="Descargar Álbum Completo"
                                                href="{{ route('collection.download', $collection->id) }}">{{ svg('entypo-download') }}</a>
                                        @else
                                            <a style="display: flex; width: 20px" href="">{{ svg('vaadin-cart') }}</a>
                                        @endif
                                    @else
                                        <a style="display: flex; width: 20px" href="">{{ svg('vaadin-cart') }}</a>
                                    @endauth
                                </div>
                            </div>
                            <div class="card academy-content shadow-none border">
                                <div class="p-2">
                                    <div class="cursor-pointer d-flex justify-content-center">
                                        <img class="w-75" style="max-height: 400px"
                                            src="{{ $collection->image ? $collection->image : asset('assets/img/front-pages/icon/collection.png') }}" />
                                    </div>
                                </div>
                                <div class="card-body pt-4">
                                    <hr class="my-6" />
                                    <h5>Lista</h5>
                                    <div class="card mb-6">
                                        <ul class="list-group list-group-flush file-list">
                                            @foreach ($results as $file)
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <spam class="text-nowrap">{{ $file['name'] }}</spam>
                                                    <spam class="flex gap-sm-10 gap-1 text-nowrap">
                                                        @auth
                                                            @if (Auth::user()->hasActivePlan())
                                                                <i><a style="display: flex; width: 20px"
                                                                        href="{{ route('file.download', $file['id']) }}">{{ svg('entypo-download') }}</a></i>
                                                            @else
                                                                <i>$ {{ $file['price'] }}</i>
                                                                <i><a style="display: flex; width: 20px"
                                                                        href="">{{ svg('vaadin-cart') }}</a></i>
                                                            @endif
                                                        @else
                                                            <i>$ {{ $file['price'] }}</i>
                                                            <i><a style="display: flex; width: 20px"
                                                                    href="">{{ svg('vaadin-cart') }}</a></i>
                                                        @endauth
                                                    </spam>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">

                </div>
            </div>
        </div>
        <!-- / Content -->
    </div>
@endsection
