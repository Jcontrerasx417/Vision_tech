<?php

namespace App\Http\Controllers;

use App\Models\Drone;
use App\Models\EstacionEntrega;
use App\Models\Factura;
use App\Models\Notificacion;
use App\Models\Pedido;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PedidoController extends Controller
{
    public function index(Request $request): View
    {
        return view('pedidos.index', [
            'pedidos' => $request->user()->pedidos()->with('pago', 'entrega.drone')->latest()->paginate(10),
        ]);
    }

    public function create(Request $request): View
    {
        return view('pedidos.create', [
            'carrito' => $request->user()->carrito()->with('items.producto')->firstOrCreate(),
            'estaciones' => EstacionEntrega::where('activa', true)->get(),
            'direcciones' => $request->user()->direcciones()->orderByDesc('principal')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tipo_entrega' => ['required', 'in:estacion,domicilio'],
            'estacion_entrega_id' => ['nullable', 'required_if:tipo_entrega,estacion', 'exists:estacion_entregas,id'],
            'direccion_entrega' => ['nullable', 'required_if:tipo_entrega,domicilio', 'string', 'max:255'],
            'destino_latitud' => ['nullable', 'required_if:tipo_entrega,domicilio', 'numeric', 'between:-90,90'],
            'destino_longitud' => ['nullable', 'required_if:tipo_entrega,domicilio', 'numeric', 'between:-180,180'],
        ]);

        $carrito = $request->user()->carrito()->with('items.producto')->firstOrCreate();
        if ($carrito->items->isEmpty()) {
            return back()->withErrors('El carrito está vacío.');
        }

        try {
            $pedido = DB::transaction(function () use ($request, $data, $carrito) {
                $pesoTotal = $carrito->items->sum(fn ($item) => $item->cantidad * $item->producto->peso_kg);
                $total = $carrito->items->sum(fn ($item) => $item->cantidad * $item->producto->precio);
                $droneDisponible = Drone::where('estado', 'disponible')->where('capacidad_kg', '>=', $pesoTotal)->first();

                if (! $droneDisponible) {
                    throw new \RuntimeException('No hay un drone disponible con capacidad suficiente para este pedido.');
                }

                $pedido = Pedido::create([
                    'user_id' => $request->user()->id,
                    'tipo_entrega' => $data['tipo_entrega'],
                    'estacion_entrega_id' => $data['estacion_entrega_id'] ?? null,
                    'direccion_entrega' => $data['direccion_entrega'] ?? null,
                    'destino_latitud' => $data['destino_latitud'] ?? null,
                    'destino_longitud' => $data['destino_longitud'] ?? null,
                    'peso_total_kg' => $pesoTotal,
                    'total' => $total,
                ]);

                foreach ($carrito->items as $item) {
                    $pedido->items()->create([
                        'producto_id' => $item->producto_id,
                        'cantidad' => $item->cantidad,
                        'precio_unitario' => $item->producto->precio,
                        'peso_unitario_kg' => $item->producto->peso_kg,
                    ]);
                    $item->producto->decrement('stock', $item->cantidad);
                }

                $pedido->entrega()->create([
                    'drone_id' => $droneDisponible->id,
                    'estacion_entrega_id' => $data['estacion_entrega_id'] ?? $droneDisponible->estacion_entrega_id,
                    'estado' => $data['tipo_entrega'] === 'domicilio' ? 'domicilio_pendiente' : 'asignada',
                    'asignada_en' => now(),
                ]);
                $droneDisponible->update(['estado' => 'en_vuelo']);
                $carrito->items()->delete();
                $this->notificar($pedido, 'Pedido creado', 'Tu pedido fue creado y tiene un drone asignado.');

                return $pedido;
            });

            return redirect()->route('pedidos.show', $pedido)->with('status', 'Pedido creado correctamente.');
        } catch (\Throwable $exception) {
            Log::error('Error creando pedido', ['error' => $exception->getMessage()]);
            return back()->withErrors($exception->getMessage());
        }
    }

    public function show(Request $request, Pedido $pedido): View
    {
        abort_unless($pedido->user_id === $request->user()->id || $request->user()->hasRole('administrador', 'personal_logistico'), 403);

        return view('pedidos.show', ['pedido' => $pedido->load('items.producto', 'pago', 'factura', 'entrega.drone', 'entrega.trackingPoints')]);
    }

    public function pagar(Pedido $pedido): RedirectResponse
    {
        abort_unless($pedido->user_id === request()->user()->id || request()->user()->hasRole('administrador'), 403);

        try {
            DB::transaction(function () use ($pedido) {
                $pedido->pago()->updateOrCreate(
                    ['pedido_id' => $pedido->id],
                    ['estado' => 'aprobado', 'metodo' => 'simulado', 'referencia_externa' => 'SIM-'.str_pad((string) $pedido->id, 6, '0', STR_PAD_LEFT), 'monto' => $pedido->total, 'pagado_en' => now()]
                );
                $pedido->update(['estado' => 'pagado']);
                Factura::updateOrCreate(
                    ['pedido_id' => $pedido->id],
                    ['numero' => 'FAC-'.now()->format('Ymd').'-'.$pedido->id, 'subtotal' => $pedido->total, 'impuestos' => 0, 'total' => $pedido->total, 'emitida_en' => now()]
                );
                $this->notificar($pedido, 'Pago aprobado', 'El pago fue aprobado y la factura fue generada.');
            });

            return back()->with('status', 'Pago simulado aprobado.');
        } catch (\Throwable $exception) {
            Log::error('Error simulando pago', ['pedido_id' => $pedido->id, 'error' => $exception->getMessage()]);
            return back()->withErrors('No fue posible procesar el pago.');
        }
    }

    public function tracking(Request $request, Pedido $pedido): View
    {
        abort_unless($pedido->user_id === $request->user()->id || $request->user()->hasRole('administrador', 'personal_logistico'), 403);

        return view('tracking.show', ['pedido' => $pedido->load('entrega.drone', 'entrega.trackingPoints')]);
    }

    private function notificar(Pedido $pedido, string $titulo, string $mensaje): void
    {
        Notificacion::create(['user_id' => $pedido->user_id, 'pedido_id' => $pedido->id, 'titulo' => $titulo, 'mensaje' => $mensaje]);
    }
}
