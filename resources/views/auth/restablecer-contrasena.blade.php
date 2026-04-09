@extends('layouts.app')

@section('title', 'Restablecer contraseña')

@section('content')
    <section class="auth-registrarse">
        <div class="formulario">
            <h1>Nueva contraseña</h1>

            {{-- Errores --}}
            @if ($errors->any())
                <div class="errores">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="post">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Nueva contraseña"
                    class="mi-input"
                    required
                >

                <!-- Requisitos de contraseña -->
                <div class="password-strength-meter" style="margin-top: 10px; margin-bottom: 20px; text-align: left;">
                    <div class="password-rules" style="background: rgba(243, 244, 246, 0.5); padding: 16px; border-radius: 12px; border: 1px solid #e5e7eb;">
                        <p style="margin: 0 0 12px 0; font-size: 0.85rem; font-weight: 700; color: #374151;">La contraseña debe incluir:</p>
                        <ul id="requirement-list" style="list-style: none; padding: 0; margin: 0; display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                            <li id="req-length" style="font-size: 0.8rem; color: #9ca3af; display: flex; align-items: center; gap: 6px;">
                                <span class="icon">○</span> Mín. 8 caracteres
                            </li>
                            <li id="req-mixed" style="font-size: 0.8rem; color: #9ca3af; display: flex; align-items: center; gap: 6px;">
                                <span class="icon">○</span> Mayús. y minúsc.
                            </li>
                            <li id="req-numbers" style="font-size: 0.8rem; color: #9ca3af; display: flex; align-items: center; gap: 6px;">
                                <span class="icon">○</span> Al menos 1 número
                            </li>
                            <li id="req-symbols" style="font-size: 0.8rem; color: #9ca3af; display: flex; align-items: center; gap: 6px;">
                                <span class="icon">○</span> Carácter especial
                            </li>
                        </ul>
                    </div>
                </div>

                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    placeholder="Confirmar contraseña"
                    class="mi-input"
                    required
                >
                <div id="match-status" style="font-size: 0.8rem; margin-top: 4px; display: none;"></div>
                <br><br>

                <button type="submit" id="btn-reset" class="btn-login">
                    Restablecer contraseña
                </button>
                <br>

                <p>
                    <a href="{{ route('login') }}">Volver a iniciar sesión</a>
                </p>
            </form>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('password');
            const confirm = document.getElementById('password_confirmation');
            const submitBtn = document.getElementById('btn-reset');
            const matchStatus = document.getElementById('match-status');

            const requirements = {
                length: { element: document.getElementById('req-length'), regex: /.{8,}/ },
                mixed: { element: document.getElementById('req-mixed'), regex: /^(?=.*[a-z])(?=.*[A-Z]).+$/ },
                numbers: { element: document.getElementById('req-numbers'), regex: /(?=.*[0-9])/ },
                symbols: { element: document.getElementById('req-symbols'), regex: /(?=.*[\W_])/ }
            };

            function validate() {
                const val = password.value;
                const confVal = confirm.value;
                let passedCount = 0;

                Object.keys(requirements).forEach(key => {
                    const req = requirements[key];
                    const isPassed = req.regex.test(val);
                    if (isPassed) {
                        req.element.style.color = '#059669';
                        req.element.querySelector('.icon').innerText = '✓';
                        passedCount++;
                    } else {
                        req.element.style.color = '#9ca3af';
                        req.element.querySelector('.icon').innerText = '○';
                    }
                });

                const matches = val === confVal && val.length > 0;
                if (confVal.length > 0) {
                    matchStatus.style.display = 'block';
                    if (matches) {
                        matchStatus.innerText = '✓ Las contraseñas coinciden';
                        matchStatus.style.color = '#059669';
                    } else {
                        matchStatus.innerText = '❌ Las contraseñas no coinciden';
                        matchStatus.style.color = '#ef4444';
                    }
                } else {
                    matchStatus.style.display = 'none';
                }

                submitBtn.disabled = !(passedCount === 4 && matches);
                submitBtn.style.opacity = submitBtn.disabled ? '0.7' : '1';
            }

            password.addEventListener('input', validate);
            confirm.addEventListener('input', validate);

            // Init state
            validate();
        });
    </script>
@endsection
