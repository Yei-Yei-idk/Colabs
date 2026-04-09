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
                placeholder="Establecer contraseña" 
                class="mi-input" 
                minlength="8"
                required
            >

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
