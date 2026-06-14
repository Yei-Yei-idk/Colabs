<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Espacio;
use App\Models\Reserva;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    /**
     * Ruta del archivo de ajustes en storage/app
     */
    private static string $settingsFile = 'settings.json';

    /**
     * Lee todos los ajustes del archivo JSON.
     */
    public static function getSettings(): array
    {
        if (!Storage::exists(self::$settingsFile)) {
            // Valores por defecto
            $defaults = ['promociones_visible' => true];
            Storage::put(self::$settingsFile, json_encode($defaults));
            return $defaults;
        }
        return json_decode(Storage::get(self::$settingsFile), true) ?? ['promociones_visible' => true];
    }

    /**
     * Muestra el dashboard del administrador / superadmin.
     */
    public function index()
    {
        // --- KPIs básicos existentes ---
        $espaciosDisponibles   = Espacio::where('esp_estado', 'Activo')->count();
        $reservas              = Reserva::where('rsva_estado', 'Aceptada')->count();
        $solicitudesPendientes = Reserva::where('rsva_estado', 'Pendiente')->count();
        $ultimasReservas       = Reserva::with(['usuario', 'espacio'])
            ->latest('rsva_fecha')->take(10)->get();

        // --- KPIs en tiempo real ---
        $hoy = now()->toDateString();

        $reservasActivasHoy = Reserva::whereDate('rsva_fecha', $hoy)
            ->whereIn('rsva_estado', ['Aceptada', 'Activa'])
            ->count();

        $ingresosDelMes = DB::table('reserva')
            ->join('espacios', 'reserva.espacio_id', '=', 'espacios.espacio_id')
            ->whereIn('rsva_estado', ['Aceptada', 'Finalizada'])
            ->whereYear('rsva_fecha', now()->year)
            ->whereMonth('rsva_fecha', now()->month)
            ->sum(DB::raw('TIMESTAMPDIFF(HOUR, rsva_hora_inicio, rsva_hora_fin) * esp_precio_hora'));

        $usuariosRegistrados = User::where('rol_id', 3)->count();

        // --- Datos para el Gantt del día ---
        $reservasDelDia = Reserva::with(['espacio', 'usuario'])
            ->whereDate('rsva_fecha', $hoy)
            ->whereIn('rsva_estado', ['Aceptada', 'Activa', 'Pendiente'])
            ->orderBy('rsva_hora_inicio')
            ->get();

        $settings = self::getSettings();
        $promocionesVisible = $settings['promociones_visible'] ?? true;

        return view('admin.dashboard', compact(
            'espaciosDisponibles',
            'reservas',
            'solicitudesPendientes',
            'ultimasReservas',
            'promocionesVisible',
            'reservasActivasHoy',
            'ingresosDelMes',
            'usuariosRegistrados',
            'reservasDelDia'
        ));
    }

    /**
     * Alterna la visibilidad del apartado de Promociones en el nav del cliente.
     */
    public function togglePromociones()
    {
        $settings = self::getSettings();
        $settings['promociones_visible'] = !($settings['promociones_visible'] ?? true);
        Storage::put(self::$settingsFile, json_encode($settings));

        $estado = $settings['promociones_visible'] ? 'habilitadas' : 'deshabilitadas';
        return back()->with('success', "Promociones $estado correctamente.");
    }
}
