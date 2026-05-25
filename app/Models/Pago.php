<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';

    public const ESTADOS = ['pendiente', 'aprobado', 'rechazado'];

    protected $fillable = ['pedido_id', 'estado', 'metodo', 'proveedor_pago', 'referencia_externa', 'proveedor_payment_id', 'proveedor_preference_id', 'checkout_url', 'monto', 'pagado_en'];

    protected function casts(): array
    {
        return ['pagado_en' => 'datetime'];
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}
