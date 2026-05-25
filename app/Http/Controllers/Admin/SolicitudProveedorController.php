<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SolicitudProveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SolicitudProveedorController extends Controller
{
    public function index(): View
    {
        return view('admin.solicitudes.index', [
            'solicitudes' => SolicitudProveedor::with('user', 'proveedor.perfil')
                ->where('estado', 'pendiente')
                ->latest()
                ->paginate(15),
        ]);
    }

    public function aprobar(SolicitudProveedor $solicitud): RedirectResponse
    {
        $solicitud->update(['estado' => 'aprobada', 'respuesta_admin' => 'Proveedor aprobado.', 'revisada_en' => now()]);
        $solicitud->proveedor?->update(['activo' => true]);
        $solicitud->proveedor?->perfil?->update(['estado' => 'aprobada']);

        return back()->with('status', 'Proveedor aprobado.');
    }

    public function rechazar(Request $request, SolicitudProveedor $solicitud): RedirectResponse
    {
        $data = $request->validate(['respuesta_admin' => ['nullable', 'string']]);
        $solicitud->update(['estado' => 'rechazada', 'respuesta_admin' => $data['respuesta_admin'] ?? 'Solicitud rechazada.', 'revisada_en' => now()]);
        $solicitud->proveedor?->update(['activo' => false]);
        $solicitud->proveedor?->perfil?->update(['estado' => 'rechazada']);

        return back()->with('status', 'Solicitud rechazada.');
    }

    public function activar(SolicitudProveedor $solicitud): RedirectResponse
    {
        $solicitud->proveedor?->update(['activo' => true]);

        return back()->with('status', 'Proveedor activado.');
    }

    public function desactivar(SolicitudProveedor $solicitud): RedirectResponse
    {
        $solicitud->proveedor?->update(['activo' => false]);

        return back()->with('status', 'Proveedor desactivado.');
    }
}
