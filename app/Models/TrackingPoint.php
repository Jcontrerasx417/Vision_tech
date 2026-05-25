<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingPoint extends Model
{
    protected $table = 'tracking_points';

    protected $fillable = ['entrega_id', 'latitud', 'longitud', 'altitud_m', 'registrado_en'];

    protected function casts(): array
    {
        return ['registrado_en' => 'datetime'];
    }

    public function entrega()
    {
        return $this->belongsTo(Entrega::class);
    }
}
