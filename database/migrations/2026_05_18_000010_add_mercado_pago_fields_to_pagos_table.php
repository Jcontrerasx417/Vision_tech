<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->string('proveedor_pago')->nullable()->after('metodo');
            $table->string('proveedor_payment_id')->nullable()->after('referencia_externa');
            $table->string('proveedor_preference_id')->nullable()->after('proveedor_payment_id');
            $table->text('checkout_url')->nullable()->after('proveedor_preference_id');
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropColumn(['proveedor_pago', 'proveedor_payment_id', 'proveedor_preference_id', 'checkout_url']);
        });
    }
};
