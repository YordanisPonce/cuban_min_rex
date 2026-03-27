@extends('layouts.app')

@php
    $cover = $playlist->cover ? $playlist->getCoverUrl() : ($playlist->user?->photo ? $playlist->user?->photo : config('app.logo'));
    
    $success = session('success');
    $error = session('error');
@endphp

@section('title', "PlayLists – ".config('app.name'))

@push('styles')
<link rel="stylesheet" href="{{ asset('/assets/vendor/libs/plyr/plyr.css') }}" />
<style>
    .playlist-card{
        background-color: #12131C77 !important;
        margin: 0 auto !important;
        padding: 0 auto;

        &>.card-body{
            margin: 0 !important;
            padding: 2rem !important;
            background-color: #12131CAA !important;
        }
    }

    section#audioPlayer{
        transition: all 0.3s ease-in;
        transform: translateY(160px);
    }
    
    .landing-footer .footer-top {
        border-top-left-radius: 0rem !important;
        border-top-right-radius: 0rem !important;
    }

    .audio-player-controls:hover{
        transform: scale(1.5);
    }

    #audioPlayer{
        position: sticky;
        bottom: 0;
        width: 100%;
        z-index: 10;
    }

    footer{
        z-index: 11;
    }

    .btn-buy{
        transition: all 0.3s ease !important;

        &>span{
            transition: all 0.4s ease-in !important;
        }

        &:hover{
            width: 100px !important;
            border-radius: 17px !important;
            gap: 10px;

            &>span{
                display: block !important;
            }
        }
    }

    table{
        width: 100%;
        border-collapse: collapse;
        color: #ccc;

        &>thead{
            border-bottom: 1px solid #ccc;
            &>tr{
                &>th{
                    padding: 0.75rem;
                    text-align: left;
                    font-weight: 500;
                }
                &>th:nth-child(1){
                    width: 40px;
                }
                &>th:nth-child(3){
                    width: 120px;
                }
            }
        }
        &>tbody{
            padding: 10px auto !important;
            &>tr{
                margin: 10px auto !important;
                transition: all 0.2s ease-in !important;
                &>td{
                    padding: 0.75rem;

                    &>div>a:hover{
                        color: var(--bs-primary) !important;
                    }
                }
                &>td:nth-child(3){
                    transform: scale(0);
                    transition: all 0.2s ease-in !important;
                }
                &:hover{
                    background-color: rgba(255, 255, 255, 0.1);
                    cursor: pointer;
                    border-radius: 12px !important;

                    &>td:nth-child(3){
                        transform: scale(1);
                    }
                }
            }
        }
    }

    @media (max-width: 768px) {
        table>tbody>tr>td:nth-child(3){
            transform: scale(1);
        }
    }
</style>
@endpush

