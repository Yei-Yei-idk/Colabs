@extends('layouts.admin')

@section('title', 'Espacios')
@section('page-title', 'Espacios')

@section('content')
    <h2>Espacios</h2>
    <p>Administra los espacios de coworking disponibles</p>

    {{-- ESTADÍSTICAS --}}
    <div class="stats">
        <div class="card">{{ $total_espacios }} <small>Total espacios</small></div>
        <div class="card">{{ $total_espacios_activos }} <small>Activos</small></div>
        <div class="card">{{ $total_espacios_inactivos }} <small>Inactivos</small></div>
    </div>

    {{-- BOTÓN NUEVO --}}
    <div class="filters filters-end">
        @if(auth()->user()->rol_id == 1)
            <a href="{{ route('admin.espacios.create') }}" class="new-btn">Nuevo espacio</a>
        @endif
    </div>

    {{-- TABLA --}}
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Espacio</th>
                <th>Descripción</th>
                <th>Capacidad</th>
                <th>Tipo</th>
                <th>Precio hora</th>
                <th>Estado</th>
                @if(auth()->user()->rol_id == 1)
                    <th>Acción</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($espacios as $espacio)
                <tr>
                    <td>{{ $espacio->espacio_id }}</td>
                    <td>{{ $espacio->esp_nombre }}</td>
                    <td>{{ $espacio->esp_descripcion }}</td>
                    <td>{{ $espacio->esp_capacidad }}</td>
                    <td>{{ $espacio->esp_tipo }}</td>
                    <td>${{ number_format($espacio->esp_precio_hora, 0, ',', '.') }}</td>
                    <td>
                        <span class="status {{ strtolower($espacio->esp_estado) == 'activo' ? 'active' : 'inactive' }}"></span>
                        {{ $espacio->esp_estado }}
                    </td>
                    @if(auth()->user()->rol_id == 1)
                        <td>
                            {{-- Botón Editar --}}
                            <a href="{{ route('admin.espacios.edit', $espacio->espacio_id) }}" class="accion-btn" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg></a>

                            {{-- Formulario para cambiar estado --}}
                            <form action="{{ route('admin.espacios.toggle', $espacio->espacio_id) }}" method="POST" class="form-inline">
                                @csrf
                                <button type="submit" class="accion-btn" title="Cambiar estado">
                                    @if($espacio->esp_estado == 'Activo')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-danger"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-success"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    @endif
                                </button>
                            </form>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Snackbar heredado del layout --}}
@endsection
