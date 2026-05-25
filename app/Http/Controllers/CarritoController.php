<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarritoController extends Controller
{
    public function index(Request $request): View
    {
        return view('carrito.index', ['carrito' => $this->carrito($request)->load('items.producto')]);
    }

    public function add(Request $request, Producto $producto): RedirectResponse
    {
        $data = $request->validate(['cantidad' => ['required', 'integer', 'min:1']]);
        $carrito = $this->carrito($request);
        $item = $carrito->items()->firstOrNew(['producto_id' => $producto->id]);
        $item->cantidad = min(($item->cantidad ?? 0) + $data['cantidad'], $producto->stock);
        $item->save();

        return redirect()->route('carrito.index')->with('status', 'Producto agregado al carrito.');
    }

    public function remove(Request $request, int $item): RedirectResponse
    {
        $this->carrito($request)->items()->whereKey($item)->delete();
        return back()->with('status', 'Producto retirado del carrito.');
    }

    public function update(Request $request, int $item): RedirectResponse
    {
        $data = $request->validate(['cantidad' => ['required', 'integer', 'min:1']]);
        $carritoItem = $this->carrito($request)->items()->with('producto')->whereKey($item)->firstOrFail();
        $carritoItem->update(['cantidad' => min($data['cantidad'], $carritoItem->producto->stock)]);

        return back()->with('status', 'Cantidad actualizada.');
    }

    private function carrito(Request $request): Carrito
    {
        return Carrito::firstOrCreate(['user_id' => $request->user()->id]);
    }
}
