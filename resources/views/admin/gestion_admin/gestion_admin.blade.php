@extends('layouts.admin')

@section('title', 'Gestión administradores')
@section('page-title', 'Gestión administradores')

@section('content')

<div class="usuarios-header">
    <div>
        <h2>Gestión administradores</h2>
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
            <th class="th-actions-center">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($usuarios as $u)
            <tr>
                <td>{{ $u->user_nombre }}</td>
                <td>{{ $u->user_correo }}</td>
                <td>{{ $u->rol->rol_nombre ?? 'Administrador' }}</td>
                <td>
                    <div class="actions-flex">
                        <a href="{{ route('admin.gestion_admin.edit', $u->id) }}" class="accion-btn" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg></a>
                        
                        <form action="{{ route('admin.gestion_admin.destroy', $u->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="accion-btn" title="Eliminar"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg></button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="empty-cell">No hay administradores registrados</td>
            </tr>
        @endforelse
    </tbody>
</table>

@endsection
