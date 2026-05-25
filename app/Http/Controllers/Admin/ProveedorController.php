<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProveedorRequest;
use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProveedorController extends Controller
{
    public function index(): View
    {
        return view('admin.proveedores.index', ['proveedores' => Proveedor::paginate(15)]);
    }

    public function create(): View
    {
        return view('admin.proveedores.form', ['proveedor' => new Proveedor()]);
    }

    public function store(ProveedorRequest $request): RedirectResponse
    {
        Proveedor::create($request->validated());
        return redirect()->route('admin.proveedores.index')->with('status', 'Proveedor creado.');
    }

    public function edit(Proveedor $proveedor): View
    {
        return view('admin.proveedores.form', ['proveedor' => $proveedor]);
    }

    public function update(ProveedorRequest $request, Proveedor $proveedor): RedirectResponse
    {
        $proveedor->update($request->validated());
        return redirect()->route('admin.proveedores.index')->with('status', 'Proveedor actualizado.');
    }

    public function destroy(Proveedor $proveedor): RedirectResponse
    {
        $proveedor->delete();
        return back()->with('status', 'Proveedor eliminado.');
    }
}
