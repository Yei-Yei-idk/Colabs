<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\BienvenidaCuentaCreadaMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RegistrarseController extends Controller
{
    /**
     * Muestra el formulario de registro.
     */
    public function mostrar()
    {
        return view('auth.registrarse');
    }

    /**
     * Procesa el formulario de registro.
     */
    public function guardar(Request $request)
    {
        $validated = $request->validate([
            'numero_documento' => ['required', 'string', 'min:6', 'regex:/^[0-9]+$/', 'unique:usuarios,numero_documento'],
            'user_nombre' => ['required', 'string', 'max:255'],
            'user_correo' => ['required', 'email', 'max:255', 'unique:usuarios,user_correo'],
            'user_telefono' => ['required', 'numeric'],
            'user_contrasena' => ['required', 'string', 'min:8'],
            'condiciones' => ['accepted'],
        ], [
            'numero_documento.required' => 'El numero de documento es obligatorio.',
            'numero_documento.string' => 'El numero de documento debe ser un texto.',
            'numero_documento.min' => 'El numero de documento debe tener al menos 6 caracteres.',
            'numero_documento.regex' => 'El numero de documento solo puede contener numeros.',
            'numero_documento.unique' => 'Este numero de documento ya esta registrado.',
            'user_nombre.required' => 'El nombre es obligatorio.',
            'user_nombre.string' => 'El nombre debe ser una cadena de texto.',
            'user_nombre.max' => 'El nombre no puede tener mas de 255 caracteres.',
            'user_correo.required' => 'El correo electronico es obligatorio.',
            'user_correo.email' => 'El correo electronico debe tener un formato valido.',
            'user_correo.max' => 'El correo electronico no puede tener mas de 255 caracteres.',
            'user_correo.unique' => 'Este correo electronico ya esta registrado.',
            'user_telefono.required' => 'El telefono es obligatorio.',
            'user_telefono.numeric' => 'El telefono debe ser un numero.',
            'user_contrasena.required' => 'La contrasena es obligatoria.',
            'user_contrasena.string' => 'La contrasena debe ser una cadena de texto.',
            'user_contrasena.min' => 'La contrasena debe tener al menos 8 caracteres.',
            'condiciones.accepted' => 'Debes aceptar los terminos y condiciones.',
        ]);

        $usuario = User::create([
            'numero_documento' => $validated['numero_documento'],
            'user_nombre' => $validated['user_nombre'],
            'user_correo' => $validated['user_correo'],
            'user_telefono' => $validated['user_telefono'],
            'user_contrasena' => $validated['user_contrasena'],
            'rol_id' => 3,
        ]);

        try {
            Mail::to($usuario->user_correo)->send(new BienvenidaCuentaCreadaMail($usuario));
        } catch (\Throwable $e) {
            Log::warning('No se pudo enviar el correo de bienvenida de registro.', [
                'usuario_id' => $usuario->id,
                'correo' => $usuario->user_correo,
                'error' => $e->getMessage(),
            ]);
        }

        Auth::login($usuario);

        event(new Registered($usuario));

        return redirect()
            ->route('verification.notice')
            ->with('status', 'Cuenta creada. Revisa tu correo electronico para verificarla.');
    }
}
