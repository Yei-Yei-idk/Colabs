@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('styles')
<style>
    /* ========== KPI CARDS ========== */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .kpi-card {
        background: #fff;
        border-radius: 16px;
        padding: 1.5rem 1.75rem;
        box-shadow: 0 2px 16px rgba(0,0,0,0.06);
        border: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 1.1rem;
        transition: transform .2s, box-shadow .2s;
        position: relative;
        overflow: hidden;
    }
    .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,0.10); }
    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        border-radius: 16px 16px 0 0;
    }
    .kpi-card.blue::before   { background: linear-gradient(90deg, #3b82f6, #6366f1); }
    .kpi-card.green::before  { background: linear-gradient(90deg, #10b981, #34d399); }
    .kpi-card.purple::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
    .kpi-card.orange::before { background: linear-gradient(90deg, #f59e0b, #fb923c); }

    .kpi-icon {
        width: 52px; height: 52px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; flex-shrink: 0;
    }
    .kpi-card.blue   .kpi-icon { background: #eff6ff; }
    .kpi-card.green  .kpi-icon { background: #ecfdf5; }
    .kpi-card.purple .kpi-icon { background: #f5f3ff; }
    .kpi-card.orange .kpi-icon { background: #fffbeb; }

    .kpi-info { flex: 1; }
    .kpi-value {
        font-size: 1.9rem; font-weight: 800; line-height: 1.1;
        color: #111827; letter-spacing: -1px;
    }
    .kpi-label { font-size: 0.8rem; color: #6b7280; margin-top: 2px; font-weight: 500; }

    /* ========== GANTT ========== */
    .gantt-wrapper {
        background: #fff;
        border-radius: 16px;
        padding: 1.5rem 1.75rem;
        box-shadow: 0 2px 16px rgba(0,0,0,0.06);
        border: 1px solid #f0f0f0;
        margin-bottom: 2rem;
        overflow-x: auto;
    }
    .gantt-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 1.25rem;
    }
    .gantt-header h3 { margin: 0; font-size: 1rem; font-weight: 700; color: #111827; }
    .gantt-header small { color: #6b7280; font-size: 0.8rem; }

    .gantt-table { width: 100%; border-collapse: collapse; min-width: 700px; }
    .gantt-table th {
        font-size: 0.7rem; font-weight: 600; color: #9ca3af;
        text-align: center; padding: 0.35rem 0.2rem;
        border-bottom: 1px solid #f3f4f6;
        background: #fafafa;
    }
    .gantt-table td {
        padding: 0.5rem 0.4rem;
        font-size: 0.78rem; color: #374151;
        border-bottom: 1px solid #f9fafb;
        white-space: nowrap;
    }
    .gantt-table td:first-child { font-weight: 600; max-width: 140px; overflow: hidden; text-overflow: ellipsis; padding-left: 0; }

    .gantt-cell { position: relative; height: 32px; }
    .gantt-bar {
        position: absolute; top: 4px; bottom: 4px;
        border-radius: 6px;
        display: flex; align-items: center;
        padding: 0 8px;
        font-size: 0.68rem; font-weight: 600; color: #fff;
        overflow: hidden; white-space: nowrap; text-overflow: ellipsis;
        min-width: 24px; cursor: default;
        box-shadow: 0 2px 6px rgba(0,0,0,0.12);
        transition: filter .2s;
    }
    .gantt-bar:hover { filter: brightness(1.1); }
    .gantt-bar.aceptada { background: linear-gradient(90deg, #10b981, #34d399); }
    .gantt-bar.pendiente { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .gantt-bar.activa    { background: linear-gradient(90deg, #3b82f6, #60a5fa); }

    .gantt-empty {
        text-align: center; padding: 2rem; color: #9ca3af; font-size: 0.875rem;
    }

    /* ========== TABLA ÚLTIMAS RESERVAS ========== */
    .section-card {
        background: #fff; border-radius: 16px;
        padding: 1.5rem 1.75rem;
        box-shadow: 0 2px 16px rgba(0,0,0,0.06);
        border: 1px solid #f0f0f0;
        margin-bottom: 2rem;
    }
    .section-card h3 { margin: 0 0 1.25rem; font-size: 1rem; font-weight: 700; color: #111827; }

    .reservas-table { width: 100%; border-collapse: collapse; }
    .reservas-table th {
        font-size: 0.73rem; font-weight: 600; color: #6b7280;
        text-align: left; padding: 0.5rem 0.75rem;
        border-bottom: 2px solid #f3f4f6; text-transform: uppercase; letter-spacing: 0.5px;
    }
    .reservas-table td { padding: 0.65rem 0.75rem; font-size: 0.85rem; border-bottom: 1px solid #f9fafb; color: #374151; }
    .reservas-table tr:hover td { background: #fafafa; }

    .badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 10px; border-radius: 999px; font-size: 0.72rem; font-weight: 700;
    }
    .badge-aceptada  { background: #dcfce7; color: #15803d; }
    .badge-pendiente { background: #fef3c7; color: #92400e; }
    .badge-finalizada{ background: #e0e7ff; color: #3730a3; }
    .badge-cancelada { background: #fee2e2; color: #b91c1c; }
    .badge-rechazada { background: #fee2e2; color: #b91c1c; }
    .badge-default   { background: #f3f4f6; color: #6b7280; }
</style>
@endsection

@section('content')

    {{-- ===== KPI CARDS ===== --}}
    <div class="kpi-grid">
        <div class="kpi-card blue">
            <div class="kpi-icon">📅</div>
            <div class="kpi-info">
                <div class="kpi-value">{{ $reservasActivasHoy }}</div>
                <div class="kpi-label">Reservas activas hoy</div>
            </div>
        </div>
        <div class="kpi-card green">
            <div class="kpi-icon">💰</div>
            <div class="kpi-info">
                <div class="kpi-value">${{ number_format($ingresosDelMes, 0, ',', '.') }}</div>
                <div class="kpi-label">Ingresos estimados del mes</div>
            </div>
        </div>
        <div class="kpi-card purple">
            <div class="kpi-icon">👥</div>
            <div class="kpi-info">
                <div class="kpi-value">{{ $usuariosRegistrados }}</div>
                <div class="kpi-label">Usuarios registrados</div>
            </div>
        </div>
        <div class="kpi-card orange">
            <div class="kpi-icon">⏳</div>
            <div class="kpi-info">
                <div class="kpi-value">{{ $solicitudesPendientes }}</div>
                <div class="kpi-label">Solicitudes pendientes</div>
            </div>
        </div>
    </div>

    {{-- ===== GANTT DEL DÍA ===== --}}
    <div class="gantt-wrapper">
        <div class="gantt-header">
            <h3>📊 Timeline de reservas — Hoy ({{ now()->translatedFormat('d \d\e F Y') }})</h3>
            <small>{{ $reservasDelDia->count() }} reserva(s) programada(s)</small>
        </div>

        @if($reservasDelDia->isEmpty())
            <div class="gantt-empty">
                🗓️ No hay reservas programadas para hoy.
            </div>
        @else
            @php
                $horaInicio = 7;
                $horaFin    = 22;
                $totalCols  = $horaFin - $horaInicio;
                // Agrupar por espacio
                $porEspacio = $reservasDelDia->groupBy('espacio_id');
            @endphp
            <table class="gantt-table">
                <thead>
                    <tr>
                        <th style="text-align:left;width:130px;">Espacio</th>
                        @for($h = $horaInicio; $h <= $horaFin; $h++)
                            <th>{{ sprintf('%02d:00', $h) }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach($porEspacio as $espacioId => $rsvs)
                        @php $espacio = $rsvs->first()->espacio; @endphp
                        <tr>
                            <td title="{{ $espacio->esp_nombre ?? 'N/D' }}">
                                {{ Str::limit($espacio->esp_nombre ?? 'N/D', 18) }}
                            </td>
                            <td colspan="{{ $totalCols + 1 }}" style="padding:0;position:relative;">
                                <div class="gantt-cell" style="width:100%;">
                                    @foreach($rsvs as $r)
                                        @php
                                            $start = (int) explode(':', $r->rsva_hora_inicio)[0] + (int) explode(':', $r->rsva_hora_inicio)[1] / 60;
                                            $end   = (int) explode(':', $r->rsva_hora_fin)[0] + (int) explode(':', $r->rsva_hora_fin)[1] / 60;
                                            $left  = max(0, ($start - $horaInicio) / $totalCols * 100);
                                            $width = max(1, ($end - $start) / $totalCols * 100);
                                            $clase = strtolower($r->rsva_estado);
                                        @endphp
                                        <div class="gantt-bar {{ $clase }}"
                                             style="left:{{ $left }}%;width:{{ $width }}%;"
                                             title="{{ $r->usuario->user_nombre ?? '' }} | {{ $r->rsva_hora_inicio }} – {{ $r->rsva_hora_fin }} | {{ $r->rsva_estado }}">
                                            {{ $r->usuario->first_name ?? '' }}
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- ===== CONFIGURACIÓN DEL SITIO ===== --}}
    <div class="section-card">
        <h3>⚙️ Configuración del sitio</h3>
        <p style="margin: -0.5rem 0 1.25rem; color: #6b7280; font-size: 0.875rem;">Controla qué secciones son visibles para los clientes en el menú de navegación.</p>

        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; padding: 1rem 1.25rem; border-radius: 10px; border: 1.5px solid {{ $promocionesVisible ? '#d1fae5' : '#fee2e2' }}; background: {{ $promocionesVisible ? '#f0fdf4' : '#fff5f5' }}; transition: all .3s;">
            <div style="display: flex; align-items: center; gap: 0.85rem;">
                <div style="font-size: 1.6rem;">🎁</div>
                <div>
                    <div style="font-weight: 700; font-size: 0.95rem; color: #1a1a2e;">Sección de Promociones</div>
                    <div style="font-size: 0.8rem; color: #6b7280; margin-top: 2px;">Enlace "Promociones" en el menú de navegación del cliente</div>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.3rem 0.75rem; border-radius: 999px; font-size: 0.8rem; font-weight: 700;
                    background: {{ $promocionesVisible ? '#dcfce7' : '#fee2e2' }};
                    color: {{ $promocionesVisible ? '#15803d' : '#b91c1c' }};">
                    <span style="width: 7px; height: 7px; border-radius: 50%; background: {{ $promocionesVisible ? '#22c55e' : '#ef4444' }}; display: inline-block;"></span>
                    {{ $promocionesVisible ? 'ACTIVO' : 'DESACTIVADO' }}
                </span>
                <form action="{{ route('admin.toggle_promociones') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit"
                        style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1.1rem;
                               border-radius: 8px; border: none; cursor: pointer; font-size: 0.88rem; font-weight: 700;
                               transition: all .2s;
                               background: {{ $promocionesVisible ? '#ef4444' : '#22c55e' }};
                               color: #fff;"
                        onmouseover="this.style.opacity='.85'"
                        onmouseout="this.style.opacity='1'">
                        @if($promocionesVisible) 🚫 Deshabilitar @else ✅ Habilitar @endif
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== ÚLTIMAS RESERVAS ===== --}}
    <div class="section-card">
        <h3>🕐 Últimas reservas</h3>
        <table class="reservas-table">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Espacio</th>
                    <th>Fecha</th>
                    <th>Hora inicio</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ultimasReservas as $reserva)
                    <tr>
                        <td>{{ $reserva->usuario->user_nombre ?? 'N/D' }}</td>
                        <td>{{ $reserva->espacio->esp_nombre ?? 'N/D' }}</td>
                        <td>{{ \Carbon\Carbon::parse($reserva->rsva_fecha)->format('d/m/Y') }}</td>
                        <td>{{ $reserva->rsva_hora_inicio }}</td>
                        <td>
                            @php $estado = strtolower($reserva->rsva_estado); @endphp
                            <span class="badge badge-{{ $estado }}">
                                {{ $reserva->rsva_estado }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:1.5rem;color:#9ca3af;">No hay reservas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
