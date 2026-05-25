<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Factura;
use App\Models\Notificacion;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PagoApiController extends Controller
{
    public function simular(Request $request, Pedido $pedido)
    {
        abort_unless($pedido->user_id === $request->user()->id || $request->user()->hasRole('administrador'), 403);

        $pago = $pedido->pago()->updateOrCreate(
            ['pedido_id' => $pedido->id],
            ['estado' => 'aprobado', 'metodo' => 'simulado', 'referencia_externa' => 'API-SIM-'.$pedido->id, 'monto' => $pedido->total, 'pagado_en' => now()]
        );
        $pedido->update(['estado' => 'pagado']);
        $factura = Factura::updateOrCreate(
            ['pedido_id' => $pedido->id],
            ['numero' => 'FAC-'.now()->format('Ymd').'-'.$pedido->id, 'subtotal' => $pedido->total, 'impuestos' => 0, 'total' => $pedido->total, 'emitida_en' => now()]
        );
        Notificacion::create(['user_id' => $pedido->user_id, 'pedido_id' => $pedido->id, 'titulo' => 'Pago aprobado', 'mensaje' => 'Pago simulado aprobado por API.']);

        return response()->json(['pago' => $pago, 'factura' => $factura]);
    }
}
