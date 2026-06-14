@extends('layouts.admin')

@section('title', 'Historial de Backups')
@section('page-title', 'Copias de seguridad')

@section('styles')
<style>
    .page-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;
    }
    .page-header h2 { margin: 0; font-size: 1.1rem; font-weight: 700; color: #111827; }

    .btn-back {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600;
        background: #f3f4f6; color: #374151; text-decoration: none; transition: background .2s;
    }
    .btn-back:hover { background: #e5e7eb; }

    .card { background: #fff; border-radius: 16px; padding: 1.5rem 1.75rem; box-shadow: 0 2px 16px rgba(0,0,0,0.06); border: 1px solid #f0f0f0; }

    .logs-table { width: 100%; border-collapse: collapse; }
    .logs-table th {
        font-size: 0.72rem; font-weight: 600; color: #6b7280; text-align: left;
        padding: 0.5rem 0.75rem; border-bottom: 2px solid #f3f4f6;
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .logs-table td { padding: 0.7rem 0.75rem; font-size: 0.85rem; color: #374151; border-bottom: 1px solid #f9fafb; }
    .logs-table tr:hover td { background: #fafafa; }

    .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 999px; font-size: 0.72rem; font-weight: 700; }
    .badge-success { background: #dcfce7; color: #15803d; }
    .badge-failed  { background: #fee2e2; color: #b91c1c; }

    .filename-col { font-family: 'Courier New', monospace; font-size: 0.8rem; color: #4b5563; }
    .error-text { font-size: 0.75rem; color: #b91c1c; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; cursor: help; }
    .empty-state { text-align: center; padding: 3rem; color: #9ca3af; }
    .empty-state .icon { font-size: 3rem; margin-bottom: 0.75rem; }
</style>
@endsection

@section('content')

<div class="page-header">
    <h2>📋 Historial de backups automáticos</h2>
    <a href="{{ route('admin.copias') }}" class="btn-back">← Volver</a>
</div>

<div class="card">
    <p style="margin: 0 0 1.25rem; color: #6b7280; font-size: 0.875rem;">
        El backup automático se ejecuta diariamente a las <strong>2:00 AM</strong> vía Laravel Scheduler.
        Aquí se registra el resultado de cada ejecución.
    </p>

    @if($logs->isEmpty())
        <div class="empty-state">
            <div class="icon">🗃️</div>
            <p>Aún no se ha ejecutado ningún backup automático.<br>
            Puedes ejecutar uno manualmente con <code>php artisan backup:database</code>.</p>
        </div>
    @else
        <table class="logs-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Estado</th>
                    <th>Archivo</th>
                    <th>Tamaño</th>
                    <th>Fecha y hora</th>
                    <th>Detalle error</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td style="color:#9ca3af;">{{ $log->id }}</td>
                        <td>
                            <span class="badge badge-{{ $log->status }}">
                                @if($log->status === 'success') ✅ Exitoso @else ❌ Fallido @endif
                            </span>
                        </td>
                        <td class="filename-col">{{ $log->filename }}</td>
                        <td>{{ $log->file_size_formatted }}</td>
                        <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        <td>
                            @if($log->error_message)
                                <span class="error-text" title="{{ $log->error_message }}">
                                    {{ $log->error_message }}
                                </span>
                            @else
                                <span style="color:#9ca3af; font-size:0.8rem;">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 1.25rem;">
            {{ $logs->links() }}
        </div>
    @endif
</div>

@endsection
