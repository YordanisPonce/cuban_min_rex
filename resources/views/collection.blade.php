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
                                                            <spam class="flex gap-sm-10 gap-1 text-nowrap">
                                                                @auth
                                                                    @if (Auth::user()->hasActivePlan())
                                                                        <i><a style="display: flex; width: 20px"
                                                                                href="{{ route('file.download', $file['id']) }}">{{ svg('entypo-download') }}</a></i>
                                                                    @else
                                                                        <i>$ {{ $file['price'] }}</i>
                                                                        <i><a style="display: flex; width: 20px"
                                                                                href="">{{ svg('vaadin-cart') }}</a></i>
                                                                        <i><a style="display: flex; width: 20px"
                                                                                href="">{{ svg('vaadin-play') }}</a></i>
                                                                    @endif
                                                                @else
                                                                    <i>$ {{ $file['price'] }}</i>
                                                                    <i><a style="display: flex; width: 20px"
                                                                            href="">{{ svg('vaadin-cart') }}</a></i>
                                                                @endauth
                                                                <i><a style="display: flex; width: 20px" class="cursor-pointer" data-url="{{$file['url']}}" data-state="pause" onclick="playAudio(this)"
                                                                    >{{ svg('vaadin-play') }}</a></i>
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