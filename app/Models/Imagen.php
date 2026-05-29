<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imagen extends Model
{
    protected $table = 'imagenes';

    protected $primaryKey = 'img_id';

    public $timestamps = false;

    protected $fillable = [
        'espacio_id',
        'foto',
    ];

    public function espacio()
    {
        return $this->belongsTo(Espacio::class, 'espacio_id', 'espacio_id');
    }
}
