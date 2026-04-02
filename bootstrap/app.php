<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SanearSesionLegacyUsuario::class,
        ]);

        $middleware->alias([
            'es.administrador' => \App\Http\Middleware\EsAdministrador::class,
            'es.cliente'       => \App\Http\Middleware\EsCliente::class,
            'perfil.google.completo' => \App\Http\Middleware\VerificarPerfilGoogleCompleto::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
