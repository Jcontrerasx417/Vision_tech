<?php

namespace Database\Seeders;

use App\Models\Carrito;
use App\Models\CategoriaProducto;
use App\Models\Drone;
use App\Models\DireccionCliente;
use App\Models\EstacionEntrega;
use App\Models\MantenimientoDrone;
use App\Models\Notificacion;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\ProductoImagen;
use App\Models\Proveedor;
use App\Models\ProveedorPerfil;
use App\Models\SolicitudProveedor;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $usuarios = [
            ['name' => 'Cliente Demo', 'email' => 'cliente@demo.com', 'role' => 'cliente'],
            ['name' => 'Admin Demo', 'email' => 'admin@demo.com', 'role' => 'administrador'],
            ['name' => 'Logistica Demo', 'email' => 'logistica@demo.com', 'role' => 'personal_logistico'],
            ['name' => 'Proveedor Demo', 'email' => 'proveedor@demo.com', 'role' => 'proveedor'],
        ];

        foreach ($usuarios as $usuario) {
            $user = User::updateOrCreate(
                ['email' => $usuario['email']],
                $usuario + ['password' => Hash::make('password')]
            );
            Carrito::firstOrCreate(['user_id' => $user->id]);
        }

        $proveedorA = Proveedor::updateOrCreate(['nombre' => 'AgroAndes'], [
            'contacto' => 'Maria Lopez',
            'email' => 'ventas@agroandes.test',
            'telefono' => '3001112233',
            'direccion' => 'Calle 12 #45-30',
            'activo' => true,
        ]);

        $proveedorDemo = User::where('email', 'proveedor@demo.com')->first();

        $proveedorB = Proveedor::updateOrCreate(['nombre' => 'TechSupply Express'], [
            'user_id' => $proveedorDemo?->id,
            'contacto' => 'Carlos Ruiz',
            'email' => 'contacto@techsupply.test',
            'telefono' => '3104445566',
            'direccion' => 'Av. Industrial #22-10',
            'activo' => true,
        ]);

        if ($proveedorDemo) {
            ProveedorPerfil::updateOrCreate(
                ['user_id' => $proveedorDemo->id],
                ['proveedor_id' => $proveedorB->id, 'nombre_comercial' => $proveedorB->nombre, 'descripcion' => 'Proveedor demo de tecnologia y accesorios.', 'telefono' => $proveedorB->telefono, 'direccion' => $proveedorB->direccion, 'estado' => 'aprobada']
            );
            SolicitudProveedor::updateOrCreate(
                ['user_id' => $proveedorDemo->id, 'proveedor_id' => $proveedorB->id],
                ['estado' => 'aprobada', 'mensaje' => 'Solicitud demo aprobada.', 'respuesta_admin' => 'Proveedor verificado.', 'revisada_en' => now()]
            );
        }

        $alimentos = CategoriaProducto::updateOrCreate(['nombre' => 'Alimentos'], ['descripcion' => 'Productos livianos de consumo.', 'activa' => true]);
        $tecnologia = CategoriaProducto::updateOrCreate(['nombre' => 'Tecnologia'], ['descripcion' => 'Dispositivos y accesorios compactos.', 'activa' => true]);

        $productos = [
            ['proveedor_id' => $proveedorA->id, 'categoria_producto_id' => $alimentos->id, 'nombre' => 'Cafe premium 500g', 'descripcion' => 'Cafe de origen empacado para entrega rapida.', 'imagen_url' => 'https://images.unsplash.com/photo-1511920170033-f8396924c348?auto=format&fit=crop&w=900&q=80', 'precio' => 28000, 'peso_kg' => 0.50, 'stock' => 40, 'activo' => true, 'estado' => 'aprobado'],
            ['proveedor_id' => $proveedorA->id, 'categoria_producto_id' => $alimentos->id, 'nombre' => 'Miel organica 350g', 'descripcion' => 'Miel natural en frasco liviano.', 'imagen_url' => 'https://images.unsplash.com/photo-1587049352851-8d4e89133924?auto=format&fit=crop&w=900&q=80', 'precio' => 22000, 'peso_kg' => 0.35, 'stock' => 35, 'activo' => true, 'estado' => 'aprobado'],
            ['proveedor_id' => $proveedorB->id, 'categoria_producto_id' => $tecnologia->id, 'nombre' => 'Sensor IoT compacto', 'descripcion' => 'Sensor pequeno compatible con monitoreo logistico.', 'imagen_url' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=900&q=80', 'precio' => 95000, 'peso_kg' => 0.20, 'stock' => 18, 'activo' => true, 'estado' => 'aprobado'],
            ['proveedor_id' => $proveedorB->id, 'categoria_producto_id' => $tecnologia->id, 'nombre' => 'Bateria portatil mini', 'descripcion' => 'Bateria liviana para dispositivos moviles.', 'imagen_url' => 'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?auto=format&fit=crop&w=900&q=80', 'precio' => 65000, 'peso_kg' => 0.30, 'stock' => 20, 'activo' => true, 'estado' => 'aprobado'],
        ];

        foreach ($productos as $producto) {
            $productoModel = Producto::updateOrCreate(['nombre' => $producto['nombre']], $producto);
            ProductoImagen::updateOrCreate(
                ['producto_id' => $productoModel->id, 'principal' => true],
                ['url' => $productoModel->imagen_url, 'orden' => 0]
            );
        }

        $estacionCentro = EstacionEntrega::updateOrCreate(['nombre' => 'Estacion Centro Bucaramanga'], [
            'direccion' => 'Carrera 15 #35-20, Centro, Bucaramanga',
            'latitud' => 7.1193490,
            'longitud' => -73.1227410,
            'activa' => true,
        ]);

        $estacionNorte = EstacionEntrega::updateOrCreate(['nombre' => 'Estacion Cabecera'], [
            'direccion' => 'Carrera 33 #48-35, Cabecera, Bucaramanga',
            'latitud' => 7.1150670,
            'longitud' => -73.1086300,
            'activa' => true,
        ]);

        $drones = [
            ['codigo' => 'DRN-001', 'modelo' => 'Falcon Lite', 'capacidad_kg' => 2.50, 'bateria' => 95, 'estado' => 'disponible', 'estacion_entrega_id' => $estacionCentro->id],
            ['codigo' => 'DRN-002', 'modelo' => 'Falcon Cargo', 'capacidad_kg' => 5.00, 'bateria' => 88, 'estado' => 'disponible', 'estacion_entrega_id' => $estacionNorte->id],
            ['codigo' => 'DRN-003', 'modelo' => 'Swift Mini', 'capacidad_kg' => 1.20, 'bateria' => 60, 'estado' => 'mantenimiento', 'estacion_entrega_id' => $estacionCentro->id],
        ];

        foreach ($drones as $drone) {
            Drone::updateOrCreate(['codigo' => $drone['codigo']], $drone);
        }

        $droneMantenimiento = Drone::where('codigo', 'DRN-003')->first();
        MantenimientoDrone::updateOrCreate(
            ['drone_id' => $droneMantenimiento->id, 'tipo' => 'Revision preventiva'],
            ['descripcion' => 'Revision de helices, sensores y bateria.', 'programado_en' => now()->addDays(2), 'realizado_en' => null]
        );

        $cliente = User::where('email', 'cliente@demo.com')->first();
        $droneDemo = Drone::where('codigo', 'DRN-002')->first();
        $productoDemo = Producto::where('nombre', 'Cafe premium 500g')->first();

        if ($cliente && $droneDemo && $productoDemo && ! Pedido::where('user_id', $cliente->id)->exists()) {
            DireccionCliente::updateOrCreate(
                ['user_id' => $cliente->id, 'nombre' => 'Casa Bucaramanga'],
                ['direccion' => 'Carrera 27 #36-14, Bucaramanga', 'latitud' => 7.1253930, 'longitud' => -73.1198040, 'principal' => true]
            );

            $pedido = Pedido::create([
                'user_id' => $cliente->id,
                'estado' => 'enviado',
                'tipo_entrega' => 'domicilio',
                'direccion_entrega' => 'Carrera 27 #36-14, Bucaramanga',
                'destino_latitud' => 7.1253930,
                'destino_longitud' => -73.1198040,
                'peso_total_kg' => 1.00,
                'total' => 56000,
            ]);

            $pedido->items()->create([
                'producto_id' => $productoDemo->id,
                'cantidad' => 2,
                'precio_unitario' => $productoDemo->precio,
                'peso_unitario_kg' => $productoDemo->peso_kg,
            ]);

            $pedido->pago()->create([
                'estado' => 'aprobado',
                'metodo' => 'simulado',
                'referencia_externa' => 'SIM-DEMO-001',
                'monto' => $pedido->total,
                'pagado_en' => now()->subMinutes(25),
            ]);

            $pedido->factura()->create([
                'numero' => 'FAC-DEMO-001',
                'subtotal' => $pedido->total,
                'impuestos' => 0,
                'total' => $pedido->total,
                'emitida_en' => now()->subMinutes(24),
            ]);

            $entrega = $pedido->entrega()->create([
                'drone_id' => $droneDemo->id,
                'estacion_entrega_id' => $estacionNorte->id,
                'estado' => 'en_camino',
                'asignada_en' => now()->subMinutes(20),
            ]);

            $entrega->trackingPoints()->createMany([
                ['latitud' => 7.1150670, 'longitud' => -73.1086300, 'altitud_m' => 985, 'registrado_en' => now()->subMinutes(18)],
                ['latitud' => 7.1193490, 'longitud' => -73.1227410, 'altitud_m' => 972, 'registrado_en' => now()->subMinutes(10)],
                ['latitud' => 7.1232000, 'longitud' => -73.1208000, 'altitud_m' => 968, 'registrado_en' => now()->subMinutes(3)],
            ]);

            $droneDemo->update(['estado' => 'en_vuelo']);

            Notificacion::create([
                'user_id' => $cliente->id,
                'pedido_id' => $pedido->id,
                'titulo' => 'Pedido en camino',
                'mensaje' => 'Tu pedido demo fue enviado y ya cuenta con seguimiento GPS.',
            ]);
        }
    }
}
