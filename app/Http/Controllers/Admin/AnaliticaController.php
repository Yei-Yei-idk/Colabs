<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class AnaliticaController extends Controller
{
    public function index()
    {
        // --- Páginas más visitadas (top 10) ---
        $paginasMasVisitadas = ActivityLog::select('url', DB::raw('COUNT(*) as visitas'))
            ->where('event', 'page_visit')
            ->groupBy('url')
            ->orderByDesc('visitas')
            ->limit(10)
            ->get();

        // --- Usuarios más activos (top 10) ---
        $usuariosMasActivos = ActivityLog::select('user_id', DB::raw('COUNT(*) as acciones'))
            ->with('usuario:id,user_nombre,user_correo')
            ->where('event', 'page_visit')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderByDesc('acciones')
            ->limit(10)
            ->get();

        // --- Logins por día (últimos 30 días) ---
        $loginsPorDia = ActivityLog::select(
                DB::raw('DATE(created_at) as fecha'),
                DB::raw('COUNT(*) as total')
            )
            ->where('event', 'login')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // --- Tiempo promedio de sesión por usuario (últimas 24h de datos) ---
        $tiempoPromedioSesion = ActivityLog::select(
                'user_id',
                DB::raw('MAX(session_duration) as duracion_max'),
                DB::raw('COUNT(*) as visitas')
            )
            ->with('usuario:id,user_nombre')
            ->where('event', 'page_visit')
            ->whereNotNull('user_id')
            ->whereNotNull('session_duration')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('user_id')
            ->orderByDesc('duracion_max')
            ->limit(10)
            ->get();

        // --- Totales globales ---
        $totalLogins       = ActivityLog::where('event', 'login')->count();
        $totalVisitas      = ActivityLog::where('event', 'page_visit')->count();
        $totalUsuariosActivos = ActivityLog::where('event', 'page_visit')
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');

        return view('admin.analitica.index', compact(
            'paginasMasVisitadas',
            'usuariosMasActivos',
            'loginsPorDia',
            'tiempoPromedioSesion',
            'totalLogins',
            'totalVisitas',
            'totalUsuariosActivos'
        ));
    }
}
