<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MantenimientoDroneRequest;
use App\Models\Drone;
use App\Models\MantenimientoDrone;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MantenimientoDroneController extends Controller
{
    public function index(): View
    {
        return view('admin.mantenimientos.index', [
            'mantenimientos' => MantenimientoDrone::with('drone')->latest('programado_en')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.mantenimientos.form', [
            'mantenimiento' => new MantenimientoDrone(['programado_en' => now()]),
            'drones' => Drone::all(),
        ]);
    }

    public function store(MantenimientoDroneRequest $request): RedirectResponse
    {
        MantenimientoDrone::create($request->validated());
        Drone::whereKey($request->validated('drone_id'))->update(['estado' => 'mantenimiento']);

        return redirect()->route('admin.mantenimientos.index')->with('status', 'Mantenimiento creado.');
    }

    public function edit(MantenimientoDrone $mantenimiento): View
    {
        return view('admin.mantenimientos.form', ['mantenimiento' => $mantenimiento, 'drones' => Drone::all()]);
    }

    public function update(MantenimientoDroneRequest $request, MantenimientoDrone $mantenimiento): RedirectResponse
    {
        $mantenimiento->update($request->validated());

        return redirect()->route('admin.mantenimientos.index')->with('status', 'Mantenimiento actualizado.');
    }

    public function destroy(MantenimientoDrone $mantenimiento): RedirectResponse
    {
        $mantenimiento->delete();

        return back()->with('status', 'Mantenimiento eliminado.');
    }
}
