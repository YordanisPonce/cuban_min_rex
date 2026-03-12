@php
use App\Models\Cart;
$cover = $item->cover ? $item->getCoverUrl() : ($item->user?->photo ? $item->user?->photo : config('app.logo'));
@endphp

<div class="col-lg-2 col-md-3 col-sm-4 col-6">
    <div class="list-card card bg-transparent" style="max-height: 250px;">
        <div class="card-body p-2">
            <div class="position-relative overflow-hidden" style="height: 150px;">
                <div>
                    <img src="{{$cover}}" alt="{{$item->name}}" class="rounded img-fluid bg-black" style="height: 150px; width: 100%;">
                </div>
                <p class="position-absolute start-0 bottom-0 p-1 m-0 d-flex align-items-center gap-1"><i class="icon-base ti tabler-music"></i> {{$item->items->count()}}</p>
                <a 
                    class="btn btn-success rounded-circle position-absolute" 
                    style="width: 50px; height: 50px; padding: 10px; right: 10px" 
                    data-name="{{$item->name}}"
                    data-status="pause"
                    onclick='playList(this,@json($item->getPlayList()),@json($item->items->pluck("name")->toArray()))'
                ><i class="icon-base ti tabler-player-play-filled"></i></a>
            </div>
            <div class="p-2">
                <div style="font-size: 18px" class="overflow-hidden"><a href="{{ route('playlist.show', $item->id) }}" class="w-100 text-truncate">{{$item->name}}</a></div>
                <div class="d-flex align-items-center mt-2">
                    <div class="avatar avatar-sm me-2 overflow-hidden rounded-circle position-relative">
                        <img src="{{$item->user?->photo ? $item->user?->photo : config('app.logo')}}" alt="{{$item->user?->name}}" class="rounded-circle img-fluid">
                        <div class="dark-screen" style="background-color: rgba(0, 0, 0, 0.4);"></div>
                    </div>
                    <div>
                        <p class="mb-0" style="font-size: 0.8rem;">{{$item->user?->name ?? 'CubanPool'}}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>