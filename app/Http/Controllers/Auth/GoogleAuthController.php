<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\BienvenidaCuentaCreadaMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\GoogleProvider;
use App\Services\MailService;
use Throwable;

class GoogleAuthController extends Controller
{
    protected MailService $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }
    public function redirect(Request $request): RedirectResponse
    {
        $redirect = $this->obtenerRedirectSeguro($request->query('redirect'));
        if ($redirect) {
            $request->session()->put('url.intended', $redirect);
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (InvalidStateException $e) {
            Log::warning('Google OAuth devolvio estado invalido. Se reintenta en modo stateless.', [
                'error' => $e->getMessage(),
            ]);

            try {
                /** @var GoogleProvider $driver */
                $driver = Socialite::driver('google');
                $googleUser = $driver->stateless()->user();
            } catch (Throwable $fallbackException) {
                Log::error('Fallo login Google en fallback stateless.', [
                    'error' => $fallbackException->getMessage(),
                ]);

                return redirect()->route('login')
                    ->withErrors(['user' => 'No fue posible iniciar sesion con Google. Intentalo nuevamente.']);
            }
        } catch (Throwable $e) {
            Log::error('Fallo login Google en callback.', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('login')
                ->withErrors(['user' => 'No fue posible iniciar sesion con Google. Intentalo nuevamente.']);
        }

        $googleId = $googleUser->getId();
        $email = $googleUser->getEmail();

        if (!$googleId || !$email) {
            return redirect()->route('login')
                ->withErrors(['user' => 'Google no devolvio los datos obligatorios para continuar.']);
        }

        $esNuevoUsuario = false;

        $usuario = User::where('google_id', $googleId)->first();
        if (!$usuario) {
            $usuario = User::where('user_correo', $email)->first();
        }

        if ($usuario) {
            $usuario->google_id = $googleId;
            $usuario->avatar = $googleUser->getAvatar();

            if (!$usuario->email_verified_at) {
                $usuario->email_verified_at = now();
            }

            $usuario->save();
        } else {
            $esNuevoUsuario = true;

            $usuario = User::create([
                'numero_documento' => null,
                'user_nombre' => $googleUser->getName() ?: 'Usuario Google',
                'user_correo' => $email,
                'user_telefono' => 0,
                'user_contrasena' => null,
                'rol_id' => 3,
                'google_id' => $googleId,
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
                'verification_token' => null,
                'verification_token_expires_at' => null,
            ]);
        }

        Auth::login($usuario, true);
        request()->session()->regenerate();
        request()->session()->regenerateToken();

        if ($this->necesitaCompletarPerfilGoogle($usuario)) {
            $mensaje = $esNuevoUsuario
                ? 'Bienvenido a Colabs. Tu cuenta con Google fue creada correctamente. Completa tu perfil para continuar.'
                : 'Tu cuenta ya existia. Completa los datos faltantes de tu perfil para continuar.';

            return redirect()->route('google.perfil.completar')->with('status', $mensaje);
        }

        if ((int) $usuario->rol_id === 1 || (int) $usuario->rol_id === 2) {
            return redirect()->route('admin.dashboard')
                ->with('status', $esNuevoUsuario
                    ? 'Bienvenido. Tu cuenta de Google fue creada correctamente.'
                    : 'Tu cuenta ya estaba vinculada con Google. Iniciaste sesion correctamente.');
        }

        return redirect()->intended(route('cliente.index'))
            ->with('status', $esNuevoUsuario
                ? 'Bienvenido a Colabs. Tu cuenta de Google fue creada correctamente.'
                : 'Tu cuenta ya estaba vinculada con Google. Iniciaste sesion correctamente.');
    }

    public function mostrarCompletarPerfil()
    {
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        if (empty($usuario->google_id)) {
            return $this->redirigirAlPanel($usuario);
        }

        if (!$this->necesitaCompletarPerfilGoogle($usuario)) {
            return $this->redirigirAlPanel($usuario)
                ->with('status', 'Tu perfil ya esta completo.');
        }

        $datos = [
            'numero_documento' => $usuario->numero_documento,
            'user_nombre' => $usuario->user_nombre,
            'user_correo' => $usuario->user_correo,
            'user_telefono' => (int) $usuario->user_telefono > 0 ? $usuario->user_telefono : '',
        ];

        return view('auth.completar-perfil-google', [
            'usuario' => $usuario,
            'datos' => $datos,
        ]);
    }

    public function guardarCompletarPerfil(Request $request): RedirectResponse
    {
        /** @var User|null $usuario */
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        if (empty($usuario->google_id)) {
            return $this->redirigirAlPanel($usuario);
        }

        $documentoNormalizado = preg_replace('/\D+/', '', (string) $request->input('numero_documento', ''));
        $telefonoNormalizado = preg_replace('/\D+/', '', (string) $request->input('user_telefono', ''));

        $request->merge([
            'numero_documento' => $documentoNormalizado,
            'user_telefono' => $telefonoNormalizado,
        ]);

        $datos = $request->validate([
            'numero_documento' => [
                'required',
                'string',
                'min:6',
                'regex:/^[0-9]+$/',
                Rule::unique('usuarios', 'numero_documento')->ignore($usuario->id, 'id'),
            ],
            'user_nombre' => ['required', 'string', 'max:100'],
            'user_telefono' => ['required', 'digits_between:7,15'],
        ], [
            'numero_documento.required' => 'El numero de documento es obligatorio.',
            'numero_documento.string' => 'El numero de documento debe ser un texto.',
            'numero_documento.min' => 'El numero de documento debe tener al menos 6 caracteres.',
            'numero_documento.regex' => 'El numero de documento solo puede contener numeros.',
            'numero_documento.unique' => 'Este numero de documento ya esta registrado.',
            'user_nombre.required' => 'El nombre completo es obligatorio.',
            'user_nombre.max' => 'El nombre completo no puede superar 100 caracteres.',
            'user_telefono.required' => 'El telefono es obligatorio.',
            'user_telefono.digits_between' => 'El telefono debe tener entre 7 y 15 digitos.',
        ]);

        $usuario->numero_documento = $datos['numero_documento'];
        $usuario->user_nombre = trim($datos['user_nombre']);
        $usuario->user_telefono = (int) $datos['user_telefono'];
        $usuario->save();

        $this->mailService->enviarBienvenida($usuario);

        return $this->redirigirAlPanel($usuario)
            ->with('status', 'Perfil completado correctamente. Bienvenido a Colabs.');
    }

    private function necesitaCompletarPerfilGoogle(User $usuario): bool
    {
        if (empty($usuario->google_id)) {
            return false;
        }

        $telefonoInvalido = (int) ($usuario->user_telefono ?? 0) <= 0;
        $documentoIncompleto = empty($usuario->numero_documento);

        return $telefonoInvalido || $documentoIncompleto;
    }

    private function redirigirAlPanel(User $usuario): RedirectResponse
    {
        if ((int) $usuario->rol_id === 1 || (int) $usuario->rol_id === 2) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('cliente.index');
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
