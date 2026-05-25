<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PerfilController extends Controller
{
    public function show(Request $request): View
    {
        return view('cliente.perfil', [
            'user' => $request->user(),
            'pedidosCount' => $request->user()->pedidos()->count(),
            'direccionesCount' => $request->user()->direcciones()->count(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$request->user()->id],
            'telefono' => ['nullable', 'string', 'max:50', 'regex:/^[0-9+()\\s-]{7,50}$/'],
        ]);

        $request->user()->update($data);

        return back()->with('status', 'Informacion del cliente actualizada.');
    }
}
