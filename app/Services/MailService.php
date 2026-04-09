<?php

namespace App\Services;

use App\Models\User;
use App\Models\Reserva;
use App\Notifications\VerifyEmailCustom;
use App\Mail\BienvenidaCuentaCreadaMail;
use App\Mail\RestablecerContrasenaMail;
use App\Notifications\ReservaStatusChanged;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailService
{
    /**
     * Envia el correo de verificacion de cuenta (Notification).
     */
    public function enviarVerificacion(User $user, string $token): bool
    {
        try {
            $user->notify(new VerifyEmailCustom($token));
            return true;
        } catch (\Throwable $e) {
            Log::warning('No se pudo enviar correo de verificacion.', [
                'usuario_id' => $user->id,
                'correo' => $user->user_correo,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Envia el correo de bienvenida a un usuario ya verificado (Mailable).
     */
    public function enviarBienvenida(User $user): bool
    {
        try {
            Mail::to($user->user_correo)->send(new BienvenidaCuentaCreadaMail($user));
            return true;
        } catch (\Throwable $e) {
            Log::warning('No se pudo enviar el correo de bienvenida.', [
                'usuario_id' => $user->id,
                'correo' => $user->user_correo,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Envia el enlace de restablecimiento de contrasena (Mailable).
     */
    public function enviarRestablecerContrasena(User $user, string $resetUrl): bool
    {
        try {
            Mail::to($user->user_correo)->send(new RestablecerContrasenaMail($user, $resetUrl));
            return true;
        } catch (\Throwable $e) {
            Log::error('Error enviando correo de restablecimiento: ' . $e->getMessage(), [
                'usuario_id' => $user->id,
                'correo' => $user->user_correo,
            ]);
            return false;
        }
    }

    /**
     * Envia notificaciones correspondientes a los cambios de estado de las reservas (Notification).
     */
    public function enviarNotificacionReserva(Reserva $reserva, string $status): bool
    {
        try {
            $reserva->usuario->notify(new ReservaStatusChanged($reserva, $status));
            return true;
        } catch (\Throwable $e) {
            // Utilizamos Log::error en lugar de Log::warning porque un error
            // en este punto podría ser más grave dado el estado de la reserva
            Log::error("Error enviando notificacion de reserva ({$status})", [
                'reserva_id' => $reserva->reserva_id,
                'usuario_id' => $reserva->user_id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
