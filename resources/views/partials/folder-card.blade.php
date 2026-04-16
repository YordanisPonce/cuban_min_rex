
@php
    use Carbon\Carbon;
@endphp

<div id="{{ $item->id }}" class="playlist-card pack-card" onclick="window.location = '{{ route('playlist.list', ['folder' => str_replace(' ','_', $item->name)]) }}'">
    <div class="cover ph" style="aspect-ratio:16/10">
        <img class="playlist-cover" src="{{ $item->cover_image ?? config('app.logo_alter') }}"/>
    </div>
    <div class="card-body">
        <div class="card-title"><a href=""><i class="fas fa-bolt text-primary"></i> {{ $item->name }}</a></div>
    </div>
</div>
