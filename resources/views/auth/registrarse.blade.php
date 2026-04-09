@extends('layouts.app')

@section('title', 'Registrarse')

@section('content')
    <section class="auth-registrarse">
    <div class="formulario">
        <h1>Registrarse</h1>

        {{-- Errores de validación --}}
        @if ($errors->any())
            <div class="errores">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('registrarse.guardar', [], false) }}" method="post" id="form-registrarse">
            @csrf

            <input
                type="text"
                name="numero_documento"
                id="numero_documento"
                placeholder="Numero de documento"
                class="mi-input"
                inputmode="numeric"
                value="{{ old('numero_documento') }}"
            >
            <br>

            <input
                type="text"
                name="user_nombre"
                id="user_nombre"
                placeholder="Nombre"
                class="mi-input"
                value="{{ old('user_nombre') }}"

            >
            <br>

            <input
                type="email"
                name="user_correo"
                id="user_correo"
                placeholder="Correo"
                class="mi-input"
                value="{{ old('user_correo') }}"
            >
            <br>

            <input
                type="number"
                name="user_telefono"
                id="user_telefono"
                placeholder="Telefono"
                class="mi-input"
                value="{{ old('user_telefono') }}"
            >
            <br>

            <input
                type="password"
                name="user_contrasena"
                id="user_contrasena"
                placeholder="Contraseña"
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

            <br>

            <input
                type="checkbox"
                name="condiciones"
                id="term_cond"
                {{ old('condiciones') ? 'checked' : '' }}
                required
            >
            <p>Al crear la cuenta aceptas nuestros términos y condiciones.</p>

            <button type="submit" name="crear" id="btn-crear-cuenta" class="btn-login" style="cursor:pointer;">Crear</button>
        </form>

        <div class="auth-separador">
            <span>o</span>
        </div>

        <a href="{{ route('google.redirect') }}" class="btn-google-auth" aria-label="Continuar con Google">
            <span class="btn-google-icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" role="img" focusable="false">
                    <path fill="#EA4335" d="M12 10.2v3.95h5.49c-.24 1.27-.96 2.34-2.02 3.06l3.27 2.54c1.9-1.75 3-4.32 3-7.37 0-.72-.06-1.41-.19-2.08H12z"/>
                    <path fill="#4285F4" d="M12 22c2.7 0 4.96-.9 6.62-2.43l-3.27-2.54c-.9.6-2.05.96-3.35.96-2.58 0-4.77-1.74-5.56-4.08l-3.38 2.61C4.72 19.8 8.08 22 12 22z"/>
                    <path fill="#FBBC05" d="M6.44 13.91c-.2-.6-.31-1.24-.31-1.91s.11-1.31.31-1.91l-3.38-2.61A9.95 9.95 0 0 0 2 12c0 1.61.39 3.14 1.06 4.52l3.38-2.61z"/>
                    <path fill="#34A853" d="M12 6.01c1.47 0 2.8.51 3.84 1.5l2.88-2.88C16.95 2.98 14.7 2 12 2 8.08 2 4.72 4.2 3.06 7.48l3.38 2.61C7.23 7.75 9.42 6.01 12 6.01z"/>
                </svg>
            </span>
            <span>Continuar con Google</span>
        </a>
    </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const formulario = document.getElementById('form-registrarse');
            const botonCrear = document.getElementById('btn-crear-cuenta');
            const passwordInput = document.getElementById('user_contrasena');

            const requirements = {
                length: { element: document.getElementById('req-length'), regex: /.{8,}/ },
                mixed: { element: document.getElementById('req-mixed'), regex: /^(?=.*[a-z])(?=.*[A-Z]).+$/ },
                numbers: { element: document.getElementById('req-numbers'), regex: /(?=.*[0-9])/ },
                symbols: { element: document.getElementById('req-symbols'), regex: /(?=.*[\W_])/ }
            };

            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    const val = passwordInput.value;
                    let passedCount = 0;

                    Object.keys(requirements).forEach(key => {
                        const req = requirements[key];
                        const isPassed = req.regex.test(val);
                        
                        if (isPassed) {
                            req.element.style.color = '#059669';
                            req.element.querySelector('.icon').innerText = '✓';
                            req.element.querySelector('.icon').style.fontWeight = 'bold';
                            passedCount++;
                        } else {
                            req.element.style.color = '#9ca3af';
                            req.element.querySelector('.icon').innerText = '○';
                            req.element.querySelector('.icon').style.fontWeight = 'normal';
                        }
                    });

                    // Habilitar/Deshabilitar botón
                    if (passedCount === 4) {
                        botonCrear.disabled = false;
                        botonCrear.style.opacity = '1';
                    } else {
                        botonCrear.disabled = true;
                        botonCrear.style.opacity = '0.7';
                    }
                });
            }

            if (formulario && botonCrear) {
                formulario.addEventListener('submit', function () {
                    botonCrear.disabled = true;
                    botonCrear.textContent = 'Creando cuenta...';
                    botonCrear.style.opacity = '0.7';
                    botonCrear.style.cursor = 'not-allowed';
                });
            }

            // Inicializar estado del botón
            if (botonCrear) {
                botonCrear.disabled = true;
                botonCrear.style.opacity = '0.7';
            }
        });
    </script>
@endsection

