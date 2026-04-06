<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestProductionMail extends Command
{
    protected $signature = 'mail:test {to}';

    protected $description = 'Envia un correo de prueba para validar SMTP en produccion';

    public function handle(): int
    {
        $to = (string) $this->argument('to');

        try {
            Mail::raw(
                'Prueba SMTP de Colabs en produccion. Si recibes este correo, el envio funciona correctamente.',
                function ($message) use ($to): void {
                    $message->to($to)->subject('Colabs - Prueba de correo en produccion');
                }
            );
        } catch (\Throwable $e) {
            $this->error('Fallo el envio: '.$e->getMessage());
            return self::FAILURE;
        }

        $this->info("Correo de prueba enviado a {$to}");

        return self::SUCCESS;
    }
}
