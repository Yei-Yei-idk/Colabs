<?php

namespace App\Console\Commands;

use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ActualizarEstadoReservas extends Command
{
    /**
     * Nombre del comando Artisan.
     * Ejecutar manualmente: php artisan reservas:actualizar-estado
     */
    protected $signature = 'reservas:actualizar-estado';

    protected $description = 'Marca como "finalizada" toda reserva activa cuya fecha/hora ya haya pasado';

    public function handle(): void
    {
        $ahora = Carbon::now();
        $this->info("[{$ahora->format('Y-m-d H:i:s')}] Iniciando sincronización automática...");

        $actualizadas = Reserva::actualizarVencidas();

        $this->info("[{$ahora->format('Y-m-d H:i:s')}] {$actualizadas} reserva(s) marcadas como finalizadas.");
    }
}
