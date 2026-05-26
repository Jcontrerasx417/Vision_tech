<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Services\MercadoPagoService;
use App\Services\PedidoPagoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MercadoPagoController extends Controller
{
    public function __construct(
        private readonly MercadoPagoService $mercadoPago,
        private readonly PedidoPagoService $pagoService
    ) {
    }

    public function iniciar(Request $request, Pedido $pedido): RedirectResponse
    {
        abort_unless($pedido->user_id === $request->user()->id || $request->user()->hasRole('administrador'), 403);

        if ($pedido->estaPagado()) {
            return redirect()->route('pedidos.show', $pedido)->with('status', 'Este pedido ya esta pagado.');
        }

        try {
            $preference = $this->mercadoPago->crearPreferencia($pedido->load('user'));
            $checkoutUrl = $this->mercadoPago->checkoutUrl($preference);

            if (! $checkoutUrl) {
                throw new \RuntimeException('Mercado Pago no devolvio una URL de checkout.');
            }

            $pedido->pago()->updateOrCreate(
                ['pedido_id' => $pedido->id],
                [
                    'estado' => 'pendiente',
                    'metodo' => 'mercado_pago',
                    'proveedor_pago' => 'mercado_pago',
                    'referencia_externa' => $this->mercadoPago->externalReference($pedido),
                    'proveedor_preference_id' => $preference['id'] ?? null,
                    'checkout_url' => $checkoutUrl,
                    'monto' => $pedido->total,
                ]
            );

            return redirect()->away($checkoutUrl);
        } catch (\Throwable $exception) {
            Log::error('Error iniciando Mercado Pago', [
                'pedido_id' => $pedido->id,
                'error' => $exception->getMessage(),
            ]);

            return back()->withErrors('No fue posible iniciar Mercado Pago. Revisa las credenciales o intenta de nuevo.');
        }
    }

    public function retorno(Request $request): RedirectResponse
    {
        $pedido = Pedido::findOrFail($request->integer('pedido'));

        try {
            $paymentId = $request->query('payment_id') ?: $request->query('collection_id');
            if ($paymentId) {
                $this->sincronizarPago((string) $paymentId);
            }
        } catch (\Throwable $exception) {
            Log::warning('No fue posible sincronizar retorno de Mercado Pago', [
                'pedido_id' => $pedido->id,
                'error' => $exception->getMessage(),
            ]);
        }

        $pedido->refresh();
        $message = $pedido->estaPagado()
            ? 'Pago aprobado por Mercado Pago. Drone asignado para entrega.'
            : 'Volviste de Mercado Pago. El pago aun esta pendiente de confirmacion.';

        return redirect()->route('pedidos.show', $pedido)->with('status', $message);
    }

    public function webhook(Request $request)
    {
        try {
            $paymentId = $this->paymentIdFromWebhook($request);
            if ($paymentId) {
                $this->sincronizarPago($paymentId);
            }
        } catch (\Throwable $exception) {
            Log::error('Error procesando webhook de Mercado Pago', [
                'payload' => $request->all(),
                'error' => $exception->getMessage(),
            ]);
        }

        return response()->json(['received' => true]);
    }

    private function sincronizarPago(string $paymentId): void
    {
        $payment = $this->mercadoPago->obtenerPago($paymentId);
        $externalReference = $payment['external_reference'] ?? null;
        $pedidoId = $this->pedidoIdFromExternalReference($externalReference);

        if (! $pedidoId) {
            throw new \RuntimeException('El pago no trae external_reference valida.');
        }

        $pedido = Pedido::findOrFail($pedidoId);
        $status = $payment['status'] ?? 'pending';

        if ($status === 'approved') {
            $this->pagoService->aprobar($pedido, [
                'metodo' => 'mercado_pago',
                'proveedor_pago' => 'mercado_pago',
                'referencia_externa' => $externalReference,
                'proveedor_payment_id' => (string) ($payment['id'] ?? $paymentId),
                'proveedor_preference_id' => $payment['preference_id'] ?? $pedido->pago?->proveedor_preference_id,
                'checkout_url' => $pedido->pago?->checkout_url,
            ], 'Pago aprobado por Mercado Pago. Factura generada y drone asignado para entrega.');

            return;
        }

        $estado = in_array($status, ['rejected', 'cancelled'], true) ? 'rechazado' : 'pendiente';
        $pedido->pago()->updateOrCreate(
            ['pedido_id' => $pedido->id],
            [
                'estado' => $estado,
                'metodo' => 'mercado_pago',
                'proveedor_pago' => 'mercado_pago',
                'referencia_externa' => $externalReference,
                'proveedor_payment_id' => (string) ($payment['id'] ?? $paymentId),
                'proveedor_preference_id' => $payment['preference_id'] ?? $pedido->pago?->proveedor_preference_id,
                'monto' => $pedido->total,
            ]
        );
    }

    private function paymentIdFromWebhook(Request $request): ?string
    {
        $dataId = data_get($request->all(), 'data.id');
        if ($dataId) {
            return (string) $dataId;
        }

        $resource = $request->input('resource');
        if (is_string($resource) && preg_match('/payments\/(\d+)/', $resource, $matches)) {
            return $matches[1];
        }

        return $request->input('id') ? (string) $request->input('id') : null;
    }

    private function pedidoIdFromExternalReference(?string $externalReference): ?int
    {
        if (! $externalReference || ! preg_match('/^pedido-(\d+)$/', $externalReference, $matches)) {
            return null;
        }

        return (int) $matches[1];
    }
}
