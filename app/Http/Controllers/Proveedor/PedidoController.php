<?php

namespace App\Http\Controllers\Proveedor;

use App\Http\Controllers\Controller;
use App\Models\PedidoItem;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PedidoController extends Controller
{
    public function index(Request $request): View
    {
        $proveedor = Proveedor::where('user_id', $request->user()->id)->firstOrFail();

        return view('proveedor.pedidos.index', [
            'proveedor' => $proveedor,
            'items' => PedidoItem::with('pedido.user', 'producto')
                ->whereHas('producto', fn ($query) => $query->where('proveedor_id', $proveedor->id))
                ->latest()
                ->paginate(15),
        ]);
    }
}
