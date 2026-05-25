<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudProveedor extends Model
{
    public const ESTADOS = ['pendiente', 'aprobada', 'rechazada'];

    protected $table = 'solicitud_proveedores';

    protected $fillable = ['user_id', 'proveedor_id', 'estado', 'mensaje', 'respuesta_admin', 'revisada_en'];

    protected function casts(): array
    {
        return ['revisada_en' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }
}
