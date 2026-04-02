<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SanearSesionLegacyUsuario
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->hasSession()) {
            return $next($request);
        }

        $session = $request->session();
        $claves = array_keys($session->all());
        $sesionLegacyDetectada = false;

        foreach ($claves as $clave) {
            if (!str_starts_with((string) $clave, 'login_')) {
                continue;
            }

            $valor = $session->get($clave);

            if (is_string($valor) && !ctype_digit($valor)) {
                $session->forget($clave);
                $sesionLegacyDetectada = true;
            }
        }

        if ($sesionLegacyDetectada) {
            foreach (array_keys($session->all()) as $clave) {
                if (str_starts_with((string) $clave, 'password_hash_')) {
                    $session->forget($clave);
                }
            }

            Auth::logout();
            $session->flash('status', 'Por seguridad cerramos una sesion antigua. Inicia sesion de nuevo.');
        }

        return $next($request);
    }
}
