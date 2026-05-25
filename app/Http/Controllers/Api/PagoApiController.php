<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Services\PedidoPagoService;
use Illuminate\Http\Request;

class PagoApiController extends Controller
{
    public function __construct(private readonly PedidoPagoService $pagoService)
    {
    }

    public function simular(Request $request, Pedido $pedido)
    {
        abort_unless($pedido->user_id === $request->user()->id || $request->user()->hasRole('administrador'), 403);

        $this->pagoService->aprobar($pedido, [
            'metodo' => 'simulado',
            'proveedor_pago' => 'simulado',
            'referencia_externa' => 'API-SIM-'.$pedido->id,
        ], 'Pago simulado aprobado por API. Drone asignado para entrega.');

        $pedido->refresh()->load('pago', 'factura');

        return response()->json(['pago' => $pedido->pago, 'factura' => $pedido->factura]);
    }
}
