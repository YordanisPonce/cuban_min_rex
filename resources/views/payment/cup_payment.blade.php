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
                <div class="step-circle active" id="sc1">1</div>
                <span class="step-label active" id="sl1">Condiciones</span>
            </div>
            <div class="step-line" id="line1"></div>
            <div class="step">
                <div class="step-circle" id="sc2">2</div>
                <span class="step-label" id="sl2">Formulario</span>
            </div>
            <div class="step-line" id="line2"></div>
            <div class="step">
                <div class="step-circle" id="sc3">3</div>
                <span class="step-label" id="sl3">Confirmación</span>
            </div>
        </div>

        <div class="card">
            <div class="panel active" id="panel1">
                <div class="title">
                    <svg width="22" height="22" fill="none" stroke="#3b82f6" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        <path d="m9 12 2 2 4-4" />
                    </svg>
                    Términos y Condiciones
                </div>
                <p class="subtitle">Por favor lee y acepta los términos antes de continuar.</p>
                <div class="terms-box">
                    <p><strong>1. Aceptación de los términos.</strong> Al utilizar este formulario, usted acepta cumplir con los presentes términos y condiciones.</p>
                    <p><strong>2. Uso de la información.</strong> La información proporcionada será utilizada únicamente para los fines descritos y será tratada con confidencialidad.</p>
                    <p><strong>3. Protección de datos.</strong> Nos comprometemos a proteger sus datos personales de acuerdo con la legislación vigente.</p>
                    <p><strong>4. Responsabilidad.</strong> El usuario es responsable de la veracidad de la información proporcionada, debe verificar que sea correcta, no nos responsabilizamos de errores o inconsistencias en los datos enviados.</p>
                    <p><strong>5. Tasa de conversión.</strong> La tasa de conversión de USD a CUP es definida por el sitio, donde 1 USD = {{$setting->currency_convertion_rate}} CUP</p>
                    <p><strong>6. Contacto.</strong> Para cualquier consulta, puede comunicarse al correo {{ $setting->confirmation_email ??'' }}.</p>
                </div>
                <div class="check-row">
                    <input type="checkbox" id="acceptCheck" />
                    <label for="acceptCheck">He leído y acepto los términos y condiciones descritos anteriormente.</label>
                </div>
                <button class="btn btn-primary" id="btnContinue" disabled>Continuar</button>
            </div>

            <div class="panel" id="panel2">
                <form id="payment-form" action="{{ route('payment.cup.proccess', $file->id) }}" method="POST">
                    @csrf
                    <div class="title">
                        <i class="ti tabler-currency-dollar" style="color: #3b82f6"></i>
                        Realizar el Pago
                    </div>
                    <div class="field">
                        <label>Archivo a Comprar </label>
                        <input type="text" name="file_name" value="{{$file->name}}" disabled/>
                    </div>
                    <div class="field">
                        <label>Tarjeta destino</label>
                        <input type="text" name="credit_card" value="{{ $setting->credit_card_info ?? 'XXXX-XXXX-XXXX-XXXX' }}" disabled/>
                    </div>
                    <div class="field">
                        <label>Monto a depositar en CUP</label>
                        <input type="text" name="price" value="{{$file->price * ($setting->currency_convertion_rate)}}" disabled/>
                    </div>
                    <div class="field">
                        <label>Número a confirmar</label>
                        <input type="text" name="confirmation" value="{{ $setting->confirmation_phone ?? '+53 XXXX XXXX' }}" disabled/>
                    </div>
                    <div class="title">
                        <svg width="22" height="22" fill="none" stroke="#3b82f6" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        Información Personal
                    </div>
                    <div class="field">
                        <label for="femail">Correo electrónico al cual se le enviará el archivo *</label>
                        <input type="email" id="femail" name="email" placeholder="correo@ejemplo.com" maxlength="255" value="{{ Auth::user()?->email ?? '' }}"/>
                        <div class="error-msg" id="errEmail"></div>
                    </div>
                    <div class="field">
                        <label for="fphone">Teléfono desde el cual envías la transferencia *</label>
                        <input type="tel" id="fphone" name="phone" placeholder="+53 xxxx xxxx" maxlength="20" value="{{ Auth::user()?->phone ?? '' }}" />
                        <div class="error-msg" id="errPhone"></div>
                    </div>
                    <div class="field">
                        <label for="fcode">Nro. Transacción *</label>
                        <input type="text" id="fcode" name="code" placeholder="XX###X#XX###" maxlength="255" />
                        <div class="error-msg" id="errCode"></div>
                    </div>
                    <div class="btn-row">
                        <a class="btn btn-outline" id="btnBack">Atrás</a>
                        <a class="btn btn-primary" id="btnSubmit">Enviar</a>
                    </div>
                </form>
            </div>

            <div class="panel" id="panel3">
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

