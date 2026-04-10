@extends('layouts.app')

@section('title', 'Completar perfil')

@section('content')
<section class="auth-registrarse">
    <div class="formulario">
        <h1>Completa tu perfil</h1>

        <p class="instrucciones" style="margin-bottom: 16px; text-align: left;">
            Para finalizar tu registro con Google, completa estos datos obligatorios.
        </p>

        @if ($errors->any())
            <div class="errores">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('google.perfil.guardar') }}" method="POST">
            @csrf

            <input
                type="text"
                name="numero_documento"
                placeholder="Numero de documento"
                class="mi-input"
                inputmode="numeric"
                value="{{ old('numero_documento', $datos['numero_documento'] ?? '') }}"
                required
            >
            <input
                type="text"
                name="user_nombre"
                placeholder="Nombre completo"
                class="mi-input"
                value="{{ old('user_nombre', $datos['user_nombre'] ?? $usuario->user_nombre) }}"
                required
            >
            <input
                type="tel"
                name="user_telefono"
                placeholder="Celular o telefono"
                class="mi-input"
                inputmode="numeric"
                value="{{ old('user_telefono', $datos['user_telefono'] ?? '') }}"
                required
            >

            <div class="campo-bloqueado-encabezado">
                <label for="correo_google" class="campo-bloqueado-label">Correo vinculado con Google</label>
                <span class="campo-bloqueado-badge">No editable</span>
            </div>
            <input
                id="correo_google"
                type="email"
                class="mi-input mi-input-bloqueado"
                value="{{ $datos['user_correo'] ?? $usuario->user_correo }}"
                disabled
                readonly
            >
            <p class="campo-bloqueado-ayuda">
                Este correo se administra desde tu cuenta de Google.
            </p>

            <button type="submit" class="btn-login">
                Guardar y continuar
            </button>
        </form>
    </div>
</section>
@endsection
