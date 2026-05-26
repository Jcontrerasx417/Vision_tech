<?php

namespace Tests\Unit;

use App\Models\Pedido;
use App\Models\User;
use App\Services\MercadoPagoService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MercadoPagoServiceTest extends TestCase
{
    public function test_usa_init_point_por_defecto_para_evitar_bucles_del_sandbox_checkout(): void
    {
        config(['services.mercado_pago.use_sandbox_url' => false]);

        $service = app(MercadoPagoService::class);

        $this->assertSame('https://www.mercadopago.com.co/checkout', $service->checkoutUrl([
            'init_point' => 'https://www.mercadopago.com.co/checkout',
            'sandbox_init_point' => 'https://sandbox.mercadopago.com.co/checkout',
        ]));
    }

    public function test_no_envia_payer_por_defecto_para_evitar_mezclar_usuarios_reales_y_prueba(): void
    {
        config([
            'services.mercado_pago.access_token' => 'TEST-token',
            'services.mercado_pago.prefill_payer' => false,
            'services.mercado_pago.verify_ssl' => false,
        ]);

        Http::fake([
            'api.mercadopago.com/checkout/preferences' => Http::response([
                'id' => 'pref-test',
                'sandbox_init_point' => 'https://sandbox.mercadopago.com.co/checkout',
            ]),
        ]);

        $pedido = new Pedido(['total' => 65000]);
        $pedido->id = 10;
        $pedido->exists = true;
        $pedido->setRelation('user', new User([
            'name' => 'Cliente Demo',
            'email' => 'cliente@demo.com',
        ]));

        app(MercadoPagoService::class)->crearPreferencia($pedido);

        Http::assertSent(function ($request) {
            return ! array_key_exists('payer', $request->data());
        });
    }
}
