<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Espacio;
use App\Models\Reserva;
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
        $espaciosDisponibles  = Espacio::where('esp_estado', 'Activo')->count();
        $reservas             = Reserva::where('rsva_estado', 'Aceptada')->count();
        $solicitudesPendientes = Reserva::where('rsva_estado', 'Pendiente')->count();
        $ultimasReservas      = Reserva::with(['usuario', 'espacio'])->latest('rsva_fecha')->get();

        $settings = self::getSettings();
        $promocionesVisible = $settings['promociones_visible'] ?? true;
        /*Hacer condicional para que solo se muestren las últimas 10 reservas*/
        $ultimasReservas = $ultimasReservas->take(10);
        return view('admin.dashboard', compact(
            'espaciosDisponibles',
            'reservas',
            'solicitudesPendientes',
            'ultimasReservas',
            'promocionesVisible'
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
