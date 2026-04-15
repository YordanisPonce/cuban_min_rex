<div class="track-card" id="{{$item['id']}}" data-intro="{{ $item['url'] }}">
    <div class="track-left">
        <div class="track-thumb">
            <img src="{{$item['img']}}" alt="{{$item['title']}}" loading="lazy">
            {{$item['isNew'] ? '<span class="tag-new">NEW</span>':''}}
        </div>
        <div class="track-info">
            <div class="track-title-row">
                <span class="track-title">{{$item['title']}}</span>
                <!--<span class="track-type">{{$item['type']}}</span>-->
            </div>
            <div class="track-artist">{{$item['artist']}}</div>
            <div class="track-waveform">
                <button class="play-btn" onclick="handlePlay({{$item['id']}})"><i class="fas fa-play"></i></button>
                <div class="waveform">
                    @for ($i = 0; $i < 60; $i++)  
                        <div class="bar"></div>
                    @endfor
                    @for ($i = 0; $i < 60; $i++)  
                        <div class="bar hidden-mobile"></div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
    <div class="track-right">
        <div class="track-genre-info">
            @foreach ($item['genre'] as $genre)
                <span class="track-badge club">{{ $genre }}</span>
            @endforeach
        </div>
        <div class="track-actions">
            <span class="text-primary"><i class="fa-solid fa-fire"></i> {{$item['downloads']}}</span>
            @if ( $item['canDownload'])
                <a href="{{$item['downloadLink']}}" target="_blank"><i class="fas fa-download"></i></a>
            @else
                <a href="{{ $item['addToCart'] }}" class="cart-btn"><i class="ti tabler-shopping-cart-plus"></i></a>
            @endif
        </div>
        <div class="track-bottom-actions"> 
            <span> BPM {{$item['bpm']}} · {{$item['key']}} </span>
            @if ( !$item['canDownload'] )
                <span><i class="fa-solid fa-dollar-sign"></i> {{$item['price']}}</span>
            @endif
        </div>
    </div>
</div>