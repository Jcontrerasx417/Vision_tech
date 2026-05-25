<?php

namespace App\Services;

use App\Models\Drone;
use App\Models\Pedido;

class PedidoDespachoService
{
    public function asignarDroneDisponible(Pedido $pedido): void
    {
        $drone = Drone::where('estado', 'disponible')
            ->where('capacidad_kg', '>=', $pedido->peso_total_kg)
            ->first();

        if (! $drone) {
            throw new \RuntimeException('No hay un drone disponible con capacidad suficiente para este pedido.');
        }

        $this->asignarDrone($pedido, $drone);
    }

    public function asignarDrone(Pedido $pedido, Drone $drone): void
    {
        if (! $pedido->estaPagado()) {
            throw new \RuntimeException('El pedido debe estar pagado antes de asignar o enviar un drone.');
        }

        if ($drone->capacidad_kg < $pedido->peso_total_kg) {
            throw new \RuntimeException('El drone seleccionado no soporta el peso del pedido.');
        }

        $pedido->entrega()->updateOrCreate(
            ['pedido_id' => $pedido->id],
            [
                'drone_id' => $drone->id,
                'estacion_entrega_id' => $pedido->estacion_entrega_id ?? $drone->estacion_entrega_id,
                'estado' => $pedido->tipo_entrega === 'domicilio' ? 'domicilio_pendiente' : 'asignada',
                'asignada_en' => now(),
            ]
        );

        $drone->update(['estado' => 'en_vuelo']);
    }
}
