<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';

    public const ESTADOS = ['pendiente', 'aprobado', 'rechazado'];

    protected $fillable = ['pedido_id', 'estado', 'metodo', 'referencia_externa', 'monto', 'pagado_en'];

    protected function casts(): array
    {
        return ['pagado_en' => 'datetime'];
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}
