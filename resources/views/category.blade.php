@extends('layouts.app')
@php
    use Carbon\Carbon;
    Carbon::setLocale('es');
@endphp
@section('title', 'Página de Resultados de Busqueda')

@push('styles')
    <style>
        @isset($radio)
        :root{
            --azul: #0079FF;
            --rojo: #ff0000;

            --bs-primary: var(--red) !important;

            --bs-paper-bg: #000 !important;

            --download-button: var(--rojo) !important;
            --play-button: var(--azul) !important;
        }
        .text-black{
            color: white !important;
        }

        .bg-body {
            background-color: black !important;
        }
        @endisset
        .packs {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            margin-top: 40px;
            & .card {
                cursor: pointer;
                box-shadow: 0px 0px 6px 1px #ccc; 
                border-radius: 12px; 
                overflow: hidden; 
                min-width: 200px;
                max-width: 200px; 
                height: 250px;
                transform: translateY(0);
                transition: transform .3s ease-in;

                &>img{
                    width: 100%; 
                    height:100%; 
                    object-fit: cover; 
                    border-radius: 12px;
                }

                & .pack-meta{
                    position: absolute;
                    width: 100%;
                    height: 100%;
                    padding: 5px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    text-align: center;
                    background: rgba(0, 0, 0, 0.4);
                }

                & .pack-links, & .pack-links-top{
                    position: absolute;
                    width: 100%;
                    height: 50px;
                    padding: 5px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    text-align: center;
                    background: rgba(0, 0, 0, 0.6);
                    transition: transform .3s ease-in;
                }

                & .pack-links{
                    bottom: 0;
                    transform: translateY(50px);

                    &>div{
                        display: flex;
                        justify-content: center;
                        align-items: center;

                        & a{
                            background: black;
                            padding: 2px;
                            border-radius: 50%;
                            width: 30px;
                            height: 30px;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            transition: all .3s linear;
                            font-size: 8px !important;

                            &:hover{
                                background-color: var(--rojo);
                                /*width: 150px;
                                border-radius: 12px;*/
                                color: white !important;
                            }
                        }
                    }
                }

                & .pack-links-top{
                    top: 0;
                    transform: translateY(-50px);
                }
                &:hover{
                    box-shadow: 0px 0px 6px 2px #ccc;
                    transform: translateY(-10px);

                    & .pack-links, & .pack-links-top{
                        transform: translateY(0);
                    }
                }
            }
        }
    </style>
@endpush

@section('content')
    <section id="landingReviews" class="section-py bg-body">
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
                @isset($radio)
                    <div class="packs">
                        @foreach ($collections as $pack)
                            @include('partials.radio-pack-card', ['pack' => $pack])
                        @endforeach
                        @if ($collections->isEmpty())
                            <h4 class="text-center text-black mt-2">Sin Packs</h4>
                        @endif
                    </div>
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $collections->links() }}
                    </div>
                @else
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
                @endisset
            </div>
        </div>
    </section>
@endsection