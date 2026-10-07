@extends('layouts.app')

@push('styles')
    <style>
        :root {
            --muted: #8a7a66;
            --border: #2a2520;
            --surface: #1e1b17;
            --danger: #e04040;
            --ok: #3ba55d;
        }

        /* MAIN */
        .main {
            margin-top: 56px;
            padding: 2.5rem 2rem 8rem;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }

        /* STATUS HERO (OK) */
        .status-hero {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            background: linear-gradient(135deg, var(--card) 0%, rgba(59, 165, 93, .07) 100%);
            border: 1px solid var(--border);
            border-left: 4px solid var(--ok);
            border-radius: 14px;
            padding: 2rem;
        }

        .status-icon {
            width: 66px;
            height: 66px;
            border-radius: 50%;
            background: var(--ok);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            flex-shrink: 0;
            box-shadow: 0 0 28px rgba(59, 165, 93, .4);
            position: relative;
        }

        .status-icon::after {
            content: "";
            position: absolute;
            inset: -8px;
            border-radius: 50%;
            border: 2px solid rgba(59, 165, 93, .35);
            animation: ring 2.2s ease-out infinite;
        }

        @keyframes ring {
            0% {
                transform: scale(.85);
                opacity: .9;
            }

            100% {
                transform: scale(1.25);
                opacity: 0;
            }
        }

        .status-copy {
            flex: 1;
            min-width: 0;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            background: rgba(59, 165, 93, .12);
            color: var(--ok);
            border: 1px solid rgba(59, 165, 93, .3);
            padding: .25rem .6rem;
            border-radius: 999px;
            font-size: .68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: .6rem;
        }

        .status-pill .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--ok);
            animation: pulse 1.6s ease-in-out infinite;
        }

        @keyframes pulse {
            50% {
                opacity: .3;
            }
        }

        .status-copy h1 {
            font-size: 1.6rem;
            font-weight: 900;
            text-transform: uppercase;
            line-height: 1.1;
            margin-bottom: .35rem;
        }

        .status-copy h1 span {
            color: var(--ok);
        }

        .status-copy p {
            font-size: .85rem;
            color: var(--muted);
            line-height: 1.55;
        }

        .status-ref {
            text-align: right;
            flex-shrink: 0;
            padding-left: 1.5rem;
            border-left: 1px solid var(--border);
        }

        .status-ref .label {
            font-size: .62rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .09em;
            font-weight: 700;
        }

        .status-ref .val {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--primary);
            font-variant-numeric: tabular-nums;
            margin: .2rem 0 .5rem;
        }

        .copy-btn {
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--muted);
            font-family: inherit;
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            padding: .35rem .65rem;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            transition: all .2s;
        }

        .copy-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .copy-btn i {
            font-size: .65rem;
        }

        /* GRID */
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        /* CARD */
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
        }

        .card-title {
            font-size: .78rem;
            font-weight: 800;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1rem;
            padding-bottom: .75rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .card-title i {
            font-size: .85rem;
        }

        /* INFO ROWS */
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            font-size: .82rem;
            padding: .6rem 0;
            border-bottom: 1px solid var(--border);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-row .k {
            color: var(--muted);
            text-transform: uppercase;
            font-size: .66rem;
            letter-spacing: .06em;
            font-weight: 700;
            white-space: nowrap;
        }

        .info-row .v {
            font-weight: 700;
            text-align: right;
        }

        .info-row .v a {
            color: var(--primary);
        }

        .info-row .v.ok {
            color: var(--ok);
        }

        .info-row .v.amount {
            color: var(--primary);
            font-size: 1rem;
        }

        /* TIMELINE */
        .timeline {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .tl-step {
            display: flex;
            align-items: flex-start;
            gap: .9rem;
            padding-bottom: 1.1rem;
            position: relative;
        }

        .tl-step:last-child {
            padding-bottom: 0;
        }

        .tl-step::before {
            content: "";
            position: absolute;
            left: 13px;
            top: 30px;
            bottom: 2px;
            width: 2px;
            background: var(--border);
        }

        .tl-step:last-child::before {
            display: none;
        }

        .tl-step.done::before {
            background: var(--ok);
        }

        .tl-dot {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--surface);
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .68rem;
            color: var(--muted);
            flex-shrink: 0;
            position: relative;
            z-index: 1;
        }

        .tl-step.done .tl-dot {
            background: var(--ok);
            border-color: var(--ok);
            color: #fff;
        }

        .tl-step.active .tl-dot {
            border-color: var(--primary);
            color: var(--primary);
            box-shadow: 0 0 0 4px rgba(245, 166, 35, .12);
        }

        .tl-txt h4 {
            font-size: .82rem;
            font-weight: 700;
            margin-bottom: .15rem;
        }

        .tl-txt p {
            font-size: .72rem;
            color: var(--muted);
            line-height: 1.45;
        }

        /* NOTE */
        .note {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-left: 3px solid var(--primary);
            border-radius: 10px;
            padding: .9rem 1.1rem;
            margin-top: 1.5rem;
        }

        .note i {
            color: var(--primary);
            font-size: .9rem;
            margin-top: 2px;
        }

        .note p {
            font-size: .75rem;
            color: var(--muted);
            line-height: 1.5;
        }

        .note p strong {
            color: var(--fg);
        }

        /* ACTIONS */
        .actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1.75rem;
            justify-content: center;
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
            letter-spacing: .04em;
            transition: all .2s;
            font-family: inherit;
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
            letter-spacing: .04em;
            transition: all .2s;
            font-family: inherit;
        }

        .btn-secondary:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        /* SUPPORT */
        .support-box {
            max-width: 520px;
            margin: 2rem auto 0;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            justify-content: center;
        }

        .support-box i {
            color: var(--primary);
            font-size: 1.3rem;
        }

        .support-box h4 {
            font-size: .88rem;
            font-weight: 700;
        }

        .support-box p {
            font-size: .74rem;
            color: var(--muted);
        }

        .support-box a {
            color: var(--primary);
            text-decoration: underline;
        }
    </style>
@endpush

@section('content')
    <div class="main">
        <div class="status-hero">
            <div class="status-icon"><i class="fas fa-check"></i></div>
            <div class="status-copy">
                <span class="status-pill"><span class="dot"></span> Aprobado</span>
                <h1>Compra <span>realizada</span></h1>
                <p>Su pago se ha procesado correctamente. Se activará en breve. Tenga paciencia. Si tarda, contacte con nuestro soporte antes de realizar otra acción.</p>
            </div>
        </div>
        <div class="actions">
            <a href="{{ route('home') }}" class="btn-secondary"><i class="fas fa-house"></i> Volver al inicio</a>
        </div>
        <div class="support-box">
            <i class="fas fa-headset"></i>
            <div>
                <h4>¿ALGO NO CUADRA?</h4>
                <p>Escríbenos con tu referencia - <a href="mailto:{{ config('contact.email') }}">Te respondemos rápido</a>
                </p>
            </div>
        </div>
    </div>
@endsection
