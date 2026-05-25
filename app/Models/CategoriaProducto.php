<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaProducto extends Model
{
    protected $table = 'categoria_productos';

    protected $fillable = ['nombre', 'descripcion', 'activa'];

    protected function casts(): array
    {
        return ['activa' => 'boolean'];
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
