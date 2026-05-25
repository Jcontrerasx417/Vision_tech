<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Drone;
use App\Models\Entrega;
use App\Models\Notificacion;
use App\Models\Pedido;
use App\Services\DroneFlightService;
use App\Services\PedidoDespachoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class PedidoAdminController extends Controller
{
    public function __construct(
        private readonly PedidoDespachoService $despachoService,
        private readonly DroneFlightService $flightService
    )
    {
    }

    public function index(): View
    {
        return view('admin.pedidos', ['pedidos' => Pedido::with('user', 'pago', 'entrega.drone')->latest()->paginate(15), 'drones' => Drone::all()]);
    }

    public function updateEstado(Request $request, Pedido $pedido): RedirectResponse
    {
        $data = $request->validate(['estado' => ['required', Rule::in(Pedido::ESTADOS)]]);
        if ($data['estado'] === 'pagado' && ! $pedido->estaPagado()) {
            return back()->withErrors('El estado pagado solo se puede usar cuando el pago este aprobado.');
        }

        if ($pedido->estadoRequierePago($data['estado']) && ! $pedido->estaPagado()) {
            return back()->withErrors('El pedido debe estar pagado antes de prepararlo, enviarlo o entregarlo.');
        }

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
                $this->despachoService->asignarDrone($pedido, $drone);
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

        if (! $entrega->pedido->estaPagado()) {
            return back()->withErrors('No se puede registrar tracking antes de que el pago este aprobado.');
        }

        $entrega->trackingPoints()->create($data + ['registrado_en' => now()]);
        $entrega->update(['estado' => 'en_camino']);
        $entrega->pedido()->update(['estado' => 'enviado']);

        return back()->with('status', 'Punto GPS registrado.');
    }

    public function simularVuelo(Pedido $pedido): RedirectResponse
    {
        try {
            $this->flightService->simularVueloPedido($pedido);

            return back()->with('status', 'Vuelo simulado: GPS generado y bateria actualizada.');
        } catch (\Throwable $exception) {
            Log::error('Error simulando vuelo', ['pedido_id' => $pedido->id, 'error' => $exception->getMessage()]);

            return back()->withErrors($exception->getMessage());
        }
    }

    public function finalizarEntrega(Entrega $entrega): RedirectResponse
    {
        try {
            $this->flightService->finalizarYRetornar($entrega);

            return back()->with('status', 'Entrega finalizada. El drone retorno a base y su estado fue actualizado.');
        } catch (\Throwable $exception) {
            Log::error('Error finalizando entrega', ['entrega_id' => $entrega->id, 'error' => $exception->getMessage()]);

            return back()->withErrors($exception->getMessage());
        }
    }
}
