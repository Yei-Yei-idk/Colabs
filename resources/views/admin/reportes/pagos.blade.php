@extends('layouts.admin')

@section('title', 'Reporte de Ingresos')
@section('page-title', 'Reportes')

@section('styles')
<style>
    .rep-header {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;
    }
    .rep-header h2 { margin: 0; font-size: 1.05rem; font-weight: 700; color: #111827; }

    .periodo-form {
        display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;
    }
    .periodo-form label { font-size: 0.82rem; color: #6b7280; }
    .periodo-form select {
        padding: 0.4rem 0.75rem; border-radius: 8px; border: 1px solid #e5e7eb;
        font-size: 0.85rem; color: #374151; background: #fff; cursor: pointer;
    }
    .btn-pdf {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600;
        background: #ef4444; color: #fff; text-decoration: none; transition: background .2s;
    }
    .btn-pdf:hover { background: #b91c1c; }

    /* KPIs resumen */
    .summary-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem; margin-bottom: 1.75rem;
    }
    .sum-card {
        background: #fff; border-radius: 14px; padding: 1.2rem 1.4rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #f0f0f0;
        border-left: 4px solid transparent;
    }
    .sum-card.green  { border-left-color: #10b981; }
    .sum-card.red    { border-left-color: #ef4444; }
    .sum-card.blue   { border-left-color: #3b82f6; }
    .sum-card.orange { border-left-color: #f59e0b; }

    .sum-val { font-size: 1.5rem; font-weight: 800; color: #111827; line-height: 1.1; }
    .sum-lbl { font-size: 0.76rem; color: #6b7280; margin-top: 3px; }

    .card {
        background: #fff; border-radius: 16px; padding: 1.4rem 1.6rem;
        box-shadow: 0 2px 16px rgba(0,0,0,0.06); border: 1px solid #f0f0f0;
        margin-bottom: 1.5rem;
    }
    .card h3 { margin: 0 0 1rem; font-size: 0.95rem; font-weight: 700; color: #111827; }

    .rep-table { width: 100%; border-collapse: collapse; }
    .rep-table th {
        font-size: 0.71rem; font-weight: 600; color: #9ca3af;
        text-align: left; padding: 0.45rem 0.6rem;
        border-bottom: 1px solid #f3f4f6; text-transform: uppercase;
    }
    .rep-table td { padding: 0.6rem 0.6rem; font-size: 0.83rem; color: #374151; border-bottom: 1px solid #f9fafb; }
    .rep-table tr:last-child td { border-bottom: none; }
    .rep-table tr:hover td { background: #fafafa; }
    .num-right { text-align: right; font-weight: 700; }

    /* Barra de ingresos */
    .ing-bar { height: 22px; background: #f0fdf4; border-radius: 6px; overflow: hidden; min-width: 80px; }
    .ing-fill { height: 100%; background: linear-gradient(90deg, #10b981, #34d399); border-radius: 6px; }

    .badge-cancelada { background: #fee2e2; color: #b91c1c; padding: 2px 8px; border-radius: 999px; font-size: 0.72rem; font-weight: 700; }
    .badge-rechazada { background: #fee2e2; color: #b91c1c; padding: 2px 8px; border-radius: 999px; font-size: 0.72rem; font-weight: 700; }

    .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
    @media(max-width:900px){ .two-col { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')

<div class="rep-header">
    <h2>💰 Reporte de ingresos estimados</h2>
    <div style="display:flex; gap:0.75rem; align-items:center; flex-wrap:wrap;">

        {{-- Selector de período --}}
        <form method="GET" action="{{ route('admin.reportes.pagos') }}" class="periodo-form">
            <label for="periodo">Agrupar por:</label>
            <select name="periodo" id="periodo" onchange="this.form.submit()">
                <option value="dia"    {{ $periodo === 'dia'    ? 'selected' : '' }}>Día</option>
                <option value="semana" {{ $periodo === 'semana' ? 'selected' : '' }}>Semana</option>
                <option value="mes"   {{ $periodo === 'mes'    ? 'selected' : '' }}>Mes</option>
            </select>
        </form>

        {{-- Exportar PDF --}}
        <a href="{{ route('admin.reportes.pagos.pdf', ['mes' => now()->format('Y-m')]) }}" class="btn-pdf">
            📄 Exportar PDF (mes actual)
        </a>
    </div>
</div>

{{-- ===== RESUMEN GLOBAL ===== --}}
<div class="summary-grid">
    <div class="sum-card green">
        <div class="sum-val">${{ number_format($resumen->ingresos_totales ?? 0, 0, ',', '.') }}</div>
        <div class="sum-lbl">Ingresos totales estimados</div>
    </div>
    <div class="sum-card red">
        <div class="sum-val">${{ number_format($resumen->ingresos_perdidos ?? 0, 0, ',', '.') }}</div>
        <div class="sum-lbl">Ingresos perdidos (canceladas/rechazadas)</div>
    </div>
    <div class="sum-card blue">
        <div class="sum-val">{{ $resumen->total_reservas ?? 0 }}</div>
        <div class="sum-lbl">Total de reservas</div>
    </div>
    <div class="sum-card orange">
        <div class="sum-val">{{ $resumen->reservas_no_efectivas ?? 0 }}</div>
        <div class="sum-lbl">Reservas no efectivas</div>
    </div>
</div>

{{-- ===== INGRESOS POR PERÍODO ===== --}}
<div class="card">
    <h3>📅 Ingresos por {{ strtolower($labelFecha) }}</h3>
    @if($ingresosPorPeriodo->isEmpty())
        <p style="color:#9ca3af; text-align:center; padding:1rem;">Sin reservas productivas en el sistema.</p>
    @else
        @php $maxIng = $ingresosPorPeriodo->max('ingresos') ?: 1; @endphp
        <table class="rep-table">
            <thead>
                <tr>
                    <th>{{ $labelFecha }}</th>
                    <th>Reservas</th>
                    <th class="num-right">Ingresos</th>
                    <th style="min-width:120px;">Gráfico</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ingresosPorPeriodo as $row)
                    <tr>
                        <td style="font-weight:600;">{{ $row->periodo }}</td>
                        <td>{{ $row->total_reservas }}</td>
                        <td class="num-right" style="color:#10b981;">${{ number_format($row->ingresos, 0, ',', '.') }}</td>
                        <td>
                            <div class="ing-bar">
                                <div class="ing-fill" style="width:{{ ($row->ingresos / $maxIng) * 100 }}%"></div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<div class="two-col">

    {{-- ===== TICKET PROMEDIO POR USUARIO ===== --}}
    <div class="card">
        <h3>🎫 Ticket promedio por usuario</h3>
        @if($ticketPorUsuario->isEmpty())
            <p style="color:#9ca3af; text-align:center; padding:1rem;">Sin datos.</p>
        @else
            <table class="rep-table">
                <thead>
                    <tr><th>Usuario</th><th>Reservas</th><th class="num-right">Ticket prom.</th></tr>
                </thead>
                <tbody>
                    @foreach($ticketPorUsuario as $u)
                        <tr>
                            <td>
                                <div style="font-weight:600; font-size:0.82rem;">{{ $u->user_nombre }}</div>
                                <div style="font-size:0.7rem; color:#9ca3af;">{{ $u->user_correo }}</div>
                            </td>
                            <td>{{ $u->reservas }}</td>
                            <td class="num-right" style="color:#6366f1;">${{ number_format($u->ticket_promedio, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- ===== RESERVAS NO EFECTIVAS ===== --}}
    <div class="card">
        <h3>❌ Últimas reservas no efectivas</h3>
        @if($noEfectivas->isEmpty())
            <p style="color:#9ca3af; text-align:center; padding:1rem;">Sin cancelaciones ni rechazos.</p>
        @else
            <table class="rep-table">
                <thead>
                    <tr><th>Usuario</th><th>Espacio</th><th>Estado</th><th class="num-right">$ Perdido</th></tr>
                </thead>
                <tbody>
                    @foreach($noEfectivas as $r)
                        <tr>
                            <td style="font-size:0.8rem;">{{ $r->user_nombre }}</td>
                            <td style="font-size:0.8rem;">{{ Str::limit($r->esp_nombre, 18) }}</td>
                            <td>
                                <span class="badge-{{ strtolower($r->rsva_estado) }}">{{ $r->rsva_estado }}</span>
                            </td>
                            <td class="num-right" style="color:#ef4444; font-size:0.8rem;">${{ number_format($r->ingreso_perdido, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>

@endsection
