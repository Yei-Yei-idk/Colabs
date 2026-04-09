<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\BienvenidaCuentaCreadaMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Services\MailService;
use Carbon\Carbon;

class VerificacionController extends Controller
{
    protected MailService $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }
    /**
     * Muestra la vista de notificacion.
     * Si es la primera vez y no tiene token, envia automaticamente el correo.
     */
    public function notice(Request $request)
    {
        $user = $request->user();

        if ($user && $user->hasVerifiedEmail()) {
            return $user->rol_id == 1 || $user->rol_id == 2
                ? redirect()->route('admin.dashboard')
                : redirect()->route('cliente.index');
        }

        // Verificar limite de 30 segundos entre envios.
        $lastEmailSent = Session::get('last_email_sent_at');
        if ($lastEmailSent) {
            if ($lastEmailSent instanceof Carbon) {
                $lastEmailSentMs = $lastEmailSent->getTimestampMs();
            } else {
                $lastEmailSentMs = (int) $lastEmailSent;
            }

            $elapsedMs = now()->getTimestampMs() - $lastEmailSentMs;
            $elapsedSeconds = (int) floor($elapsedMs / 1000);
            if ($elapsedSeconds < 30) {
                return view('auth.verificar-correo');
            }
        }

        // Si es la primera vez (sin token), enviar automaticamente.
        if ($user && !$user->verification_token) {
            $token = Str::random(60);
            $user->verification_token = $token;
            $user->verification_token_expires_at = now()->addHour();
            $user->save();

            if ($this->mailService->enviarVerificacion($user, $token)) {
                Session::put('last_email_sent_at', now()->getTimestampMs());
                Session::put('reenvios_verificacion', 1); // Contar como primer intento.
            } else {
                session()->flash('error', 'No pudimos enviar el correo de verificacion. Intenta reenviar en un momento.');
            }
        }

        return view('auth.verificar-correo');
    }

    /**
     * Genera un nuevo token, lo guarda y despacha el correo.
     * Implementa un contador de maximo 3 reenvios por sesion.
     * Verifica limite de 30 segundos entre envios.
     */
    public function send(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $user->rol_id == 1 || $user->rol_id == 2
                ? redirect()->route('admin.dashboard')
                : redirect()->route('cliente.index');
        }

        $lastEmailSent = Session::get('last_email_sent_at');
        if ($lastEmailSent) {
            if ($lastEmailSent instanceof Carbon) {
                $lastEmailSentMs = $lastEmailSent->getTimestampMs();
            } else {
                $lastEmailSentMs = (int) $lastEmailSent;
            }

            $elapsedMs = now()->getTimestampMs() - $lastEmailSentMs;
            $elapsedSeconds = (int) floor($elapsedMs / 1000);
            if ($elapsedSeconds < 30) {
                $segundosRestantes = 30 - $elapsedSeconds;
                return back()->with('error', "Por favor, espera {$segundosRestantes} segundos antes de reenviar.");
            }
        }

        $intentos = Session::get('reenvios_verificacion', 0);
        if ($intentos >= 3) {
            return back()->with('error', 'Has superado el limite de reenvios por ahora. Intenta mas tarde.');
        }

        $token = Str::random(60);
        $user->verification_token = $token;
        $user->verification_token_expires_at = now()->addHour();
        $user->save();

        if (!$this->mailService->enviarVerificacion($user, $token)) {
            return back()->with('error', 'No pudimos enviar el correo de verificacion. Intenta de nuevo en un momento.');
        }

        Session::put('last_email_sent_at', now()->getTimestampMs());
        Session::put('reenvios_verificacion', $intentos + 1);

        return back()->with('status', 'verification-link-sent')->with('intentos', $intentos + 1);
    }

    /**
     * Valida el enlace firmado en el correo contra el token de la DB.
     * Enlace: /email/verify/{id}/{token}
     */
    public function verify(Request $request, $id, $token)
    {
        $user = User::find($id);
        $usuarioAutenticado = $request->user();

        if (!$user) {
            abort(404);
        }

        if ($user->hasVerifiedEmail()) {
            if ($usuarioAutenticado && (string) $usuarioAutenticado->getKey() === (string) $user->getKey()) {
                $destino = ($user->rol_id == 1 || $user->rol_id == 2) ? 'admin.dashboard' : 'cliente.index';
                return redirect()->route($destino)->with('status', 'Tu correo ya estaba verificado.');
            }

            return redirect()->route('login')->with('status', 'Tu correo ya estaba verificado. Ya puedes iniciar sesion.');
        }

        if (!$user->verification_token || !hash_equals($user->verification_token, $token)) {
            $mensaje = 'El enlace de verificacion no es valido. Inicia sesion para solicitar un nuevo correo.';
            if ($usuarioAutenticado) {
                return redirect()->route('verification.notice')->with('error', $mensaje);
            }

            return redirect()->route('login')->with('status', $mensaje);
        }

        if ($user->verification_token_expires_at && now()->gt($user->verification_token_expires_at)) {
            $mensaje = 'El enlace de verificacion ya vencio. Inicia sesion para reenviar uno nuevo.';
            if ($usuarioAutenticado) {
                return redirect()->route('verification.notice')->with('error', $mensaje);
            }

            return redirect()->route('login')->with('status', $mensaje);
        }

        $user->email_verified_at = now();
        $user->verification_token = null;
        $user->verification_token_expires_at = null;
        $user->save();

        $this->mailService->enviarBienvenida($user);

        Session::forget('reenvios_verificacion');

        if (!$usuarioAutenticado || (string) $usuarioAutenticado->getKey() !== (string) $user->getKey()) {
            return redirect()->route('login')->with('status', 'Correo verificado con exito. Ya puedes iniciar sesion.');
        }

        $dest = ($user->rol_id == 1 || $user->rol_id == 2) ? 'admin.dashboard' : 'cliente.index';
        return redirect()->route($dest)->with('status', 'Correo verificado con exito. Bienvenido a Colabs.');
    }

    /**
     * Muestra el formulario para cambiar correo.
     */
    public function formCambiarCorreo(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('cliente.perfil');
        }

        Session::put('cambiar_correo_access', true);

        return view('auth.cambiar-correo', compact('user'));
    }

    /**
     * Cambia el correo electronico del usuario y lo requiere verificar nuevamente.
     */
    public function cambiarCorreo(Request $request)
    {
        $user = $request->user();

        if (!Session::has('cambiar_correo_access')) {
            return redirect()->route('verification.notice');
        }

        $lastEmailSent = Session::get('last_email_sent_at');
        if ($lastEmailSent) {
            if ($lastEmailSent instanceof Carbon) {
                $lastEmailSentMs = $lastEmailSent->getTimestampMs();
            } else {
                $lastEmailSentMs = (int) $lastEmailSent;
            }

            $elapsedMs = now()->getTimestampMs() - $lastEmailSentMs;
            $elapsedSeconds = (int) floor($elapsedMs / 1000);
            if ($elapsedSeconds < 30) {
                $segundosRestantes = 30 - $elapsedSeconds;
                return back()->with('error', "Por favor, espera {$segundosRestantes} segundos antes de cambiar el correo nuevamente.");
            }
        }

        $request->validate([
            'correo_nuevo' => 'required|email|unique:usuarios,user_correo|max:100',
            'correo_confirmacion' => 'required|same:correo_nuevo',
        ], [
            'correo_nuevo.required' => 'El correo es obligatorio.',
            'correo_nuevo.email' => 'El formato del correo es invalido.',
            'correo_nuevo.unique' => 'Este correo electronico ya esta registrado.',
            'correo_confirmacion.same' => 'Los correos no coinciden.',
        ]);

        User::where('id', $user->id)->update([
            'user_correo' => $request->correo_nuevo,
            'email_verified_at' => null,
            'verification_token' => Str::random(60),
            'verification_token_expires_at' => now()->addHour(),
        ]);

        $user = $user->fresh();

        if (!$this->mailService->enviarVerificacion($user, $user->verification_token)) {
            return back()->with('error', 'No pudimos enviar el correo de verificacion al nuevo email. Intenta nuevamente.');
        }

        Session::put('last_email_sent_at', now()->getTimestampMs());
        Session::forget('reenvios_verificacion');
        Session::put('reenvios_verificacion', 1);

        Session::forget('cambiar_correo_access');

        return redirect()
            ->route('verification.notice')
            ->with('status', 'verification-email-changed')
            ->with('success', 'Se ha enviado un enlace de verificacion a tu nuevo correo. Revisa tu bandeja de entrada.');
    }

}
