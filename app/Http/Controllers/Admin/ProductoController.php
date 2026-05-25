<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductoRequest;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductoController extends Controller
{
    public function index(): View
    {
        return view('admin.productos.index', ['productos' => Producto::with('proveedor')->paginate(15)]);
    }

    public function create(): View
    {
        return view('admin.productos.form', ['producto' => new Producto(), 'proveedores' => Proveedor::all()]);
    }

    public function store(ProductoRequest $request): RedirectResponse
    {
        Producto::create($request->validated() + ['activo' => $request->boolean('activo')]);
        return redirect()->route('admin.productos.index')->with('status', 'Producto creado.');
    }

    public function edit(Producto $producto): View
    {
        return view('admin.productos.form', ['producto' => $producto, 'proveedores' => Proveedor::all()]);
    }

    public function update(ProductoRequest $request, Producto $producto): RedirectResponse
    {
        $producto->update($request->validated() + ['activo' => $request->boolean('activo')]);
        return redirect()->route('admin.productos.index')->with('status', 'Producto actualizado.');
    }

    public function destroy(Producto $producto): RedirectResponse
    {
        $producto->delete();
        return back()->with('status', 'Producto eliminado.');
    }
}
