<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';

    public const ESTADOS = ['pendiente', 'en_preparacion', 'pagado', 'enviado', 'proximo_a_llegar', 'entregado', 'cancelado'];
    public const ESTADOS_DESPACHO = ['en_preparacion', 'enviado', 'proximo_a_llegar', 'entregado'];

    protected $fillable = ['user_id', 'estado', 'tipo_entrega', 'estacion_entrega_id', 'direccion_cliente_id', 'direccion_entrega', 'destino_latitud', 'destino_longitud', 'peso_total_kg', 'total'];

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

    public function direccionCliente()
    {
        return $this->belongsTo(DireccionCliente::class);
    }

    public function estaPagado(): bool
    {
        if ($this->relationLoaded('pago')) {
            return $this->pago?->estado === 'aprobado';
        }

        return $this->pago()->where('estado', 'aprobado')->exists();
    }

    public function estadoRequierePago(string $estado): bool
    {
        return in_array($estado, self::ESTADOS_DESPACHO, true);
    }
}
