<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EstacionEntregaRequest;
use App\Models\EstacionEntrega;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EstacionEntregaController extends Controller
{
    public function index(): View
    {
        return view('admin.estaciones.index', ['estaciones' => EstacionEntrega::paginate(15)]);
    }

    public function create(): View
    {
        return view('admin.estaciones.form', ['estacion' => new EstacionEntrega(['activa' => true])]);
    }

    public function store(EstacionEntregaRequest $request): RedirectResponse
    {
        EstacionEntrega::create($request->validated() + ['activa' => $request->boolean('activa')]);
        return redirect()->route('admin.estaciones.index')->with('status', 'Estación creada.');
    }

    public function edit(EstacionEntrega $estacion): View
    {
        return view('admin.estaciones.form', ['estacion' => $estacion]);
    }

    public function update(EstacionEntregaRequest $request, EstacionEntrega $estacion): RedirectResponse
    {
        $estacion->update($request->validated() + ['activa' => $request->boolean('activa')]);
        return redirect()->route('admin.estaciones.index')->with('status', 'Estación actualizada.');
    }

    public function destroy(EstacionEntrega $estacion): RedirectResponse
    {
        $estacion->delete();
        return back()->with('status', 'Estación eliminada.');
    }
}
