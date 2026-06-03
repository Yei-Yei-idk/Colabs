<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'reserva';

    protected $primaryKey = 'reserva_id';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'espacio_id',
        'rsva_fecha',
        'rsva_hora_inicio',
        'rsva_hora_fin',
        'rsva_estado',
        'rsva_descripcion',
        'rsva_num_invitados',
    ];

    public function espacio()
    {
        return $this->belongsTo(Espacio::class, 'espacio_id', 'espacio_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Busca y marca como 'Finalizada' toda reserva aceptada/activa cuya fecha/hora ya pasó.
     *
     * @return int Cantidad de reservas actualizadas.
     */
    public static function actualizarVencidas(): int
    {
        $ahora = Carbon::now(); // Respeta el timezone definido en config/app.php

        $reservas = self::query()->whereIn('rsva_estado', ['activa', 'Activa', 'aceptada', 'Aceptada'])
            ->where(function ($q) use ($ahora) {
                $q->whereDate('rsva_fecha', '<', $ahora->toDateString())
                    ->orWhere(function ($q2) use ($ahora) {
                        $q2->whereDate('rsva_fecha', $ahora->toDateString())
                            ->whereTime('rsva_hora_fin', '<=', $ahora->toTimeString());
                    });
            })
            ->get();

        foreach ($reservas as $res) {
            $res->rsva_estado = 'Finalizada';
            $res->save(); // Dispara el Observer (email)
        }

        return $reservas->count();
    }
}
