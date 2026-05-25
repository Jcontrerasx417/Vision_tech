<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Drone extends Model
{
    protected $table = 'drones';

    public const ESTADOS = ['disponible', 'en_vuelo', 'mantenimiento', 'fuera_servicio'];

    protected $fillable = ['codigo', 'modelo', 'capacidad_kg', 'bateria', 'estado', 'estacion_entrega_id'];

    public function estacionEntrega()
    {
        return $this->belongsTo(EstacionEntrega::class);
    }

    public function entregas()
    {
        return $this->hasMany(Entrega::class);
    }

    public function mantenimientos()
    {
        return $this->hasMany(MantenimientoDrone::class);
    }
}
