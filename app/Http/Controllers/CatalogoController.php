<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\View\View;

class CatalogoController extends Controller
{
    public function index(): View
    {
        return view('catalogo.index', [
            'productos' => Producto::with('proveedor', 'categoria', 'imagenes')
                ->where('activo', true)
                ->whereIn('estado', ['aprobado', 'activo'])
                ->whereHas('proveedor', fn ($query) => $query->where('activo', true))
                ->paginate(12),
        ]);
    }

    public function show(Producto $producto): View
    {
        abort_unless($producto->estaPublicado() && $producto->proveedor?->activo, 404);

        return view('catalogo.show', [
            'producto' => $producto->load('proveedor', 'categoria', 'imagenes'),
            'relacionados' => Producto::where('activo', true)
                ->whereIn('estado', ['aprobado', 'activo'])
                ->where('id', '!=', $producto->id)
                ->where('proveedor_id', $producto->proveedor_id)
                ->take(3)
                ->with('imagenes')
                ->get(),
        ]);
    }
}
