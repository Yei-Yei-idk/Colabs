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
            Administrador: <strong>{{ $usuario->user_nombre }}</strong> ({{ $usuario->numero_documento }})
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

            <label style="font-size: 0.85rem; font-weight: 700; color: #374151; margin-left: 15px; display: block;">Correo electrónico</label>
            <input 
                type="email" 
                name="correo" 
                placeholder="Nuevo correo electrónico" 
                class="mi-input" 
                value="{{ old('correo', $usuario->user_correo) }}" 
                required
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
