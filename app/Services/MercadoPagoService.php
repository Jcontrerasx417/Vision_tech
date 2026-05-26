<?php

namespace App\Services;

use App\Models\Pedido;
use Illuminate\Support\Facades\Http;

class MercadoPagoService
{
    private const API_BASE = 'https://api.mercadopago.com';

    public function crearPreferencia(Pedido $pedido): array
    {
        $externalReference = $this->externalReference($pedido);

        $successUrl = route('mercado-pago.retorno', ['pedido' => $pedido->id]);
        $webhookUrl = route('mercado-pago.webhook');
        $payload = [
            'items' => [
                [
                    'id' => (string) $pedido->id,
                        'title' => 'Pedido VisionTech #'.$pedido->id,
                        'description' => 'Pago de pedido para entrega por dron',
                        'quantity' => 1,
                        'currency_id' => config('services.mercado_pago.currency', 'COP'),
                        'unit_price' => (float) $pedido->total,
                    ],
            ],
            'external_reference' => $externalReference,
            'back_urls' => [
                'success' => $successUrl,
                'failure' => route('mercado-pago.retorno', ['pedido' => $pedido->id]),
                'pending' => route('mercado-pago.retorno', ['pedido' => $pedido->id]),
            ],
        ];

        if (config('services.mercado_pago.prefill_payer', false)) {
            $payload['payer'] = $this->payerPayload($pedido);
        }

        if ($this->isPublicUrl($webhookUrl)) {
            $payload['notification_url'] = $webhookUrl;
        }

        if ($this->canAutoReturn($successUrl)) {
            $payload['auto_return'] = 'approved';
        }

        $response = $this->http()->post(self::API_BASE.'/checkout/preferences', $payload);

        if ($response->failed()) {
            throw new \RuntimeException('Mercado Pago rechazo la preferencia: '.$response->body());
        }

        return $response->json();
    }

    public function checkoutUrl(array $preference): ?string
    {
        if (config('services.mercado_pago.use_sandbox_url', false)) {
            return $preference['sandbox_init_point'] ?? $preference['init_point'] ?? null;
        }

        return $preference['init_point'] ?? $preference['sandbox_init_point'] ?? null;
    }

    public function obtenerPago(string $paymentId): array
    {
        $response = $this->http()->get(self::API_BASE.'/v1/payments/'.$paymentId);

        if ($response->failed()) {
            throw new \RuntimeException('No fue posible consultar el pago en Mercado Pago: '.$response->body());
        }

        return $response->json();
    }

    public function externalReference(Pedido $pedido): string
    {
        return 'pedido-'.$pedido->id;
    }

    private function accessToken(): string
    {
        $token = config('services.mercado_pago.access_token');
        if (! $token) {
            throw new \RuntimeException('Falta configurar MERCADO_PAGO_ACCESS_TOKEN.');
        }

        return $token;
    }

    private function http()
    {
        $request = Http::withToken($this->accessToken())->acceptJson();

        if (! config('services.mercado_pago.verify_ssl', true)) {
            $request = $request->withoutVerifying();
        }

        return $request;
    }

    private function payerPayload(Pedido $pedido): array
    {
        $payer = [
            'name' => $pedido->user->name,
            'email' => $pedido->user->email,
        ];

        if ($pedido->user->telefono) {
            $payer['phone'] = ['number' => $pedido->user->telefono];
        }

        return $payer;
    }

    private function canAutoReturn(string $url): bool
    {
        return $this->isPublicUrl($url);
    }

    private function isPublicUrl(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);

        return ! in_array($host, ['localhost', '127.0.0.1'], true)
            && ! str_ends_with((string) $host, '.test')
            && ! str_ends_with((string) $host, '.local');
    }
}
