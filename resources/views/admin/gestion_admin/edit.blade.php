@extends('layouts.admin')

@section('title', 'Editar administrador')
@section('page-title', 'Gestionar administradores')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/formularios.css') }}">
@endsection

@section('content')
<section class="auth-registrarse admin-form-section">
    <div class="formulario">
        <h1>Editar administrador</h1>

        <p class="instrucciones edit-instrucciones">
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

            <label class="edit-label">Correo electrónico</label>
            <input 
                type="email" 
                name="correo" 
                placeholder="Nuevo correo electrónico" 
                class="mi-input" 
                value="{{ old('correo', $usuario->user_correo) }}" 
                required
            >

            <div class="admin-form-actions">
                <button type="submit" class="btn-login">
                    Guardar cambios
                </button>
                <a href="{{ route('admin.gestion_admin.index') }}" class="btn-login btn-cancel">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</section>
@endsection
