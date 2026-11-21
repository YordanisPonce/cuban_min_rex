@extends('layouts.app')
@php
    use Carbon\Carbon;
    Carbon::setLocale('es');
    $success = session('success');
    $error = session('error');
@endphp
@section('title', ($metadata['title'] ?? 'Catálogo') . ' - ' . config('app.name'))

@push('styles')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --fg: #f0e8d8;
            --muted: #8a7a66;
            --border: #2a2520;
            --surface: #1e1b17;
            --ok: #3ba55d;
            --danger: #e04040;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--fg);
            min-height: 100vh;
            overflow-x: hidden;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        img {
            display: block;
        }

        #wloader {
            display: none;
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
            padding: 1.5rem var(--gutter);
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

        .sk-folders .sk {
            height: 150px;
        }

        .sk-list .sk {
            height: 64px;
            margin-bottom: .6rem;
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
            max-width: 1240px;
            margin: 0 auto;
            padding: 2.2rem 2rem 0;
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

        @media(max-width:768px) {
            nav {
                padding: 0 1rem;
            }

            .nav-links {
                display: none;
            }

            .hero h1 {
                font-size: 2.2rem;
            }

            .footer-inner {
                flex-direction: column;
                text-align: center;
            }

            .player-right span,
            .player-right i:not(.fa-volume-high) {
                display: none;
            }
        }

        /* SKELETON (música) */
        .sk-layout {
            display: grid;
            gap: 1rem;
        }

        .sk-filters {
            height: 70px;
            margin-bottom: 1.2rem;
        }

        .sk-row {
            height: 96px;
        }

        /* LAYOUT */
        .music-layout {
            max-width: var(--maxw);
            margin: 0 auto;
            padding: 2.2rem var(--gutter);
            display: block;
        }

        .results-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin: 1rem 0;
            flex-wrap: wrap;
            font-size: .78rem;
            color: var(--muted);
        }

        .results-bar strong {
            color: var(--primary);
        }

        .btn-ghost {
            background: var(--card);
            border: 1px solid var(--border);
            color: var(--fg);
            padding: .55rem 1rem;
            border-radius: 8px;
            font-family: inherit;
            font-size: .78rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            transition: all .2s;
        }

        .btn-ghost:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .filter-toggle {
            display: none;
        }

        /* FILTERS */
        .filters {
            margin-bottom: 1.6rem;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.1rem;
        }

        .filters-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: .9rem;
        }

        .filters-head h3 {
            font-size: .85rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .06em;
            display: flex;
            gap: .5rem;
            align-items: center;
        }

        .filters-head h3 i {
            color: var(--primary);
        }

        .filters-close {
            display: none;
            background: none;
            border: none;
            color: var(--muted);
            font-size: 1.2rem;
            cursor: pointer;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: 2fr repeat(4, 1fr) auto;
            gap: .8rem;
            align-items: end;
        }

        .f-field label {
            display: block;
            font-size: .66rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--muted);
            margin-bottom: .35rem;
        }

        .f-search {
            position: relative;
        }

        .f-search i {
            position: absolute;
            left: .8rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: .8rem;
        }

        .f-field input,
        .f-field select {
            width: 100%;
            background: var(--bg);
            border: 1px solid var(--border);
            color: var(--fg);
            border-radius: 8px;
            padding: .62rem .75rem;
            font-family: inherit;
            font-size: .8rem;
            outline: none;
            transition: border-color .2s;
        }

        .f-search input {
            padding-left: 2.1rem;
        }

        .f-field input:focus,
        .f-field select:focus {
            border-color: var(--primary);
        }

        .f-reset {
            background: none;
            border: 1px solid var(--border);
            color: var(--muted);
            border-radius: 8px;
            padding: .62rem .9rem;
            font-family: inherit;
            font-size: .75rem;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
            transition: all .2s;
        }

        .f-reset:hover {
            color: var(--primary);
            border-color: var(--primary);
        }

        .chips {
            display: flex;
            gap: .4rem;
            flex-wrap: wrap;
        }

        .chip {
            background: rgba(245, 166, 35, .12);
            color: var(--primary);
            border: 1px solid rgba(245, 166, 35, .3);
            border-radius: 99px;
            padding: .2rem .6rem;
            font-size: .68rem;
            font-weight: 700;
            cursor: pointer;
        }

        .chip i {
            margin-left: .3rem;
        }

        .backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .6);
            z-index: 140;
            opacity: 0;
            pointer-events: none;
            transition: opacity .3s;
        }

        .backdrop.show {
            opacity: 1;
            pointer-events: auto;
        }

        .tracks {
            display: flex;
            flex-direction: column;
            gap: .7rem;
        }

        .track {
            display: grid;
            grid-template-columns: 72px minmax(160px, 2fr) 1.5fr auto;
            gap: 1.1rem;
            align-items: center;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: .75rem;
            transition: all .25s;
        }

        .track:hover {
            background: #171613;
            border-color: rgba(245, 166, 35, .35);
            transform: translateY(-2px);
            box-shadow: 0 10px 26px rgba(0, 0, 0, .35);
        }

        .track.playing {
            border-color: var(--primary);
        }

        .t-cover {
            position: relative;
            width: 72px;
            height: 72px;
            border-radius: 8px;
            overflow: hidden;
        }

        .t-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .35s;
        }

        .track:hover .t-cover img {
            transform: scale(1.08);
        }

        .t-play {
            inset: 0;
            margin: auto;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary);
            color: #14100b;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .85rem;
            box-shadow: 0 4px 14px rgba(0, 0, 0, .5);
            transition: transform .2s;
        }

        .t-play:hover {
            transform: scale(1.1);
        }

        .t-info {
            min-width: 0;
        }

        .t-info h4 {
            font-size: .92rem;
            font-weight: 800;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .t-info p {
            font-size: .76rem;
            color: var(--primary);
            margin: .15rem 0 .4rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .t-meta {
            display: flex;
            gap: .4rem;
            flex-wrap: wrap;
        }

        .tag {
            font-size: .64rem;
            font-weight: 700;
            padding: .18rem .5rem;
            border-radius: 5px;
            background: var(--surface);
            border: 1px solid var(--border);
            color: #cfc4b2;
        }

        .tag.key {
            color: white;
            border-color: rgba(245, 166, 35, .35);
        }

        .t-wave {
            display: flex;
            align-items: center;
            gap: .7rem;
            min-width: 0;
        }

        .t-wave>div{
            display: flex;
            gap: .5rem;
            align-items: center;
            width: 100%;
            overflow: hidden;
            padding: 10px;
        }
        .wave {
            flex: 1;
            height: 42px;
            display: flex;
            align-items: center;
            gap: 2px;
            cursor: pointer;
            min-width: 0;
        }

        .wave span {
            flex: 1;
            min-width: 2px;
            background: #3a332b;
            border-radius: 2px;
            transition: background .2s;
        }

        .track.playing .wave span {
            background: var(--primary);
        }

        .track.playing .wave span {
            animation: bounce .6s ease-in-out infinite alternate;
        }

        .wave span:nth-child(3n) {
            animation-delay: .15s !important;
        }

        .wave span:nth-child(3n+1) {
            animation-delay: .3s !important;
        }

        @keyframes bounce {
            from {
                transform: scaleY(.7);
            }

            to {
                transform: scaleY(1.15);
            }
        }

        .t-time {
            font-size: .7rem;
            color: var(--muted);
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .t-actions {
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        .price {
            font-weight: 900;
            font-size: 1rem;
            color: var(--fg);
            margin-right: .3rem;
        }

        .price small {
            color: var(--muted);
            font-size: .65rem;
            font-weight: 600;
        }

        .ic-btn {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--fg);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .2s;
        }

        .ic-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .ic-btn.cart {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #14100b;
            border: none;
        }

        .ic-btn.cart:hover {
            color: #14100b;
            transform: translateY(-2px);
        }

        .ic-btn.added {
            background: var(--ok);
            color: #fff;
        }

        .empty {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--muted);
            background: var(--card);
            border: 1px dashed var(--border);
            border-radius: 12px;
        }

        .empty i {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: .8rem;
            display: block;
        }

        /* PAGINATION */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: .4rem;
            margin-top: 1.6rem;
            flex-wrap: wrap;
        }

        .pg {
            min-width: 38px;
            height: 38px;
            padding: 0 .7rem;
            border-radius: 8px;
            background: var(--card);
            border: 1px solid var(--border);
            color: var(--fg);
            font-family: inherit;
            font-weight: 700;
            font-size: .8rem;
            cursor: pointer;
            transition: all .2s;
        }

        .pg:hover:not(:disabled) {
            border-color: var(--primary);
            color: var(--primary);
        }

        .pg.active {
            background: var(--primary);
            color: #14100b;
            border-color: var(--primary);
        }

        .pg:disabled {
            opacity: .35;
            cursor: not-allowed;
        }

        /* EXCLUSIVE */
        .exclusive {
            max-width: var(--maxw);
            margin: 3rem auto 0;
            padding: 0 var(--gutter);
        }

        .ex-box {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(245, 166, 35, .3);
            background: linear-gradient(135deg, #1f1a12, #14110d);
            padding: 2rem;
        }

        .ex-box::before {
            content: '';
            position: absolute;
            top: -120px;
            right: -120px;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(245, 166, 35, .22), transparent 70%);
        }

        .ex-head {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.4rem;
        }

        .ex-badge {
            display: inline-flex;
            gap: .4rem;
            align-items: center;
            font-size: .66rem;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: var(--primary);
            margin-bottom: .5rem;
        }

        .ex-head h2 {
            font-size: 1.6rem;
            font-weight: 900;
            text-transform: uppercase;
            font-style: italic;
        }

        .ex-head h2 span {
            color: var(--primary);
        }

        .ex-head p {
            font-size: .82rem;
            color: var(--muted);
            margin-top: .3rem;
            max-width: 480px;
        }

        .ex-grid {
            position: relative;
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 1rem;
        }

        .ex-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            transition: all .25s;
            cursor: pointer;
        }

        .ex-card:hover {
            transform: translateY(-4px);
            border-color: var(--primary);
            box-shadow: 0 14px 30px rgba(0, 0, 0, .45);
        }

        .ex-card .img {
            position: relative;
            aspect-ratio: 16/10;
            overflow: hidden;
        }

        .ex-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .4s;
        }

        .ex-card:hover img {
            transform: scale(1.08);
        }

        .ex-card .q {
            position: absolute;
            top: .5rem;
            left: .5rem;
            background: rgba(15, 13, 11, .85);
            color: var(--primary);
            font-size: .6rem;
            font-weight: 800;
            padding: .2rem .5rem;
            border-radius: 5px;
            letter-spacing: .06em;
        }

        .ex-card .b {
            padding: .8rem;
        }

        .ex-card h4 {
            font-size: .85rem;
            font-weight: 800;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .ex-card p {
            font-size: .72rem;
            color: var(--muted);
            margin-top: .2rem;
        }

        /* ===== RESPONSIVE ===== */
        /* Monitor >2000px: aside izquierdo */
        @media(min-width:2001px) {

            .music-layout {
                display: grid;
                grid-template-columns: 300px 1fr;
                gap: 2rem;
                align-items: start;
            }

            .filters {
                position: sticky;
                top: 80px;
            }

            .filters-grid {
                grid-template-columns: 1fr;
            }

            .track {
                grid-template-columns: 96px minmax(220px, 2fr) 2fr auto;
            }

            .t-cover {
                width: 96px;
                height: 96px;
            }

            .ex-grid {
                grid-template-columns: repeat(6, 1fr);
            }
        }

        /* Laptop: filtros arriba */
        @media(max-width:1180px) {
            .filters-grid {
                grid-template-columns: repeat(4, 1fr);
            }

            .f-search {
                grid-column: 1/-1;
            }

            .f-reset-wrap {
                grid-column: 1/-1;
                display: flex;
                justify-content: flex-end;
            }
        }

        /* Tablet */
        @media(max-width:1024px) {
            .track {
                grid-template-columns: auto 1fr;
                grid-template-areas: "c i" "a w";
            }

            .t-cover {
                grid-area: c;
            }

            .t-info {
                grid-area: i;
            }

            .t-actions {
                grid-area: a;
            }

            .t-wave {
                grid-area: w;
            }

            .ex-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Mobile: off-canvas */
        @media(max-width:768px) {

            .music-layout,
            .exclusive {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .filter-toggle {
                display: inline-flex;
            }

            .filters {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: 86%;
                max-width: 340px;
                z-index: 150;
                border-radius: 0 14px 14px 0;
                transform: translateX(-105%);
                transition: transform .35s ease;
                overflow-y: auto;
                height: 100vh;
            }

            .filters.open {
                transform: none;
            }

            .filters-close {
                display: block;
            }

            .filters-grid {
                grid-template-columns: 1fr;
            }

            .track {
                grid-template-columns: 100px 1fr;
                gap: .5rem;
            }

            .t-cover {
                width: 100px;
                height: 100px;
            }

            .t-actions {
                justify-content: space-evenly;
                gap: 0.3rem;
            }

            .ex-box {
                padding: 1.3rem;
            }

            .ex-head h2 {
                font-size: 1.25rem;
            }
        }

        @media(max-width:480px) {
            .ex-grid {
                grid-template-columns: 1fr;
            }

            .ex-card {
                display: flex;
            }

            .ex-card .img{
                aspect-ratio: 16/5;
            }
        }

        .bottom-player.video {
            width: 500px;
            height: 500px;
            bottom: 20px;
            right: 20px;
            left: auto;
            border: 0.5px solid var(--primary);
            padding: 0;
            margin: 0 auto;
        }

        @media (max-width: 700px) {
            .bottom-player.video {
                width: 300px;
                height: 300px;
            }
        }

        .bottom-player.video .player-inner {
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 100%;
            padding: 15px;
            gap: 5px;

            & .video-player {
                width: 100%;
                height: 100%;

                & .plyr {
                    width: 100%;
                    height: 100%;
                }
            }

            & .player-controls {
                width: 100%;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                gap: 10px;

                & .waveform {
                    width: 100%;
                    height: auto;
                    min-height: 30px;
                }

                & .main-play {
                    width: 30px;
                    height: 30px;

                    &:hover {
                        color: var(--text-muted)
                    }
                }
            }

            & .player-track {
                display: grid;
                width: 100%;
                position: relative;
                gap: 10px;
                grid-template-columns: 40px 1fr;
                padding-right: 15px;

                & .close {
                    position: absolute;
                    top: 10px;
                    right: 0;
                }
            }
        }
    </style>
@endpush

@section('content')

    <div id="skeleton" aria-hidden="true">
        <div class="sk-wrap">
            <div class="sk sk-hero"></div>
            <div class="sk sk-filters"></div>
            <div class="sk sk-title"></div>
            <div class="sk-layout">
                <div class="sk sk-row"></div>
                <div class="sk sk-row"></div>
                <div class="sk sk-row"></div>
                <div class="sk sk-row"></div>
                <div class="sk sk-row"></div>
            </div>
        </div>
    </div>

    <div id="content">
        <div class="main">
            @include('partials.hero', [
                'baseDescription' =>
                    $metadata['heroDescription'] ??
                    'Descarga edits, intros, mashups y más musical para hacer historia en la pista.',
                'baseLinkText' => $metadata['heroLinkText'] ? $metadata['heroLinkText'] : '',
                'baseLinkAddress' => $metadata['heroLinkAddress'] ? $metadata['heroLinkAddress'] : '',
            ])

            <div class="music-layout" id="musicSection">
                <aside class="filters" id="filters" aria-label="Filtros">
                    <div class="filters-head">
                        <h3><i class="fas fa-sliders"></i> Filtros</h3>
                        <button class="filters-close" id="filtersClose" aria-label="Cerrar filtros"><i
                                class="fas fa-xmark"></i></button>
                    </div>
                    <div class="filters-grid">
                        <div class="f-field f-search"><label for="fSearch">Buscar título</label><i class="fas fa-search"
                                style="top:calc(50% + .55rem)"></i><input id="fSearch" type="search"
                                placeholder="Buscar por título..."></div>
                        <div class="f-field"><label for="fArtist">Artista</label><select id="fArtist"></select></div>
                        <div class="f-field"><label for="fBpm">BPM</label><select id="fBpm"></select></div>
                        <div class="f-field"><label for="fKey">Nota</label><select id="fKey"></select></div>
                        <div class="f-field"><label for="fGenre">Género</label><select id="fGenre"></select></div>
                        <div class="f-reset-wrap"><button class="f-reset" id="fReset"><i class="fas fa-rotate-left"></i>
                                Limpiar</button></div>
                    </div>
                </aside>

                <section>
                    <div class="section-head" style="margin-bottom:0">
                        <div>
                            <h2><span>{{ $metadata['subtitle'] }}</span> disponibles</h2>
                            <div class="section-sub">Precio fijo por tema · descarga en alta calidad</div>
                        </div>
                        <button class="btn-ghost filter-toggle" id="filterToggle"><i class="fas fa-sliders"></i>
                            Filtros</button>
                    </div>
                    <div class="results-bar"><span id="resultsInfo"></span>
                        <div class="chips" id="chips"></div>
                    </div>
                    <div class="tracks" id="tracks"></div>
                    <div class="pagination" id="pagination" role="navigation" aria-label="Paginación"></div>
                </section>
            </div>

            @if (count($extras) > 0)
                <section class="exclusive">
                    <div class="ex-box">
                        <div class="ex-head">
                            <div>
                                <div class="ex-badge"><i class="fas fa-crown"></i> Contenido exclusivo</div>
                                <h2>Calidad <span>Premium</span></h2>
                                <p>Edits extendidos, stems y versiones exclusivas en WAV 24-bit, solo disponibles en
                                    CubanPool.
                                </p>
                            </div>
                            <a href="{{ route('exclusives') }}" class="btn-cta">Ver más <i class="fas fa-arrow-right"></i></a>
                        </div>
                        <div class="ex-grid" id="exGrid"></div>
                    </div>
                </section>
            @endif
        </div>

        <div class="toast-wrap" id="toastWrap"></div>
    </div>

    @include('partials.bottom-player')
    @include('partials.bottom-video-player')

@endsection

@push('scripts')
    <script>
        const tracks = @json($tracks);
        const exclusives = @json($extras);
        const ARTISTS = @json($artists);
        const GENRES = @json($genres);
        const BPM = [
            'BPM Variable',
            'Menos de 100 BPM',
            'Entre 100 BPM y 130 BPM',
            'Entre 130 BPM y 150 BPM',
            'Más de 150 BPM'
        ];
        const KEYS = [];
        for (let n = 1; n <= 12; n++) KEYS.push(n + 'A');
        for (let n = 1; n <= 12; n++) KEYS.push(n + 'B');

        // ===== State =====
        const PER_PAGE = 10;

        let page = 1;

        const $ = id => document.getElementById(id);

        const F = {
            search: $('fSearch'),
            artist: $('fArtist'),
            bpm: $('fBpm'),
            key: $('fKey'),
            genre: $('fGenre')
        };

        function fillSelect(el, label, values) {
            if (el == F.artist) {
                el.innerHTML = `<option value="">${label}</option>` + values.map(v =>
                        `<option value="${v.name}">${v.name}</option>`)
                    .join('');
            } else if (el == F.genre) {
                el.innerHTML = `<option value="">${label}</option>` + values.map(v =>
                        `<option value="${v.name}">${v.name}</option>`)
                    .join('');
            } else {
                el.innerHTML = `<option value="">${label}</option>` + values.map(v => `<option value="${v}">${v}</option>`)
                    .join('');
            }
        }

        fillSelect(F.artist, 'Todos los artistas', [...ARTISTS].sort());
        fillSelect(F.bpm, 'Todos los BPM', [...BPM]);
        fillSelect(F.key, 'Todas las notas', KEYS);
        fillSelect(F.genre, 'Todos los géneros', [...GENRES].sort());

        function hasNumsLeft(str, value) {
            const regex = /-?\d+(\.\d+)?/g;
            const nums = str.match(regex);
            if (!nums) {
                return false;
            }
            return nums.some(num => parseFloat(num) < value);
        }

        function hasNumsHight(str, value) {
            const regex = /-?\d+(\.\d+)?/g;
            const nums = str.match(regex);
            if (!nums) {
                return false;
            }
            return nums.some(num => parseFloat(num) > value);
        }

        function isVariable(str) {
            const regex = /-?\d+(\.\d+)?/g;
            const nums = str.match(regex);
            if (!nums) {
                return str.toLowerCase == 'variable';
            }
            return nums.length > 1;
        }

        const hasBPM = (t) => {
            let selectedBPM = F.bpm.value;
            switch (selectedBPM) {
                case 'BPM Variable':
                    return isVariable(t.bpm);
                case 'Menos de 100 BPM':
                    return hasNumsLeft(t.bpm, 100);
                case 'Entre 100 BPM y 130 BPM':
                    return hasNumsHight(t.bpm, 99) && hasNumsLeft(t.bpm, 130);
                case 'Entre 130 BPM y 150 BPM':
                    return hasNumsHight(t.bpm, 129) && hasNumsLeft(t.bpm, 151);
                case 'Más de 150 BPM':
                    return hasNumsHight(t.bpm, 150);
                default:
                    return true;
            }
        }

        const hasGenre = (t) => {
            let selected = F.genre.value;
            return t.genre.includes(selected);
        }

        function filtered() {
            const q = F.search.value.trim().toLowerCase();
            return tracks.filter(t => (!q || t.title.toLowerCase().includes(q)) && (!F.artist.value || t.artist === F.artist
                .value) && hasBPM(t) && (!F.key.value || t.key === F.key.value) && (!F.genre
                .value || hasGenre(t)));
        }

        function waveHTML() {
            let waves = '';
            for (let index = 0; index < 64; index++) {
                let h = Math.random() * 30 + 20;
                waves += `<span style="height:${Math.round(h)}%"></span>`;
            }
            return waves;
        }

        function actions(t) {
            return '<div class="t-actions">' + (!t.canBeDownload ? `<span class="price">$ ${t.price}</span>` :
                    '') +
                (t.canBeDownload ?
                    `<a class="ic-btn" aria-label="Descargar" href="${t.downloadLink}"><i class="fas fa-download"></i></a>` :
                    `<a class="ic-btn cart" aria-label="Agregar al carrito" href="${t.addToCart}"><i class="fas fa-cart-plus"></i></a>`
                ) +
                '</div>';
        }

        function genres(t) {
            let genres = "";
            t.genre.forEach(g => {
                genres += `<span class="tag">${g}</span>`;
            });
            return genres;
        }

        function render() {
            const list = filtered(),
                pages = Math.max(1, Math.ceil(list.length / PER_PAGE));
            if (page > pages) page = pages;
            const slice = list.slice((page - 1) * PER_PAGE, page * PER_PAGE);
            $('resultsInfo').innerHTML = list.length ?
                `Mostrando <strong>${(page-1)*PER_PAGE+1}–${(page-1)*PER_PAGE+slice.length}</strong> de <strong>${list.length}</strong> temas` :
                'Sin resultados';
            $('tracks').innerHTML = slice.length ? slice.map(t => `
                <article class="track" id="${t.id}">
                <div class="t-cover"><img src="${t.cover}" alt="Cover de ${t.title}" loading="lazy"></div>
                <div class="t-info"><h4 title="${t.title}">${t.title}</h4><p>${t.artist}</p>
                    <div class="t-meta"><span class="tag"><i class="fas fa-gauge-high"></i> ${t.bpm} BPM</span><span class="tag"><i class="fas fa-download"></i> ${t.downloads}</span><span class="tag key key-${t.key}"><i class="fas fa-music"></i> ${t.key}</span>${ genres(t) }</div></div>
                <div class="t-wave"><div><button class="t-play" aria-label="Reproducir" data-id="${t.id}" data-track="${t.title}" data-artist="${t.artist}" data-cover="${t.cover}" data-url="${t.url}" data-flag="${t.flag}"><i class="fas fa-play"></i></button><div class="wave">${waveHTML(t)}</div></div></div>${actions(t)}
                </article>`).join('') :
                `<div class="empty"><i class="fas fa-compact-disc"></i>No hay temas que coincidan con los filtros.<br><br><button class="btn-ghost" onclick="resetFilters()">Limpiar filtros</button></div>`;
            renderPagination(pages);
            renderChips();
        }

        function renderPagination(pages) {
            let h =
                `<button class="pg" data-pg="${page-1}" ${page===1?'disabled':''} aria-label="Anterior"><i class="fas fa-chevron-left"></i></button>`;
            for (let i = 1; i <= pages; i++) {
                if (i === 1 || i === pages || Math.abs(i - page) <= 1) h +=
                    `<button class="pg ${i===page?'active':''}" data-pg="${i}">${i}</button>`;
                else if (Math.abs(i - page) === 2) h += `<span style="color:var(--muted)">…</span>`;
            }
            h +=
                `<button class="pg" data-pg="${page+1}" ${page===pages?'disabled':''} aria-label="Siguiente"><i class="fas fa-chevron-right"></i></button>`;
            $('pagination').innerHTML = h;
        }

        function renderChips() {
            const labels = {
                search: 'Título',
                artist: 'Artista',
                bpm: 'BPM',
                key: 'Nota',
                genre: 'Género'
            };
            $('chips').innerHTML = Object.entries(F).filter(([, el]) => el.value !== '').map(([k, el]) =>
                    `<span class="chip" data-chip="${k}">${labels[k]}: ${el.value}<i class="fas fa-xmark"></i></span>`)
                .join('');
            const n = Object.values(F).filter(el => el.value !== '').length;
            $('filterToggle').innerHTML = `<i class="fas fa-sliders"></i> Filtros${n?' ('+n+')':''}`;
        }

        function resetFilters() {
            Object.values(F).forEach(el => el.value = '');
            page = 1;
            render();
        }
        Object.values(F).forEach(el => el.addEventListener(el.tagName === 'INPUT' ? 'input' : 'change', () => {
            page = 1;
            render();
        }));
        $('fReset').onclick = resetFilters;
        $('chips').onclick = e => {
            const c = e.target.closest('[data-chip]');
            if (c) {
                F[c.dataset.chip].value = '';
                page = 1;
                render();
            }
        };
        $('pagination').onclick = e => {
            const b = e.target.closest('[data-pg]');
            if (b && !b.disabled) {
                page = +b.dataset.pg;
                render();
                $('musicSection').scrollIntoView({
                    behavior: 'smooth'
                });
            }
        };

        const openF = () => {
            $('filters').classList.add('open');
        };
        const closeF = () => {
            $('filters').classList.remove('open');
        };
        $('filterToggle').onclick = openF;
        $('filtersClose').onclick = closeF;

        function showToast(msg, icon) {
            const t = document.createElement('div');
            t.className = 'toast';
            t.innerHTML = `<i class="fas fa-${icon||'info-circle'}"></i>${msg}`;
            $('toastWrap').appendChild(t);
            setTimeout(() => t.remove(), 2600);
        }

        if (exclusives.length > 0) {
            $('exGrid').innerHTML = exclusives.map((x, i) =>
                `<div class="ex-card"><div class="img"><img src="${x.cover}" alt="${x.title}" loading="lazy"><span class="q">EXCLUSIVO</span></div><div class="b"><h4>${x.title}</h4><p>${x.artist}</p></div></div>`
            ).join('');
        }

        render();

        window.addEventListener('load', () => setTimeout(() => {
            $('skeleton').classList.add('hide');
            $('content').classList.add('show');
            setTimeout(() => $('skeleton').remove(), 600);
        }, 1200));
    </script>
    <script>
        let playBtns = document.querySelectorAll('.t-play');
        let trackCards = document.querySelectorAll('.track');

        function resetBtns(element) {
            playBtns.forEach(button => () => {
                var icon = button.querySelector('i');
                if (icon != null && button != element) {
                    icon.className = 'fas fa-play';
                }
            });
            trackCards.forEach(card => () => {
                card.classList.remove('playing');
            });
        }

        function closePlayer(element, flag = 'audio') {
            if (element != undefined && element != null) {
                const trackCard = $(''+ element.dataset.id);
                trackCard.classList.remove('playing');
            }
            const el = document.getElementById('bottom-player');
            const elv = document.getElementById('video-player');
            const audioPlayer = document.getElementById('plyr-audio-player');
            const videoPlayer = document.getElementById('plyr-video-player');
            el.classList.remove('active');
            elv.classList.remove('active');
            audioPlayer.pause();
            videoPlayer.pause();
        }

        function showPlayer(element, flag = 'audio') {
            const trackCard = $('' + element.dataset.id);
            const el = document.getElementById('bottom-player');
            const elv = document.getElementById('video-player');
            const audioPlayer = document.getElementById('plyr-audio-player');
            const videoPlayer = document.getElementById('plyr-video-player');
            if (flag == 'video') {
                elv.classList.add('active');
                document.getElementById('video-player-img').src = element.dataset.cover;
                document.getElementById('video-player-title').textContent = element.dataset.track;
                document.getElementById('video-player-artist').textContent = element.dataset.artist;
            } else {
                el.classList.add('active');
                document.getElementById('player-img').src = element.dataset.cover;
                document.getElementById('player-title').textContent = element.dataset.track;
                document.getElementById('player-artist').textContent = element.dataset.artist;
            }
            trackCard.classList.add('playing');
            var icon = element.querySelector('i');
            if (icon != null) {
                icon.className = 'fas fa-play';
            }
        }

        function pausePlayer(element, flag = 'audio') {
            const trackCard = $(element.dataset.id);
            trackCard.classList.remove('playing');
            var icon = element.querySelector('i');
            if (icon != null) {
                icon.className = 'fas fa-play';
            }
        }

        function verifySource(element, flag = 'audio') {
            const audioPlayer = document.getElementById('plyr-audio-player');
            const videoPlayer = document.getElementById('plyr-video-player');
            closePlayer(element);
            var audio = new Audio(element.dataset.url);
            if (flag == 'video') {
                audio = document.createElement('video');
                audio.src = element.dataset.url;
            }
            audio.addEventListener("canplaythrough", () => {
                if (flag == 'video') {
                    videoPlayer.src = element.dataset.url;
                    videoPlayer.play();
                } else {
                    audioPlayer.src = element.dataset.url;
                    audioPlayer.play();
                }
            });
            videoPlayer.addEventListener('play', () => {
                showPlayer(element, 'video');
            });
            videoPlayer.addEventListener('pause', () => {
                pausePlayer(element, 'video');
            });
            videoPlayer.addEventListener("ended", () => {
                closePlayer();
            });
            audioPlayer.addEventListener('play', () => {
                showPlayer(element, 'audio');
            });
            audioPlayer.addEventListener('pause', () => {
                pausePlayer(element, 'audio');
            });
            audioPlayer.addEventListener("ended", () => {
                closePlayer();
            });
        }

        function playTrack(element) {
            showToast('Cargando ' + element.dataset.track);
            resetBtns(element);
            var icon = element.querySelector('i');
            if (icon != null) {
                icon.className = 'fa fa-spinner fa-spin';
            }
            try {
                verifySource(element, element.dataset.flag);
            } catch (error) {
                setTimeout(() => {
                    showToast('No se pudo cargar el recurso: ' + element.dataset.track);
                    if (icon != null) {
                        icon.className = 'fas fa-play';
                    }
                }, 1000);
            }
        }

        playBtns.forEach(button => {
            button.addEventListener('click', () => playTrack(button));
        });
    </script>
    @isset($success)
        <script>
            Swal.fire({
                title: '{{ $success }}',
                icon: 'success'
            });
        </script>
    @endisset
    @isset($error)
        <script>
            Swal.fire({
                title: '{{ $error }}',
                icon: 'error'
            });
        </script>
    @endisset
@endpush
