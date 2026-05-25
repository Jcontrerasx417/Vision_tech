<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';

    public const ESTADOS = ['pendiente', 'en_preparacion', 'pagado', 'enviado', 'proximo_a_llegar', 'entregado', 'cancelado'];

    protected $fillable = ['user_id', 'estado', 'tipo_entrega', 'estacion_entrega_id', 'direccion_entrega', 'destino_latitud', 'destino_longitud', 'peso_total_kg', 'total'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function pago()
    {
        return $this->hasOne(Pago::class);
    }

    public function factura()
    {
        return $this->hasOne(Factura::class);
    }

    public function entrega()
    {
        return $this->hasOne(Entrega::class);
    }

    public function estacionEntrega()
    {
        return $this->belongsTo(EstacionEntrega::class);
    }
}
