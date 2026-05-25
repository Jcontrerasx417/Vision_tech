<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    protected $table = 'entregas';

    public const ESTADOS = ['asignada', 'en_camino', 'en_estacion', 'domicilio_pendiente', 'finalizada'];

    protected $fillable = ['pedido_id', 'drone_id', 'estacion_entrega_id', 'estado', 'asignada_en', 'finalizada_en'];

    protected function casts(): array
    {
        return ['asignada_en' => 'datetime', 'finalizada_en' => 'datetime'];
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function drone()
    {
        return $this->belongsTo(Drone::class);
    }

    public function trackingPoints()
    {
        return $this->hasMany(TrackingPoint::class);
    }
}
