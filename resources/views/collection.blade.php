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
                                                <li class="list-group-item">
                                                    <div class=" row">
                                                        <div class="col-9">
                                                            <spam class="d-block w-100 text-nowrap overflow-hidden" style="text-overflow: ellipsis;">{{ $file['name'] }}</spam>
                                                        </div>
                                                        <div class="col-3">
                                                            <spam class="flex gap-sm-10 gap-1 text-nowrap justify-content-end">
                                                                @auth
                                                                    @if (Auth::user()->hasActivePlan())
                                                                        <i><a style="display: flex; width: 20px"
                                                                            href="{{ route('file.download', $file['id']) }}">{{ svg('entypo-download') }}</a></i>
                                                                    @else
                                                                        <i>$ {{ $file['price'] }}</i>
                                                                        <i><a style="display: flex; width: 20px"
                                                                            href="{{ route('file.pay', $file['id'])}}">{{ svg('vaadin-cart') }}</a></i>
                                                                    @endif
                                                                @else
                                                                    <i>$ {{ $file['price'] }}</i>
                                                                    <i><a style="display: flex; width: 20px"
                                                                            href="{{ route('file.pay', $file['id'])}}">{{ svg('vaadin-cart') }}</a></i>
                                                                @endauth
                                                                <i>
                                                                    @if ($file['isZip'])
                                                                    <a style="color: transparent">zip</a>
                                                                    @else
                                                                    <a style="display: flex; width: 20px" class="cursor-pointer" data-url="{{$file['url']}}" data-state="pause" onclick="playAudio(this)"
                                                                    >{{ svg('vaadin-play') }}</a>
                                                                    @endif
                                                                </i>
                                                            </spam>
                                                        </div>
                                                    </div>
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
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="pb-1 mb-6">Colecciones Relacionadas</h5>
                            <hr class="my-6" />
                            <div class="row mb-12 g-6">
                                @foreach ($relationeds as $coll)
                                <div class="col-12">
                                    <a class="card card-relationed relative" href="{{ route('collection.show', $coll->id) }}">
                                        <div class="row g-0">
                                            <div class="col-md-4">
                                                <img class="card-img card-img-left w-100" style="max-height: 80px;" src="{{ $coll->image ? $coll->image : asset('assets/img/front-pages/icon/collection.png') }}" />
                                            </div>
                                            <div class="col-md-8">
                                                <div class="card-body p-3">
                                                    <h5 class="card-title">{{$coll->name}}</h5>
                                                    <p class="card-text"><small class="text-body-secondary">Subido por {{$coll->user->name}}</small></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="dark-screen d-none"></div>
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

@push('scripts')
<script>
    let currentAudio = null;

    function stopCurrentAudio() {
        if (currentAudio) {
            currentAudio.pause();
            currentAudio.currentTime = 0;
            currentAudio = null;
        }
    }

    function playAudio(element){
        let audio = document.createElement('audio');

        let binaryData = element.dataset.url;

        let byteCharacters = atob(binaryData);
        let byteNumbers = new Array(byteCharacters.length);
        for (let i = 0; i < byteCharacters.length; i++) {
            byteNumbers[i] = byteCharacters.charCodeAt(i);
        }
        let byteArray = new Uint8Array(byteNumbers);
        let blob = new Blob([byteArray], { type: 'audio/mpeg' });
        let url = URL.createObjectURL(blob);
        
        audio.src = url;

        if(element.dataset.state == "pause"){
            stopCurrentAudio();
            currentAudio = audio;
            audio.play();
            element.innerHTML = '{{ svg('vaadin-pause') }}';
            element.dataset.state = "play";
        } else {
            element.innerHTML = '{{ svg('vaadin-play') }}';
            stopCurrentAudio();
            element.dataset.state = "pause";
        }
        
        audio.addEventListener('ended', () => {
            element.innerHTML = '{{ svg('vaadin-play') }}';
            element.dataset.state = "pause";
        });
    }
</script>
@endpush