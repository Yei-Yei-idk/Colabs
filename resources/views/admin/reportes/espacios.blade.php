@extends('layouts.admin')

@section('title', 'Reporte de Espacios')
@section('page-title', 'Reportes')

@section('content')

{{-- ===== TASA DE OCUPACIÓN ===== --}}
<div class="card">
    <h3 class="section-title"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg> Tasa de ocupación por espacio</h3>
    <table class="rep-table">
        <thead>
            <tr>
                <th>Espacio</th>
                <th>Tipo</th>
                <th>Total reservas</th>
                <th>Productivas</th>
                <th>Ocupación</th>
                <th>% Gráfico</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ocupacion as $row)
                @php
                    $pct = $row->tasa_ocupacion ?? 0;
                    $color = $pct >= 70 ? '#10b981' : ($pct >= 40 ? '#f59e0b' : '#ef4444');
                @endphp
                <tr>
                    <td class="td-espacio">{{ $row->esp_nombre }}</td>
                    <td><span class="tipo-badge">{{ $row->esp_tipo }}</span></td>
                    <td>{{ $row->total_reservas }}</td>
                    <td>{{ $row->reservas_productivas }}</td>
                    <td class="td-bold" style="color: {{ $color }}">{{ $pct }}%</td>
                    <td class="td-occ-graph">
                        <div class="occ-bar">
                            <div class="occ-fill" style="width:{{ $pct }}%; background:{{ $color }};"></div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="td-center">Sin datos de reservas.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="report-grid">

    {{-- ===== HORAS PICO ===== --}}
    <div class="card">
        <h3 class="section-title"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Horas pico de reserva</h3>
        @if($horasPico->isEmpty())
            <p class="empty-table-msg">Sin datos.</p>
        @else
            <div class="flex-between">
                @foreach($horasPico as $h)
                    <div class="flex-row-between">
                        <span class="peak-time">
                            {{ sprintf('%02d:00', $h->hora) }}
                        </span>
                        <div class="peak-bar peak-bar-flex">
                            <div class="peak-fill" style="width:{{ ($h->total / $maxHoraPico) * 100 }}%">
                                <span>{{ $h->total }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ===== ESPACIOS SUBUTILIZADOS ===== --}}
    <div class="card">
        <h3 class="section-title"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svg-icon-warning"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> Espacios subutilizados <small class="small-subdued">(último mes, &lt; {{ $umbralSubutilizados }} reservas)</small></h3>
        @if($subutilizados->isEmpty())
            <p class="report-success">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svg-icon-middle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> Todos los espacios tienen actividad suficiente.
            </p>
        @else
            <table class="rep-table">
                <thead>
                    <tr><th>Espacio</th><th>Tipo</th><th>Reservas (mes)</th></tr>
                </thead>
                <tbody>
                    @foreach($subutilizados as $esp)
                        <tr>
                            <td class="td-espacio">{{ $esp->esp_nombre }}</td>
                            <td><span class="tipo-badge">{{ $esp->esp_tipo }}</span></td>
                            <td>
                                <span class="tag-warning"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svg-icon-warning"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> {{ $esp->reservas_mes }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>

{{-- ===== RATIO DE CANCELACIONES ===== --}}
<div class="card">
    <h3 class="section-title"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svg-icon-danger"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> Ratio de cancelaciones / rechazos por espacio</h3>
    @if($cancelaciones->isEmpty())
        <p class="empty-table-msg">Sin datos.</p>
    @else
        <table class="rep-table">
            <thead>
                <tr>
                    <th>Espacio</th>
                    <th>Total reservas</th>
                    <th>No efectivas</th>
                    <th>Ratio</th>
                    <th class="th-graph">Gráfico</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cancelaciones as $row)
                    @php $pct = $row->ratio_cancelacion ?? 0; @endphp
                    <tr>
                        <td class="td-espacio">{{ $row->esp_nombre }}</td>
                        <td>{{ $row->total }}</td>
                        <td>{{ $row->no_efectivas }}</td>
                        <td style="font-weight:700; color:{{ $pct >= 50 ? '#b91c1c' : ($pct >= 25 ? '#d97706' : '#374151') }}">{{ $pct }}%</td>
                        <td>
                            <div class="cancel-bar">
                                <div class="cancel-fill" style="width:{{ $pct }}%">
                                    @if($pct > 15)<span>{{ $pct }}%</span>@endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@endsection
