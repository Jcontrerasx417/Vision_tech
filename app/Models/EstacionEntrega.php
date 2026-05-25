<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstacionEntrega extends Model
{
    protected $table = 'estacion_entregas';

    protected $fillable = ['nombre', 'direccion', 'latitud', 'longitud', 'activa'];

    protected function casts(): array
    {
        return ['activa' => 'boolean'];
    }

    public function drones()
    {
        return $this->hasMany(Drone::class);
    }
}
