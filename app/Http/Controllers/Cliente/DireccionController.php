<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\DireccionCliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DireccionController extends Controller
{
    public function index(Request $request): View
    {
        return view('cliente.direcciones.index', [
            'direcciones' => $request->user()->direcciones()->latest()->get(),
            'direccion' => new DireccionCliente(['latitud' => 7.1253930, 'longitud' => -73.1198040]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['principal'] = $request->boolean('principal');
        if ($data['principal']) {
            $request->user()->direcciones()->update(['principal' => false]);
        }
        $request->user()->direcciones()->create($data);

        return back()->with('status', 'Direccion guardada.');
    }

    public function destroy(Request $request, DireccionCliente $direccion): RedirectResponse
    {
        abort_unless($direccion->user_id === $request->user()->id, 403);
        $direccion->delete();

        return back()->with('status', 'Direccion eliminada.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'direccion' => ['required', 'string', 'max:255'],
            'latitud' => ['required', 'numeric', 'between:-90,90'],
            'longitud' => ['required', 'numeric', 'between:-180,180'],
            'principal' => ['nullable', 'boolean'],
        ]);
    }
}
