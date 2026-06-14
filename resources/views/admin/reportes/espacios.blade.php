@extends('layouts.admin')

@section('title', 'Reporte de Espacios')
@section('page-title', 'Reportes')

@section('styles')
<style>
    .section-title {
        font-size: 1rem; font-weight: 700; color: #111827;
        margin: 0 0 1rem; display: flex; align-items: center; gap: 0.5rem;
    }
    .card {
        background: #fff; border-radius: 16px;
        padding: 1.4rem 1.6rem;
        box-shadow: 0 2px 16px rgba(0,0,0,0.06);
        border: 1px solid #f0f0f0;
        margin-bottom: 1.5rem;
    }
    .report-grid {
        display: grid; grid-template-columns: 1fr 1fr;
        gap: 1.25rem; margin-bottom: 1.5rem;
    }
    @media(max-width:900px){ .report-grid { grid-template-columns: 1fr; } }

    /* Tabla de ocupación */
    .rep-table { width: 100%; border-collapse: collapse; }
    .rep-table th {
        font-size: 0.71rem; font-weight: 600; color: #9ca3af;
        text-align: left; padding: 0.45rem 0.6rem;
        border-bottom: 1px solid #f3f4f6; text-transform: uppercase;
    }
    .rep-table td { padding: 0.6rem 0.6rem; font-size: 0.83rem; color: #374151; border-bottom: 1px solid #f9fafb; }
    .rep-table tr:hover td { background: #fafafa; }
    .rep-table tr:last-child td { border-bottom: none; }

    /* Barra de ocupación */
    .occ-bar { height: 8px; background: #f3f4f6; border-radius: 99px; min-width: 80px; overflow: hidden; }
    .occ-fill { height: 100%; border-radius: 99px; transition: width .4s; }

    /* Barra de horas pico */
    .peak-bar { height: 28px; background: #f3f4f6; border-radius: 6px; overflow: hidden; }
    .peak-fill {
        height: 100%; border-radius: 6px;
        background: linear-gradient(90deg, #6366f1, #8b5cf6);
        display: flex; align-items: center; padding-left: 8px;
    }
    .peak-fill span { font-size: 0.7rem; font-weight: 700; color: #fff; }

    /* Badge tipo espacio */
    .tipo-badge {
        display: inline-block; padding: 2px 8px; border-radius: 6px;
        font-size: 0.7rem; font-weight: 600;
        background: #eff6ff; color: #1d4ed8;
    }

    /* Cancelaciones */
    .cancel-bar { height: 20px; background: #f3f4f6; border-radius: 6px; overflow: hidden; flex: 1; }
    .cancel-fill {
        height: 100%; border-radius: 6px;
        background: linear-gradient(90deg, #f87171, #ef4444);
        display: flex; align-items: center; padding-left: 6px;
    }
    .cancel-fill span { font-size: 0.68rem; font-weight: 700; color: #fff; }

    .tag-warning {
        display: inline-flex; align-items: center; gap: 4px;
        background: #fef3c7; color: #92400e;
        padding: 3px 10px; border-radius: 999px; font-size: 0.72rem; font-weight: 700;
    }
</style>
@endsection

@section('content')

{{-- ===== TASA DE OCUPACIÓN ===== --}}
<div class="card">
    <h3 class="section-title">📊 Tasa de ocupación por espacio</h3>
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
                    <td style="font-weight:600;">{{ $row->esp_nombre }}</td>
                    <td><span class="tipo-badge">{{ $row->esp_tipo }}</span></td>
                    <td>{{ $row->total_reservas }}</td>
                    <td>{{ $row->reservas_productivas }}</td>
                    <td style="font-weight:700; color: {{ $color }}">{{ $pct }}%</td>
                    <td style="min-width:100px;">
                        <div class="occ-bar">
                            <div class="occ-fill" style="width:{{ $pct }}%; background:{{ $color }};"></div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; padding:1.5rem; color:#9ca3af;">Sin datos de reservas.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="report-grid">

    {{-- ===== HORAS PICO ===== --}}
    <div class="card">
        <h3 class="section-title">🕐 Horas pico de reserva</h3>
        @if($horasPico->isEmpty())
            <p style="color:#9ca3af; text-align:center; padding:1rem;">Sin datos.</p>
        @else
            <div style="display:flex; flex-direction:column; gap:8px;">
                @foreach($horasPico as $h)
                    <div style="display:flex; align-items:center; gap:10px;">
                        <span style="font-size:0.75rem; font-weight:600; color:#374151; min-width:56px;">
                            {{ sprintf('%02d:00', $h->hora) }}
                        </span>
                        <div class="peak-bar" style="flex:1;">
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
        <h3 class="section-title">⚠️ Espacios subutilizados <small style="font-weight:400; color:#9ca3af;">(último mes, &lt; {{ $umbralSubutilizados }} reservas)</small></h3>
        @if($subutilizados->isEmpty())
            <p style="color:#10b981; text-align:center; padding:1rem; font-weight:600;">
                ✅ Todos los espacios tienen actividad suficiente.
            </p>
        @else
            <table class="rep-table">
                <thead>
                    <tr><th>Espacio</th><th>Tipo</th><th>Reservas (mes)</th></tr>
                </thead>
                <tbody>
                    @foreach($subutilizados as $esp)
                        <tr>
                            <td style="font-weight:600;">{{ $esp->esp_nombre }}</td>
                            <td><span class="tipo-badge">{{ $esp->esp_tipo }}</span></td>
                            <td>
                                <span class="tag-warning">⚠️ {{ $esp->reservas_mes }}</span>
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
    <h3 class="section-title">❌ Ratio de cancelaciones / rechazos por espacio</h3>
    @if($cancelaciones->isEmpty())
        <p style="color:#9ca3af; text-align:center; padding:1rem;">Sin datos.</p>
    @else
        <table class="rep-table">
            <thead>
                <tr>
                    <th>Espacio</th>
                    <th>Total reservas</th>
                    <th>No efectivas</th>
                    <th>Ratio</th>
                    <th style="min-width:120px;">Gráfico</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cancelaciones as $row)
                    @php $pct = $row->ratio_cancelacion ?? 0; @endphp
                    <tr>
                        <td style="font-weight:600;">{{ $row->esp_nombre }}</td>
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
