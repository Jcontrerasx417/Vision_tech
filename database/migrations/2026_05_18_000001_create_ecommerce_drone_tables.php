<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('contacto')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->timestamps();
        });

        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->constrained('proveedores')->cascadeOnDelete();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 12, 2);
            $table->decimal('peso_kg', 8, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('carritos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('carrito_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrito_id')->constrained('carritos')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->unsignedInteger('cantidad')->default(1);
            $table->timestamps();
            $table->unique(['carrito_id', 'producto_id']);
        });

        Schema::create('estacion_entregas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('direccion');
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });

        Schema::create('drones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('modelo');
            $table->decimal('capacidad_kg', 8, 2);
            $table->unsignedTinyInteger('bateria')->default(100);
            $table->string('estado')->default('disponible')->index();
            $table->foreignId('estacion_entrega_id')->nullable()->constrained('estacion_entregas')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('estado')->default('pendiente')->index();
            $table->string('tipo_entrega')->default('estacion');
            $table->foreignId('estacion_entrega_id')->nullable()->constrained('estacion_entregas')->nullOnDelete();
            $table->string('direccion_entrega')->nullable();
            $table->decimal('peso_total_kg', 8, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('pedido_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->restrictOnDelete();
            $table->unsignedInteger('cantidad');
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('peso_unitario_kg', 8, 2);
            $table->timestamps();
        });

        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->unique()->constrained('pedidos')->cascadeOnDelete();
            $table->string('estado')->default('pendiente')->index();
            $table->string('metodo')->default('simulado');
            $table->string('referencia_externa')->nullable();
            $table->decimal('monto', 12, 2);
            $table->timestamp('pagado_en')->nullable();
            $table->timestamps();
        });

        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->unique()->constrained('pedidos')->cascadeOnDelete();
            $table->string('numero')->unique();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('impuestos', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->timestamp('emitida_en');
            $table->timestamps();
        });

        Schema::create('entregas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->unique()->constrained('pedidos')->cascadeOnDelete();
            $table->foreignId('drone_id')->nullable()->constrained('drones')->nullOnDelete();
            $table->foreignId('estacion_entrega_id')->nullable()->constrained('estacion_entregas')->nullOnDelete();
            $table->string('estado')->default('asignada')->index();
            $table->timestamp('asignada_en')->nullable();
            $table->timestamp('finalizada_en')->nullable();
            $table->timestamps();
        });

        Schema::create('tracking_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrega_id')->constrained('entregas')->cascadeOnDelete();
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);
            $table->decimal('altitud_m', 8, 2)->nullable();
            $table->timestamp('registrado_en');
            $table->timestamps();
        });

        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('mensaje');
            $table->timestamp('leida_en')->nullable();
            $table->timestamps();
        });

        Schema::create('mantenimiento_drones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drone_id')->constrained('drones')->cascadeOnDelete();
            $table->string('tipo');
            $table->text('descripcion')->nullable();
            $table->timestamp('programado_en');
            $table->timestamp('realizado_en')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mantenimiento_drones');
        Schema::dropIfExists('notificaciones');
        Schema::dropIfExists('tracking_points');
        Schema::dropIfExists('entregas');
        Schema::dropIfExists('facturas');
        Schema::dropIfExists('pagos');
        Schema::dropIfExists('pedido_items');
        Schema::dropIfExists('pedidos');
        Schema::dropIfExists('drones');
        Schema::dropIfExists('estacion_entregas');
        Schema::dropIfExists('carrito_items');
        Schema::dropIfExists('carritos');
        Schema::dropIfExists('productos');
        Schema::dropIfExists('proveedores');
    }
};
