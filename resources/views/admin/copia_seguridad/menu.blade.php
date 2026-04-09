@extends('layouts.admin')

@section('title', 'Copias de seguridad')
@section('page-title', 'Copias de seguridad')

@section('content')
    <div class="backup-container">
        <div class="backup-header">
            <h2>Copias de seguridad</h2>
            <p>Haz y carga copias de seguridad de la información de Colabs</p>
        </div>

        <div class="backup-buttons">
            <button type="button" class="backup-btn" onclick="mostrarFormularioCrear()">Crear copia de seguridad</button>
            <button type="button" class="backup-btn" onclick="mostrarFormularioRestaurar()">Cargar copia de seguridad</button>
        </div>

        <!-- FORMULARIO OCULTO CREAR -->
        <div id="backup-form-crear" style="display:none; margin-top:20px;">
            <form action="{{ route('admin.backup.create') }}" method="POST">
                @csrf
                <p>¿Estás seguro de que deseas crear y descargar una copia de seguridad?</p>
                <button type="submit" class="backup-btn">Sí, crear copia</button>
                <button type="button" class="backup-btn" style="background:#4b5563;" onclick="ocultarFormularios()">Cancelar</button>
            </form>
        </div>

        <!-- FORMULARIO OCULTO RESTAURAR -->
        <div id="backup-form-restaurar" style="display:none; margin-top:20px;">
            <form action="{{ route('admin.backup.restore') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('ATENCIÓN: Cargar una copia de seguridad sobrescribirá toda la base de datos actual. ¿Deseas continuar?')">
                @csrf
                <p>Selecciona un archivo .sql o .txt para restaurar tu base de datos:</p>
                <div style="margin-bottom: 15px;">
                    <input type="file" name="backup" accept=".sql,.txt" required style="padding: 10px; border: 1px solid #ccc; border-radius: 8px;">
                </div>
                <button type="submit" class="backup-btn" style="background:#ef4444;">Sí, restaurar BD</button>
                <button type="button" class="backup-btn" style="background:#4b5563;" onclick="ocultarFormularios()">Cancelar</button>
            </form>
        </div>

        <!-- MENSAJES DE ESTADO -->
        <div class="backup-messages" style="margin-top: 30px;">
            @if(session('success'))
                <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 12px; border: 1px solid #bbf7d0; display: flex; align-items: center; gap: 10px; animation: fadeInUp 0.3s ease; margin-bottom: 15px;">
                    <span style="font-size: 1.2rem;">✅</span>
                    <span style="font-weight: 500;">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div style="background: #fef2f2; color: #991b1b; padding: 15px; border-radius: 12px; border: 1px solid #fecaca; display: flex; align-items: center; gap: 10px; animation: fadeInUp 0.3s ease; margin-bottom: 15px;">
                    <span style="font-size: 1.2rem;">❌</span>
                    <span style="font-weight: 500;">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div style="background: #fef2f2; color: #991b1b; padding: 15px; border-radius: 12px; border: 1px solid #fecaca; animation: fadeInUp 0.3s ease;">
                    <span style="font-weight: 500;">❌ Errores:</span>
                    <ul style="margin-top: 5px; margin-bottom: 0;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <script>
            function mostrarFormularioCrear() {
                ocultarFormularios();
                document.getElementById('backup-form-crear').style.display = 'block';
            }

            function mostrarFormularioRestaurar() {
                ocultarFormularios();
                document.getElementById('backup-form-restaurar').style.display = 'block';
            }

            function ocultarFormularios() {
                document.getElementById('backup-form-crear').style.display = 'none';
                document.getElementById('backup-form-restaurar').style.display = 'none';
            }
        </script>
    </div>
@endsection