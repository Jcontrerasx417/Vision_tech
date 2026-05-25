<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProveedorPerfil extends Model
{
    protected $table = 'proveedor_perfiles';

    protected $fillable = ['user_id', 'proveedor_id', 'nombre_comercial', 'descripcion', 'nit', 'telefono', 'direccion', 'estado'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }
}
