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

        /* STATUS HERO (KO) */
        .status-hero {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            background: linear-gradient(135deg, var(--card) 0%, rgba(224, 64, 64, .08) 100%);
            border: 1px solid var(--border);
            border-left: 4px solid var(--danger);
            border-radius: 14px;
            padding: 2rem;
        }

        .status-icon {
            width: 66px;
            height: 66px;
            border-radius: 50%;
            background: var(--danger);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.9rem;
            flex-shrink: 0;
            box-shadow: 0 0 28px rgba(224, 64, 64, .4);
        }

        .status-copy {
            flex: 1;
            min-width: 0;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            background: rgba(224, 64, 64, .12);
            color: var(--danger);
            border: 1px solid rgba(224, 64, 64, .32);
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
            background: var(--danger);
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
            color: var(--danger);
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
            margin: .2rem 0 .35rem;
        }

        .status-ref .code {
            font-size: .68rem;
            color: var(--danger);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
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
            margin-top: .5rem;
        }

        .copy-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .copy-btn i {
            font-size: .65rem;
        }

        /* NOTE */
        .note {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            background: var(--bg);
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
            <div class="status-icon"><i class="fas fa-xmark"></i></div>
            <div class="status-copy">
                <span class="status-pill"><span class="dot"></span> Rechazado</span>
                <h1>Compra <span>no completada</span></h1>
                <p>No pudimos procesar tu pago y la operación quedó anulada. Tu método de pago no fue cobrado.</p>
            </div>
        </div>

        <div class="note">
            <i class="fas fa-shield-halved"></i>
            <p><strong>No se realizó ningún cargo.</strong> Si ves un cobro pendiente en tu entidad, es una retención
                temporal que se libera sola en 3-5 días hábiles. Con tu referencia podemos verificarlo.</p>
        </div>

        <div class="actions">
            <a href="{{ route('home') }}" class="btn-secondary"><i class="fas fa-house"></i> Volver al inicio</a>
        </div>

        <div class="support-box">
            <i class="fas fa-headset"></i>
            <div>
                <h4>¿SIGUE SIN FUNCIONAR?</h4>
                <p>Envíanos tu referencia y el código de error - <a href="#">Te ayudamos</a></p>
            </div>
        </div>
    </div>
@endsection
