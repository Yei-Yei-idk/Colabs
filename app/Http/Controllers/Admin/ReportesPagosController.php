<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportesPagosController extends Controller
{
    /**
     * Calcular ingresos estimados: precio_hora * horas_reservadas
     * Solo se cuentan reservas Aceptadas y Finalizadas.
     */
    public function index(Request $request)
    {
        $periodo = $request->get('periodo', 'mes'); // dia | semana | mes

        // --- Agrupación de ingresos según período ---
        $formatoFecha = match ($periodo) {
            'dia'    => '%Y-%m-%d',
            'semana' => '%Y-%u',
            default  => '%Y-%m',
        };
        $labelFecha = match ($periodo) {
            'dia'    => 'Día',
            'semana' => 'Semana',
            default  => 'Mes',
        };

        $ingresosPorPeriodo = DB::table('reserva')
            ->join('espacios', 'reserva.espacio_id', '=', 'espacios.espacio_id')
            ->select(
                DB::raw("DATE_FORMAT(rsva_fecha, '{$formatoFecha}') as periodo"),
                DB::raw('COUNT(*) as total_reservas'),
                DB::raw('SUM(TIMESTAMPDIFF(HOUR, rsva_hora_inicio, rsva_hora_fin) * esp_precio_hora) as ingresos')
            )
            ->whereIn('rsva_estado', ['Aceptada', 'Finalizada'])
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->get();

        // --- Ticket promedio por usuario ---
        $ticketPorUsuario = DB::table('reserva')
            ->join('espacios', 'reserva.espacio_id', '=', 'espacios.espacio_id')
            ->join('usuarios', 'reserva.user_id', '=', 'usuarios.id')
            ->select(
                'usuarios.id',
                'usuarios.user_nombre',
                'usuarios.user_correo',
                DB::raw('COUNT(*) as reservas'),
                DB::raw('SUM(TIMESTAMPDIFF(HOUR, rsva_hora_inicio, rsva_hora_fin) * esp_precio_hora) as ingreso_total'),
                DB::raw('ROUND(SUM(TIMESTAMPDIFF(HOUR, rsva_hora_inicio, rsva_hora_fin) * esp_precio_hora) / COUNT(*), 0) as ticket_promedio')
            )
            ->whereIn('rsva_estado', ['Aceptada', 'Finalizada'])
            ->groupBy('usuarios.id', 'usuarios.user_nombre', 'usuarios.user_correo')
            ->orderByDesc('ticket_promedio')
            ->limit(15)
            ->get();

        // --- Reservas no efectivas (equivalente a "pagos fallidos") ---
        $noEfectivas = DB::table('reserva')
            ->join('espacios', 'reserva.espacio_id', '=', 'espacios.espacio_id')
            ->join('usuarios', 'reserva.user_id', '=', 'usuarios.id')
            ->select(
                'reserva.reserva_id',
                'usuarios.user_nombre',
                'espacios.esp_nombre',
                'reserva.rsva_fecha',
                'reserva.rsva_estado',
                DB::raw('TIMESTAMPDIFF(HOUR, rsva_hora_inicio, rsva_hora_fin) * esp_precio_hora as ingreso_perdido')
            )
            ->whereIn('rsva_estado', ['Cancelada', 'Rechazada'])
            ->orderByDesc('rsva_fecha')
            ->limit(20)
            ->get();

        // --- Resumen global ---
        $resumen = DB::table('reserva')
            ->join('espacios', 'reserva.espacio_id', '=', 'espacios.espacio_id')
            ->selectRaw('
                COUNT(*) as total_reservas,
                SUM(CASE WHEN rsva_estado IN ("Aceptada","Finalizada") THEN TIMESTAMPDIFF(HOUR, rsva_hora_inicio, rsva_hora_fin) * esp_precio_hora ELSE 0 END) as ingresos_totales,
                SUM(CASE WHEN rsva_estado IN ("Cancelada","Rechazada") THEN TIMESTAMPDIFF(HOUR, rsva_hora_inicio, rsva_hora_fin) * esp_precio_hora ELSE 0 END) as ingresos_perdidos,
                SUM(CASE WHEN rsva_estado IN ("Cancelada","Rechazada") THEN 1 ELSE 0 END) as reservas_no_efectivas
            ')
            ->first();

        return view('admin.reportes.pagos', compact(
            'ingresosPorPeriodo',
            'ticketPorUsuario',
            'noEfectivas',
            'resumen',
            'periodo',
            'labelFecha'
        ));
    }

    /**
     * Exportar reporte mensual en PDF.
     */
    public function exportarPdf(Request $request)
    {
        $mes = $request->get('mes', now()->format('Y-m'));

        [$anio, $numMes] = explode('-', $mes);

        $ingresosPorEspacio = DB::table('reserva')
            ->join('espacios', 'reserva.espacio_id', '=', 'espacios.espacio_id')
            ->select(
                'espacios.esp_nombre',
                'espacios.esp_tipo',
                DB::raw('COUNT(*) as total_reservas'),
                DB::raw('SUM(TIMESTAMPDIFF(HOUR, rsva_hora_inicio, rsva_hora_fin) * esp_precio_hora) as ingresos')
            )
            ->whereIn('rsva_estado', ['Aceptada', 'Finalizada'])
            ->whereYear('rsva_fecha', $anio)
            ->whereMonth('rsva_fecha', $numMes)
            ->groupBy('espacios.espacio_id', 'espacios.esp_nombre', 'espacios.esp_tipo')
            ->orderByDesc('ingresos')
            ->get();

        $resumen = DB::table('reserva')
            ->join('espacios', 'reserva.espacio_id', '=', 'espacios.espacio_id')
            ->whereYear('rsva_fecha', $anio)
            ->whereMonth('rsva_fecha', $numMes)
            ->selectRaw('
                COUNT(*) as total_reservas,
                SUM(CASE WHEN rsva_estado IN ("Aceptada","Finalizada") THEN TIMESTAMPDIFF(HOUR, rsva_hora_inicio, rsva_hora_fin) * esp_precio_hora ELSE 0 END) as ingresos_totales,
                SUM(CASE WHEN rsva_estado IN ("Cancelada","Rechazada") THEN 1 ELSE 0 END) as canceladas,
                SUM(CASE WHEN rsva_estado = "Finalizada" THEN 1 ELSE 0 END) as finalizadas
            ')
            ->first();

        $nombreMes = \Carbon\Carbon::createFromDate($anio, $numMes, 1)
            ->translatedFormat('F Y');

        $pdf = Pdf::loadView('admin.reportes.pdf.reporte_mensual', compact(
            'ingresosPorEspacio',
            'resumen',
            'nombreMes',
            'mes'
        ))->setPaper('a4', 'portrait');

        return $pdf->download("reporte-colabs-{$mes}.pdf");
    }
}
