@extends('layouts.admin')

@section('title', 'Gestión de admin')
@section('page-title', 'Gestión de admin')

@section('content')

<div class="usuarios-header">
    <div>
        <h2>Gestión de admin</h2>
        <p>Administra las cuentas con nivel administrativo en el sistema</p>
    </div>
    <a href="{{ route('admin.gestion_admin.create') }}" class="btn-agregar-admin">Agregar administrador</a>
</div>

<table class="us">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($usuarios as $u)
            <tr>
                <td>{{ $u->user_nombre }}</td>
                <td>{{ $u->user_correo }}</td>
                <td>{{ $u->rol->rol_nombre ?? 'Administrador' }}</td>
                <td>
                    <div style="display: flex; gap: 8px; justify-content: center;">
                        <a href="{{ route('admin.gestion_admin.edit', $u->id) }}" class="accion-btn">✏️</a>
                        
                        <form action="{{ route('admin.gestion_admin.destroy', $u->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="accion-btn">❌</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 20px; color: #6b7280;">No hay administradores registrados</td>
            </tr>
        @endforelse
    </tbody>
</table>

@endsection

@section('styles')
<style>
    .usuarios-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .usuarios-header h2 {
        margin: 0;
        font-size: 1.5rem;
        color: #111827;
    }

    .usuarios-header p {
        margin: 4px 0 0 0;
        color: #6b7280;
    }

    .btn-agregar-admin {
        background-color: #1a1a2e;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: background-color 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn-agregar-admin:hover {
        background-color: #2e2e4a;
    }

    .us {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .us th {
        background: #f9fafb;
        padding: 16px;
        text-align: left;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
    }

    .us td {
        padding: 16px;
        border-bottom: 1px solid #f3f4f6;
        color: #4b5563;
    }

    .us tr:last-child td {
        border-bottom: none;
    }

    .accion-btn {
        background: #f3f4f6;
        border: none;
        padding: 8px;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        font-size: 1.2rem;
    }

    .accion-btn:hover {
        background: #e5e7eb;
    }
</style>
@endsection
