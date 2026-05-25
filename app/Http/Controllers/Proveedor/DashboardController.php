<?php

namespace App\Http\Controllers\Proveedor;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $proveedor = $this->proveedor($request);

        return view('proveedor.dashboard', [
            'proveedor' => $proveedor,
            'productos' => $proveedor->productos()->latest()->take(6)->get(),
            'totalProductos' => $proveedor->productos()->count(),
            'stockTotal' => $proveedor->productos()->sum('stock'),
            'activos' => $proveedor->productos()->where('activo', true)->count(),
        ]);
    }

    private function proveedor(Request $request): Proveedor
    {
        return Proveedor::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['nombre' => $request->user()->name, 'contacto' => $request->user()->name, 'email' => $request->user()->email]
        );
    }
}
