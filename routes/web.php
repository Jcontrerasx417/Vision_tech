<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DroneController;
use App\Http\Controllers\Admin\EstacionEntregaController;
use App\Http\Controllers\Admin\MantenimientoDroneController;
use App\Http\Controllers\Admin\PedidoAdminController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\ProveedorController;
use App\Http\Controllers\Admin\SolicitudProveedorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\Cliente\DireccionController as ClienteDireccionController;
use App\Http\Controllers\Cliente\PerfilController as ClientePerfilController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\Proveedor\DashboardController as ProveedorDashboardController;
use App\Http\Controllers\Proveedor\PerfilController as ProveedorPerfilController;
use App\Http\Controllers\Proveedor\PedidoController as ProveedorPedidoController;
use App\Http\Controllers\Proveedor\ProductoController as ProveedorProductoController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('home');
Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo.index');
Route::get('/catalogo/{producto}', [CatalogoController::class, 'show'])->name('catalogo.show');

Route::middleware('guest')->group(function () {
    Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registro', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:cliente'])->group(function () {
    Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
    Route::post('/carrito/{producto}', [CarritoController::class, 'add'])->name('carrito.add');
    Route::patch('/carrito/item/{item}', [CarritoController::class, 'update'])->name('carrito.update');
    Route::delete('/carrito/item/{item}', [CarritoController::class, 'remove'])->name('carrito.remove');
    Route::get('/pedidos', [PedidoController::class, 'index'])->name('cliente.pedidos');
    Route::get('/pedidos/crear', [PedidoController::class, 'create'])->name('pedidos.create');
    Route::post('/pedidos', [PedidoController::class, 'store'])->name('pedidos.store');
    Route::post('/pedidos/{pedido}/pagar', [PedidoController::class, 'pagar'])->name('pedidos.pagar');
    Route::get('/cliente/perfil', [ClientePerfilController::class, 'show'])->name('cliente.perfil');
    Route::put('/cliente/perfil', [ClientePerfilController::class, 'update'])->name('cliente.perfil.update');
    Route::get('/cliente/direcciones', [ClienteDireccionController::class, 'index'])->name('cliente.direcciones.index');
    Route::post('/cliente/direcciones', [ClienteDireccionController::class, 'store'])->name('cliente.direcciones.store');
    Route::delete('/cliente/direcciones/{direccion}', [ClienteDireccionController::class, 'destroy'])->name('cliente.direcciones.destroy');
});

Route::middleware(['auth', 'role:cliente,administrador,personal_logistico'])->group(function () {
    Route::get('/pedidos/{pedido}', [PedidoController::class, 'show'])->name('pedidos.show');
    Route::get('/pedidos/{pedido}/tracking', [PedidoController::class, 'tracking'])->name('pedidos.tracking');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:administrador,personal_logistico'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/pedidos', [PedidoAdminController::class, 'index'])->name('pedidos.index');
    Route::patch('/pedidos/{pedido}/estado', [PedidoAdminController::class, 'updateEstado'])->name('pedidos.estado');
    Route::patch('/pedidos/{pedido}/drone', [PedidoAdminController::class, 'asignarDrone'])->name('pedidos.drone');
    Route::post('/entregas/{entrega}/tracking', [PedidoAdminController::class, 'registrarTracking'])->name('entregas.tracking');

    Route::resource('drones', DroneController::class)->except(['show']);
    Route::resource('estaciones', EstacionEntregaController::class)->parameters(['estaciones' => 'estacion'])->except(['show']);
    Route::resource('mantenimientos', MantenimientoDroneController::class)->parameters(['mantenimientos' => 'mantenimiento'])->except(['show']);
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:administrador'])->group(function () {
    Route::resource('productos', ProductoController::class)->except(['show']);
    Route::resource('proveedores', ProveedorController::class)->parameters(['proveedores' => 'proveedor'])->except(['show']);
    Route::get('/solicitudes-proveedores', [SolicitudProveedorController::class, 'index'])->name('solicitudes.index');
    Route::patch('/solicitudes-proveedores/{solicitud}/aprobar', [SolicitudProveedorController::class, 'aprobar'])->name('solicitudes.aprobar');
    Route::patch('/solicitudes-proveedores/{solicitud}/rechazar', [SolicitudProveedorController::class, 'rechazar'])->name('solicitudes.rechazar');
    Route::patch('/solicitudes-proveedores/{solicitud}/activar', [SolicitudProveedorController::class, 'activar'])->name('solicitudes.activar');
    Route::patch('/solicitudes-proveedores/{solicitud}/desactivar', [SolicitudProveedorController::class, 'desactivar'])->name('solicitudes.desactivar');
});

Route::prefix('proveedor')->name('proveedor.')->middleware(['auth', 'role:proveedor'])->group(function () {
    Route::get('/', ProveedorDashboardController::class)->name('dashboard');
    Route::get('/perfil', [ProveedorPerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [ProveedorPerfilController::class, 'update'])->name('perfil.update');
    Route::get('/pedidos', [ProveedorPedidoController::class, 'index'])->name('pedidos.index');
    Route::patch('/productos/{producto}/desactivar', [ProveedorProductoController::class, 'desactivar'])->name('productos.desactivar');
    Route::patch('/productos/{producto}/activar', [ProveedorProductoController::class, 'activar'])->name('productos.activar');
    Route::resource('productos', ProveedorProductoController::class)->except(['show']);
});
