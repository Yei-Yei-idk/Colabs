@extends('layouts.admin')

@section('title', 'Editar administrador')
@section('page-title', 'Gestionar administradores')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/formularios.css') }}">
@endsection

@section('content')
<section class="auth-registrarse" style="min-height: auto; background: transparent; padding: 0; display: block;">
    <div class="formulario">
        <h1>Editar administrador</h1>

        <p class="instrucciones" style="margin-bottom: 24px; color: #6b7280; font-size: 0.95rem;">
            Modifique los datos necesarios. Deje la contraseña en blanco si no desea cambiarla.
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

        <form action="{{ route('admin.gestion_admin.update', $usuario->id) }}" method="POST">
            @csrf
            @method('PUT')

            <label style="font-size: 0.85rem; font-weight: 700; color: #374151; margin-left: 15px;">Documento de identidad</label>
            <input 
                type="text" 
                name="cedula" 
                placeholder="Número de documento" 
                class="mi-input" 
                inputmode="numeric" 
                value="{{ old('cedula', $usuario->numero_documento) }}" 
                minlength="7"
                maxlength="10"
                required
            >

            <label style="font-size: 0.85rem; font-weight: 700; color: #374151; margin-left: 15px; margin-top: 15px; display: block;">Nombre completo</label>
            <input 
                type="text" 
                name="nombre" 
                placeholder="Nombre completo" 
                class="mi-input" 
                value="{{ old('nombre', $usuario->user_nombre) }}" 
                required
            >

            <label style="font-size: 0.85rem; font-weight: 700; color: #374151; margin-left: 15px; margin-top: 15px; display: block;">Correo electrónico</label>
            <input 
                type="email" 
                name="correo" 
                placeholder="Correo electrónico" 
                class="mi-input" 
                value="{{ old('correo', $usuario->user_correo) }}" 
                required
            >

            <label style="font-size: 0.85rem; font-weight: 700; color: #374151; margin-left: 15px; margin-top: 15px; display: block;">Teléfono</label>
            <input 
                type="tel" 
                name="telefono" 
                placeholder="Número de celular" 
                class="mi-input" 
                inputmode="numeric" 
                value="{{ old('telefono', $usuario->user_telefono) }}" 
                minlength="10"
                maxlength="10"
                required
            >

            <label style="font-size: 0.85rem; font-weight: 700; color: #374151; margin-left: 15px; margin-top: 15px; display: block;">Nueva contraseña (opcional)</label>
            <input 
                type="password" 
                name="contra" 
                placeholder="Dejar en blanco para no cambiar" 
                class="mi-input" 
                minlength="8"
            >

            <div style="margin-top: 24px; display: flex; gap: 12px;">
                <button type="submit" class="btn-login" style="flex: 1;">
                    Guardar cambios
                </button>
                <a href="{{ route('admin.gestion_admin.index') }}" class="btn-login btn-cancel" style="flex: 1;">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</section>
@endsection
