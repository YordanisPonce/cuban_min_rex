<div class="dj-card">
    <img class="dj-card-img" src="{{ $item['img'] }}" alt="{{ $item['name'] }}" loading="lazy">
    <div class="dj-card-overlay"></div>
    <div class="dj-card-info">
        <div class="dj-card-name">{{ $item['name'] }}</div>
        <a class="dj-card-btn" href="{{ route('dj', str_replace(' ', '_', $item['name'])) }}">VER PERFIL</a>
    </div>
</div>
