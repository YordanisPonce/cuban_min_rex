<div id="{{ $item['id'] }}" class="playlist-card pack-card" data-intro="{{ $item['url'] }}">
    <div class="cover ph" style="aspect-ratio:16/10">
        @if ( $item['isNew'])
            <div class="badge">NEW</div>
        @endif
        @if ($item['isExclusive'])
            <div class="lock"><i class="fa-solid fa-lock"></i></div>
            <div class="badge exclusive">EXCLUSIVO</div>
        @endif
        <div class="play-overlay" onclick="handlePlay({{ $item['id'] }})"><i class="fas fa-play"></i></div>
        <img class="playlist-cover" src="{{ $item['img'] }}"/>
    </div>
    <div class="card-body">
        <div class="card-title"><i class="fas fa-bolt text-primary"></i> <span class="pack-name">{{ $item['title'] }}</span></div>
        <div class="card-meta">
            <div>
                @foreach ($item['genre'] as $g)
                    <div class="badge">{{ $g }}</div>  
                @endforeach
            </div>
            <span>BPM {{ $item['bpm'] }}</span>
            <span><i class="fas fa-dollar-sign"></i> {{ $item['price'] }}</span>
        </div>
        <div class="card-footer">
            <div class="dj-info">
                <div class="dj-avatar"><i class="fas fa-user"></i></div>
                <div>
                    <span class="dj-name">{{ $item['artist'] }}</span>
                    <span class="dj-sub">Real Records</span>
                </div>
            </div>
            <div class="card-actions">
                <span><i class="fas fa-fire text-primary"></i> {{ $item['downloads'] }}</span>
                @if ($item['canDownload'])
                    <a href="{{ $item['downloadLink'] }}" target="_blank"><i class="fas fa-download"></i></a>
                @else
                    <a href="{{ $item['addToCart'] }}" target="_blank"><i class="fas fa-shopping-cart"></i></a>
                @endif
            </div>
        </div>
    </div>
</div>