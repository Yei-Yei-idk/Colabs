@extends('layouts.admin')

@section('title', 'Nuevo Espacio')
@section('page-title', 'Espacios')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/espacios.css') }}">
@endsection

@section('content')
    <div class="form-container">
        <div class="form-header">
            <h2>Nuevo espacio</h2>
            <p>Habilita un nuevo espacio de coworking mediante el siguiente formulario</p>
        </div>

        {{-- Errores de validación --}}
        @if ($errors->any())
            <div class="form-error-alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.espacios.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-grid">
                <div class="form-section">
                    <h3>Información Básica</h3>
                    <div class="form-group">
                        <label for="esp_id">ID del Espacio *</label>
                        <input type="number" id="esp_id" name="esp_id" value="{{ old('esp_id') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="nombre">Nombre del Espacio *</label>
                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" placeholder="Ej. Sala de Innovación" required>
                    </div>

                    <div class="form-group">
                        <h4 class="tipo-heading">Tipo de oficina</h4>
                        <div class="radio-group">
                            @php
                                $tipos = [
                                    'Oficina' => 'Espacio privado para trabajo individual',
                                    'Sala de reuniones' => 'Espacio ideal para reuniones de equipo y juntas',
                                    'Sala de eventos' => 'Espacio evento para conferencias y eventos',
                                    'Aula' => 'Espacio equipado para capacitaciones'
                                ];
                            @endphp

                            @foreach ($tipos as $valor => $descripcion)
                                <div class="radio-option">
                                    <input type="radio" id="tipo_{{ Str::slug($valor) }}" name="tipo_oficina" value="{{ $valor }}" {{ old('tipo_oficina') == $valor ? 'checked' : '' }} required>
                                    <div class="radio-content">
                                        <div class="title">{{ $valor }}</div>
                                        <div class="description">{{ $descripcion }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Detalles del espacio</h3>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" placeholder="Describe las características, equipamiento y servicios disponibles" required>{{ old('descripcion') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="aforo">Aforo Máximo *</label>
                        <div class="aforo-container">
                            <input type="number" id="aforo" name="capacidad" value="{{ old('capacidad', 1) }}" min="1" max="50" required>
                            <span>personas</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="precio">Precio por hora *</label>
                        <div class="aforo-container">
                            <input type="number" id="precio" name="Precio_hora" value="{{ old('Precio_hora') }}" placeholder="$" required>
                            <span>COP</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="foto">Imagen del espacio *</label>
                        <input type="file" id="foto" name="foto" accept="image/*" required>
                    </div>
                </div>
            </div>

            <div class="form-confirmation">
                <p><strong>¿Confirmó que todos los datos son correctos?</strong></p>
                <p>Pulse 'Crear nuevo espacio' para finalizar.</p>
            </div>

            <button type="submit" class="btn-crear">Crear nuevo espacio</button>
        </form>
    </div>
@endsection
