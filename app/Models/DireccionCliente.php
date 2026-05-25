<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DireccionCliente extends Model
{
    protected $table = 'direccion_clientes';

    protected $fillable = ['user_id', 'nombre', 'direccion', 'latitud', 'longitud', 'principal'];

    protected function casts(): array
    {
        return ['principal' => 'boolean'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
