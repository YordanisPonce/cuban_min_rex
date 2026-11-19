@extends('layouts.app')

@section('title', 'PlayLists - ' . config('app.name'))

@push('styles')
    <style>
        #wloader{
            display: none;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --border: #2a2520;
            --surface: #1e1b17;
            --ok: #3ba55d;
            --danger: #e04040;
            --maxw: 100%;
            --gutter: 10%;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        img {
            display: block;
        }

        /* ============ SKELETON ============ */
        #skeleton {
            position: fixed;
            inset: 0;
            z-index: 300;
            background: var(--bg);
            overflow: hidden;
            transition: opacity .45s ease;
            padding-top: 60px;
        }

        #skeleton.hide {
            opacity: 0;
            pointer-events: none;
        }

        .sk {
            position: relative;
            overflow: hidden;
            background: #4a4238;
            border-radius: 10px;
        }

        .sk::after {
            content: '';
            position: absolute;
            inset: 0;
            transform: translateX(-100%);
            background: linear-gradient(90deg, transparent, rgba(245, 166, 35, .08) 40%, rgba(255, 255, 255, .07) 50%, rgba(245, 166, 35, .08) 60%, transparent);
            animation: shimmer 1.4s infinite;
        }

        @keyframes shimmer {
            100% {
                transform: translateX(100%);
            }
        }

        .sk-wrap {
            max-width: var(--maxw);
            margin: 0 auto;
            padding: 30px var(--gutter);
        }

        .sk-hero {
            height: 60vh;
            border-radius: 14px;
            margin-bottom: 2rem;
        }

        .sk-title {
            height: 22px;
            width: 240px;
            margin-bottom: 1rem;
        }

        .sk-folders {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 1rem;
            margin-bottom: 2.2rem;
        }

        .sk-folders .sk {
            height: 150px;
        }

        .sk-list .sk {
            height: 64px;
            margin-bottom: .6rem;
        }

        .hero {
            position: relative;
            overflow: hidden;
            padding: 0;
            border-bottom: 1px solid var(--border);
            background: var(--bg);
            min-height: 80dvh;
        }

        .hero-track {
            display: grid;
            min-height: clamp(20rem, 48vh, 34rem);
        }

        .hero-slide {
            grid-area: 1 / 1;
            position: absolute;
            display: flex;
            align-items: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity .9s ease, visibility 0s linear .9s;
        }

        .hero-slide.is-active {
            opacity: 1;
            visibility: visible;
            z-index: 1;
            transition: opacity .9s ease, visibility 0s;
        }

        .hero-bg {
            position: absolute;
            inset: 0;
            overflow: hidden;
        }

        .hero-bg img, .hero-bg video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(1.07);
            transition: transform 8s ease-out;
            aspect-ratio: auto;
            position: relative;
            max-width: 100%;
        }

        .hero-slide.is-active .hero-bg img, .hero-bg video {
            transform: scale(1);
        }

        .hero-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(15, 13, 11, .97) 0%, rgba(15, 13, 11, .85) 38%, rgba(15, 13, 11, .35) 70%, rgba(15, 13, 11, .55) 100%);
        }

        .hero-inner {
            position: relative;
            z-index: 2;
            max-width: var(--maxw);
            margin: 0 auto;
            padding: 2.6rem var(--gutter) 3.6rem;
            width: 100%;
        }

        .hero-inner>* {
            opacity: 0;
            transform: translateY(14px);
            transition: opacity .6s ease, transform .6s ease;
        }

        .hero-slide.is-active .hero-inner>* {
            opacity: 1;
            transform: none;
        }

        .hero-slide.is-active .hero-inner> :nth-child(1) {
            transition-delay: .15s;
        }

        .hero-slide.is-active .hero-inner> :nth-child(2) {
            transition-delay: .27s;
        }

        .hero-slide.is-active .hero-inner> :nth-child(3) {
            transition-delay: .39s;
        }

        .hero-slide.is-active .hero-inner> :nth-child(4) {
            transition-delay: .5s;
        }

        .hero-kicker {
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: var(--fg);
            margin-bottom: .8rem;
        }

        .hero-kicker em {
            color: var(--primary);
            font-style: normal;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 900;
            font-style: italic;
            text-transform: uppercase;
            line-height: 1.02;
            letter-spacing: -.01em;
            visibility: visible !important;
        }

        .hero-title span {
            display: block;
            background: linear-gradient(90deg, var(--primary), #ffcf6e);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .hero-text {
            margin: .9rem 0 1.4rem;
            font-size: .9rem;
            color: #cfc4b2;
            max-width: 26rem;
            line-height: 1.55;
        }

        .btn-cta {
            display: inline-flex;
            align-items: center;
            gap: .6rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #14100b;
            padding: .8rem 1.7rem;
            border-radius: 8px;
            font-weight: 800;
            font-size: .82rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            border: none;
            cursor: pointer;
            font-family: inherit;
            transition: all .2s;
            box-shadow: 0 8px 24px rgba(245, 166, 35, .3);
        }

        .btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(245, 166, 35, .45);
        }

        .hero-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 5;
            width: 2.6rem;
            height: 2.6rem;
            border-radius: 50%;
            border: 1px solid rgba(240, 232, 216, .25);
            background: rgba(15, 13, 11, .55);
            backdrop-filter: blur(6px);
            color: var(--fg);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .85rem;
            opacity: 0;
            transition: opacity .25s, background .2s, border-color .2s;
        }

        .hero-arrow.prev {
            left: max(.8rem, calc((100% - var(--maxw)) / 2 + .6rem));
        }

        .hero-arrow.next {
            right: max(.8rem, calc((100% - var(--maxw)) / 2 + .6rem));
        }

        .hero:hover .hero-arrow,
        .hero-arrow:focus-visible {
            opacity: 1;
        }

        .hero-arrow:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: #14100b;
        }

        .hero-nav {
            position: absolute;
            bottom: 1.1rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: .55rem;
            z-index: 5;
        }

        .hero-dot {
            position: relative;
            width: 8px;
            height: 8px;
            padding: 0;
            border: none;
            border-radius: 99px;
            background: rgba(240, 232, 216, .32);
            cursor: pointer;
            transition: all .25s;
        }

        .hero-dot::after {
            content: '';
            position: absolute;
            inset: -10px -4px;
        }

        .hero-dot.active {
            background: var(--primary);
            width: 22px;
        }

        .hero-pause {
            margin-left: .4rem;
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 50%;
            border: 1px solid rgba(240, 232, 216, .3);
            background: rgba(15, 13, 11, .45);
            color: var(--fg);
            font-size: .55rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-pause:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .hero-progress {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 3px;
            background: rgba(240, 232, 216, .1);
            z-index: 5;
        }

        .hero-progress span {
            display: block;
            height: 100%;
            background: var(--primary);
            transform-origin: left;
            transform: scaleX(0);
        }

        .hero-progress span.run {
            animation: heroProgress var(--hero-interval, 6s) linear forwards;
        }

        @keyframes heroProgress {
            to {
                transform: scaleX(1);
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .hero-slide,
            .hero-slide.is-active {
                transition: none;
            }

            .hero-bg img {
                transition: none;
                transform: none;
            }

            .hero-inner>* {
                transition: none;
                transform: none;
            }
        }

        /* ============ CONTENT ============ */
        #content {
            opacity: 0;
            transition: opacity .5s ease;
        }

        #content.show {
            opacity: 1;
        }

        .main {
            padding-bottom: 6.5rem;
        }

        /* SECTIONS */
        .section {
            max-width: var(--maxw);
            margin: 0 auto;
            padding: 30px var(--gutter);
        }

        .section-head {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-bottom: 1.2rem;
        }

        .section-head h2 {
            font-size: 1.45rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -.01em;
        }

        .section-head h2 span {
            color: var(--primary);
        }

        .section-sub {
            font-size: .78rem;
            color: var(--muted);
            margin-top: .25rem;
        }

        /* FOLDER CAROUSEL */
        .folders-wrap {
            position: relative;
        }

        .folders {
            display: flex;
            gap: 1rem;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: .3rem .2rem 1rem;
            scrollbar-width: none;
        }

        .folders::-webkit-scrollbar {
            display: none;
        }

        .folder {
            position: relative;
            flex: 0 0 168px;
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 1/1.1;
            cursor: pointer;
            border: 1px solid var(--border);
            transition: all .25s;
            background: var(--card);
        }

        .folder img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .35s;
        }

        .folder::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(10, 8, 6, .2) 0%, transparent 35%, rgba(10, 8, 6, .94) 85%);
        }

        .folder:hover {
            transform: translateY(-4px);
            border-color: var(--primary);
            box-shadow: 0 14px 30px rgba(0, 0, 0, .55);
        }

        .folder:hover img {
            transform: scale(1.08);
        }

        .folder.active {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px var(--primary), 0 14px 30px rgba(245, 166, 35, .25);
        }

        .folder.active::after {
            background: linear-gradient(180deg, rgba(245, 166, 35, .15) 0%, transparent 40%, rgba(10, 8, 6, .94) 85%);
        }

        .folder-info {
            position: absolute;
            bottom: .75rem;
            left: .8rem;
            right: .8rem;
            z-index: 2;
        }

        .folder-info h4 {
            font-size: .82rem;
            font-weight: 800;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .folder-info small {
            font-size: .64rem;
            color: #cfc4b2;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: .35rem;
            margin-top: .3rem;
        }

        .folder-info small i {
            color: var(--primary);
            font-size: .6rem;
        }

        .folder.all {
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(160deg, rgba(245, 166, 35, .16), var(--card) 60%);
        }

        .folder.all::after {
            display: none;
        }

        .folder.all .all-inner {
            text-align: center;
        }

        .folder.all .all-icon {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #14100b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            margin: 0 auto .7rem;
            box-shadow: 0 8px 20px rgba(245, 166, 35, .35);
            transition: transform .3s;
        }

        .folder.all:hover .all-icon {
            transform: scale(1.1) rotate(-8deg);
        }

        .folder.all h4 {
            font-size: .85rem;
            font-weight: 900;
            text-transform: uppercase;
        }

        .folder.all small {
            font-size: .64rem;
            color: var(--muted);
            font-weight: 600;
        }

        .car-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(15, 13, 11, .85);
            border: 1px solid var(--border);
            color: var(--fg);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 5;
            font-size: .75rem;
            transition: all .2s;
        }

        .car-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .car-btn.prev {
            left: -10px;
        }

        .car-btn.next {
            right: -10px;
        }

        /* PLAYLIST LIST */
        .list-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .list-count {
            font-size: .78rem;
            color: var(--muted);
        }

        .list-count b {
            color: var(--primary);
            font-weight: 800;
        }

        .pl-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: .75rem 1rem;
            margin-bottom: .65rem;
            cursor: pointer;
            transition: all .22s;
            animation: plIn .35s ease both;
        }

        @keyframes plIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        .pl-item:hover {
            background: var(--card-hover);
            border-color: var(--primary);
            transform: translateX(6px);
            box-shadow: -4px 0 0 var(--primary), 0 10px 24px rgba(0, 0, 0, .4);
        }

        .pl-cover {
            position: relative;
            width: 56px;
            height: 56px;
            border-radius: 9px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .pl-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .3s;
        }

        .pl-item:hover .pl-cover img {
            transform: scale(1.12);
        }

        .pl-play {
            position: absolute;
            inset: 0;
            background: rgba(15, 13, 11, .55);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: .8rem;
            opacity: 0;
            transition: opacity .2s;
        }

        .pl-item:hover .pl-play {
            opacity: 1;
        }

        .pl-info {
            flex: 1;
            min-width: 0;
        }

        .pl-info h4 {
            font-size: .9rem;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pl-info p {
            font-size: .72rem;
            color: var(--muted);
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pl-folder-tag {
            font-size: .62rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            background: rgba(245, 166, 35, .12);
            color: var(--primary);
            padding: .28rem .6rem;
            border-radius: 99px;
            white-space: nowrap;
        }

        .pl-count {
            display: flex;
            align-items: center;
            gap: .4rem;
            font-size: .75rem;
            color: var(--muted);
            font-weight: 600;
            white-space: nowrap;
        }

        .pl-count i {
            color: var(--primary);
            font-size: .68rem;
        }

        .pl-open {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 1.5px solid var(--border);
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .65rem;
            transition: all .2s;
            flex-shrink: 0;
        }

        .pl-item:hover .pl-open {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(245, 166, 35, .08);
        }

        .pl-empty {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--muted);
            font-size: .85rem;
        }

        .pl-empty i {
            font-size: 2rem;
            color: var(--primary);
            display: block;
            margin-bottom: .8rem;
        }

        /* TOAST */
        .toast-wrap {
            position: fixed;
            bottom: 96px;
            right: 1.5rem;
            z-index: 200;
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }

        .toast {
            background: var(--surface);
            border: 1px solid var(--border);
            border-left: 3px solid var(--primary);
            color: var(--fg);
            font-size: .78rem;
            padding: .7rem 1rem;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .5);
            display: flex;
            align-items: center;
            gap: .6rem;
            animation: toastIn .3s ease;
        }

        .toast i {
            color: var(--primary);
        }

        @keyframes toastIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        /* RESPONSIVE */
        @media(max-width:860px) {
            .sk-folders {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media(max-width:768px) {

            .hero h1 {
                font-size: 2.2rem;
            }

            .folder {
                flex: 0 0 138px;
            }

            .pl-folder-tag {
                display: none;
            }

            .sk-folders {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
@endpush

@section('content')
    <!-- SKELETON -->
    <div id="skeleton" aria-hidden="true">
        <div class="sk-wrap">
            <div class="sk sk-hero"></div>
            <div class="sk sk-title"></div>
            <div class="sk-folders">
                <div class="sk"></div>
                <div class="sk"></div>
                <div class="sk"></div>
                <div class="sk"></div>
                <div class="sk"></div>
                <div class="sk"></div>
            </div>
            <div class="sk sk-title"></div>
            <div class="sk-list">
                <div class="sk"></div>
                <div class="sk"></div>
                <div class="sk"></div>
                <div class="sk"></div>
                <div class="sk"></div>
                <div class="sk"></div>
            </div>
        </div>
    </div>

    <!-- CONTENT -->
    <div id="content">
        <div class="main">

            <!-- HERO -->
            <section class="hero" id="hero" aria-roledescription="carousel" aria-label="Destacados"></section>

            <!-- FOLDERS CAROUSEL -->
            <section class="section">
                <div class="section-head" data-aos="fade-right" data-aos-delay="300">
                    <h2>Carpetas <span>de Playlists</span></h2>
                </div>
                <div class="folders-wrap">
                    <button class="car-btn prev" id="carPrev" aria-label="Anterior" data-aos="fade-in" data-aos-delay="300"><i
                            class="fas fa-chevron-left"></i></button>
                    <div class="folders" id="folders"></div>
                    <button class="car-btn next" id="carNext" aria-label="Siguiente" data-aos="fade-in" data-aos-delay="300"><i
                            class="fas fa-chevron-right"></i></button>
                </div>
            </section>

            <!-- PLAYLIST LIST -->
            <section class="section" id="playlistSection">
                <div class="section-head" data-aos="fade-in" data-aos-delay="100">
                    <h2 id="listTitle">Todas las <span>Playlists</span></h2>
                </div>
                <div class="list-meta" data-aos="fade-in" data-aos-delay="100">
                    <span class="list-count" id="listCount"></span>
                </div>
                <div id="playlistList"></div>
            </section>

        </div>
    </div><!-- /content -->
@endsection

@push('scripts')
    <script>
        // ===== Data =====
        const folders = @json($folders);

        const playlists = @json($playlists);

        // ===== Skeleton loader =====
        const skeleton = document.getElementById('skeleton'), content = document.getElementById('content');
        window.addEventListener('load', () => {
            setTimeout(() => {
                skeleton.classList.add('hide');
                content.classList.add('show');
                setTimeout(() => skeleton.remove(), 600);
            }, 1200);
        });

        // ===== Toasts =====
        const toastWrap = document.createElement('div'); toastWrap.className = 'toast-wrap'; document.body.appendChild(toastWrap);
        function showToast(msg, icon) {
            const t = document.createElement('div'); t.className = 'toast';
            t.innerHTML = '<i class="fas ' + (icon || 'fa-circle-info') + '"></i><span>' + msg + '</span>';
            toastWrap.appendChild(t);
            setTimeout(() => { t.style.transition = 'opacity .3s,transform .3s'; t.style.opacity = '0'; t.style.transform = 'translateY(8px)'; setTimeout(() => t.remove(), 300); }, 2800);
        }

        const HERO_CONFIG = {
            autoplay: true,
            interval: 6000,
            pauseOnHover: true,
            loop: true,
            slides: @json($banners)
        };

        (function initHero() {
            const hero = document.getElementById('hero');
            const slides = (HERO_CONFIG.slides || []).filter(s => s && s.image);
            if (!hero || !slides.length) return;

            const len = slides.length, multi = len > 1;
            const interval = Math.max(2000, +HERO_CONFIG.interval || 6000);
            const autoplay = multi && HERO_CONFIG.autoplay !== false;
            const loop = HERO_CONFIG.loop !== false;
            const esc = s => String(s == null ? 'Playlists' : s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
            const render_slide = (s, i) => {
                if(s.isVideo) {
                    return '<video src="' + esc(s.image) + '" alt="" decoding="async" ' + (i === 0 ? 'fetchpriority="high"' : 'loading="lazy"') + ' style="object-position:' + esc(s.position || 'center') + '; object-fit: fill" autoplay muted loop></video>'
                }
                return '<img src="' + esc(s.image) + '" alt="" decoding="async" ' + (i === 0 ? 'fetchpriority="high"' : 'loading="lazy"') + ' style="object-position:' + esc(s.position || 'center') + '; object-fit: cover">';
            }

            hero.style.setProperty('--hero-interval', interval + 'ms');
            hero.innerHTML =
                '<div class="hero-track">' + slides.map((s, i) => {
                    const tag = i === 0 ? 'h1' : 'h2';
                    const cta = s.cta ? '<button class="btn-cta" data-cta data-href="' + esc(s.cta.href) + '" data-toast="' + esc(s.cta.toast) + '">' + esc(s.cta.label) + ' <i class="fas fa-arrow-right"></i></button>' : '';
                    return '<article class="hero-slide' + (i === 0 ? ' is-active' : '') + '" role="group" aria-roledescription="slide" aria-label="' + (i + 1) + ' de ' + len + '">' +
                        '<div class="hero-bg">'+ render_slide(s, i) +'</div>' +
                        '<div class="hero-inner">' +
                        '<div class="hero-kicker">' + (s.kicker || 'Colecciones curadas para DJs') + '</div>' +
                        '<' + tag + ' class="hero-title">' + esc(s.title) + (s.accent ? '<span>' + esc(s.accent) + '</span>' : '<span>Para Tus Sesiones</span>') + '</' + tag + '>' +
                        '<p class="hero-text">' + (s.text ? esc(s.text) : 'Selecciones listas para pinchar, organizadas por carpetas según tu estilo y tu pista.') + '</p>' + cta +
                        '</div></article>';
                }).join('') + '</div>' +
                (multi ?
                    '<button class="hero-arrow prev" aria-label="Slide anterior"><i class="fas fa-chevron-left"></i></button>' +
                    '<button class="hero-arrow next" aria-label="Slide siguiente"><i class="fas fa-chevron-right"></i></button>' +
                    '<div class="hero-nav">' + slides.map((_, i) => '<button class="hero-dot' + (i === 0 ? ' active' : '') + '" aria-label="Ir al slide ' + (i + 1) + '"></button>').join('') +
                    (autoplay ? '<button class="hero-pause" aria-label="Pausar carousel"><i class="fas fa-pause"></i></button>' : '') + '</div>' +
                    (autoplay ? '<div class="hero-progress" aria-hidden="true"><span></span></div>' : '')
                    : '');

            hero.addEventListener('click', e => {
                const b = e.target.closest('[data-cta]'); if (!b) return;
                const href = b.dataset.href;
                if (href && href.startsWith('#')) { const t = document.querySelector(href); if (t) t.scrollIntoView({ behavior: 'smooth' }); }
                else if (href) { location.href = href; }
                else showToast(b.dataset.toast || 'Explorando el catálogo…', 'fa-compact-disc');
            });

            if (!multi) return;

            const els = hero.querySelectorAll('.hero-slide'), dots = hero.querySelectorAll('.hero-dot');
            const bar = hero.querySelector('.hero-progress span'), pauseBtn = hero.querySelector('.hero-pause');
            let idx = 0, timer = null, remaining = interval, startedAt = 0;
            const hold = { user: false, hover: false, focus: false, hidden: document.hidden, offscreen: false };
            const canRun = () => autoplay && !Object.values(hold).some(Boolean);

            function restartBar() {
                if (!bar) return;
                bar.classList.remove('run'); void bar.offsetWidth;
                if (autoplay) bar.classList.add('run');
            }
            function stop() {
                if (timer) { clearTimeout(timer); timer = null; remaining = Math.max(0, remaining - (Date.now() - startedAt)); }
                if (bar) bar.style.animationPlayState = 'paused';
            }
            function start() {
                if (timer) { clearTimeout(timer); timer = null; }
                if (!canRun()) { if (bar) bar.style.animationPlayState = 'paused'; return; }
                if (bar) bar.style.animationPlayState = 'running';
                startedAt = Date.now(); timer = setTimeout(() => go(idx + 1), remaining);
            }
            function sync() { stop(); start(); }

            function go(n) {
                if (!loop && (n < 0 || n >= len)) { stop(); return; }
                idx = (n + len) % len;
                els.forEach((el, i) => el.classList.toggle('is-active', i === idx));
                dots.forEach((d, i) => { d.classList.toggle('active', i === idx); d.setAttribute('aria-current', i === idx ? 'true' : 'false'); });
                stop(); remaining = interval; restartBar(); start();
            }

            hero.querySelector('.prev').addEventListener('click', () => go(idx - 1));
            hero.querySelector('.next').addEventListener('click', () => go(idx + 1));
            dots.forEach((d, i) => d.addEventListener('click', () => go(i)));

            if (pauseBtn) pauseBtn.addEventListener('click', () => {
                hold.user = !hold.user;
                pauseBtn.innerHTML = '<i class="fas ' + (hold.user ? 'fa-play' : 'fa-pause') + '"></i>';
                pauseBtn.setAttribute('aria-label', hold.user ? 'Reanudar carousel' : 'Pausar carousel');
                sync();
            });

            if (HERO_CONFIG.pauseOnHover !== false) {
                hero.addEventListener('pointerenter', e => { if (e.pointerType === 'mouse') { hold.hover = true; sync(); } });
                hero.addEventListener('pointerleave', () => { hold.hover = false; sync(); });
            }
            hero.addEventListener('focusin', e => { if (e.target.matches(':focus-visible')) { hold.focus = true; sync(); } });
            hero.addEventListener('focusout', () => { hold.focus = false; sync(); });
            document.addEventListener('visibilitychange', () => { hold.hidden = document.hidden; sync(); });
            if ('IntersectionObserver' in window) new IntersectionObserver(([en]) => { hold.offscreen = !en.isIntersecting; sync(); }, { threshold: .2 }).observe(hero);

            hero.addEventListener('keydown', e => { if (e.key === 'ArrowLeft') go(idx - 1); else if (e.key === 'ArrowRight') go(idx + 1); });
            let sx = 0, sy = 0;
            hero.addEventListener('touchstart', e => { sx = e.touches[0].clientX; sy = e.touches[0].clientY; }, { passive: true });
            hero.addEventListener('touchend', e => {
                const dx = e.changedTouches[0].clientX - sx, dy = e.changedTouches[0].clientY - sy;
                if (Math.abs(dx) > 45 && Math.abs(dx) > Math.abs(dy) * 1.3) go(idx + (dx < 0 ? 1 : -1));
            }, { passive: true });

            restartBar(); start();
        })();

        // ===== Folders carousel =====
        const foldersEl = document.getElementById('folders');
        let activeFolder = 'all';

        function folderCount(id) { return playlists.filter(p => p.folder === id).length; }

        function renderFolders() {
            let i = 0;
            let html = `<div class="folder all ${activeFolder === 'all' ? 'active' : ''}" data-folder="all" data-aos="fade-in" data-aos-delay="${ 300 + i++ * 150 }">
    <div class="all-inner"><div class="all-icon"><i class="fas fa-layer-group"></i></div><h4>Todos</h4><small>${playlists.length} playlists</small></div>
  </div>`;
            folders.forEach(f => {
                html += `<div class="folder ${activeFolder === f.id ? 'active' : ''}" data-folder="${f.name}" data-aos="fade-in" data-aos-delay="${ 300 + i++ * 150 }">
      <img src="${f.img}" alt="${f.name}" loading="lazy">
      <div class="folder-info"><h4>${f.name}</h4><small><i class="fas fa-list"></i> ${folderCount(f.name)} playlists</small></div>
    </div>`;
            });
            foldersEl.innerHTML = html;
            foldersEl.querySelectorAll('.folder').forEach(el => {
                el.addEventListener('click', () => {
                    activeFolder = el.dataset.folder;
                    renderFolders();
                    renderPlaylists();
                });
            });
        }

        document.getElementById('carPrev').addEventListener('click', () => foldersEl.scrollBy({ left: -380, behavior: 'smooth' }));
        document.getElementById('carNext').addEventListener('click', () => foldersEl.scrollBy({ left: 380, behavior: 'smooth' }));

        // ===== Playlist list =====
        const listEl = document.getElementById('playlistList');
        const listTitle = document.getElementById('listTitle');
        const listCount = document.getElementById('listCount');

        function folderName(id) { const f = folders.find(f => f.name === id); return f ? f.name : ''; }

        function renderPlaylists() {
            const items = activeFolder === 'all' ? playlists : playlists.filter(p => p.folder === activeFolder);
            listTitle.innerHTML = activeFolder === 'all' ? 'Todas las <span>Playlists</span>' : 'Carpeta: <span>' + folderName(activeFolder) + '</span>';
            listCount.innerHTML = 'Mostrando <b>' + items.length + '</b> playlist' + (items.length === 1 ? '' : 's');
            if (!items.length) {
                listEl.innerHTML = '<div class="pl-empty"><i class="fas fa-folder-open"></i>No hay playlists en esta carpeta todavía.</div>';
                return;
            }
            listEl.innerHTML = items.map((p, i) => `
    <div class="pl-item" data-name="${p.name}" data-url="${p.url}" style="animation-delay:${i * 40}ms">
      <div class="pl-cover"><img src="${p.img}" alt="${p.name}" loading="lazy"><div class="pl-play"><i class="fas fa-arrow-right"></i></div></div>
      <div class="pl-info"><h4>${p.name}</h4><p>${p.artist}</p></div>
      <span class="pl-folder-tag">${p.folder}</span>
      <span class="pl-count"><i class="fas fa-music"></i> ${p.items} elementos</span>
      <a class="pl-open" href="${p.url}"><i class="fas fa-arrow-right"></i></a>
    </div>`).join('');
            listEl.querySelectorAll('.pl-item').forEach(el => {
                el.addEventListener('click', () => { 
                    showToast('Abriendo playlist: ' + el.dataset.name, 'fa-list');
                    setTimeout(() => {
                        window.location = el.dataset.url;
                    }, 1000);
                });
            });
        }

        renderFolders();
        renderPlaylists();
    </script>
@endpush
