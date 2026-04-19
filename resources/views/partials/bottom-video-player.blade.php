<!-- BOTTOM PLAYER -->
<div class="bottom-player container" id="bottom-player">
    <div class="player-inner">
        <div class="player-track">
            <img id="player-img" src="" alt="">
            <div class="track-info">
                <div class="track-title" id="player-title">Nombre del Video</div>
                <div class="track-artist" id="player-artist">Autor</div>
            </div>
            <div class="close">
                <button onclick="closePlayer()"><i class="fa-solid fa-close"></i></button>
            </div>
        </div>
        <div class="video-player">
            <video class="w-100" poster="{{ config('app.logo') }}" id="plyr-video-player" playsinline controls></video>
        </div>
    </div>
</div>
<script>
    new Plyr("#plyr-video-player");
</script>