@section('content')
<section id="landingReviews" class="section-py bg-body mt-10">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="playlist-card card">
                    <div class="card-header">
                        <div class="container">
                            <div class="row g-2">
                                <div class="col-md-3 col-12 d-flex justify-content-center">
                                    <img src="{{ $cover }}" class="rounded" alt="{{ $playlist->name }}" style="height: 200px; width: 200px">
                                </div>
                                <div class="col-md-9 col-12">
                                    <div class="mb-4 mt-5">
                                        <span class="badge bg-label-primary">PlayList</span>
                                    </div>
                                    <div class="mb-3">
                                        <h1>{{ $playlist->name }}</h1>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center mt-2">
                                            <div class="avatar avatar-sm me-2 overflow-hidden rounded-circle position-relative">
                                                <img src="{{$playlist->user?->photo ? $playlist->user?->photo : config('app.logo')}}" alt="{{$playlist->user?->name}}" class="rounded-circle img-fluid">
                                                <div class="dark-screen" style="background-color: rgba(0, 0, 0, 0.4);"></div>
                                            </div>
                                            <div>
                                            <a href="{{ route('dj', str_replace(' ', '_', $playlist->user?->name ?? 'CubanPool'))}}" class="mb-0" style="font-size: 0.8rem;">{{$playlist->user?->name ?? 'CubanPool'}}</a> • {{$playlist->created_at->year}} • {{ $playlist->items()->count() }} {{ Str::plural('Canción', $playlist->items()->count()) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-4 mb-4">
                            <div class="d-flex align-items-center gap-3">
                                <a id="playlist-btn" class="btn btn-success rounded-circle" style="width: 50px; height: 50px; padding: 10px;" onclick="playList()" data-status="pause"><i class="icon-base ti tabler-player-play-filled"></i></a>
                                @auth
                                    @if(auth()->user()->hasActivePlan())
                                        <a href="{{route('playlist.download', $playlist->id )}}" class="btn btn-light rounded-circle" style="width: 30px; height: 30px; padding: 5px;"><i class="icon-base ti tabler-download"></i></a>
                                    @else
                                        @if($playlist->isInCart())
                                            <a href="{{ route('playlist.remove.cart', $playlist->id) }}" class="btn btn-light rounded-circle text-nowrap overflow-hidden btn-buy" style="width: 30px; height: 30px; padding: 5px;"><span class="d-none">$ {{$playlist->price}}</span> <i class="icon-base ti tabler-shopping-cart-x"></i></a>
                                        @else
                                            <a href="{{ route('playlist.add.cart', $playlist->id) }}" class="btn btn-light rounded-circle text-nowrap overflow-hidden btn-buy" style="width: 30px; height: 30px; padding: 5px;"><span class="d-none">$ {{$playlist->price}}</span> <i class="icon-base ti tabler-shopping-cart-plus"></i></a>
                                        @endif
                                    @endif
                                @else
                                    @if($playlist->isInCart())
                                        <a href="{{ route('playlist.remove.cart', $playlist->id) }}" class="btn btn-light rounded-circle text-nowrap overflow-hidden btn-buy" style="width: 30px; height: 30px; padding: 5px;"><span class="d-none">$ {{$playlist->price}}</span> <i class="icon-base ti tabler-shopping-cart-x"></i></a>
                                    @else
                                        <a href="{{ route('playlist.add.cart', $playlist->id) }}" class="btn btn-light rounded-circle text-nowrap overflow-hidden btn-buy" style="width: 30px; height: 30px; padding: 5px;"><span class="d-none">$ {{$playlist->price}}</span> <i class="icon-base ti tabler-shopping-cart-plus"></i></a>
                                    @endif
                                @endauth
                            </div>
                        </div>
                        <div class="row g-4">
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Canción</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($playlist->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="text-wrap">{{ $item->title ?? 'Archivo sin nombre' }}</td>
                                        <td> 
                                            <div class="d-flex gap-2 align-items-center justify-content-end">
                                                @auth
                                                    @if(auth()->user()->hasActivePlan())
                                                        <a href="{{ route('playlist.download_item', [$playlist->id, $item->id]) }}"><i class="icon-base ti tabler-download"></i></a>
                                                    @else
                                                        @if($item->isInCart())
                                                            <a href="{{ route('playlist.remove.item.cart', [$playlist->id, $item->id]) }}" class="d-flex gap-2 align-items-center justify-content-center"><span class="d-flex gap-1">$ {{$item->price}}</span> <i class="icon-base ti tabler-shopping-cart-x"></i></a>
                                                        @else
                                                            <a href="{{ route('playlist.add.item.cart', [$playlist->id, $item->id]) }}" class="d-flex gap-2 align-items-center justify-content-center"><span class="d-flex gap-1">$ {{$item->price}}</span> <i class="icon-base ti tabler-shopping-cart-plus"></i></a>
                                                        @endif   
                                                    @endif
                                                @else
                                                    @if($item->isInCart())
                                                        <a href="{{ route('playlist.remove.item.cart', [$playlist->id, $item->id]) }}" class="d-flex gap-2 align-items-center justify-content-center"><span class="d-flex gap-1">$ {{$item->price}}</span> <i class="icon-base ti tabler-shopping-cart-x"></i></a>
                                                    @else
                                                        <a href="{{ route('playlist.add.item.cart', [$playlist->id, $item->id]) }}" class="d-flex gap-2 align-items-center justify-content-center"><span class="d-flex gap-1">$ {{$item->price}}</span> <i class="icon-base ti tabler-shopping-cart-plus"></i></a>
                                                    @endif 
                                                @endauth
                                                <a data-index="{{$index}}" onclick="playAudio(this.dataset.index)"><i class="icon-base ti tabler-player-play-filled"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section id="audioPlayer">
    <div class="container">
        <div class="row">
            <!-- Audio Player -->
            <div class="col-12">
                <div class="card">
                    <h5 class="card-header d-flex justify-content-between">
                        <span class="cursor-pointer audio-player-controls hidden-xl" onclick="playPrevAudio()"><i class="icon-base ti tabler-chevron-left icon-md scaleX-n1-rtl"></i></span>
                        <span id="plyr-audio-name" class="d-block w-100 text-nowrap overflow-hidden"
                            style="text-overflow:ellipsis; text-align:center">Audio</span>
                        <span class="cursor-pointer audio-player-controls hidden-xl" onclick="playNextAudio()"><i class="icon-base ti tabler-chevron-right icon-md scaleX-n1-rtl"></i></span>
                    </h5>
                    <div class="card-body">
                        <audio class="w-100" id="plyr-audio-player" type="audio/mp3" src="https://demos.pixinvent.com/vuexy-html-admin-template/assets/audio/Water_Lily.mp3" controls></audio>
                    </div>
                </div>
            </div>
            <!-- /Audio Player -->
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('/assets/vendor/libs/plyr/plyr.js') }}"></script>
<script>
    new Plyr("#plyr-audio-player");

    const audioPlayer = document.getElementById('audioPlayer');
    const audio = document.getElementById('plyr-audio-player');
    const tracks = @json($playlist->getPlayList());
    const names = @json($playlist->items()->get()->pluck('title')->toArray());

    if (tracks.length <= 1) {
        document.querySelectorAll('.audio-player-controls').forEach(control => {
            control.style.display="none";
        });
    }

    function showAudioPlayer() {
        audioPlayer.style.transform = 'translateY(0)';
        audio.play();
    }

    function hiddenAudioPlayer() {
        audioPlayer.style.transform = 'translateY(160px)';
        audio.pause();
    }

    function playList(){
        const play = document.getElementById('playlist-btn');
        if(play.dataset.status==='pause'){
            play.innerHTML = '<i class="icon-base ti tabler-player-pause-filled"></i>';
            play.dataset.status = 'play';
            audio.src = tracks[0];
            const name = names[0] === null ? 'Archivo sin nombre' : names[0];
            document.getElementById('plyr-audio-name').textContent = "{{$playlist->name}}" + ' - ' + name;
            showAudioPlayer();
        } else {
            play.innerHTML = '<i class="icon-base ti tabler-player-play-filled"></i>';
            play.dataset.status = 'pause';
            hiddenAudioPlayer();
        }
    }

    function playNextAudio() {
        const currentIndex = tracks.findIndex(track => track === audio.src);
        const nextIndex = (currentIndex + 1) % tracks.length;
        const name = names[nextIndex] === null ? 'Archivo sin nombre' : names[nextIndex];
        audio.src = tracks[nextIndex];
        document.getElementById('plyr-audio-name').textContent = "{{$playlist->name}}" + ' - ' + name;
        audio.play();
    }

    function playAudio(index){
        audio.src = tracks[index];
        const name = names[index] === null ? 'Archivo sin nombre' : names[index];
        document.getElementById('plyr-audio-name').textContent = "{{$playlist->name}}" + ' - ' + name;
        showAudioPlayer();
    }

    // Cuando termina un audio, cargar el siguiente
    audio.addEventListener("ended", () => {
        playNextAudio();
    });
</script>

@isset($error)
<script>
    Swal.fire({
        title: 'Error',
        text: '{{ $error }}',
        icon: 'error'
    });
</script>
@endisset
@isset($success)
<script>
    Swal.fire({
        title: ' ',
        text: '{{ $success }}',
        icon: 'success'
    });
</script>
@endisset
@endpush