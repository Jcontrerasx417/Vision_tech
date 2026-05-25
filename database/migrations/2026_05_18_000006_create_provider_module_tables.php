<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categoria_productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->text('descripcion')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });

        Schema::table('proveedores', function (Blueprint $table) {
            $table->boolean('activo')->default(false)->after('direccion');
        });

        Schema::create('proveedor_perfiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('proveedor_id')->unique()->constrained('proveedores')->cascadeOnDelete();
            $table->string('nombre_comercial');
            $table->text('descripcion')->nullable();
            $table->string('nit')->nullable();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->string('estado')->default('pendiente');
            $table->timestamps();
        });

        Schema::create('solicitud_proveedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->cascadeOnDelete();
            $table->string('estado')->default('pendiente')->index();
            $table->text('mensaje')->nullable();
            $table->text('respuesta_admin')->nullable();
            $table->timestamp('revisada_en')->nullable();
            $table->timestamps();
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->foreignId('categoria_producto_id')->nullable()->after('proveedor_id')->constrained('categoria_productos')->nullOnDelete();
            $table->string('estado')->default('pendiente_revision')->after('activo')->index();
            $table->text('motivo_rechazo')->nullable()->after('estado');
            $table->boolean('reportado')->default(false)->after('motivo_rechazo');
        });

        Schema::create('producto_imagenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->string('url');
            $table->boolean('principal')->default(false);
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_imagenes');

        Schema::table('productos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('categoria_producto_id');
            $table->dropColumn(['estado', 'motivo_rechazo', 'reportado']);
        });

        Schema::dropIfExists('solicitud_proveedores');
        Schema::dropIfExists('proveedor_perfiles');

        Schema::table('proveedores', function (Blueprint $table) {
            $table->dropColumn('activo');
        });

        Schema::dropIfExists('categoria_productos');
    }
};
