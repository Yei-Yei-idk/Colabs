<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarPerfilGoogleCompleto
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if (!$usuario) {
            return $next($request);
        }

        $esCuentaGoogle = !empty($usuario->google_id);
        $perfilIncompleto = (int) ($usuario->user_telefono ?? 0) <= 0 || empty($usuario->numero_documento);

        if ($esCuentaGoogle && $perfilIncompleto) {
            return redirect()
                ->route('google.perfil.completar')
                ->with('status', 'Completa tu perfil para continuar.');
        }

        return $next($request);
    }
}
