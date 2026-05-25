<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $table = 'facturas';

    protected $fillable = ['pedido_id', 'numero', 'subtotal', 'impuestos', 'total', 'emitida_en'];

    protected function casts(): array
    {
        return ['emitida_en' => 'datetime'];
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}
