@extends('layouts.admin')

@section('title', 'Analítica de Usuarios')
@section('page-title', 'Analítica')

@section('styles')
<style>
    .kpi-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 1rem; margin-bottom: 2rem;
    }
    .kpi-mini {
        background: #fff; border-radius: 12px; padding: 1.1rem 1.25rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #f0f0f0;
        text-align: center;
    }
    .kpi-mini .val { font-size: 1.8rem; font-weight: 800; color: #111827; line-height: 1; }
    .kpi-mini .lbl { font-size: 0.75rem; color: #6b7280; margin-top: 4px; }

    .analytics-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
        margin-bottom: 1.25rem;
    }
    @media (max-width: 900px) { .analytics-grid { grid-template-columns: 1fr; } }

    .card {
        background: #fff; border-radius: 16px;
        padding: 1.4rem 1.6rem;
        box-shadow: 0 2px 16px rgba(0,0,0,0.06);
        border: 1px solid #f0f0f0;
    }
    .card h3 { margin: 0 0 1rem; font-size: 0.95rem; font-weight: 700; color: #111827; }

    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th {
        font-size: 0.7rem; font-weight: 600; color: #9ca3af;
        text-align: left; padding: 0.4rem 0.5rem;
        border-bottom: 1px solid #f3f4f6; text-transform: uppercase; letter-spacing: 0.4px;
    }
    .data-table td { padding: 0.55rem 0.5rem; font-size: 0.82rem; color: #374151; border-bottom: 1px solid #f9fafb; }
    .data-table tr:last-child td { border-bottom: none; }

    /* Barras de logins por día */
    .bar-chart { display: flex; flex-direction: column; gap: 6px; }
    .bar-row { display: flex; align-items: center; gap: 8px; }
    .bar-label { font-size: 0.72rem; color: #6b7280; min-width: 80px; }
    .bar-track { flex: 1; background: #f3f4f6; border-radius: 6px; height: 22px; overflow: hidden; }
    .bar-fill { height: 100%; border-radius: 6px; background: linear-gradient(90deg, #6366f1, #8b5cf6); display: flex; align-items: center; padding-left: 8px; transition: width .4s; }
    .bar-fill span { font-size: 0.7rem; font-weight: 700; color: #fff; }

    /* URL con truncado */
    .url-cell { max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 0.78rem; color: #4b5563; }

    .empty-state { text-align: center; padding: 2rem; color: #9ca3af; font-size: 0.85rem; }

    .full-card { grid-column: 1 / -1; }
</style>
@endsection

@section('content')

{{-- ===== KPIs GLOBALES ===== --}}
<div class="kpi-strip">
    <div class="kpi-mini">
        <div class="val">{{ number_format($totalLogins) }}</div>
        <div class="lbl">Total logins</div>
    </div>
    <div class="kpi-mini">
        <div class="val">{{ number_format($totalVisitas) }}</div>
        <div class="lbl">Páginas visitadas</div>
    </div>
    <div class="kpi-mini">
        <div class="val">{{ number_format($totalUsuariosActivos) }}</div>
        <div class="lbl">Usuarios activos</div>
    </div>
</div>

<div class="analytics-grid">

    {{-- ===== LOGINS POR DÍA (ÚLTIMOS 30 DÍAS) ===== --}}
    <div class="card">
        <h3>📈 Logins por día (últimos 30 días)</h3>
        @if($loginsPorDia->isEmpty())
            <div class="empty-state">Sin datos de logins aún.</div>
        @else
            @php $maxLogins = $loginsPorDia->max('total') ?: 1; @endphp
            <div class="bar-chart">
                @foreach($loginsPorDia->take(-15) as $item)
                    <div class="bar-row">
                        <span class="bar-label">{{ \Carbon\Carbon::parse($item->fecha)->format('d M') }}</span>
                        <div class="bar-track">
                            <div class="bar-fill" style="width: {{ ($item->total / $maxLogins) * 100 }}%">
                                <span>{{ $item->total }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ===== PÁGINAS MÁS VISITADAS ===== --}}
    <div class="card">
        <h3>🔥 Páginas más visitadas</h3>
        @if($paginasMasVisitadas->isEmpty())
            <div class="empty-state">Sin datos de visitas aún.</div>
        @else
            <table class="data-table">
                <thead>
                    <tr><th>URL</th><th style="text-align:right">Visitas</th></tr>
                </thead>
                <tbody>
                    @foreach($paginasMasVisitadas as $pag)
                        <tr>
                            <td class="url-cell" title="{{ $pag->url }}">{{ $pag->url }}</td>
                            <td style="text-align:right; font-weight:700; color:#6366f1;">{{ $pag->visitas }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- ===== USUARIOS MÁS ACTIVOS ===== --}}
    <div class="card">
        <h3>🏆 Usuarios más activos</h3>
        @if($usuariosMasActivos->isEmpty())
            <div class="empty-state">Sin datos de actividad aún.</div>
        @else
            <table class="data-table">
                <thead>
                    <tr><th>#</th><th>Usuario</th><th style="text-align:right">Acciones</th></tr>
                </thead>
                <tbody>
                    @foreach($usuariosMasActivos as $i => $u)
                        <tr>
                            <td style="color:#9ca3af;">{{ $i + 1 }}</td>
                            <td>
                                <div style="font-weight:600;">{{ $u->usuario->user_nombre ?? 'Usuario eliminado' }}</div>
                                <div style="font-size:0.72rem; color:#9ca3af;">{{ $u->usuario->user_correo ?? '' }}</div>
                            </td>
                            <td style="text-align:right; font-weight:700; color:#10b981;">{{ $u->acciones }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- ===== TIEMPO EN SESIÓN ===== --}}
    <div class="card">
        <h3>⏱️ Tiempo en sesión (últimos 7 días)</h3>
        @if($tiempoPromedioSesion->isEmpty())
            <div class="empty-state">Sin datos de sesión aún.</div>
        @else
            <table class="data-table">
                <thead>
                    <tr><th>Usuario</th><th style="text-align:right">Visitas</th><th style="text-align:right">Máx. sesión</th></tr>
                </thead>
                <tbody>
                    @foreach($tiempoPromedioSesion as $u)
                        @php
                            $mins = floor($u->duracion_max / 60);
                            $segs = $u->duracion_max % 60;
                        @endphp
                        <tr>
                            <td>{{ $u->usuario->user_nombre ?? 'N/D' }}</td>
                            <td style="text-align:right;">{{ $u->visitas }}</td>
                            <td style="text-align:right; font-weight:700; color:#f59e0b;">
                                {{ $mins > 0 ? $mins . 'm ' : '' }}{{ $segs }}s
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>

@endsection
