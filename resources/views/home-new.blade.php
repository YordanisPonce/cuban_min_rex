@extends('layouts.app')
@php
    use App\Models\Cart;
    $success = session('success');
    $error = session('error');
@endphp

@section('title', 'Inicio - ' . config('app.name'))

@push('styles')
    <style>
        :root {
            --border: #2a2520;
            --surface: #1e1b17;
            --ok: #3ba55d;
            --danger: #e04040;
            --nav-h: 60px;
            --maxw: 100%;
            --gutter: 10%;
        }
        
        html {
            font-size: 15px;
            -webkit-text-size-adjust: 100%;
            scroll-behavior: smooth;
        }

        @media (min-width: 1600px) {
            html {
                font-size: 16.5px;
            }
        }

        @media (min-width: 1920px) {
            html {
                font-size: 18px;
            }
        }

        @media (min-width: 2560px) {
            html {
                font-size: 22px;
            }
        }

        @media (min-width: 3200px) {
            html {
                font-size: 27px;
            }
        }

        .window-loader {
            display: none;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        :focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
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

        #skeleton {
            position: fixed;
            inset: 0;
            z-index: 300;
            background: var(--bg);
            overflow: hidden;
            transition: opacity .45s ease;
            padding-top: var(--nav-h);
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
            height: clamp(18rem, 46vh, 32rem);
            border-radius: 14px;
            margin-bottom: 1.25rem;
        }

        .sk-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .sk-strip .sk {
            height: 52px;
        }

        .sk-search {
            height: 46px;
            margin-bottom: 2rem;
        }

        .sk-title {
            height: 22px;
            width: 240px;
            margin-bottom: 1rem;
        }

        .sk-row {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .sk-row .sk {
            height: 150px;
        }

        .sk-row.ten {
            grid-template-columns: repeat(10, 1fr);
        }

        .sk-row.ten .sk {
            height: 110px;
        }

        .sk-cols {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .sk-cols .sk {
            height: 230px;
        }

        .sk-plans {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .sk-plans .sk {
            height: 250px;
        }

        .sk-djs {
            display: flex;
            gap: 2.5rem;
            justify-content: center;
        }

        .sk-djs .sk {
            width: 120px;
            height: 120px;
            border-radius: 50%;
        }

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

        .hero {
            position: relative;
            overflow: hidden;
            padding: 0;
            border-bottom: 1px solid var(--border);
            background: var(--bg);
            min-height: 60dvh;
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

        .hero-bg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(1.07);
            transition: transform 8s ease-out;
            aspect-ratio: auto;
            position: relative;
            max-width: 100%;
        }

        .hero-slide.is-active .hero-bg img {
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
            font-size: .74rem;
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
            font-size: clamp(2rem, 5.2vw + .4rem, 3.2rem);
            font-weight: 900;
            font-style: italic;
            text-transform: uppercase;
            line-height: 1.02;
            letter-spacing: -.01em;
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

        .features {
            max-width: var(--maxw);
            margin: 0 auto;
            padding: 1.2rem var(--gutter);
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .feature {
            display: flex;
            align-items: center;
            gap: .85rem;
        }

        .feature i {
            font-size: 1.5rem;
            color: var(--primary);
            width: 34px;
            text-align: center;
            flex-shrink: 0;
        }

        .feature h4 {
            font-size: .82rem;
            font-weight: 800;
        }

        .feature p {
            font-size: .68rem;
            color: var(--muted);
            margin-top: 2px;
        }

        .search-zone {
            max-width: var(--maxw);
            margin: 0 auto;
            padding: 1.2rem var(--gutter) 0;
            scroll-margin-top: calc(var(--nav-h) + .5rem);
        }

        .search-form {
            display: grid;
            grid-template-columns: minmax(13rem, 1.7fr) repeat(4, minmax(0, 1fr)) auto;
            gap: .55rem;
            align-items: stretch;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: .55rem;
        }

        .search-input {
            display: flex;
            align-items: center;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 9px;
            transition: border-color .2s;
            min-width: 0;
        }

        .search-input:focus-within {
            border-color: var(--primary);
        }

        .search-input i {
            color: var(--muted);
            padding: 0 .3rem 0 .9rem;
            font-size: .85rem;
        }

        .search-input input {
            flex: 1;
            min-width: 0;
            background: transparent;
            border: none;
            outline: none;
            color: var(--fg);
            font-family: inherit;
            font-size: .85rem;
            padding: .75rem .7rem;
        }

        .search-input input::placeholder {
            color: var(--muted);
        }

        .search-form select {
            width: 100%;
            height: 100%;
            min-height: 2.7rem;
            appearance: none;
            -webkit-appearance: none;
            color-scheme: dark;
            background-color: var(--bg);
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'><path d='M1 1l4 4 4-4' fill='none' stroke='%238a7a66' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'/></svg>");
            background-repeat: no-repeat;
            background-position: right .9rem center;
            color: var(--muted);
            border: 1px solid var(--border);
            border-radius: 9px;
            padding: .7rem 2.1rem .7rem .9rem;
            font-family: inherit;
            font-size: .78rem;
            font-weight: 600;
            cursor: pointer;
            transition: border-color .2s, color .2s;
            text-overflow: ellipsis;
        }

        .search-form select:hover {
            border-color: #4a4238;
        }

        .search-form select:focus {
            border-color: var(--primary);
            outline: none;
        }

        .search-form select.is-set {
            color: var(--fg);
            border-color: var(--primary);
        }

        .btn-search {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #14100b;
            border: none;
            font-family: inherit;
            font-weight: 800;
            font-size: .8rem;
            padding: .7rem 1.5rem;
            border-radius: 9px;
            cursor: pointer;
            transition: opacity .2s, transform .2s;
        }

        .btn-search:hover {
            opacity: .92;
            transform: translateY(-1px);
        }

        .section {
            max-width: var(--maxw);
            margin: 0 auto;
            padding: 1.8rem var(--gutter) 0;
            scroll-margin-top: calc(var(--nav-h) + .5rem);
        }

        .section-head {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-bottom: 1rem;
            gap: 1rem;
        }

        .section-head h2 {
            font-size: 1.3rem;
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

        .see-all {
            font-size: .75rem;
            font-weight: 700;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: .4rem;
            transition: color .2s;
            cursor: pointer;
            white-space: nowrap;
        }

        .see-all:hover {
            color: var(--primary);
        }

        .genres {
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            gap: .7rem;
        }

        .genre {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            aspect-ratio: 3/4;
            cursor: pointer;
            border: 1px solid var(--border);
            transition: all .25s;
        }

        .genre img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .35s;
        }

        .genre::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, transparent 35%, rgba(10, 8, 6, .92) 100%);
        }

        .genre:hover {
            transform: translateY(-4px);
            border-color: var(--primary);
            box-shadow: 0 12px 26px rgba(0, 0, 0, .5);
        }

        .genre:hover img {
            transform: scale(1.08);
        }

        .genre span {
            position: absolute;
            bottom: .6rem;
            left: .6rem;
            right: .6rem;
            z-index: 2;
            font-size: .66rem;
            font-weight: 800;
            text-transform: uppercase;
            line-height: 1.25;
            letter-spacing: .02em;
        }

        .releases {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 1rem;
        }

        .release {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 1/1;
            cursor: pointer;
            border: 1px solid var(--border);
            transition: all .25s;
            background: var(--card);
        }

        .release img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .35s;
        }

        .release::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(10, 8, 6, .15) 0%, transparent 30%, rgba(10, 8, 6, .94) 82%);
        }

        .release:hover {
            transform: translateY(-4px);
            border-color: var(--primary);
            box-shadow: 0 14px 30px rgba(0, 0, 0, .55);
        }

        .release:hover img {
            transform: scale(1.06);
        }

        .release-info {
            position: absolute;
            bottom: .8rem;
            left: .9rem;
            right: .9rem;
            z-index: 2;
        }

        .release-info h4 {
            font-size: .75rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: .45rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .release-badge {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #14100b;
            font-size: .6rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .04em;
            padding: .22rem .55rem;
            border-radius: 99px;
            margin-bottom: .45rem;
        }

        .release-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .release-meta small {
            font-size: .66rem;
            color: #cfc4b2;
            font-weight: 600;
        }

        .release-play {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 1.5px solid rgba(240, 232, 216, .7);
            background: rgba(15, 13, 11, .5);
            color: var(--fg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .5rem;
            cursor: pointer;
            transition: all .2s;
            flex-shrink: 0;
        }

        .release-play:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: #14100b;
        }

        .two-col {
            display: grid;
            grid-template-columns: 60% 40%;
            gap: 2rem;
            align-items: start;
        }

        .top-list {
            display: flex;
            flex-direction: column;
        }

        .top-item {
            display: flex;
            align-items: center;
            gap: .9rem;
            padding: .68rem .4rem;
            border-bottom: 1px solid var(--border);
            cursor: pointer;
            transition: background .2s;
            border-radius: 8px;
        }

        .top-item:hover {
            background: var(--card);
        }

        .top-item:last-child {
            border-bottom: none;
        }

        .top-rank {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--primary);
            font-weight: 900;
            font-size: .85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .top-item:nth-child(1) .top-rank {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #14100b;
            border-color: transparent;
        }

        .top-info {
            flex: 1;
            min-width: 0;
        }

        .top-info h4 {
            font-size: .82rem;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .top-info p {
            font-size: .66rem;
            color: var(--muted);
            margin-top: 1px;
        }

        .top-dl {
            display: flex;
            align-items: center;
            gap: .35rem;
            font-size: .7rem;
            color: var(--muted);
            font-weight: 700;
        }

        .top-dl i {
            color: var(--primary);
            font-size: .65rem;
        }

        .top-play {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 1.5px solid var(--border);
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .58rem;
            transition: all .2s;
            flex-shrink: 0;
        }

        .top-item:hover .top-play {
            border-color: var(--primary);
            color: var(--primary);
        }

        .playlists-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .playlist-card {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 1/1.05;
            cursor: pointer;
            border: 1px solid var(--border);
            transition: all .25s;
        }

        .playlist-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .35s;
        }

        .playlist-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, transparent 30%, rgba(10, 8, 6, .93) 90%);
        }

        .playlist-card:hover {
            transform: translateY(-4px);
            border-color: var(--primary);
            box-shadow: 0 14px 30px rgba(0, 0, 0, .55);
        }

        .playlist-card:hover img {
            transform: scale(1.07);
        }

        .playlist-info {
            position: absolute;
            bottom: .85rem;
            left: .9rem;
            right: .9rem;
            z-index: 2;
        }

        .playlist-info h4 {
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            line-height: 1;
            margin-bottom: .4rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .playlist-info small {
            font-size: .66rem;
            color: #cfc4b2;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        .playlist-info small i {
            color: var(--primary);
        }

        .plans {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.2rem;
            align-items: stretch;
        }

        .plan {
            position: relative;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem 1.4rem;
            display: flex;
            flex-direction: column;
            transition: all .25s;

            &>div {
                display: grid;
                grid-template-columns: 40% auto;
                gap: 10%;
            }
        }

        .plan:hover {
            transform: translateY(-4px);
            border-color: #4a4238;
        }

        .plan.featured {
            border: 2px solid var(--primary);
            box-shadow: 0 0 34px rgba(245, 166, 35, .22);
            background: linear-gradient(180deg, rgba(245, 166, 35, .07), var(--card) 45%);
        }

        .plan-flag {
            position: absolute;
            top: -14px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #14100b;
            font-size: .62rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
            padding: .35rem .9rem;
            border-radius: 99px;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: .35rem;
            box-shadow: 0 6px 16px rgba(245, 166, 35, .4);
        }

        .plan h3 {
            font-size: 1.25rem;
            font-weight: 800;
            line-height: 1.3;
            text-align: center;
        }

        .plan h3 span {
            color: var(--primary);
            font-size: 1rem;
        }

        .plan-price {
            margin: 1rem auto;
            width: 100%;
        }

        .plan-price .amount {
            font-size: 1.25rem;
            font-weight: 900;
            font-style: italic;
        }

        .plan.featured .plan-price .amount {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #14100b;
            padding: .25rem .9rem;
            border-radius: 8px;
            display: inline-block;
        }

        .plan ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: .55rem;
            margin-bottom: 1.4rem;
            flex: 1;
        }

        .plan li {
            font-size: .74rem;
            color: #cfc4b2;
            display: flex;
            align-items: center;
            gap: .55rem;
            line-height: 1.4;
        }

        .plan li i {
            color: var(--primary);
            font-size: .7rem;
            margin-top: 2px;
        }

        .plan-btn {
            width: 100%;
            padding: .8rem;
            border-radius: 9px;
            font-family: inherit;
            font-weight: 800;
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .03em;
            cursor: pointer;
            transition: all .2s;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--fg);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
        }

        .plan-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .plan.featured .plan-btn {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #14100b;
            border: none;
            box-shadow: 0 8px 20px rgba(245, 166, 35, .3);
        }

        .plan.featured .plan-btn:hover {
            transform: translateY(-2px);
            color: #14100b;
        }

        .djs-row {
            display: flex;
            justify-content: center;
            gap: 2.4rem;
            flex-wrap: wrap;
            padding: .3rem 0 .8rem;
            margin: -40px 0 0 260px;
        }

        .dj-card {
            text-align: center;
            cursor: pointer;
        }

        .dj-avatar {
            width: 10rem;
            height: 10rem;
            border-radius: 50%;
            padding: 4px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            margin: 0 auto .7rem;
            transition: all .25s;
            box-shadow: 0 0 0 rgba(245, 166, 35, 0);
        }

        .dj-card:hover .dj-avatar {
            transform: scale(1.05);
            box-shadow: 0 0 26px rgba(245, 166, 35, .4);
        }

        .dj-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--bg);
        }

        .dj-card h4 {
            font-size: .85rem;
            font-weight: 800;
        }

        .toast-wrap {
            position: fixed;
            bottom: 5.8rem;
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

        @media (max-width: 1400px) {        
            .plan>div{
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }

            .plan-price {
                display: flex;
                justify-content: center;
            }
        }

        @media (max-width: 1200px){
            .djs-row{
                margin: 0;
            }
        }

        @media (max-width: 1100px) {

            .search-form {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }

            .search-form .field-q {
                grid-column: 1 / 4;
            }

            .search-form .btn-search {
                grid-column: 4;
            }

            .genres {
                grid-template-columns: repeat(5, 1fr);
            }

            /* 5 cards -> 3 arriba + 2 abajo, sin huecos */
            .releases {
                grid-template-columns: repeat(6, 1fr);
            }

            .release {
                grid-column: span 2;
            }

            .release:nth-child(n+4) {
                grid-column: span 3;
                aspect-ratio: 1.5 / 1;
            }

            .sk-row.ten {
                grid-template-columns: repeat(5, 1fr);
            }
        }
        
        @media (max-width: 960px) {
            .two-col {
                grid-template-columns: minmax(0, 1fr);
                gap: 1.6rem;
            }

            .two-col>div {
                min-width: 0;
            }

            .playlists-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 860px) {
            :root {
                --gutter: 1.4rem;
            }

            .plans {
                grid-template-columns: 1fr;
                max-width: 28rem;
                margin: 0 auto;
                gap: 1.5rem;
            }

            .features {
                grid-template-columns: repeat(2, 1fr);
            }

            .sk-strip {
                grid-template-columns: repeat(2, 1fr);
            }

            .sk-cols,
            .sk-plans {
                grid-template-columns: 1fr;
            }
        }

        /* Móvil / tablets pequeñas */
        @media (max-width: 768px) {
            .hero:not(.radio) {
                height: 80vh;
                display: flex;
                align-items: unset;
            }

            .hero-arrow {
                display: none;
            }

            .hero-inner {
                padding: 2.2rem var(--gutter) 3.4rem;
            }

            .hero-bg::after {
                background: linear-gradient(180deg, rgba(15, 13, 11, .6) 0%, rgba(15, 13, 11, .93) 100%);
            }

            .hero-text {
                max-width: 100%;
            }

            .genres {
                grid-template-columns: repeat(5, 1fr);
                gap: .5rem;
            }

            .genre span {
                font-size: .6rem;
                left: .4rem;
                right: .4rem;
                bottom: .45rem;
            }

            .sk-row,
            .sk-row.ten {
                grid-template-columns: repeat(2, 1fr);
            }

            .djs-row{
                display: grid;
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Teléfonos */
        @media (max-width: 900px) {
            :root {
                --gutter: 1rem;
            }

            .search-form {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                padding: .5rem;
            }

            .search-form .field-q,
            .search-form .btn-search {
                grid-column: 1 / -1;
            }

            .genres,
            .releases,
            .playlists-grid {
                display: flex;
                overflow-x: auto;
                scroll-snap-type: x mandatory;
                gap: .65rem;
                padding: 0 var(--gutter) .5rem;
                scrollbar-width: none;
                -webkit-overflow-scrolling: touch;
            }

            .genres::-webkit-scrollbar,
            .releases::-webkit-scrollbar,
            .playlists-grid::-webkit-scrollbar {
                display: none;
            }

            .genre {
                flex: 0 0 6.4rem;
                scroll-snap-align: start;
            }

            .genre span {
                font-size: .64rem;
            }

            .release,
            .release:nth-child(n+4) {
                flex: 0 0 11rem;
                aspect-ratio: 1 / 1.08;
                scroll-snap-align: start;
            }

            .playlist-card {
                flex: 0 0 10rem;
                scroll-snap-align: start;
                aspect-ratio: 1 / 1.12;
            }

            .section-head h2 {
                font-size: 1.15rem;
            }

            .section-sub {
                font-size: .74rem;
            }

            .features {
                gap: .9rem .7rem;
                padding: 1rem var(--gutter);
            }

            .feature {
                gap: .55rem;
            }

            .feature i {
                font-size: 1.15rem;
                width: 24px;
            }

            .feature h4 {
                font-size: .76rem;
            }

            .feature p {
                font-size: .64rem;
            }

            .hero-track {
                min-height: 22rem;
            }

            .btn-cta {
                padding: .75rem 1.4rem;
            }

            .djs-row {
                gap: 1.2rem 1.6rem;
            }

            .dj-avatar {
                width: 10rem;
                height: 10rem;
            }

            .top-item {
                gap: .65rem;
                padding: .6rem .2rem;
            }

            .toast-wrap {
                left: 1rem;
                right: 1rem;
                bottom: 5rem;
            }
        }

        @media (max-width: 380px) {
            .search-form {
                grid-template-columns: 1fr;
            }
        }

        /* Pantallas táctiles: sin hover "pegajoso" en cards */
        @media (hover: none) {

            .genre:hover,
            .release:hover,
            .playlist-card:hover,
            .plan:hover {
                transform: none;
            }
        }
    </style>
@endpush

@section('content')
    <div id="skeleton" aria-hidden="true">
        <div class="sk-wrap">
            <div class="sk sk-hero"></div>
            <div class="sk-strip">
                <div class="sk"></div>
                <div class="sk"></div>
                <div class="sk"></div>
                <div class="sk"></div>
            </div>
            <div class="sk sk-search"></div>
            <div class="sk sk-title"></div>
            <div class="sk-row ten">
                <div class="sk"></div>
                <div class="sk"></div>
                <div class="sk"></div>
                <div class="sk"></div>
                <div class="sk"></div>
                <div class="sk"></div>
                <div class="sk"></div>
                <div class="sk"></div>
                <div class="sk"></div>
                <div class="sk"></div>
            </div>
            <div class="sk sk-title"></div>
            <div class="sk-row">
                <div class="sk"></div>
                <div class="sk"></div>
                <div class="sk"></div>
                <div class="sk"></div>
                <div class="sk"></div>
            </div>
            <div class="sk-cols">
                <div class="sk"></div>
                <div class="sk"></div>
            </div>
            <div class="sk-plans">
                <div class="sk"></div>
                <div class="sk"></div>
                <div class="sk"></div>
            </div>
            <div class="sk-djs">
                <div class="sk"></div>
                <div class="sk"></div>
                <div class="sk"></div>
                <div class="sk"></div>
            </div>
        </div>
    </div>
    
    <div id="content">
        <div class="main">
            <section class="hero" id="hero" aria-roledescription="carousel" aria-label="Destacados"></section>

            <section class="features">
                <div class="feature"><i class="fas fa-headphones"></i>
                    <div>
                        <h4>Audio de Alta Calidad</h4>
                        <p>WAV / MP3 320 kbps</p>
                    </div>
                </div>
                <div class="feature"><i class="fas fa-bolt"></i>
                    <div>
                        <h4>Descargas Inmediatas</h4>
                        <p>sin esperas</p>
                    </div>
                </div>
                <div class="feature"><i class="fas fa-layer-group"></i>
                    <div>
                        <h4>Catálogo Actualizado</h4>
                        <p>nuevos remixes cada semana</p>
                    </div>
                </div>
                <div class="feature"><i class="fas fa-shield-halved"></i>
                    <div>
                        <h4>Contenido Exclusivo</h4>
                        <p>para DJs</p>
                    </div>
                </div>
            </section>

            <section class="search-zone" id="buscar">
                <form class="search-form" id="searchForm" action="{{ route('search') }}" method="get" role="search" autocomplete="off">
                    <div class="search-input field-q">
                        <i class="fas fa-search" aria-hidden="true"></i>
                        <label class="sr-only" for="searchInput">Buscar</label>
                        <input type="text" id="searchInput" name="name"
                            placeholder="Buscar remixes, artistas o canciones..." enterkeyhint="search">
                    </div>
                    <div><label class="sr-only" for="fGenre">Género</label>
                        <select id="fGenre" name="genre">
                            <option value="">Género</option>
                            <option value="Reggaeton Old School">Reggaeton Old School</option>
                            <option value="Cubaton">Cubaton</option>
                            <option value="Salsa">Salsa</option>
                            <option value="Timba">Timba</option>
                            <option value="Tech House">Tech House</option>
                            <option value="Latin House">Latin House</option>
                            <option value="Afro House">Afro House</option>
                            <option value="EDM">EDM</option>
                            <option value="Hip Hop R&amp;B">Hip Hop R&amp;B</option>
                            <option value="Pop">Pop</option>
                        </select>
                    </div>
                    <div><label class="sr-only" for="fBpm">BPM</label>
                        <select id="fBpm" name="bpm">
                            <option value="">BPM</option>
                            <option value="&lt;90">Menos de 90 BPM</option>
                            <option value="90-100">90 – 100 BPM</option>
                            <option value="100-115">100 – 115 BPM</option>
                            <option value="115-125">115 – 125 BPM</option>
                            <option value="125-130">125 – 130 BPM</option>
                            <option value="130+">130+ BPM</option>
                        </select>
                    </div>
                    <div><label class="sr-only" for="fKey">Key (Tonalidad)</label>
                        <select id="fKey" name="key">
                            <option value="">Key (Tonalidad)</option>
                            <option value="1A">1A</option>
                            <option value="1B">1B</option>
                            <option value="2A">2A</option>
                            <option value="2B">2B</option>
                            <option value="3A">3A</option>
                            <option value="3B">3B</option>
                            <option value="4A">4A</option>
                            <option value="4B">4B</option>
                            <option value="5A">5A</option>
                            <option value="5B">5B</option>
                            <option value="6A">6A</option>
                            <option value="6B">6B</option>
                            <option value="7A">7A</option>
                            <option value="7B">7B</option>
                            <option value="8A">8A</option>
                            <option value="8B">8B</option>
                            <option value="9A">9A</option>
                            <option value="9B">9B</option>
                            <option value="10A">10A</option>
                            <option value="10B">10B</option>
                            <option value="11A">11A</option>
                            <option value="11B">11B</option>
                            <option value="12A">12A</option>
                            <option value="12B">12B</option>
                        </select>
                    </div>
                    <div><label class="sr-only" for="fType">Tipo</label>
                        <select id="fType" name="type">
                            <option value="">Tipo</option>
                            <option value="Club Edit">Club Edit</option>
                            <option value="Mashup">Mashup</option>
                            <option value="Intro / Outro">Intro / Outro</option>
                            <option value="Pack">Pack</option>
                            <option value="Acapella">Acapella</option>
                            <option value="Remix">Remix</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-search" id="searchBtn"><i class="fas fa-search"
                            aria-hidden="true"></i> Buscar</button>
                </form>
            </section>

            @if(count($geners) > 0)
            <section class="section">
                <div class="section-head">
                    <h2>Explora por <span>Géneros</span></h2>
                </div>
                <div class="genres">
                    @foreach($geners as $g)
                        <div class="genre" onclick="window.location = '{{ $g['route'] }}'"><img src="{{ $g['cover'] }}"
                            alt="Reggaeton Old School" loading="lazy"><span>{{ $g['name'] }}</span></div>
                    @endforeach
                </div>
            </section>
            @endif

            <section class="section">
                <div class="section-head">
                    <h2>Últimos <span>Lanzamientos</span></h2>
                    <a class="see-all" href="{{ route('remixes') }}">Ver Todos <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="releases">
                    @foreach($newItems as $release)
                    <div class="release">
                        <img src="{{ $release['img'] }}" alt="{{ $release['title'] }}"
                            loading="lazy">
                        <div class="release-info">
                            <h4>{{ $release['title'] }}</h4>
                            <span class="release-badge">{{ $release['genre'] }}</span>
                            <div class="release-meta">
                                <small>{{ $release['bpm'] }} BPM | {{ $release['key'] }} 
                                @if(!$release['canDownload'])
                                | $ {{ $release['price'] }}
                                @endif
                                </small>
                                @if(!$release['canDownload'])
                                <a href="{{ $release['addToCart'] }}" class="release-play btn-buy"
                                    ><i class="fas fa-shopping-cart"></i></a>
                                @else
                                <a href="{{ $release['downloadLink'] }}" class="release-play btn-buy"
                                    ><i class="fas fa-download"></i></a>
                                @endif
                                <span class="release-play btn-play"
                                    data-track="{{ $release['title'] }}" data-artist="{{ $release['artist'] }}" data-cover="{{ $release['img'] }}" data-url="{{ $release['url'] }}" data-flag="{{ $release['flag']}}"><i class="fas fa-play"></i></span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            <section class="section two-col">
                <div>
                    <div class="section-head">
                        <h2>Más <span>Descargados</span></h2>
                    </div>
                    <div class="top-list">
                        @foreach($topItems as $i => $item)
                            <div class="top-item">
                                <div class="top-rank">{{ $i + 1 }}</div>
                                <div class="top-info">
                                    <h4>{{ $item['title'] }}</h4>
                                    <p>{{ $item['artist'] }} • {{ $item['genre'] }} @if(!$item['canDownload'])
                                • $ {{ $release['price'] }}
                                @endif</p>
                                </div><span class="top-dl"><i class="fas fa-download"></i> {{ $item['downloads'] }}</span>
                                @if(!$release['canDownload'])
                                <a href="{{ $release['addToCart'] }}" class="top-play btn-buy"
                                    ><i class="fas fa-shopping-cart"></i></a>
                                @else
                                <a href="{{ $release['downloadLink'] }}" class="top-play btn-buy"
                                    ><i class="fas fa-download"></i></a>
                                @endif
                                <span class="top-play btn-play"
                                    data-track="{{ $item['title'] }}" data-artist="{{ $item['artist'] }}" data-cover="{{ $item['img'] }}" data-url="{{ $item['url'] }}" data-flag="{{ $item['flag']}}"><i class="fas fa-play"></i></span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div>
                    <div class="section-head">
                        <h2>Playlists <span>Destacadas</span></h2>
                        <a class="see-all" href="{{ route('playlist.index') }}">Ver Todas <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="playlists-grid">
                        @foreach($playlists as $playlist)
                        <div class="playlist-card" onclick="window.location = '{{ $playlist['route'] }}'">
                            <img src="{{ $playlist['img'] }}"
                                alt="{{ $playlist['title'] }}" loading="lazy">
                            <div class="playlist-info">
                                <h4>{{ $playlist['title'] }}</h4><small><i class="fas fa-list"></i> {{ $playlist['items'] }} Tracks</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="section">
                <div class="section-head">
                    <h2>Contenido <span>Exclusivo</span></h2>
                    <a class="see-all" href="{{ route('exclusives') }}">Ver Todos <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="releases">
                    @foreach($exclusives as $release)
                    <div class="release">
                        <img src="{{ $release['img'] }}" alt="{{ $release['title'] }}"
                            loading="lazy">
                        <div class="release-info">
                            <h4>{{ $release['title'] }}</h4>
                            <span class="release-badge">{{ $release['genre'] }}</span>
                            <div class="release-meta">
                                <small>{{ $release['bpm'] }} BPM | {{ $release['key'] }} 
                                @if(!$release['canDownload'])
                                | $ {{ $release['price'] }}
                                @endif
                                </small>
                                @if(!$release['canDownload'])
                                <a href="{{ $release['addToCart'] }}" class="release-play btn-buy"
                                    ><i class="fas fa-shopping-cart"></i></a>
                                @else
                                <a href="{{ $release['downloadLink'] }}" class="release-play btn-buy"
                                    ><i class="fas fa-download"></i></a>
                                @endif
                                <span class="release-play btn-play"
                                    data-track="{{ $release['title'] }}" data-artist="{{ $release['artist'] }}" data-cover="{{ $release['img'] }}" data-url="{{ $release['url'] }}" data-flag="{{ $release['flag']}}"><i class="fas fa-play"></i></span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            <section class="section">
                <div class="section-head">
                    <h2>MIX <span>SESSIONS</span></h2>
                    <a class="see-all" href="{{ route('remixes', ['genre' => 'Mix']) }}">Ver Todos <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="releases">
                    @foreach($mixes as $release)
                    <div class="release">
                        <img src="{{ $release['img'] }}" alt="{{ $release['title'] }}"
                            loading="lazy">
                        <div class="release-info">
                            <h4>{{ $release['title'] }}</h4>
                            <span class="release-badge">{{ $release['genre'] }}</span>
                            <div class="release-meta">
                                <small>{{ $release['bpm'] }} BPM | {{ $release['key'] }} 
                                @if(!$release['canDownload'])
                                | $ {{ $release['price'] }}
                                @endif
                                </small>
                                @if(!$release['canDownload'])
                                <a href="{{ $release['addToCart'] }}" class="release-play btn-buy"
                                    ><i class="fas fa-shopping-cart"></i></a>
                                @else
                                <a href="{{ $release['downloadLink'] }}" class="release-play btn-buy"
                                    ><i class="fas fa-download"></i></a>
                                @endif
                                <span class="release-play btn-play"
                                    data-track="{{ $release['title'] }}" data-artist="{{ $release['artist'] }}" data-cover="{{ $release['img'] }}" data-url="{{ $release['url'] }}" data-flag="{{ $release['flag']}}"><i class="fas fa-play"></i></span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            <section class="section" id="planes">
                <div class="section-head" style="flex-direction:column;align-items:flex-start;gap:.2rem; margin-bottom: 2rem">
                    <h2>Planes de <span>Suscripción</span></h2>
                    <p class="section-sub">Accede a todo el catálogo y lleva tus sets al siguiente nivel.</p>
                </div>
                <div class="plans">
                    @foreach($plans as $i => $plan)
                    <div class="plan {{ $i === 0 ? 'featured' : '' }}">
                        <div>
                            <div>
                                @if($i === 0)
                                <div class="plan-flag"><i class="fas fa-fire"></i> El plan más elegido</div>
                                @endif
                                <h3>{{ $plan->downloads }} Descargas <br><span>por {{ $plan->duration_months > 1 ? $plan->duration_months . ' meses' : $plan->duration_months . ' mes' }}</span></h3>
                                <div class="plan-price"><span class="amount">$ {{ $plan->price }}</span></div>
                            </div>
                            <div>  
                                <ul>
                                    @foreach($plan->features as $feature)
                                    <li><i class="fas fa-check"></i> {{ $feature['value'] }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <a class="plan-btn" href="{{ route('payment.form', str_replace(' ', '_', $plan->name)) }}">Comenzar Ahora <i
                                class="fas fa-arrow-right"></i></a>
                    </div>
                    @endforeach
                </div>
            </section>

            <section class="section">
                <div class="section-head">
                    <div>
                        <h2>DJs <span>Destacados</span></h2>
                        <p class="section-sub">Conoce a los DJs de la comunidad CubanPool.</p>
                    </div>
                    <a class="see-all" href="{{ route('djs') }}">Ver Todos <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="djs-row">
                    @foreach($tops as $dj)
                    <div class="dj-card" onclick="window.location = '{{ $dj['route'] }}'">
                        <div class="dj-avatar"><img
                                src="{{ $dj['img'] }}"
                                alt="{{ $dj['name'] }}"></div>
                        <h4>{{ $dj['name'] }}</h4>
                    </div>
                    @endforeach
                </div>
            </section>

        </div>
    </div>
    @include('partials.bottom-player')
    @include('partials.bottom-video-player')
@endsection

@push('scripts')
    <script>
        
        document.addEventListener('DOMContentLoaded', () => {
            const skeleton = document.getElementById('skeleton'), content = document.getElementById('content');
            skeleton.classList.add('hide');
            content.classList.add('show');
            skeleton.remove();
        });

        const toastWrap = document.createElement('div'); toastWrap.className = 'toast-wrap'; document.body.appendChild(toastWrap);
        function showToast(msg, icon) {
            const t = document.createElement('div'); t.className = 'toast';
            const ic = document.createElement('i'); ic.className = 'fas ' + (icon || 'fa-circle-info');
            const sp = document.createElement('span'); sp.textContent = msg; /* textContent: evita inyección HTML desde el buscador */
            t.append(ic, sp);
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
            const esc = s => String(s == null ? '' : s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

            hero.style.setProperty('--hero-interval', interval + 'ms');
            hero.innerHTML =
                '<div class="hero-track">' + slides.map((s, i) => {
                    const tag = i === 0 ? 'h1' : 'h2';
                    const cta = s.cta ? '<button class="btn-cta" data-cta data-href="' + esc(s.cta.href) + '" data-toast="' + esc(s.cta.toast) + '">' + esc(s.cta.label) + ' <i class="fas fa-arrow-right"></i></button>' : '';
                    return '<article class="hero-slide' + (i === 0 ? ' is-active' : '') + '" role="group" aria-roledescription="slide" aria-label="' + (i + 1) + ' de ' + len + '">' +
                        '<div class="hero-bg"><img src="' + esc(s.image) + '" alt="" decoding="async" ' + (i === 0 ? 'fetchpriority="high"' : 'loading="lazy"') + ' style="object-position:' + esc(s.position || 'center') + '"></div>' +
                        '<div class="hero-inner">' +
                        '<div class="hero-kicker">' + (s.kicker || '') + '</div>' +
                        '<' + tag + ' class="hero-title">' + esc(s.title) + (s.accent ? '<span>' + esc(s.accent) + '</span>' : '') + '</' + tag + '>' +
                        '<p class="hero-text">' + esc(s.text) + '</p>' + cta +
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
        
        const searchForm = document.getElementById('searchForm'), searchInput = document.getElementById('searchInput');
        const selects = searchForm.querySelectorAll('select');
        const syncSelects = () => selects.forEach(s => s.classList.toggle('is-set', s.value !== ''));
        selects.forEach(s => s.addEventListener('change', syncSelects));
        searchForm.addEventListener('reset', () => setTimeout(syncSelects, 0));
        searchForm.addEventListener('submit', e => {
            const q = searchInput.value.trim();
            const filters = [['genre', 'Género'], ['bpm', 'BPM'], ['key', 'Key'], ['type', 'Tipo']]
                .filter(([n]) => searchForm.elements[n].value)
                .map(([n, l]) => l + ': ' + searchForm.elements[n].selectedOptions[0].textContent);
            showToast(q || filters.length
                ? 'Buscando' + (q ? ' "' + q + '"' : '') + (filters.length ? ' · ' + filters.join(' · ') : '') + '…'
                : 'Escribe un remix, artista o canción, o elige un filtro', 'fa-search');
        });
    </script>
    <script>
        let playBtns = document.querySelectorAll('.btn-play');
            
        function resetBtns() {
            playBtns.forEach(button => () => {
                var icon = button.querySelector('i');
                if (icon != null) {
                    icon.className = 'fas fa-play';
                }
            });
        }

        function closePlayer(element, flag = 'audio') {
            const el = document.getElementById('bottom-player');
            const elv = document.getElementById('bottom-video-player');
            const audioPlayer = document.getElementById('plyr-audio-player');
            const videoPlayer = document.getElementById('plyr-video-player');
            el.classList.remove('active');
            elv.classList.remove('active');
            audioPlayer.pause();
            videoPlayer.pause();
        }

        function showPlayer(element, flag = 'audio') {
            const el = document.getElementById('bottom-player');
            const elv = document.getElementById('video-player');
            const audioPlayer = document.getElementById('plyr-audio-player');
            const videoPlayer = document.getElementById('plyr-video-player');
            el.classList.remove('active');
            elv.classList.remove('active');
            if (flag == 'video') {
                elv.classList.add('active');
                document.getElementById('video-player-img').src = element.dataset.img;
                document.getElementById('video-player-title').textContent = element.dataset.title;
                document.getElementById('video-player-artist').textContent = element.dataset.artist;
            } else {
                el.classList.add('active');
                document.getElementById('player-img').src = element.dataset.img;
                document.getElementById('player-title').textContent = element.dataset.title;
                document.getElementById('player-artist').textContent = element.dataset.artist;
            }
            var icon = element.querySelector('i');
            if (icon != null) {
                icon.className = 'fas fa-play';
            }
        }

        function verifySource(element, flag = 'audio') {
            const audioPlayer = document.getElementById('plyr-audio-player');
            const videoPlayer = document.getElementById('plyr-video-player');
            var audio = new Audio(element.dataset.url);
            if(flag == 'video'){
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
            audioPlayer.addEventListener('play', () => {
                showPlayer(element, 'audio');
            });
        }

        function playTrack(element) {
            showToast('Cargando ' + element.dataset.track);
            resetBtns();
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
@endpush
