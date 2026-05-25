<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = ['user_id', 'nombre', 'contacto', 'email', 'telefono', 'direccion', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    public function perfil()
    {
        return $this->hasOne(ProveedorPerfil::class);
    }

    public function solicitudes()
    {
        return $this->hasMany(SolicitudProveedor::class);
    }
}
