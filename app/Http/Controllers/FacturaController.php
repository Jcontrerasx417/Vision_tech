<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacturaController extends Controller
{
    public function index(Request $request): View
    {
        $query = Factura::with('pedido.user')->latest('emitida_en');

        if ($request->user()->hasRole('cliente')) {
            $query->whereHas('pedido', fn ($pedido) => $pedido->where('user_id', $request->user()->id));
        }

        return view('facturas.index', ['facturas' => $query->paginate(15)]);
    }

    public function show(Request $request, Factura $factura): View
    {
        $factura->load('pedido.user', 'pedido.items.producto', 'pedido.pago', 'pedido.entrega.drone');
        abort_unless(
            $factura->pedido->user_id === $request->user()->id || $request->user()->hasRole('administrador'),
            403
        );

        return view('facturas.show', ['factura' => $factura]);
    }
}
