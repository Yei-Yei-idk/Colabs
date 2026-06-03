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
        <div id="backup-form-crear" class="backup-form-section">
            <form action="{{ route('admin.backup.create') }}" method="POST" onsubmit="procesarBoton(this.querySelector('.btn-submit-backup'))">
                @csrf
                <p>¿Estás seguro de que deseas crear y descargar una copia de seguridad?</p>
                <div style="margin-top: 15px; display: flex; gap: 10px;">
                    <button type="submit" class="backup-btn btn-submit-backup">Sí, crear copia</button>
                    <button type="button" class="backup-btn" style="background:#4b5563;" onclick="ocultarFormularios()">Cancelar</button>
                </div>
            </form>
        </div>

        <!-- FORMULARIO OCULTO RESTAURAR -->
        <div id="backup-form-restaurar" class="backup-form-section">
            <form action="{{ route('admin.backup.restore') }}" method="POST" enctype="multipart/form-data" 
                  onsubmit="if(confirm('ATENCIÓN: Cargar una copia de seguridad sobrescribirá toda la base de datos actual. ¿Deseas continuar?')) { procesarBoton(this.querySelector('.btn-restore-backup')); return true; } return false;">
                @csrf
                <p style="margin-bottom: 15px;">Selecciona un archivo .sql o .txt para restaurar tu base de datos:</p>
                <div style="margin-bottom: 20px;">
                    <input type="file" name="backup" accept=".sql,.txt" required style="padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; width: 100%; background: #fff;">
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="backup-btn btn-restore-backup" style="background:#ef4444;">Sí, restaurar BD</button>
                    <button type="button" class="backup-btn" style="background:#4b5563;" onclick="ocultarFormularios()">Cancelar</button>
                </div>
            </form>
        </div>

        <!-- MENSAJES DE ESTADO -->
        <div class="backup-messages" style="margin-top: 30px;">
            @if(session('success'))
                <div class="backup-alert alert-success">
                    <span>✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="backup-alert alert-error">
                    <span>❌</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="backup-alert alert-error" style="flex-direction: column; align-items: flex-start;">
                    <span style="font-weight: 700;">❌ Errores detectados:</span>
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

            function procesarBoton(btn) {
                if (!btn) return;
                const textoOriginal = btn.innerHTML;
                
                // Pequeño delay para asegurar que el submit del formulario se dispare antes de deshabilitar
                setTimeout(() => {
                    btn.disabled = true;
                    btn.innerHTML = '⌛ Procesando...';
                    btn.style.opacity = '0.7';
                    btn.style.cursor = 'not-allowed';
                }, 10);

                // Restaurar después de 6 segundos (tiempo prudencial para descargas)
                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = textoOriginal;
                    btn.style.opacity = '1';
                    btn.style.cursor = 'pointer';
                }, 6000);
            }
        </script>
    </div>
@endsection