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
     * Muestra el formulario para registrar un administrador.
     */
    public function create()
    {
        if (auth()->user()->rol_id != 1) {
            return redirect()->route('admin.dashboard')->with('error', 'No tienes permisos.');
        }
        return view('admin.gestion_admin.create');
    }

    /**
     * Guarda un nuevo administrador en la base de datos.
     */
    public function store(Request $request)
    {
        if (auth()->user()->rol_id != 1) {
            return redirect()->route('admin.dashboard')->with('error', 'No tienes permisos.');
        }

        $request->validate([
            'cedula'   => ['required', 'numeric', 'min_digits:7', 'unique:usuarios,numero_documento'],
            'nombre'   => ['required', 'string', 'max:255'],
            'correo'   => ['required', 'email', 'unique:usuarios,user_correo'],
            'telefono' => ['required', 'numeric', 'digits:10'],
            'contra'   => ['required', 'string', 'min:8'],
        ], [
            'cedula.required' => 'La cédula es obligatoria.',
            'cedula.numeric' => 'La cédula solo puede contener números.',
            'cedula.min_digits' => 'La cédula en Colombia debe tener al menos 7 dígitos.',
            'cedula.unique' => 'Esta cédula ya está registrada.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.numeric' => 'El teléfono solo puede contener números.',
            'telefono.digits' => 'El número de teléfono en Colombia debe tener 10 dígitos.',
            'contra.required' => 'La contraseña es obligatoria.',
            'contra.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        User::create([
            'numero_documento' => $request->cedula,
            'user_nombre'      => $request->nombre,
            'user_correo'      => $request->correo,
            'user_telefono'    => $request->telefono,
            'user_contrasena'  => $request->contra, // Laravel lo hashea por el cast en el modelo
            'rol_id'           => 2,
        ]);

        return redirect()->route('admin.gestion_admin.index')
                         ->with('success', 'Administrador registrado correctamente');
    }

    /**
     * Muestra el formulario para editar un administrador.
     */
    public function edit($id)
    {
        if (auth()->user()->rol_id != 1) {
            return redirect()->route('admin.dashboard')->with('error', 'No tienes permisos.');
        }

        $usuario = User::findOrFail($id);
        
        return view('admin.gestion_admin.edit', compact('usuario'));
    }

    /**
     * Actualiza los datos de un administrador.
     */
    public function update(Request $request, $id)
    {
        if (auth()->user()->rol_id != 1) {
            return redirect()->route('admin.dashboard')->with('error', 'No tienes permisos.');
        }

        $usuario = User::findOrFail($id);

        $request->validate([
            'cedula'   => ['required', 'numeric', 'min_digits:7', 'unique:usuarios,numero_documento,' . $id],
            'nombre'   => ['required', 'string', 'max:255'],
            'correo'   => ['required', 'email', 'unique:usuarios,user_correo,' . $id],
            'telefono' => ['required', 'numeric', 'digits:10'],
            'contra'   => ['nullable', 'string', 'min:8'], // Opcional al editar
        ], [
            'cedula.min_digits' => 'La cédula en Colombia debe tener al menos 7 dígitos.',
            'telefono.digits'   => 'El número de teléfono en Colombia debe tener 10 dígitos.',
            'contra.min'        => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $data = [
            'numero_documento' => $request->cedula,
            'user_nombre'      => $request->nombre,
            'user_correo'      => $request->correo,
            'user_telefono'    => $request->telefono,
        ];

        // Solo actualizar contraseña si se proporcionó una nueva
        if ($request->filled('contra')) {
            $data['user_contrasena'] = $request->contra;
        }

        $usuario->update($data);

        return redirect()->route('admin.gestion_admin.index')
                         ->with('success', 'Administrador actualizado correctamente');
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
