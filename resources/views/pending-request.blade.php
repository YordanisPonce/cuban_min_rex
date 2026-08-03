@extends('layouts.app')

@php
use Carbon\Carbon;
Carbon::setLocale('es');
@endphp

@push('styles')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg: #0f0d0b;
            --card: #1a1714;
            --card-hover: #221f1a;
            --primary: #f5a623;
            --fg: #f0e8d8;
            --muted: #8a7a66;
            --border: #2a2520;
            --tag-new: #e04040;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--fg);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: rgba(15, 13, 11, .95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            height: 56px;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-weight: 800;
            font-size: 1.1rem;
        }

        .nav-logo i {
            color: var(--primary);
            font-size: 1.2rem;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
            font-size: .85rem;
            font-weight: 500;
        }

        .nav-links a {
            color: var(--muted);
            transition: color .2s;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .btn-primary {
            background: var(--primary);
            color: var(--bg);
            padding: .4rem 1rem;
            border-radius: 6px;
            font-size: .8rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
        }

        .wrap {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6rem 2rem 3rem;
            position: relative;
            overflow: hidden;
        }

        .bg-fx::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(245, 166, 35, .08) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(60px);
        }

        .card-main {
            position: relative;
            z-index: 2;
            max-width: 620px;
            width: 100%;
            text-align: center;
        }

        .status-icon {
            width: 130px;
            height: 130px;
            margin: 0 auto 2rem;
            background: linear-gradient(135deg, var(--card) 0%, var(--card-hover) 100%);
            border: 2px solid var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3.4rem;
            color: var(--primary);
            position: relative;
            box-shadow: 0 0 40px rgba(245, 166, 35, .2);
        }

        .status-icon i {
            animation: tick 3s ease-in-out infinite;
        }

        @keyframes tick {

            0%,
            100% {
                transform: rotate(0);
            }

            50% {
                transform: rotate(20deg);
            }
        }

        .status-icon::after {
            content: "";
            position: absolute;
            inset: -8px;
            border: 1px dashed var(--primary);
            border-radius: 50%;
            opacity: .4;
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            to {
                transform: rotate(360deg);
            }
        }

        .code {
            font-size: .8rem;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: .3em;
            margin-bottom: .75rem;
            text-transform: uppercase;
        }

        .title {
            font-size: 2.15rem;
            font-weight: 800;
            margin-bottom: .75rem;
            text-transform: uppercase;
            line-height: 1.1;
        }

        .title span {
            color: var(--primary);
            font-style: italic;
        }

        .desc {
            color: var(--muted);
            font-size: 1rem;
            line-height: 1.6;
            max-width: 480px;
            margin: 0 auto 2rem;
        }

        .ticket {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            max-width: 480px;
            margin: 0 auto 2rem;
            text-align: left;
        }

        .ticket-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: .85rem;
            padding: .55rem 0;
            border-bottom: 1px solid var(--border);
        }

        .ticket-row:last-child {
            border-bottom: none;
        }

        .ticket-row .k {
            color: var(--muted);
            text-transform: uppercase;
            font-size: .72rem;
            letter-spacing: .08em;
            font-weight: 700;
        }

        .ticket-row .v {
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            text-transform: uppercase;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: rgba(245, 166, 35, .12);
            color: var(--primary);
            border: 1px solid rgba(245, 166, 35, .3);
            padding: .25rem .65rem;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .badge .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--primary);
            animation: pulse 1.6s ease-in-out infinite;
        }

        @keyframes pulse {
            50% {
                opacity: .25;
            }
        }

        .steps {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            gap: 0;
            max-width: 480px;
            margin: 0 auto 2.25rem;
        }

        .step {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .5rem;
            position: relative;
        }

        .step:not(:last-child)::after {
            content: "";
            position: absolute;
            top: 14px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: var(--border);
            z-index: 0;
        }

        .step.done:not(:last-child)::after {
            background: var(--primary);
        }

        .step .dotc {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--card);
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .7rem;
            color: var(--muted);
            position: relative;
            z-index: 1;
        }

        .step.done .dotc {
            background: var(--primary);
            border-color: var(--primary);
            color: var(--bg);
        }

        .step.active .dotc {
            border-color: var(--primary);
            color: var(--primary);
            box-shadow: 0 0 0 4px rgba(245, 166, 35, .12);
        }

        .step .lbl {
            font-size: .68rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            font-weight: 700;
            text-align: center;
        }

        .step.active .lbl,
        .step.done .lbl {
            color: var(--fg);
        }

        .actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }

        .btn-cta {
            background: var(--primary);
            color: var(--bg);
            padding: .85rem 1.75rem;
            border-radius: 8px;
            font-weight: 800;
            font-size: .85rem;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            text-transform: uppercase;
            transition: all .2s;
        }

        .btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(245, 166, 35, .3);
        }

        .btn-secondary {
            background: transparent;
            color: var(--fg);
            padding: .85rem 1.75rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: .85rem;
            border: 1px solid var(--border);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            text-transform: uppercase;
            transition: all .2s;
        }

        .btn-secondary:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .note {
            font-size: .75rem;
            color: var(--muted);
            line-height: 1.6;
        }

        .note a {
            color: var(--primary);
            font-weight: 700;
        }

        footer {
            border-top: 1px solid var(--border);
            padding: 1.25rem 2rem;
            text-align: center;
            color: var(--muted);
            font-size: .75rem;
        }

        footer a {
            color: var(--primary);
        }

        @media(max-width:640px) {
            .status-icon {
                width: 104px;
                height: 104px;
                font-size: 2.6rem;
            }

            .title {
                font-size: 1.5rem;
            }

            .nav-links {
                display: none;
            }

            nav {
                padding: 0 1rem;
            }
        }
    </style>
@endpush

@section('content')
    <section class="wrap">
        <div class="bg-fx"></div>
        <div class="card-main">
            <div class="status-icon"><i class="fa-solid fa-hourglass-half"></i></div>
            <div class="code">— Solicitud en proceso —</div>
            <h1 class="title">Tu solicitud está <span>en revisión</span></h1>
            <p class="desc">{{ $requestDescription }}</p>

            <div class="ticket">
                <div class="ticket-row">
                    <span class="k">Nº de solicitud</span>
                    <span class="v">#{{ $requestId }}</span>
                </div>
                <div class="ticket-row">
                    <span class="k">Enviada</span>
                    <span class="v">{{ Carbon::parse($requestDate)->diffForHumans() }}</span>
                </div>
                <div class="ticket-row">
                    <span class="k">Estado</span>
                    <span class="badge"><span class="dot"></span> {{ $requestStatus }}</span>
                </div>
            </div>

            <div class="actions">
                <a href="{{ route('home') }}" class="btn-cta"><i class="fa-solid fa-house"></i> Volver al inicio</a>
                <a href="tel:{{ config('contact.phone') }}" class="btn-secondary"><i class="fa-solid fa-headset"></i> Contactar soporte</a>
            </div>

            <p class="note">Si no recibes respuesta pasado el plazo estimado, escríbenos a <a
                    href="mailto:{{ config('contact.email') }}">{{ config('contact.email')}}</a> indicando tu número de solicitud.</p>
        </div>
    </section>
@endsection
