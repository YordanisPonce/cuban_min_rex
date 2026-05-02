@extends('layouts.app')

@section('title', 'Acesso denegado - ' . config('app.name'))

@push('styles')
    <style>
        :root {
            --bg: #0f0d0b;
            --card: #1a1714;
            --card-hover: #221f1a;
            --primary: #f5a623;
            --primary-dark: #d48e1a;
            --fg: #f0e8d8;
            --muted: #8a7a66;
            --border: #2a2520;
            --surface: #1e1b17;
            --tag-new: #e04040;
        }

        .error-wrap {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6rem 2rem 3rem;
            position: relative;
            overflow: hidden;
        }

        .error-bg::before {
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

        .error-card {
            position: relative;
            z-index: 2;
            max-width: 620px;
            text-align: center;
        }

        .error-icon-big {
            width: 140px;
            height: 140px;
            margin: 0 auto 2rem;
            background: linear-gradient(135deg, var(--card) 0%, var(--card-hover) 100%);
            border: 2px solid var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: var(--primary);
            position: relative;
            box-shadow: 0 0 40px rgba(245, 166, 35, .2);
        }

        .error-icon-big::after {
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

        .error-code-small {
            font-size: .8rem;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: .3em;
            margin-bottom: .75rem;
            text-transform: uppercase;
        }

        .error-title {
            font-size: 2.25rem;
            font-weight: 800;
            margin-bottom: .75rem;
            text-transform: uppercase;
            line-height: 1.1;
        }

        .error-title span {
            color: var(--primary);
            font-style: italic;
        }

        .error-desc {
            color: var(--muted);
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
            max-width: 480px;
            margin: 0 auto 2rem;
        }

        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 3rem;
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

        .upgrade-card {
            background: linear-gradient(135deg, var(--card) 0%, rgba(245, 166, 35, .05) 100%);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            text-align: left;
            max-width: 520px;
            margin: 0 auto;
        }

        .upgrade-card .icon {
            flex-shrink: 0;
            width: 48px;
            height: 48px;
            border-radius: 10px;
            background: var(--primary);
            color: var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .upgrade-card .text {
            flex: 1;
        }

        .upgrade-card .text h4 {
            font-size: .95rem;
            font-weight: 800;
            margin-bottom: .2rem;
        }

        .upgrade-card .text p {
            color: var(--muted);
            font-size: .8rem;
            line-height: 1.5;
        }

        .upgrade-card a {
            color: var(--primary);
            font-weight: 700;
            font-size: .8rem;
            text-transform: uppercase;
            white-space: nowrap;
        }

        @media(max-width:640px) {
            .error-icon-big {
                width: 110px;
                height: 110px;
                font-size: 3rem;
            }

            .error-title {
                font-size: 1.5rem;
            }

            .upgrade-card {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
@endpush

@section('content')
    <section class="error-wrap">
        <div class="error-bg"></div>
        <div class="error-card">
            <div class="error-icon-big"><i class="fa-solid fa-lock"></i></div>
            <div class="error-code-small">— Error 403 · Acceso restringido —</div>
            <h1 class="error-title">Esta zona es <span>solo para miembros vip</span></h1>
            <p class="error-desc">No tienes permisos para acceder a este contenido. Inicia sesión con una cuenta autorizada o
                actualiza tu plan para desbloquear esta sección.</p>

            <div class="error-actions">
                @auth
                @else
                    <a href="{{ route('login') }}" class="btn-cta"><i class="fa-solid fa-right-to-bracket"></i> Iniciar sesión</a>
                @endauth
                <a href="{{ route('home') }}" class="btn-secondary"><i class="fa-solid fa-house"></i> Ir al inicio</a>
            </div>

            <div class="upgrade-card">
                <div class="icon"><i class="fa-solid fa-crown"></i></div>
                <div class="text">
                    <h4>¿Quieres acceso completo?</h4>
                    <p>Con un plan Premium accedes a todos los remixes y contenido exclusivo.</p>
                </div>
                <a href="{{ route('plans') }}">Ver planes →</a>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
@endpush
