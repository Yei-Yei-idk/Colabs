<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Espacio;
use Illuminate\Support\Facades\DB;

class ReportesEspaciosController extends Controller
{
    public function index()
    {
        // --- Tasa de ocupación por espacio ---
        // Reservas productivas (Aceptada + Finalizada) vs total de reservas
        $ocupacion = DB::table('reserva')
            ->join('espacios', 'reserva.espacio_id', '=', 'espacios.espacio_id')
            ->select(
                'espacios.espacio_id',
                'espacios.esp_nombre',
                'espacios.esp_tipo',
                DB::raw('COUNT(*) as total_reservas'),
                DB::raw("SUM(CASE WHEN rsva_estado IN ('Aceptada','Finalizada') THEN 1 ELSE 0 END) as reservas_productivas"),
                DB::raw("ROUND(SUM(CASE WHEN rsva_estado IN ('Aceptada','Finalizada') THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 1) as tasa_ocupacion")
            )
            ->groupBy('espacios.espacio_id', 'espacios.esp_nombre', 'espacios.esp_tipo')
            ->orderByDesc('tasa_ocupacion')
            ->get();

        // --- Horas pico de reserva ---
        $horasPico = DB::table('reserva')
            ->select(
                DB::raw('HOUR(rsva_hora_inicio) as hora'),
                DB::raw('COUNT(*) as total')
            )
            ->whereIn('rsva_estado', ['Aceptada', 'Finalizada', 'Pendiente'])
            ->groupBy('hora')
            ->orderByDesc('total')
            ->get();

        $maxHoraPico = $horasPico->max('total') ?: 1;

        // --- Espacios subutilizados (menos de 3 reservas en el último mes) ---
        $umbralSubutilizados = 3;
        $espaciosTotales = Espacio::where('esp_estado', 'Activo')->get();

        $reservasUltimoMes = DB::table('reserva')
            ->select('espacio_id', DB::raw('COUNT(*) as total'))
            ->where('rsva_fecha', '>=', now()->subDays(30)->toDateString())
            ->groupBy('espacio_id')
            ->pluck('total', 'espacio_id');

        $subutilizados = $espaciosTotales->filter(function ($espacio) use ($reservasUltimoMes, $umbralSubutilizados) {
            return ($reservasUltimoMes[$espacio->espacio_id] ?? 0) < $umbralSubutilizados;
        })->map(function ($espacio) use ($reservasUltimoMes) {
            $espacio->reservas_mes = $reservasUltimoMes[$espacio->espacio_id] ?? 0;
            return $espacio;
        })->sortBy('reservas_mes');

        // --- Ratio de cancelaciones por espacio ---
        $cancelaciones = DB::table('reserva')
            ->join('espacios', 'reserva.espacio_id', '=', 'espacios.espacio_id')
            ->select(
                'espacios.esp_nombre',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN rsva_estado IN ('Cancelada','Rechazada') THEN 1 ELSE 0 END) as no_efectivas"),
                DB::raw("ROUND(SUM(CASE WHEN rsva_estado IN ('Cancelada','Rechazada') THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 1) as ratio_cancelacion")
            )
            ->groupBy('espacios.espacio_id', 'espacios.esp_nombre')
            ->orderByDesc('ratio_cancelacion')
            ->get();

        return view('admin.reportes.espacios', compact(
            'ocupacion',
            'horasPico',
            'maxHoraPico',
            'subutilizados',
            'umbralSubutilizados',
            'cancelaciones'
        ));
    }
}
