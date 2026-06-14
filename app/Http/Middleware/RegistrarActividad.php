<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RegistrarActividad
{
    /**
     * Registra cada página visitada por usuarios autenticados.
     * Los eventos de login se registran en el listener de autenticación.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Solo registrar si el usuario está autenticado
        if (!Auth::check()) {
            return $response;
        }

        // Excluir rutas que no aportan valor analítico
        $excluidas = [
            '_debugbar',
            'livewire',
            'sanctum',
            'favicon',
            '__clockwork',
        ];

        $path = $request->path();
        foreach ($excluidas as $excluida) {
            if (str_contains($path, $excluida)) {
                return $response;
            }
        }

        // Solo registrar peticiones GET (navegación de páginas)
        if (!$request->isMethod('GET')) {
            return $response;
        }

        // Calcular duración de sesión desde el primer request de esta sesión
        $sessionStart = $request->session()->get('activity_session_start');
        if (!$sessionStart) {
            $request->session()->put('activity_session_start', now()->timestamp);
            $sessionStart = now()->timestamp;
        }
        $sessionDuration = now()->timestamp - $sessionStart;

        ActivityLog::create([
            'user_id'          => Auth::id(),
            'event'            => 'page_visit',
            'url'              => $request->fullUrl(),
            'ip'               => $request->ip(),
            'user_agent'       => substr($request->userAgent() ?? '', 0, 500),
            'session_duration' => $sessionDuration,
            'created_at'       => now(),
        ]);

        return $response;
    }
}
