<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    protected $table = 'carritos';

    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CarritoItem::class);
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'carrito_items')->withPivot('cantidad')->withTimestamps();
    }
}
