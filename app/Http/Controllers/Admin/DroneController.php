<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DroneRequest;
use App\Models\Drone;
use App\Models\EstacionEntrega;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DroneController extends Controller
{
    public function index(): View
    {
        return view('admin.drones.index', ['drones' => Drone::with('estacionEntrega')->paginate(15)]);
    }

    public function create(): View
    {
        return view('admin.drones.form', ['drone' => new Drone(['estado' => 'disponible', 'bateria' => 100]), 'estaciones' => EstacionEntrega::all()]);
    }

    public function store(DroneRequest $request): RedirectResponse
    {
        Drone::create($request->validated());
        return redirect()->route('admin.drones.index')->with('status', 'Drone creado.');
    }

    public function edit(Drone $drone): View
    {
        return view('admin.drones.form', ['drone' => $drone, 'estaciones' => EstacionEntrega::all()]);
    }

    public function update(DroneRequest $request, Drone $drone): RedirectResponse
    {
        $drone->update($request->validated());
        return redirect()->route('admin.drones.index')->with('status', 'Drone actualizado.');
    }

    public function destroy(Drone $drone): RedirectResponse
    {
        $drone->delete();
        return back()->with('status', 'Drone eliminado.');
    }
}
