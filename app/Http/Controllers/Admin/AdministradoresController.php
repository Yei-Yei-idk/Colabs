<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\MailService;
use Illuminate\Validation\Rules\Password;

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
            'contra'   => [
                'required', 
                'string', 
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
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
            'contra.mixedCase' => 'La contraseña debe tener al menos una letra mayúscula y una minúscula.',
            'contra.numbers' => 'La contraseña debe tener al menos un número.',
            'contra.symbols' => 'La contraseña debe tener al menos un carácter especial.',
        ]);

        $usuario = User::create([
            'numero_documento' => $request->cedula,
            'user_nombre'      => $request->nombre,
            'user_correo'      => $request->correo,
            'user_telefono'    => $request->telefono,
            'user_contrasena'  => $request->contra, // Laravel lo hashea por el cast en el modelo
            'rol_id'           => 2,
        ]);

        // Enviar correo de acceso
        app(MailService::class)->enviarCorreoNuevoAdmin($usuario, $request->contra);

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
     * Actualiza solo el correo de un administrador (según el código original del usuario).
     */
    public function update(Request $request, $id)
    {
        if (auth()->user()->rol_id != 1) {
            return redirect()->route('admin.dashboard')->with('error', 'No tienes permisos.');
        }

        $usuario = User::findOrFail($id);

        $request->validate([
            'correo'   => ['required', 'email', 'unique:usuarios,user_correo,' . $id],
        ], [
            'correo.required' => 'El correo es obligatorio.',
            'correo.email'    => 'El formato del correo no es válido.',
            'correo.unique'   => 'Este correo ya está registrado.',
        ]);

        $usuario->update([
            'user_correo' => $request->correo
        ]);

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
