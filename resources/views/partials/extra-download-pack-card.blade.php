<div class="pack-card">
    @if($isFeatured)
        <span class="pack-tag">MÁS POPULAR</span>
    @endif
    <div class="pack-price"><sup>$</sup>{{ number_format($item->price, 2) }}</div>
    <div class="pack-qty"><b>{{ $item->extra_downloads }}</b><span>descargas</span></div>
    <div class="pack-per">${{ number_format($item->price / $item->extra_downloads, 2) }} por descarga</div>
    <button class="pack-btn" data-id="{{ $item->id }}">Obtener pack</button>
</div>
