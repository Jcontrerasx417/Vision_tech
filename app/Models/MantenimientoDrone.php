<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MantenimientoDrone extends Model
{
    protected $table = 'mantenimiento_drones';

    protected $fillable = ['drone_id', 'tipo', 'descripcion', 'programado_en', 'realizado_en'];

    protected function casts(): array
    {
        return ['programado_en' => 'datetime', 'realizado_en' => 'datetime'];
    }

    public function drone()
    {
        return $this->belongsTo(Drone::class);
    }
}
