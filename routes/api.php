<?php

use App\Http\Controllers\Api\NotificacionApiController;
use App\Http\Controllers\Api\PagoApiController;
use App\Http\Controllers\Api\PedidoApiController;
use App\Http\Controllers\Api\TrackingApiController;
use App\Models\Proveedor;
use App\Models\SolicitudProveedor;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/pedidos', [PedidoApiController::class, 'index']);
    Route::get('/pedidos/{pedido}', [PedidoApiController::class, 'show']);
    Route::patch('/pedidos/{pedido}/estado', [PedidoApiController::class, 'updateEstado'])->middleware('role:administrador,personal_logistico');

    Route::post('/pedidos/{pedido}/pago/simular', [PagoApiController::class, 'simular']);

    Route::get('/pedidos/{pedido}/tracking', [TrackingApiController::class, 'pedido']);
    Route::post('/entregas/{entrega}/tracking', [TrackingApiController::class, 'store'])->middleware('role:administrador,personal_logistico');

    Route::get('/notificaciones', [NotificacionApiController::class, 'index']);
    Route::patch('/notificaciones/{notificacion}/leida', [NotificacionApiController::class, 'marcarLeida']);

    Route::middleware('role:proveedor')->prefix('proveedor')->group(function () {
        Route::get('/productos', function () {
            $proveedor = Proveedor::where('user_id', request()->user()->id)->firstOrFail();

            return $proveedor->productos()->with('categoria', 'imagenes')->latest()->paginate(15);
        });

        Route::get('/pedidos', function () {
            $proveedor = Proveedor::where('user_id', request()->user()->id)->firstOrFail();

            return \App\Models\PedidoItem::with('pedido.user', 'producto')
                ->whereHas('producto', fn ($query) => $query->where('proveedor_id', $proveedor->id))
                ->latest()
                ->paginate(15);
        });
    });

    Route::middleware('role:administrador')->prefix('admin')->group(function () {
        Route::get('/solicitudes-proveedores', fn () => SolicitudProveedor::with('user', 'proveedor.perfil')->latest()->paginate(15));
    });
});
