@extends('layouts.admin')

@section('title', 'Reporte de Ingresos')
@section('page-title', 'Reportes')

@section('content')

<div class="rep-header">
    <h2 class="card-title-inline"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg> Reporte de ingresos estimados</h2>
    <div class="header-actions">

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
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Exportar PDF (mes actual)
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
    <h3 class="card-title-inline"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> Ingresos por {{ strtolower($labelFecha) }}</h3>
    @if($ingresosPorPeriodo->isEmpty())
        <p class="empty-table-msg">Sin reservas productivas en el sistema.</p>
    @else
        @php $maxIng = $ingresosPorPeriodo->max('ingresos') ?: 1; @endphp
        <table class="rep-table">
            <thead>
                <tr>
                    <th>{{ $labelFecha }}</th>
                    <th>Reservas</th>
                    <th class="num-right">Ingresos</th>
                    <th class="th-graph">Gráfico</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ingresosPorPeriodo as $row)
                    <tr>
                        <td class="td-periodo">{{ $row->periodo }}</td>
                        <td>{{ $row->total_reservas }}</td>
                        <td class="num-right num-green">${{ number_format($row->ingresos, 0, ',', '.') }}</td>
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
        <h3 class="card-title-inline"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v2z"/><path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/></svg> Ticket promedio por usuario</h3>
        @if($ticketPorUsuario->isEmpty())
            <p class="empty-table-msg">Sin datos.</p>
        @else
            <table class="rep-table">
                <thead>
                    <tr><th>Usuario</th><th>Reservas</th><th class="num-right">Ticket prom.</th></tr>
                </thead>
                <tbody>
                    @foreach($ticketPorUsuario as $u)
                        <tr>
                            <td>
                                <div class="usuario-cell-name">{{ $u->user_nombre }}</div>
                                <div class="usuario-cell-email">{{ $u->user_correo }}</div>
                            </td>
                            <td>{{ $u->reservas }}</td>
                            <td class="num-right num-purple">${{ number_format($u->ticket_promedio, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- ===== RESERVAS NO EFECTIVAS ===== --}}
    <div class="card">
        <h3 class="card-title-inline"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svg-icon-danger"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> Últimas reservas no efectivas</h3>
        @if($noEfectivas->isEmpty())
            <p class="empty-table-msg">Sin cancelaciones ni rechazos.</p>
        @else
            <table class="rep-table">
                <thead>
                    <tr><th>Usuario</th><th>Espacio</th><th>Estado</th><th class="num-right">$ Perdido</th></tr>
                </thead>
                <tbody>
                    @foreach($noEfectivas as $r)
                        <tr>
                            <td>{{ $r->user_nombre }}</td>
                            <td>{{ Str::limit($r->esp_nombre, 18) }}</td>
                            <td>
                                <span class="badge-{{ strtolower($r->rsva_estado) }}">{{ $r->rsva_estado }}</span>
                            </td>
                            <td class="num-right num-red">${{ number_format($r->ingreso_perdido, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>

@endsection
