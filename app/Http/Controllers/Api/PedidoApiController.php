<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PedidoApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pedido::with('items.producto', 'pago', 'factura', 'entrega.drone');
        if ($request->user()->role === 'cliente') {
            $query->where('user_id', $request->user()->id);
        }

        return $query->latest()->paginate(10);
    }

    public function show(Request $request, Pedido $pedido)
    {
        abort_unless($pedido->user_id === $request->user()->id || $request->user()->hasRole('administrador', 'personal_logistico'), 403);
        return $pedido->load('items.producto', 'pago', 'factura', 'entrega.drone', 'entrega.trackingPoints');
    }

    public function updateEstado(Request $request, Pedido $pedido)
    {
        $data = $request->validate(['estado' => ['required', Rule::in(Pedido::ESTADOS)]]);
        if ($data['estado'] === 'pagado' && ! $pedido->estaPagado()) {
            abort(422, 'El estado pagado solo se puede usar cuando el pago este aprobado.');
        }

        if ($pedido->estadoRequierePago($data['estado']) && ! $pedido->estaPagado()) {
            abort(422, 'El pedido debe estar pagado antes de prepararlo, enviarlo o entregarlo.');
        }

        $pedido->update($data);
        Notificacion::create(['user_id' => $pedido->user_id, 'pedido_id' => $pedido->id, 'titulo' => 'Estado actualizado', 'mensaje' => "Tu pedido ahora está en estado {$pedido->estado}."]);

        return $pedido->fresh();
    }
}
