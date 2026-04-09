<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

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
            'user_contrasena' => [
                'required', 
                'string', 
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
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
            'user_contrasena.mixedCase' => 'La contraseña debe tener al menos una letra mayúscula y una minúscula.',
            'user_contrasena.numbers' => 'La contraseña debe tener al menos un número.',
            'user_contrasena.symbols' => 'La contraseña debe tener al menos un carácter especial.',
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

        Auth::login($usuario);

        // El envio del correo de verificacion se maneja desde verification.notice
        // para evitar que un fallo SMTP bloquee el registro.
        try {
            // Conservamos el punto de extension por si existen listeners de negocio
            // distintos al correo.
            event(new \Illuminate\Auth\Events\Registered($usuario));
        } catch (\Throwable $e) {
            Log::warning('No se pudo despachar el evento Registered tras crear cuenta.', [
                'usuario_id' => $usuario->id,
                'correo' => $usuario->user_correo,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('verification.notice')
            ->with('status', 'Cuenta creada. Revisa tu correo electronico para verificarla.');
    }
}
