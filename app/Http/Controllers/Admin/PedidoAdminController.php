<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Drone;
use App\Models\Entrega;
use App\Models\Notificacion;
use App\Models\Pedido;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class PedidoAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.pedidos', ['pedidos' => Pedido::with('user', 'entrega.drone')->latest()->paginate(15), 'drones' => Drone::all()]);
    }

    public function updateEstado(Request $request, Pedido $pedido): RedirectResponse
    {
        $data = $request->validate(['estado' => ['required', Rule::in(Pedido::ESTADOS)]]);
        $pedido->update($data);
        Notificacion::create(['user_id' => $pedido->user_id, 'pedido_id' => $pedido->id, 'titulo' => 'Estado actualizado', 'mensaje' => "Tu pedido ahora está en estado {$pedido->estado}."]);

        return back()->with('status', 'Estado del pedido actualizado.');
    }

    public function asignarDrone(Request $request, Pedido $pedido): RedirectResponse
    {
        $data = $request->validate(['drone_id' => ['required', 'exists:drones,id']]);

        try {
            DB::transaction(function () use ($pedido, $data) {
                $drone = Drone::findOrFail($data['drone_id']);
                if ($drone->capacidad_kg < $pedido->peso_total_kg) {
                    throw new \RuntimeException('El drone seleccionado no soporta el peso del pedido.');
                }
                $pedido->entrega()->updateOrCreate(
                    ['pedido_id' => $pedido->id],
                    ['drone_id' => $drone->id, 'estado' => 'asignada', 'asignada_en' => now()]
                );
                $drone->update(['estado' => 'en_vuelo']);
            });

            return back()->with('status', 'Drone asignado al pedido.');
        } catch (\Throwable $exception) {
            Log::error('Error asignando drone', ['pedido_id' => $pedido->id, 'error' => $exception->getMessage()]);
            return back()->withErrors($exception->getMessage());
        }
    }

    public function registrarTracking(Request $request, Entrega $entrega): RedirectResponse
    {
        $data = $request->validate([
            'latitud' => ['required', 'numeric', 'between:-90,90'],
            'longitud' => ['required', 'numeric', 'between:-180,180'],
            'altitud_m' => ['nullable', 'numeric', 'min:0'],
        ]);

        $entrega->trackingPoints()->create($data + ['registrado_en' => now()]);
        $entrega->update(['estado' => 'en_camino']);
        $entrega->pedido()->update(['estado' => 'enviado']);

        return back()->with('status', 'Punto GPS registrado.');
    }
}
