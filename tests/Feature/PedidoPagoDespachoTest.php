<?php

namespace Tests\Feature;

use App\Models\Carrito;
use App\Models\Drone;
use App\Models\EstacionEntrega;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PedidoPagoDespachoTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_drone_solo_se_asigna_despues_de_aprobar_el_pago(): void
    {
        $cliente = User::factory()->create(['role' => 'cliente']);
        $proveedor = Proveedor::create(['nombre' => 'Proveedor prueba']);
        $estacion = EstacionEntrega::create([
            'nombre' => 'Estacion centro',
            'direccion' => 'Calle 1',
            'latitud' => 7.1253930,
            'longitud' => -73.1198040,
            'activa' => true,
        ]);
        $drone = Drone::create([
            'codigo' => 'DRN-TEST',
            'modelo' => 'Ligero',
            'capacidad_kg' => 5,
            'bateria' => 100,
            'estado' => 'disponible',
            'estacion_entrega_id' => $estacion->id,
        ]);
        $producto = Producto::create([
            'proveedor_id' => $proveedor->id,
            'nombre' => 'Producto prueba',
            'precio' => 25000,
            'peso_kg' => 1,
            'stock' => 4,
            'activo' => true,
        ]);

        $carrito = Carrito::create(['user_id' => $cliente->id]);
        $carrito->items()->create(['producto_id' => $producto->id, 'cantidad' => 1]);

        $this->actingAs($cliente)
            ->post(route('pedidos.store'), [
                'tipo_entrega' => 'estacion',
                'estacion_entrega_id' => $estacion->id,
            ])
            ->assertRedirect();

        $pedido = $cliente->pedidos()->firstOrFail();
        $this->assertFalse($pedido->entrega()->exists());
        $this->assertSame('disponible', $drone->fresh()->estado);

        $this->actingAs($cliente)
            ->post(route('pedidos.pagar', $pedido))
            ->assertSessionHas('status');

        $this->assertTrue($pedido->fresh()->estaPagado());
        $this->assertSame($drone->id, $pedido->entrega()->firstOrFail()->drone_id);
        $this->assertSame('en_vuelo', $drone->fresh()->estado);
    }

    public function test_el_pedido_a_domicilio_usa_la_direccion_guardada_como_destino(): void
    {
        $cliente = User::factory()->create(['role' => 'cliente']);
        $proveedor = Proveedor::create(['nombre' => 'Proveedor prueba']);
        $producto = Producto::create([
            'proveedor_id' => $proveedor->id,
            'nombre' => 'Producto prueba',
            'precio' => 25000,
            'peso_kg' => 1,
            'stock' => 4,
            'activo' => true,
        ]);
        $direccion = $cliente->direcciones()->create([
            'nombre' => 'Casa',
            'direccion' => 'Carrera 27 #36-14, Bucaramanga',
            'latitud' => 7.1189000,
            'longitud' => -73.1221000,
            'principal' => true,
        ]);

        $carrito = Carrito::create(['user_id' => $cliente->id]);
        $carrito->items()->create(['producto_id' => $producto->id, 'cantidad' => 1]);

        $this->actingAs($cliente)
            ->post(route('pedidos.store'), [
                'tipo_entrega' => 'domicilio',
                'direccion_cliente_id' => $direccion->id,
            ])
            ->assertRedirect();

        $pedido = $cliente->pedidos()->firstOrFail();
        $this->assertSame($direccion->id, $pedido->direccion_cliente_id);
        $this->assertSame($direccion->direccion, $pedido->direccion_entrega);
        $this->assertEqualsWithDelta(7.1189000, (float) $pedido->destino_latitud, 0.0000001);
        $this->assertEqualsWithDelta(-73.1221000, (float) $pedido->destino_longitud, 0.0000001);
    }

    public function test_al_completar_tracking_el_drone_queda_disponible(): void
    {
        $cliente = User::factory()->create(['role' => 'cliente']);
        $proveedor = Proveedor::create(['nombre' => 'Proveedor prueba']);
        $estacion = EstacionEntrega::create([
            'nombre' => 'Estacion centro',
            'direccion' => 'Calle 1',
            'latitud' => 7.1253930,
            'longitud' => -73.1198040,
            'activa' => true,
        ]);
        $drone = Drone::create([
            'codigo' => 'DRN-RETURN',
            'modelo' => 'Ligero',
            'capacidad_kg' => 5,
            'bateria' => 100,
            'estado' => 'disponible',
            'estacion_entrega_id' => $estacion->id,
        ]);
        $producto = Producto::create([
            'proveedor_id' => $proveedor->id,
            'nombre' => 'Producto prueba',
            'precio' => 25000,
            'peso_kg' => 1,
            'stock' => 4,
            'activo' => true,
        ]);

        $carrito = Carrito::create(['user_id' => $cliente->id]);
        $carrito->items()->create(['producto_id' => $producto->id, 'cantidad' => 1]);

        $this->actingAs($cliente)
            ->post(route('pedidos.store'), [
                'tipo_entrega' => 'estacion',
                'estacion_entrega_id' => $estacion->id,
            ])
            ->assertRedirect();

        $pedido = $cliente->pedidos()->firstOrFail();

        $this->actingAs($cliente)
            ->post(route('pedidos.pagar', $pedido))
            ->assertSessionHas('status');

        $this->assertSame('en_vuelo', $drone->fresh()->estado);

        $this->actingAs($cliente)
            ->postJson(route('pedidos.tracking.completar', $pedido))
            ->assertOk()
            ->assertJson([
                'pedido_estado' => 'entregado',
                'entrega_estado' => 'finalizada',
                'drone_estado' => 'disponible',
            ])
            ->assertJsonPath('gps_count', 8);

        $this->assertSame('entregado', $pedido->fresh()->estado);
        $this->assertSame('finalizada', $pedido->entrega()->firstOrFail()->estado);
        $this->assertSame('disponible', $drone->fresh()->estado);
        $this->assertCount(8, $pedido->entrega()->firstOrFail()->trackingPoints);
    }
}
