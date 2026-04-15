<div class="track-card" id="{{ $item['id'] }}" data-intro="{{ $item['url'] }}">
    <div class="track-left">
        <div class="track-thumb">
            <img src="{{ $item['img'] }}" alt="{{ $item['title'] }}" loading="lazy">
            {{ $item['isNew'] ? '<span class="tag-new">NEW</span>' : '' }}
        </div>
        <div class="track-info">
            <div class="track-title-row">
                <span class="track-title">{{ $item['title'] }}</span>
            </div>
            <div class="track-artist">{{ $item['artist'] }}</div>
            <div class="track-waveform">
                <button class="play-btn" onclick="handlePlay({{ $item['id'] }})"><i class="fas fa-play"></i></button>
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
        <div class="track-price">
            <div class="track-actions" style="color: var(--fg-muted); font-size: 0.8rem;">
                @if (!$item['canDownload'])
                    <span>{{ $item['price'] }} USD</span>@if ($cup_aviable)<span>{{ $item['price'] * $setting->currency_convertion_rate }} CUP</span>@endif
                @endif
            </div>
            <div class="track-bottom-actions">
                <a href="{{ $item['usd_pay'] }}" class="btn btn-outline" title="PAGO USD"><i
                        class="ti tabler-shopping-cart-plus"></i> USD</a>
                @if ($cup_aviable)
                    <a href="{{ $item['cup_pay'] }}" class="btn btn-outline" title="PAGO CUP"><i
                            class="ti tabler-credit-card"></i> CUP</a>
                @endif
            </div>
        </div>
    </div>
</div>
