<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entrega;
use App\Models\Pedido;
use Illuminate\Http\Request;

class TrackingApiController extends Controller
{
    public function store(Request $request, Entrega $entrega)
    {
        $data = $request->validate([
            'latitud' => ['required', 'numeric', 'between:-90,90'],
            'longitud' => ['required', 'numeric', 'between:-180,180'],
            'altitud_m' => ['nullable', 'numeric', 'min:0'],
        ]);

        abort_unless($entrega->pedido->estaPagado(), 422, 'No se puede registrar tracking antes de que el pago este aprobado.');

        return $entrega->trackingPoints()->create($data + ['registrado_en' => now()]);
    }

    public function pedido(Request $request, Pedido $pedido)
    {
        abort_unless($pedido->user_id === $request->user()->id || $request->user()->hasRole('administrador', 'personal_logistico'), 403);
        abort_unless($pedido->estaPagado(), 403, 'El tracking estara disponible cuando el pago sea aprobado.');
        return $pedido->load('entrega.trackingPoints')->entrega?->trackingPoints ?? [];
    }
}
