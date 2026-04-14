<?php

namespace App\Observers;

use App\Models\Reserva;
use App\Notifications\ReservaStatusChanged;
use App\Services\MailService;

class ReservaObserver
{
    protected MailService $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }
    /**
     * Se dispara al CREAR una reserva.
     */
    public function created(Reserva $reserva): void
    {
        if ($reserva->rsva_estado == 'Pendiente') {
            $this->mailService->enviarNotificacionReserva($reserva, 'Pendiente');
        }
    }

    /**
     * Se dispara al ACTUALIZAR una reserva.
     */
    public function updated(Reserva $reserva): void
    {
        // En el evento "updated" debemos usar wasChanged para detectar cambios persistidos.
        if ($reserva->wasChanged('rsva_estado')) {
            $nuevoEstado = $reserva->rsva_estado;

            // Mapeo seguro de estados para disparar notificaciones
            $estadosNotificables = ['Aceptada', 'Rechazada', 'Cancelada', 'Finalizada'];

            // Normalización simple (el campo puede venir en minúsculas por procesos automáticos)
            $estadoNormalizado = ucfirst(strtolower($nuevoEstado));

            if (in_array($estadoNormalizado, $estadosNotificables)) {
                $this->mailService->enviarNotificacionReserva($reserva, $estadoNormalizado);
            }
        }
    }
}