@push('scripts')
<script>
    var acceptCheck = document.getElementById('acceptCheck');
    var btnContinue = document.getElementById('btnContinue');
    var btnBack = document.getElementById('btnBack');
    var btnSubmit = document.getElementById('btnSubmit');
    var btnReset = document.getElementById('btnReset');
    var panels = [document.getElementById('panel1'), document.getElementById('panel2'), document.getElementById('panel3')];
    var circles = [document.getElementById('sc1'), document.getElementById('sc2'), document.getElementById('sc3')];
    var labels = [document.getElementById('sl1'), document.getElementById('sl2'), document.getElementById('sl3')];
    var lines = [document.getElementById('line1'), document.getElementById('line2')];
    var checkSvg = '<svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>';

    function goToStep(step) {
        for (var i = 0; i < 3; i++) {
            panels[i].classList.remove('active');
            circles[i].classList.remove('active', 'complete');
            labels[i].classList.remove('active', 'complete');
            var s = i + 1;
            if (s < step) {
                circles[i].classList.add('complete');
                circles[i].innerHTML = checkSvg;
                labels[i].classList.add('complete');
            } else if (s === step) {
                circles[i].classList.add('active');
                circles[i].textContent = s;
                labels[i].classList.add('active');
            } else {
                circles[i].textContent = s;
            }
        }
        panels[step - 1].classList.add('active');
        for (var j = 0; j < 2; j++) {
            lines[j].classList.toggle('complete', j + 1 < step);
        }
    }

    acceptCheck.addEventListener('change', function() {
        btnContinue.disabled = !acceptCheck.checked;
    });
    btnContinue.addEventListener('click', function() {
        goToStep(2);
    });
    btnBack.addEventListener('click', function() {
        goToStep(1);
    });

    btnSubmit.addEventListener('click', function(e) {
        e.preventDefault();
        var email = document.getElementById('femail');
        var phone = document.getElementById('fphone');
        var code = document.getElementById('fcode');
        var errEmail = document.getElementById('errEmail');
        var errPhone = document.getElementById('errPhone');
        var errCode = document.getElementById('errCode');
        errEmail.textContent = '';
        errPhone.textContent = '';
        errCode.textContent = '';
        email.classList.remove('error');
        phone.classList.remove('error');
        code.classList.remove('error');
        var valid = true;
        if (!email.value.trim()) {
            errEmail.textContent = 'El correo es requerido';
            email.classList.add('error');
            valid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
            errEmail.textContent = 'Correo inválido';
            email.classList.add('error');
            valid = false;
        }
        if (!phone.value.trim()) {
            errPhone.textContent = 'El teléfono es requerido';
            phone.classList.add('error');
            valid = false;
        }
        if (!code.value.trim()) {
            errCode.textContent = 'El Nro. Transacción es requerido';
            code.classList.add('error');
            valid = false;
        }
        if (valid) {
            Swal.fire({
                'title' : '¿Enviar Datos?',
                'text' : '¿Has verificado bien los datos introducidos?',
                'icon' : 'warning',
                showCancelButton: true, 
                confirmButtonText: 'Sí, enviar datos', 
                cancelButtonText: 'No, volver a revisar',
            }).then((result) => {
                if (result.isConfirmed) { 
                    document.getElementById('payment-form').submit();
                } 
            });
        }
    });

    btnReset.addEventListener('click', function(e) {
        e.preventDefault();
        acceptCheck.checked = false;
        btnContinue.disabled = true;
        document.getElementById('femail').value = '';
        document.getElementById('fphone').value = '';
        goToStep(1);
    });
</script>
@endpush