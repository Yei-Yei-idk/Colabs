@extends('layouts.admin')

@section('title', 'Gestionar administradores')
@section('page-title', 'Gestionar administradores')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/formularios.css') }}">
@endsection

@section('content')
<section class="auth-registrarse admin-form-section">
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
            <div class="password-strength-meter">
                <div class="password-rules">
                    <p>La contraseña debe incluir:</p>
                    <ul class="requirement-list">
                        <li id="req-length" class="requirement-item">
                            <span class="icon">○</span> Mín. 8 caracteres
                        </li>
                        <li id="req-mixed" class="requirement-item">
                            <span class="icon">○</span> Mayús. y minúsc.
                        </li>
                        <li id="req-numbers" class="requirement-item">
                            <span class="icon">○</span> Al menos 1 número
                        </li>
                        <li id="req-symbols" class="requirement-item">
                            <span class="icon">○</span> Carácter especial
                        </li>
                    </ul>
                </div>
            </div>

            <div class="admin-form-actions">
                <button type="submit" id="btn-submit" class="btn-login">
                    Crear administrador
                </button>
                <a href="{{ route('admin.gestion_admin.index') }}" class="btn-login btn-cancel">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</section>
@endsection

@section('scripts')
    <script src="{{ asset('js/auth/password-validation.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initPasswordValidation({
                inputId: 'password-input',
                submitBtnId: 'btn-submit'
            });
        });
    </script>
@endsection
