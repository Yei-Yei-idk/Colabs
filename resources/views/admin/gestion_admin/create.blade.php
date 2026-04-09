@extends('layouts.admin')

@section('title', 'Gestionar administradores')
@section('page-title', 'Gestionar administradores')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/formularios.css') }}">
@endsection

@section('content')
<section class="auth-registrarse" style="min-height: auto; background: transparent; padding: 0; display: block;">
    <div class="formulario">
        <h1>Registrar administrador</h1>

        @if ($errors->any())
            <div class="errores">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.gestion_admin.store') }}" method="POST">
            @csrf

            <input 
                type="text" 
                name="cedula" 
                placeholder="Número de documento" 
                class="mi-input" 
                inputmode="numeric" 
                value="{{ old('cedula') }}" 
                minlength="7"
                maxlength="10"
                required
            >

            <input 
                type="text" 
                name="nombre" 
                placeholder="Nombre completo" 
                class="mi-input" 
                value="{{ old('nombre') }}" 
                required
            >

            <input 
                type="email" 
                name="correo" 
                placeholder="Correo electrónico" 
                class="mi-input" 
                value="{{ old('correo') }}" 
                required
            >

            <input 
                type="tel" 
                name="telefono" 
                placeholder="Número de celular" 
                class="mi-input" 
                inputmode="numeric" 
                value="{{ old('telefono') }}" 
                minlength="10"
                maxlength="10"
                required
            >

            <input 
                type="password" 
                name="contra" 
                id="password-input"
                placeholder="Establecer contraseña" 
                class="mi-input" 
                required
            >

            <!-- Requisitos de contraseña -->
            <div class="password-strength-meter" style="margin-top: -16px; margin-bottom: 24px;">
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

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const passwordInput = document.getElementById('password-input');
                    const submitBtn = document.querySelector('.btn-login[type="submit"]');
                    
                    const requirements = {
                        length: { element: document.getElementById('req-length'), regex: /.{8,}/ },
                        mixed: { element: document.getElementById('req-mixed'), regex: /^(?=.*[a-z])(?=.*[A-Z]).+$/ },
                        numbers: { element: document.getElementById('req-numbers'), regex: /(?=.*[0-9])/ },
                        symbols: { element: document.getElementById('req-symbols'), regex: /(?=.*[\W_])/ }
                    };

                    passwordInput.addEventListener('input', function() {
                        const val = passwordInput.value;
                        let passedCount = 0;

                        // Validar requisitos individuales
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
                            submitBtn.disabled = false;
                            submitBtn.style.opacity = '1';
                        } else {
                            submitBtn.disabled = true;
                            submitBtn.style.opacity = '0.7';
                        }
                    });

                    // Inicializar botón deshabilitado
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.7';
                    submitBtn.title = 'Completa los requisitos de seguridad para continuar';
                });
            </script>

            <div style="margin-top: 24px; display: flex; gap: 12px;">
                <button type="submit" class="btn-login" style="flex: 1;">
                    Crear administrador
                </button>
                <a href="{{ route('admin.gestion_admin.index') }}" class="btn-login btn-cancel" style="flex: 1;">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</section>
@endsection
