<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->decimal('destino_latitud', 10, 7)->nullable()->after('direccion_entrega');
            $table->decimal('destino_longitud', 10, 7)->nullable()->after('destino_latitud');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['destino_latitud', 'destino_longitud']);
        });
    }
};
