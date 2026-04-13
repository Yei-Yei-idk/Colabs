<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IniciarSesionController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesion.
     */
    public function mostrarFormulario(Request $request)
    {
        $redirect = $this->obtenerRedirectSeguro($request->query('redirect'));
        if ($redirect) {
            $request->session()->put('url.intended', $redirect);
        }

        return view('auth.login');
    }

    /**
     * Procesa el inicio de sesion del usuario.
     */
    public function autenticar(Request $request)
    {
        $request->validate([
            'user' => ['required', 'string'],
            'contra' => ['required', 'string'],
            'redirect' => ['nullable', 'string'],
        ], [
            'user.required' => 'El correo o numero de documento es obligatorio.',
            'contra.required' => 'La contrasena es obligatoria.',
        ]);

        $loginInput = $request->input('user');
        $password = $request->input('contra');
        $remember = $request->boolean('remember');

        $usuario = User::where('numero_documento', $loginInput)
            ->orWhere('user_correo', $loginInput)
            ->first();

        if (!$usuario) {
            return back()
                ->withInput($request->only('user'))
                ->withErrors(['user' => 'Las credenciales proporcionadas no son validas.']);
        }

        if (!Auth::attempt([
            'user_correo' => $usuario->user_correo,
            'password' => $password,
        ], $remember)) {
            return back()
                ->withInput($request->only('user'))
                ->withErrors(['user' => 'Las credenciales proporcionadas no son validas.']);
        }

        $request->session()->regenerate();

        $redirect = $this->obtenerRedirectSeguro($request->input('redirect'));
        if ($redirect) {
            $request->session()->put('url.intended', $redirect);
        }

        if (!$usuario->hasVerifiedEmail()) {
            return redirect()
                ->route('verification.notice')
                ->with('error', 'Tu cuenta existe, pero aun no ha sido verificada. Usa el boton de reenviar para recibir un nuevo enlace.');
        }

        switch ((int) $usuario->rol_id) {
            case 3:
                return redirect()->intended(route('cliente.index'))->with('status', 'Bienvenido/a ' . $usuario->user_nombre . '.');

            case 1:
            case 2:
                return redirect()->route('admin.dashboard')->with('status', 'Bienvenido al panel de control.');
        }

        Auth::logout();

        return back()
            ->withInput($request->only('user'))
            ->withErrors(['user' => 'No fue posible identificar tu rol de acceso.']);
    }

    private function obtenerRedirectSeguro(?string $redirect): ?string
    {
        if (empty($redirect)) {
            return null;
        }

        if (!str_starts_with($redirect, '/') || str_starts_with($redirect, '//')) {
            return null;
        }

        return url($redirect);
    }
}
