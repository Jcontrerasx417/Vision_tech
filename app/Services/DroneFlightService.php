<?php

namespace App\Services;

use App\Models\Entrega;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;

class DroneFlightService
{
    public function simularVueloPedido(Pedido $pedido): void
    {
        $pedido->loadMissing('entrega.drone.estacionEntrega', 'estacionEntrega', 'direccionCliente');
        $entrega = $pedido->entrega;

        if (! $pedido->estaPagado()) {
            throw new \RuntimeException('El pedido debe estar pagado antes de simular el vuelo.');
        }

        if (! $entrega?->drone) {
            throw new \RuntimeException('El pedido no tiene un drone asignado.');
        }

        DB::transaction(function () use ($pedido, $entrega) {
            $origin = $this->originPoint($entrega);
            $destination = $this->destinationPoint($pedido);
            $distance = $this->distanceKm($origin['lat'], $origin['lng'], $destination['lat'], $destination['lng']);
            $batteryCost = $this->batteryCost($distance, (float) $pedido->peso_total_kg, false);

            $this->consumeBattery($entrega, $batteryCost);
            $entrega->trackingPoints()->delete();

            foreach ($this->routePoints($origin, $destination, 7) as $index => $point) {
                $entrega->trackingPoints()->create([
                    'latitud' => $point['lat'],
                    'longitud' => $point['lng'],
                    'altitud_m' => $point['alt'],
                    'registrado_en' => now()->addSeconds($index * 45),
                ]);
            }

            $entrega->update(['estado' => 'en_camino']);
            $pedido->update(['estado' => 'enviado']);
            $entrega->drone->update(['estado' => 'en_vuelo']);
        });
    }

    public function finalizarYRetornar(Entrega $entrega): void
    {
        $entrega->loadMissing('pedido.estacionEntrega', 'pedido.direccionCliente', 'drone.estacionEntrega');
        $pedido = $entrega->pedido;

        if (! $pedido->estaPagado()) {
            throw new \RuntimeException('No se puede finalizar una entrega sin pago aprobado.');
        }

        if (! $entrega->drone) {
            throw new \RuntimeException('La entrega no tiene drone asignado.');
        }

        DB::transaction(function () use ($entrega, $pedido) {
            $destination = $this->destinationPoint($pedido);
            $base = $this->originPoint($entrega);
            $returnDistance = $this->distanceKm($destination['lat'], $destination['lng'], $base['lat'], $base['lng']);
            $this->consumeBattery($entrega, $this->batteryCost($returnDistance, 0, true));

            $entrega->trackingPoints()->create([
                'latitud' => $destination['lat'],
                'longitud' => $destination['lng'],
                'altitud_m' => 0,
                'registrado_en' => now(),
            ]);

            $entrega->update([
                'estado' => 'finalizada',
                'finalizada_en' => now(),
            ]);
            $pedido->update(['estado' => 'entregado']);

            $drone = $entrega->drone->fresh();
            $drone->update([
                'estado' => $drone->bateria <= 20 ? 'mantenimiento' : 'disponible',
            ]);
        });
    }

    private function routePoints(array $origin, array $destination, int $count): array
    {
        $points = [];
        for ($i = 0; $i < $count; $i++) {
            $progress = $count === 1 ? 1 : $i / ($count - 1);
            $curve = sin($progress * pi()) * 0.0012;
            $points[] = [
                'lat' => $origin['lat'] + (($destination['lat'] - $origin['lat']) * $progress) + $curve,
                'lng' => $origin['lng'] + (($destination['lng'] - $origin['lng']) * $progress) - ($curve / 2),
                'alt' => $i === 0 || $i === $count - 1 ? 0 : 70 + (sin($progress * pi()) * 35),
            ];
        }

        return $points;
    }

    private function originPoint(Entrega $entrega): array
    {
        $station = $entrega->drone?->estacionEntrega ?? $entrega->pedido->estacionEntrega;

        return [
            'lat' => (float) ($station?->latitud ?? 7.1150670),
            'lng' => (float) ($station?->longitud ?? -73.1086300),
        ];
    }

    private function destinationPoint(Pedido $pedido): array
    {
        if ($pedido->tipo_entrega === 'domicilio') {
            return [
                'lat' => (float) ($pedido->destino_latitud ?? $pedido->direccionCliente?->latitud ?? 7.1253930),
                'lng' => (float) ($pedido->destino_longitud ?? $pedido->direccionCliente?->longitud ?? -73.1198040),
            ];
        }

        return [
            'lat' => (float) ($pedido->estacionEntrega?->latitud ?? 7.1253930),
            'lng' => (float) ($pedido->estacionEntrega?->longitud ?? -73.1198040),
        ];
    }

    private function batteryCost(float $distanceKm, float $payloadKg, bool $returnToBase): int
    {
        $cost = ($distanceKm * 4.2) + ($payloadKg * 3.5) + ($returnToBase ? 4 : 7);

        return max(3, min(45, (int) ceil($cost)));
    }

    private function consumeBattery(Entrega $entrega, int $cost): void
    {
        $drone = $entrega->drone;
        $drone->update(['bateria' => max(0, $drone->bateria - $cost)]);
    }

    private function distanceKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;
        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);
        $a = sin($latDelta / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lngDelta / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
