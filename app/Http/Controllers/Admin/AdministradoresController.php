<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdministradoresController extends Controller
{
    /**
     * Lista los administradores (rol_id = 2). Solo accesible por SuperAdmin (1).
     */
    public function index()
    {
        // Bloqueo de seguridad: Solo SuperAdmin (1) puede entrar
        if (auth()->user()->rol_id != 1) {
            return redirect()->route('admin.dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $usuarios = User::with('rol')
            ->where('rol_id', 2)
            ->get();

        return view('admin.gestion_admin.gestion_admin', compact('usuarios'));
    }

    /**
     * Elimina un administrador. Solo accesible por SuperAdmin (1).
     */
    public function destroy($id)
    {
        if (auth()->user()->rol_id != 1) {
             return redirect()->route('admin.dashboard')->with('error', 'Acción no permitida.');
        }

        $usuario = User::findOrFail($id);
        $usuario->delete();

        return redirect()->route('admin.gestion_admin.index')->with('success', 'Registro eliminado correctamente');
    }
}
