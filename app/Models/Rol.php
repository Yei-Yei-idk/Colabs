<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'rol';
    protected $primaryKey = 'rol_id';
    public $timestamps = false;

    protected $fillable = ['rol_nombre'];

    public function usuarios()
    {
        return $this->hasMany(User::class, 'rol_id', 'rol_id');
    }
}
