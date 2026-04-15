
@php
    use Carbon\Carbon;
@endphp

<div id="{{ $item->id }}" class="playlist-card pack-card">
    <div class="cover ph" style="aspect-ratio:16/10">
        @if ( Carbon::parse($item->created_at)->isCurrentDay() )
            <div class="badge">NEW</div>
        @endif
        <!--<div class="lock"><i class="fa-solid fa-lock"></i></div>-->
        <div class="play-overlay" onclick="handlePlay({{ $item->id }})"><i class="fas fa-play"></i></div>
        <img class="playlist-cover" src="{{ $item->cover ? $item->getCoverUrl() : $item->user->photo ?? config('app.logo_alter') }}"/>
    </div>
    <div class="card-body">
        <div class="card-title"><a href="{{ route('playlist.show', $item->name)}}"><i class="fas fa-bolt text-primary"></i> {{ $item->name }}</a></div>
        <div class="card-meta">
            <span><i class="fas fa-music"></i> {{ $item->items->count() }} </span>
            <span>{{ $item->bpm ?? '' }}</span>
            <span><i class="fas fa-dollar-sign"></i> {{ $item->price }}</span>
        </div>
        <div class="card-footer">
            <div class="dj-info">
                <div class="dj-avatar"><i class="fas fa-user"></i></div>
                <div>
                    <span class="dj-name">{{ $item->user->name }}</span>
                    <span class="dj-sub">{{ $item->folder?->name ?? '' }}</span>
                </div>
            </div>
            <div class="card-actions">
                <span><i class="fas fa-fire text-primary"></i> {{ $item->downloads->count() }}</span>
                @if ($item->canBeDownload())
                    <a href="{{ route('playlist.download', str_replace(' ', '_' , $item->name)) }}"><i class="fas fa-download"></i></a>
                @else
                    <a href="{{ route('playlist.add.cart', str_replace(' ', '_' , $item->name) ) }}"><i class="fas fa-shopping-cart"></i></a>
                @endif
            </div>
        </div>
    </div>
</div>
