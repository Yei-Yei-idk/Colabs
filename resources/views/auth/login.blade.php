@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
    <section class="auth-registrarse">
        <div class="formulario">
            <h1>Iniciar sesión</h1>

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


            <form action="{{ route('login.autenticar') }}" method="post">
                @csrf
                <input type="hidden" name="redirect" value="{{ old('redirect', request('redirect')) }}">

                <input
                    type="text"
                    name="user"
                    placeholder="Documento o Correo"
                    class="mi-input"
                    value="{{ old('user') }}"
                    
                >
                <br>

                <input
                    type="password"
                    name="contra"
                    placeholder="Contraseña"
                    class="mi-input"
                    
                >
                <div class="auth-remember-wrapper">
                    <label class="auth-label-checkbox">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Recordarme en este equipo
                    </label>
                </div>

                <button type="submit" name="login" class="btn-login">
                    Ingresar
                </button>
                <br>

                <p>
                    <a href="{{ route('password.request') }}">¿Has olvidado tu contraseña?</a>
                </p>
            </form>

            <div class="auth-separador">
                <span>o</span>
            </div>

            <a href="{{ route('google.redirect', ['redirect' => request('redirect')]) }}" class="btn-google-auth" aria-label="Continuar con Google">
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
@endsection
