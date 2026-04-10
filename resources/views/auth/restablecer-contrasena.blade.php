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
                    id="password-input"
                    placeholder="Nueva contraseña"
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

                <input
                    type="password"
                    name="password_confirmation"
                    id="password-confirm"
                    placeholder="Confirmar contraseña"
                    class="mi-input"
                    required
                >
                <div id="password-match-status" style="font-size: 0.8rem; margin-top: 4px; display: none;"></div>
                <br><br>

                <button type="submit" id="btn-submit" class="btn-login">
                    Restablecer contraseña
                </button>
                <br>

                <p>
                    <a href="{{ route('login') }}">Volver a iniciar sesión</a>
                </p>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/auth/password-validation.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initPasswordValidation({
                inputId: 'password-input',
                confirmId: 'password-confirm',
                submitBtnId: 'btn-submit',
                matchStatusId: 'password-match-status'
            });
        });
    </script>
@endpush
