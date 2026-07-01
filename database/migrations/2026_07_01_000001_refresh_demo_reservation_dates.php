<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('reserva')) {
            return;
        }

        $baseDate = Carbon::today(config('app.timezone'));

        foreach ($this->demoReservations() as $reservaId => $reservation) {
            DB::table('reserva')
                ->where('reserva_id', $reservaId)
                ->update([
                    'rsva_fecha' => $baseDate->copy()->addDays($reservation['day_offset'])->toDateString(),
                    'rsva_estado' => $reservation['state'],
                ]);
        }
    }

    public function down(): void
    {
        // The previous demo dates depended on when the old migration was run, so
        // there is no reliable static value to restore.
    }

    private function demoReservations(): array
    {
        return [
            1 => ['day_offset' => -8, 'state' => 'Finalizada'],
            2 => ['day_offset' => -7, 'state' => 'Finalizada'],
            3 => ['day_offset' => -6, 'state' => 'Finalizada'],
            4 => ['day_offset' => -5, 'state' => 'Finalizada'],
            5 => ['day_offset' => -4, 'state' => 'Finalizada'],
            6 => ['day_offset' => 1, 'state' => 'Pendiente'],
            7 => ['day_offset' => 2, 'state' => 'Pendiente'],
            8 => ['day_offset' => 3, 'state' => 'Pendiente'],
            9 => ['day_offset' => 4, 'state' => 'Pendiente'],
            10 => ['day_offset' => 5, 'state' => 'Pendiente'],
            11 => ['day_offset' => 2, 'state' => 'Aceptada'],
            12 => ['day_offset' => 3, 'state' => 'Aceptada'],
            13 => ['day_offset' => 4, 'state' => 'Aceptada'],
            14 => ['day_offset' => 5, 'state' => 'Aceptada'],
            15 => ['day_offset' => 1, 'state' => 'Aceptada'],
            16 => ['day_offset' => -7, 'state' => 'Rechazada'],
            17 => ['day_offset' => -6, 'state' => 'Rechazada'],
            18 => ['day_offset' => -5, 'state' => 'Rechazada'],
            19 => ['day_offset' => -4, 'state' => 'Rechazada'],
            20 => ['day_offset' => -3, 'state' => 'Rechazada'],
            21 => ['day_offset' => -6, 'state' => 'Cancelada'],
            22 => ['day_offset' => -5, 'state' => 'Cancelada'],
            23 => ['day_offset' => -4, 'state' => 'Cancelada'],
            24 => ['day_offset' => -3, 'state' => 'Cancelada'],
            25 => ['day_offset' => -2, 'state' => 'Cancelada'],
            26 => ['day_offset' => -5, 'state' => 'Finalizada'],
            27 => ['day_offset' => -8, 'state' => 'Finalizada'],
            28 => ['day_offset' => -8, 'state' => 'Finalizada'],
            29 => ['day_offset' => -8, 'state' => 'Finalizada'],
            30 => ['day_offset' => -4, 'state' => 'Finalizada'],
            31 => ['day_offset' => -4, 'state' => 'Finalizada'],
            32 => ['day_offset' => -7, 'state' => 'Finalizada'],
            33 => ['day_offset' => -7, 'state' => 'Finalizada'],
            34 => ['day_offset' => -3, 'state' => 'Finalizada'],
            35 => ['day_offset' => -3, 'state' => 'Finalizada'],
            36 => ['day_offset' => -3, 'state' => 'Finalizada'],
            37 => ['day_offset' => -6, 'state' => 'Finalizada'],
            38 => ['day_offset' => -2, 'state' => 'Finalizada'],
            39 => ['day_offset' => -2, 'state' => 'Finalizada'],
            40 => ['day_offset' => -2, 'state' => 'Finalizada'],
            41 => ['day_offset' => -5, 'state' => 'Finalizada'],
            42 => ['day_offset' => 0, 'state' => 'Finalizada'],
            43 => ['day_offset' => 0, 'state' => 'Finalizada'],
            44 => ['day_offset' => 0, 'state' => 'Finalizada'],
            45 => ['day_offset' => -1, 'state' => 'Finalizada'],
            46 => ['day_offset' => 1, 'state' => 'Aceptada'],
            47 => ['day_offset' => 2, 'state' => 'Aceptada'],
            48 => ['day_offset' => 3, 'state' => 'Aceptada'],
            49 => ['day_offset' => 4, 'state' => 'Aceptada'],
            50 => ['day_offset' => 5, 'state' => 'Aceptada'],
            51 => ['day_offset' => 5, 'state' => 'Aceptada'],
        ];
    }
};
