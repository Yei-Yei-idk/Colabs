<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Mensual — Co•labs</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px; color: #1a1a2e;
            background: #fff; padding: 30px 35px;
        }

        /* HEADER */
        .header {
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 3px solid #6366f1; padding-bottom: 16px; margin-bottom: 24px;
        }
        .brand { font-size: 22px; font-weight: 900; color: #6366f1; letter-spacing: -1px; }
        .brand small { font-size: 11px; color: #6b7280; font-weight: 400; display: block; margin-top: 2px; }
        .report-meta { text-align: right; }
        .report-meta .title { font-size: 15px; font-weight: 800; color: #111827; }
        .report-meta .date  { font-size: 10px; color: #6b7280; margin-top: 4px; }

        /* KPI BOXES */
        .kpi-row { display: flex; gap: 10px; margin-bottom: 22px; }
        .kpi-box {
            flex: 1; border: 1px solid #e5e7eb; border-radius: 10px;
            padding: 12px 14px; border-left: 4px solid #6366f1;
        }
        .kpi-box.green  { border-left-color: #10b981; }
        .kpi-box.red    { border-left-color: #ef4444; }
        .kpi-box.blue   { border-left-color: #3b82f6; }
        .kpi-val { font-size: 18px; font-weight: 900; color: #111827; }
        .kpi-lbl { font-size: 9px; color: #6b7280; margin-top: 3px; text-transform: uppercase; letter-spacing: 0.3px; }

        /* SECTION */
        .section { margin-bottom: 22px; }
        .section-title {
            font-size: 11px; font-weight: 800; color: #6366f1;
            text-transform: uppercase; letter-spacing: 0.5px;
            border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; margin-bottom: 10px;
        }

        /* TABLE */
        table { width: 100%; border-collapse: collapse; }
        th {
            background: #f9fafb; font-size: 9px; font-weight: 700;
            color: #6b7280; text-transform: uppercase; letter-spacing: 0.3px;
            padding: 6px 8px; text-align: left; border-bottom: 2px solid #e5e7eb;
        }
        td { padding: 7px 8px; font-size: 10px; border-bottom: 1px solid #f3f4f6; }
        tr:last-child td { border-bottom: none; }
        tr:nth-child(even) td { background: #fafafa; }
        .num { text-align: right; font-weight: 700; }
        .green-num { color: #10b981; }
        .red-num   { color: #ef4444; }

        /* FOOTER */
        .footer {
            margin-top: 30px; padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            display: flex; justify-content: space-between;
            font-size: 9px; color: #9ca3af;
        }

        .badge-a { color: #15803d; font-weight: 700; }
        .badge-f { color: #3730a3; font-weight: 700; }
        .badge-c { color: #b91c1c; font-weight: 700; }
    </style>
</head>
<body>

    {{-- ENCABEZADO --}}
    <div class="header">
        <div class="brand">
            Co•labs
            <small>Sistema de gestión de espacios</small>
        </div>
        <div class="report-meta">
            <div class="title">Reporte Mensual — {{ $nombreMes }}</div>
            <div class="date">Generado el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }}</div>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="kpi-row">
        <div class="kpi-box">
            <div class="kpi-val">{{ $resumen->total_reservas ?? 0 }}</div>
            <div class="kpi-lbl">Total reservas</div>
        </div>
        <div class="kpi-box green">
            <div class="kpi-val">${{ number_format($resumen->ingresos_totales ?? 0, 0, ',', '.') }}</div>
            <div class="kpi-lbl">Ingresos estimados</div>
        </div>
        <div class="kpi-box red">
            <div class="kpi-val">{{ $resumen->canceladas ?? 0 }}</div>
            <div class="kpi-lbl">Canceladas</div>
        </div>
        <div class="kpi-box blue">
            <div class="kpi-val">{{ $resumen->finalizadas ?? 0 }}</div>
            <div class="kpi-lbl">Finalizadas</div>
        </div>
    </div>

    {{-- TABLA DE INGRESOS POR ESPACIO --}}
    <div class="section">
        <div class="section-title">Ingresos por espacio — {{ $nombreMes }}</div>
        @if($ingresosPorEspacio->isEmpty())
            <p style="color:#9ca3af; padding:12px 0;">No hay reservas productivas en este mes.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Espacio</th>
                        <th>Tipo</th>
                        <th class="num">Reservas</th>
                        <th class="num">Ingresos estimados</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalEspacios = 0; @endphp
                    @foreach($ingresosPorEspacio as $i => $row)
                        @php $totalEspacios += $row->ingresos; @endphp
                        <tr>
                            <td style="color:#9ca3af;">{{ $i + 1 }}</td>
                            <td style="font-weight:700;">{{ $row->esp_nombre }}</td>
                            <td>{{ $row->esp_tipo }}</td>
                            <td class="num">{{ $row->total_reservas }}</td>
                            <td class="num green-num">${{ number_format($row->ingresos, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr style="background:#f0fdf4; font-weight:700;">
                        <td colspan="3" style="font-weight:800;">TOTAL</td>
                        <td class="num">{{ $ingresosPorEspacio->sum('total_reservas') }}</td>
                        <td class="num green-num">${{ number_format($totalEspacios, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        @endif
    </div>

    {{-- NOTA METODOLÓGICA --}}
    <div class="section">
        <div class="section-title">Nota metodológica</div>
        <p style="font-size:10px; color:#6b7280; line-height:1.6;">
            Los <strong>ingresos estimados</strong> se calculan multiplicando el precio por hora de cada espacio
            por la duración en horas de cada reserva con estado <strong>Aceptada</strong> o <strong>Finalizada</strong>.
            Las reservas Canceladas, Rechazadas o Pendientes no se incluyen en el cálculo de ingresos.
        </p>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <span>Co•labs — Panel Administrativo</span>
        <span>Reporte generado automáticamente · {{ now()->format('d/m/Y H:i:s') }}</span>
    </div>

</body>
</html>
