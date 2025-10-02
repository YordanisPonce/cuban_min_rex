@extends('layouts.app')
@php
    use Carbon\Carbon;
    Carbon::setLocale('es');
@endphp
@section('title', 'Página de Resultados de Busqueda')

@push('styles')
    <style>
        /* Card “Relacionadas” clickable + overlay + flecha de entrar */
        .card-relationed {
            position: relative;
            overflow: hidden;
            border-radius: 14px;
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .card-relationed:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, .08);
        }

        .card-relationed .dark-screen {
            position: absolute;
            inset: 0;
            background: linear-gradient(0deg, rgba(0, 0, 0, .35), rgba(0, 0, 0, .10));
            opacity: 0;
            transition: opacity .15s ease;
        }

        .card-relationed:hover .dark-screen {
            opacity: 1;
        }

        .card-relationed .enter-arrow {
            position: absolute;
            right: 10px;
            bottom: 10px;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(255, 255, 255, .9);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transform: translateY(6px);
            opacity: 0;
            transition: opacity .15s ease, transform .15s ease, background .15s ease;
            border: 1px solid rgba(0, 0, 0, .08);
        }

        .card-relationed:hover .enter-arrow {
            opacity: 1;
            transform: translateY(0);
        }

        .card-relationed .enter-arrow:hover {
            background: #fff;
        }

        /* Mejoras pequeñas en la lista de archivos */
        .file-list .list-group-item {
            border-left: 0;
            border-right: 0;
        }

        .file-list .list-group-item:first-child {
            border-top: 0;
        }

        .file-list .list-group-item:last-child {
            border-bottom: 0;
        }
    </style>
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
                                    <p class="mb-0">Autor. <span
                                            class="fw-medium text-heading">{{ $collection->user->name }}</span></p>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    @auth
                                        @if (Auth::user()->hasActivePlan())
                                            <a style="display:flex;width:20px" title="Descargar Álbum Completo"
                                                href="{{ route('collection.download', $collection->id) }}">
                                                {!! svg('entypo-download') !!}
                                            </a>
                                        @else
                                            <a style="display:flex;width:20px" title="Comprar colección" href="#">
                                                {!! svg('vaadin-cart') !!}
                                            </a>
                                        @endif
                                    @else
                                        <a style="display:flex;width:20px" title="Comprar colección" href="#">
                                            {!! svg('vaadin-cart') !!}
                                        </a>
                                    @endauth
                                </div>
                            </div>

                            <div class="card academy-content shadow-none border">
                                <div class="p-2">
                                    <div class="cursor-pointer d-flex justify-content-center">
                                        <img class="w-75" style="max-height:400px"
                                            src="{{ $collection->image ? $collection->image : asset('assets/img/front-pages/icon/collection.png') }}" />
                                    </div>
                                </div>

                                <div class="card-body pt-4">
                                    <hr class="my-6" />
                                    <h5>Lista</h5>
                                    <div class="card mb-6">
                                        <ul class="list-group list-group-flush file-list">
                                            @foreach ($results as $file)
                                                <li class="list-group-item">
                                                    <div class="row align-items-center">
                                                        <div class="col-9">
                                                            <span class="d-block w-100 text-nowrap overflow-hidden"
                                                                style="text-overflow:ellipsis;">
                                                                {{ $file['name'] }}
                                                            </span>
                                                        </div>
                                                        <div class="col-3">
                                                            <span
                                                                class="d-flex gap-2 justify-content-end align-items-center text-nowrap">
                                                                @auth
                                                                    @if (Auth::user()->hasActivePlan())
                                                                        <a style="display:flex;width:20px"
                                                                            title="Descargar archivo"
                                                                            href="{{ route('file.download', $file['id']) }}">
                                                                            {!! svg('entypo-download') !!}
                                                                        </a>
                                                                    @else
                                                                        <i class="small">$ {{ $file['price'] }}</i>
                                                                        <a data-url="{{ route('file.pay', $file['id']) }}"
                                                                            style="display:flex;width:20px;cursor:pointer"
                                                                            onclick="proccessPayment(this.dataset.url)"
                                                                            title="Comprar archivo">
                                                                            {!! svg('vaadin-cart') !!}
                                                                        </a>
                                                                    @endif
                                                                @else
                                                                    <i class="small">$ {{ $file['price'] }}</i>
                                                                    <a data-url="{{ route('file.pay', $file['id']) }}"
                                                                        style="display:flex;width:20px;cursor:pointer"
                                                                        onclick="proccessPayment(this.dataset.url)"
                                                                        title="Comprar archivo">
                                                                        {!! svg('vaadin-cart') !!}
                                                                    </a>
                                                                @endauth

                                                                {{-- Nota: mantenemos tu preview si NO es zip --}}
                                                                @if (!$file['isZip'])
                                                                    <a style="display:flex;width:20px"
                                                                        class="cursor-pointer"
                                                                        data-url="{{ $file['url'] }}" data-state="pause"
                                                                        onclick="playAudio(this)" title="Escuchar preview">
                                                                        {!! svg('vaadin-play') !!}
                                                                    </a>
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div> {{-- /card content --}}
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="pb-1 mb-6">Colecciones Relacionadas</h5>
                            <hr class="my-6" />
                            <div class="row mb-12 g-6">
                                @foreach ($relationeds as $coll)
                                    <div class="col-12">
                                        {{-- Toda la card es enlace; el “play” se sustituyó por flecha de entrar --}}
                                        <a class="card card-relationed" href="{{ route('collection.show', $coll->id) }}">
                                            <div class="row g-0 align-items-center">
                                                <div class="col-md-4">
                                                    <img class="card-img card-img-left w-100"
                                                        style="max-height:80px;object-fit:cover"
                                                        src="{{ $coll->image ? $coll->image : asset('assets/img/front-pages/icon/collection.png') }}" />
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="card-body p-3">
                                                        <h6 class="card-title mb-1 text-truncate">{{ $coll->name }}</h6>
                                                        <p class="card-text mb-0">
                                                            <small class="text-body-secondary">Subido por
                                                                {{ $coll->user->name }}</small>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Overlay + flecha de entrar --}}
                                            <div class="dark-screen"></div>
                                            <div class="enter-arrow" aria-hidden="true" title="Entrar a la colección">
                                                {!! svg('tabler-arrow-right') !!}
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- / Content -->
    </div>
@endsection
