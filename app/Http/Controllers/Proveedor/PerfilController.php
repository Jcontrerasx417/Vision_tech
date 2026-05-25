<?php

namespace App\Http\Controllers\Proveedor;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PerfilController extends Controller
{
    public function edit(Request $request): View
    {
        $proveedor = $this->proveedor($request);

        return view('proveedor.perfil.edit', [
            'proveedor' => $proveedor,
            'perfil' => $proveedor->perfil,
            'solicitud' => $proveedor->solicitudes()->latest()->first(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $proveedor = $this->proveedor($request);
        $data = $request->validate([
            'nombre_comercial' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'nit' => ['nullable', 'string', 'max:80'],
            'telefono' => ['nullable', 'string', 'max:80'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'mensaje' => ['nullable', 'string'],
        ]);

        $proveedor->update([
            'nombre' => $data['nombre_comercial'],
            'telefono' => $data['telefono'] ?? $proveedor->telefono,
            'direccion' => $data['direccion'] ?? $proveedor->direccion,
        ]);

        $proveedor->perfil()->updateOrCreate(
            ['proveedor_id' => $proveedor->id],
            $data + ['user_id' => $request->user()->id, 'estado' => 'pendiente']
        );

        $proveedor->solicitudes()->create([
            'user_id' => $request->user()->id,
            'estado' => 'pendiente',
            'mensaje' => $data['mensaje'] ?? 'Solicitud de aprobacion de proveedor.',
        ]);

        return back()->with('status', 'Perfil enviado a revision del administrador.');
    }

    private function proveedor(Request $request): Proveedor
    {
        return Proveedor::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['nombre' => $request->user()->name, 'contacto' => $request->user()->name, 'email' => $request->user()->email]
        );
    }
}
