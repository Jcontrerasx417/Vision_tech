<?php

namespace App\Services;

use App\Models\Factura;
use App\Models\Notificacion;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PedidoPagoService
{
    public function __construct(private readonly PedidoDespachoService $despachoService)
    {
    }

    public function aprobar(Pedido $pedido, array $pagoData, string $mensajeNotificacion): bool
    {
        DB::transaction(function () use ($pedido, $pagoData, $mensajeNotificacion) {
            $pedido->pago()->updateOrCreate(
                ['pedido_id' => $pedido->id],
                array_merge([
                    'estado' => 'aprobado',
                    'monto' => $pedido->total,
                    'pagado_en' => now(),
                ], $pagoData)
            );

            $pedido->update(['estado' => 'pagado']);
            Factura::updateOrCreate(
                ['pedido_id' => $pedido->id],
                [
                    'numero' => 'FAC-'.now()->format('Ymd').'-'.$pedido->id,
                    'subtotal' => $pedido->total,
                    'impuestos' => 0,
                    'total' => $pedido->total,
                    'emitida_en' => now(),
                ]
            );
        });

        $pedido->refresh();
        $droneAsignado = $pedido->entrega()->exists();

        if (! $droneAsignado) {
            try {
                $this->despachoService->asignarDroneDisponible($pedido);
                $droneAsignado = true;
            } catch (\Throwable $exception) {
                Log::warning('Pago aprobado sin drone disponible', [
                    'pedido_id' => $pedido->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        Notificacion::create([
            'user_id' => $pedido->user_id,
            'pedido_id' => $pedido->id,
            'titulo' => 'Pago aprobado',
            'mensaje' => $droneAsignado
                ? $mensajeNotificacion
                : 'El pago fue aprobado y la factura fue generada. La asignacion del drone queda pendiente por disponibilidad.',
        ]);

        return $droneAsignado;
    }
}
