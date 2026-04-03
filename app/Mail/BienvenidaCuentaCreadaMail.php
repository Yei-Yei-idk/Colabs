<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BienvenidaCuentaCreadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $usuario;

    public function __construct(User $usuario)
    {
        $this->usuario = $usuario;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenido a Colabs',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bienvenida-cuenta-creada',
            with: [
                'usuario' => $this->usuario,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
