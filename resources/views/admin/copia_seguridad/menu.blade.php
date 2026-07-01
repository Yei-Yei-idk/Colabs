@extends('layouts.admin')

@section('title', 'Copias de seguridad')
@section('page-title', 'Copias de seguridad')

@section('content')
    <div class="backup-container">
        <div class="backup-header">
            <h2>Copias de seguridad</h2>
            <p>Guarda la informacion, Restaura la informacion, descarga la informacion</p>
        </div>

        <div class="backup-buttons">
            <button type="button" class="btn-nueva-copia" onclick="mostrarFormularioCrear()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/></svg>
                Nueva Copia
            </button>
        </div>

        <!-- FORMULARIO OCULTO CREAR -->
        <div id="backup-form-crear" class="backup-form-section">
            <form action="{{ route('admin.backup.create') }}" method="POST" onsubmit="procesarBoton(this.querySelector('.btn-submit-backup'))">
                @csrf
                <div class="backup-form-inner">
                    <div class="backup-form-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M4.406 3.342A5.53 5.53 0 0 1 8 2c2.69 0 4.923 2 5.166 4.579C14.758 6.804 16 8.137 16 9.773 16 11.569 14.502 13 12.687 13H3.781C1.708 13 0 11.366 0 9.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383z"/></svg>
                    </div>
                    <div class="backup-form-text">
                        <h4>Confirmar nueva copia</h4>
                        <p>¿Estás seguro de que deseas crear una copia de seguridad y guardarla en el servidor?</p>
                    </div>
                </div>
                <div class="backup-form-actions">
                    <button type="submit" class="btn-submit-backup">Confirmar y Crear</button>
                    <button type="button" class="btn-cancel-backup" onclick="ocultarFormularios()">Cancelar</button>
                </div>
            </form>
        </div>



        <!-- MENSAJES DE ESTADO -->
        <div class="backup-messages">
            @if(session('success'))
                <div class="backup-alert alert-success">
                    <span><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="backup-alert alert-error">
                    <span><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="backup-alert alert-error backup-alert-column">
                    <span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svg-icon-danger"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> Errores detectados:</span>
                    <ul class="backup-error-list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- TABLA DE HISTORIAL DE BACKUPS -->
        <div class="section-card backup-history-card">
            <h3 class="backup-history-title"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg> Historial de Copias de Seguridad</h3>
            @if($logs->count() > 0)
                <table class="reservas-table">
                    <thead>
                        <tr>
                            <th>Nombre de la copia</th>
                            <th>Tamaño</th>
                            <th>Creado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td>
                                    <div class="backup-file-cell">
                                        <div class="backup-file-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M4.406 3.342A5.53 5.53 0 0 1 8 2c2.69 0 4.923 2 5.166 4.579C14.758 6.804 16 8.137 16 9.773 16 11.569 14.502 13 12.687 13H3.781C1.708 13 0 11.366 0 9.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383zm.653.757c-.757.653-1.153 1.44-1.153 2.056v.448l-.445.049C2.064 6.805 1 7.952 1 9.318 1 10.785 2.23 12 3.781 12h8.906C13.98 12 15 10.988 15 9.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 4.825 10.328 3 8 3a4.53 4.53 0 0 0-2.941 1.1z"/></svg>
                                        </div>
                                        <span class="backup-file-name">{{ $log->filename }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="backup-meta-cell">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M4.5 9a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1zM3 10.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zm-2-4a.5.5 0 1 1 1 0 .5.5 0 0 1-1 0zm9-4a.5.5 0 1 1 1 0 .5.5 0 0 1-1 0zM13 3a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1zm2 1.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zM13 11a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm12 1a1 1 0 0 1 1 1v2H1V2a1 1 0 0 1 1-1h12zm1 4v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V5h14z"/></svg>
                                        {{ $log->file_size_formatted }}
                                    </div>
                                </td>
                                <td>
                                    <div class="backup-time-cell">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/></svg>
                                        {{ $log->created_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td>
                                    <div class="backup-actions-cell">
                                        <a href="{{ route('admin.backup.download', $log->id) }}" title="Descargar" class="backup-action-btn download">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/></svg>
                                        </a>
                                        
                                        <form action="{{ route('admin.backup.restoreHistory', $log->id) }}" method="POST" onsubmit="return confirm('ATENCIÓN: Restaurar esta copia sobrescribirá la base de datos actual. ¿Deseas continuar?');" class="form-inline">
                                            @csrf
                                            <button type="submit" title="Restaurar" class="backup-action-btn restore">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"/><path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z"/></svg>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.backup.delete', $log->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta copia de seguridad?');" class="form-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Eliminar" class="backup-action-btn delete">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="backup-pagination">
                    {{ $logs->links() }}
                </div>
            @else
                <p class="backup-empty">No hay copias de seguridad registradas.</p>
            @endif
        </div>

        <script>
            function mostrarFormularioCrear() {
                ocultarFormularios();
                document.getElementById('backup-form-crear').style.display = 'block';
            }

            function ocultarFormularios() {
                document.getElementById('backup-form-crear').style.display = 'none';
            }

            function procesarBoton(btn) {
                if (!btn) return;
                const textoOriginal = btn.innerHTML;
                
                // Pequeño delay para asegurar que el submit del formulario se dispare antes de deshabilitar
                setTimeout(() => {
                    btn.disabled = true;
                    btn.innerHTML = 'Procesando...';
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