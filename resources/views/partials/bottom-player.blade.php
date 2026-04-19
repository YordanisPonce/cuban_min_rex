<!-- BOTTOM PLAYER -->
<div class="bottom-player" id="bottom-player">
    <div class="player-inner">
        <div class="player-track">
            <img id="player-img" src="" alt="">
            <div class="track-info">
                <div class="track-title" id="player-title">—</div>
                <div class="track-artist" id="player-artist">—</div>
            </div>
        </div>
        <div class="player-controls">
            <audio style="width: 100%" id="plyr-audio-player" controls></audio>
            <button class="btn btn-primary-reverse" name="close" onclick="closePlayer()"><i class="fa-solid fa-close"></i></button>
        </div>
    </div>
</div>
<script>
    new Plyr("#plyr-audio-player");
</script>

