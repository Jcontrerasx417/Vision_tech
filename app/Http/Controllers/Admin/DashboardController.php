<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Drone;
use App\Models\EstacionEntrega;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'productos' => Producto::count(),
            'proveedores' => Proveedor::count(),
            'pedidos' => Pedido::latest()->with('user', 'entrega.drone')->take(8)->get(),
            'pedidosPendientes' => Pedido::whereIn('estado', ['pendiente', 'en_preparacion'])->count(),
            'pedidosEnRuta' => Pedido::whereIn('estado', ['enviado', 'proximo_a_llegar'])->count(),
            'pagosPendientes' => Pago::where('estado', 'pendiente')->count(),
            'dronesDisponibles' => Drone::where('estado', 'disponible')->count(),
            'dronesEnVuelo' => Drone::where('estado', 'en_vuelo')->count(),
            'dronesMantenimiento' => Drone::where('estado', 'mantenimiento')->count(),
            'dronesCriticos' => Drone::where('bateria', '<=', 25)->get(),
            'stockBajo' => Producto::where('stock', '<=', 5)->get(),
            'drones' => Drone::count(),
            'estaciones' => EstacionEntrega::count(),
        ]);
    }
}
