<?php

namespace Tests\Feature;

use App\Models\Factura;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacturaAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_logistica_no_puede_ver_facturas(): void
    {
        $cliente = User::factory()->create(['role' => 'cliente']);
        $logistica = User::factory()->create(['role' => 'personal_logistico']);
        $pedido = Pedido::create([
            'user_id' => $cliente->id,
            'tipo_entrega' => 'estacion',
            'peso_total_kg' => 1,
            'total' => 65000,
        ]);
        $factura = Factura::create([
            'pedido_id' => $pedido->id,
            'numero' => 'FAC-TEST',
            'subtotal' => 65000,
            'impuestos' => 0,
            'total' => 65000,
            'emitida_en' => now(),
        ]);

        $this->actingAs($logistica)->get(route('facturas.index'))->assertForbidden();
        $this->actingAs($logistica)->get(route('facturas.show', $factura))->assertForbidden();
    }

    public function test_cliente_y_admin_pueden_ver_facturas(): void
    {
        $cliente = User::factory()->create(['role' => 'cliente']);
        $admin = User::factory()->create(['role' => 'administrador']);
        $pedido = Pedido::create([
            'user_id' => $cliente->id,
            'tipo_entrega' => 'estacion',
            'peso_total_kg' => 1,
            'total' => 65000,
        ]);
        $factura = Factura::create([
            'pedido_id' => $pedido->id,
            'numero' => 'FAC-TEST',
            'subtotal' => 65000,
            'impuestos' => 0,
            'total' => 65000,
            'emitida_en' => now(),
        ]);

        $this->actingAs($cliente)->get(route('facturas.show', $factura))->assertOk();
        $this->actingAs($admin)->get(route('facturas.index'))->assertOk();
    }
}
