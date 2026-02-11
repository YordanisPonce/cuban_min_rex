@extends('layouts.app')

@section('title', 'Checkout')

@push('styles')
<style>
    .steps-wraper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        color: #1a1d23 !important;
    }

    .steps-container {
        width: 100%;
        max-width: 480px;
        margin-top: 5rem;
    }

    .steps {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2rem;
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .step-circle {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        font-weight: 700;
        background: #d1d5db;
        color: #6b7280;
        transition: all 0.4s ease;
    }

    .step-circle.active {
        background: #3b82f6;
        color: #fff;
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.35);
    }

    .step-circle.complete {
        background: #10b981;
        color: #fff;
    }

    .step-label {
        margin-top: 0.5rem;
        font-size: 0.7rem;
        font-weight: 600;
        color: #9ca3af;
        transition: color 0.3s;
    }

    .step-label.active {
        color: #3b82f6;
    }

    .step-label.complete {
        color: #10b981;
    }

    .step-line {
        width: 70px;
        height: 2px;
        background: #d1d5db;
        margin: 0 0.5rem;
        margin-bottom: 1.25rem;
        transition: background 0.5s;
    }

    .step-line.complete {
        background: #10b981;
    }

    .card {
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    }

    .panel {
        display: none;
        animation: fadeIn 0.4s ease;
    }

    .panel.active {
        display: block;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(12px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .title {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .title svg {
        flex-shrink: 0;
    }

    .subtitle {
        font-size: 0.85rem;
        color: #6b7280;
        margin-bottom: 1.25rem;
    }

    .terms-box {
        height: 200px;
        overflow-y: auto;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 1rem;
        background: #f9fafb;
        font-size: 0.82rem;
        color: #6b7280;
        line-height: 1.65;
        margin-bottom: 1.25rem;
    }

    .terms-box::-webkit-scrollbar {
        width: 5px;
    }

    .terms-box::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 4px;
    }

    .terms-box strong {
        color: #374151;
    }

    .check-row {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 1rem;
        border-radius: 10px;
        background: #f3f4f6;
        margin-bottom: 1.5rem;
        cursor: pointer;
    }

    .check-row input[type="checkbox"] {
        accent-color: #3b82f6;
        width: 18px;
        height: 18px;
        margin-top: 2px;
        cursor: pointer;
    }

    .check-row label {
        font-size: 0.85rem;
        cursor: pointer;
        line-height: 1.4;
        color: #374151;
    }

    .btn {
        width: 100%;
        height: 48px;
        border: none;
        border-radius: 10px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-primary:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-primary:disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }

    .btn-outline {
        background: transparent;
        border: 1.5px solid #d1d5db;
    }

    .btn-outline:hover {
        background: #ccc;
    }

    .btn-row {
        display: flex;
        gap: 0.75rem;
    }

    .btn-row .btn {
        flex: 1;
    }

    .field {
        margin-bottom: 1rem;
    }

    .field label {
        display: block;
        font-size: 0.82rem;
        font-weight: 600;
        margin-bottom: 0.35rem;
        color: #6b7280;
    }

    .field input,
    .field textarea {
        width: 100%;
        padding: 0.7rem 0.85rem;
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        font-size: 0.9rem;
        background: #fff;
        color: #1a1d23;
        transition: border-color 0.2s, box-shadow 0.2s;
        outline: none;
        font-family: inherit;
    }

    .field input:focus,
    .field textarea:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
    }

    .field input.error,
    .field textarea.error {
        border-color: #ef4444;
    }

    .field textarea {
        min-height: 70px;
        resize: vertical;
    }

    .error-msg {
        font-size: 0.75rem;
        color: #ef4444;
        margin-top: 0.25rem;
    }

    .success {
        text-align: center;
        padding: 2rem 0;
    }

    .success-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: rgba(16, 185, 129, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
    }

    .success h2 {
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .success p {
        color: #6b7280;
        font-size: 0.9rem;
        max-width: 320px;
        margin: 0 auto 1.5rem;
        line-height: 1.5;
    }

    .success .btn {
        width: auto;
        padding: 0 2rem;
        margin: 0 auto;
        display: block;
    }
</style>
@endpush

@section('content')
<div class="steps-wraper">
    <div class="steps-container">
        <div class="steps">
            <div class="step">
                <div class="step-circle complete" id="sc1">1</div>
                <span class="step-label complete" id="sl1">Condiciones</span>
            </div>
            <div class="step-line" id="line1"></div>
            <div class="step">
                <div class="step-circle complete" id="sc2">2</div>
                <span class="step-label complete" id="sl2">Formulario</span>
            </div>
            <div class="step-line" id="line2"></div>
            <div class="step">
                <div class="step-circle active" id="sc3">3</div>
                <span class="step-label active" id="sl3">Confirmación</span>
            </div>
        </div>

        <div class="card">
            <div class="panel active" id="panel3">
                <div class="success">
                    <div class="success-icon">
                        <svg width="36" height="36" fill="none" stroke="#10b981" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                    </div>
                    <h2>¡Formulario Enviado!</h2>
                    <p>Se ha enviado tu información, pronto se confirmara tu pago y se te enviará el archivo a la bandeja de entrada de tu correo.</p>
                    <a href="{{ route('radio') }}">Ver más archivos</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection