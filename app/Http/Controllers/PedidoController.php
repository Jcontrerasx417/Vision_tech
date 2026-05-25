<?php

namespace App\Http\Controllers;

use App\Models\EstacionEntrega;
use App\Models\Notificacion;
use App\Models\Pedido;
use App\Services\PedidoPagoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PedidoController extends Controller
{
    public function __construct(private readonly PedidoPagoService $pagoService)
    {
    }

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
        $hasSavedAddress = $request->filled('direccion_cliente_id');

        $data = $request->validate([
            'tipo_entrega' => ['required', 'in:estacion,domicilio'],
            'estacion_entrega_id' => ['nullable', 'required_if:tipo_entrega,estacion', 'exists:estacion_entregas,id'],
            'direccion_cliente_id' => ['nullable', 'exists:direccion_clientes,id'],
            'direccion_entrega' => [$hasSavedAddress ? 'nullable' : 'required_if:tipo_entrega,domicilio', 'string', 'max:255'],
            'destino_latitud' => [$hasSavedAddress ? 'nullable' : 'required_if:tipo_entrega,domicilio', 'numeric', 'between:-90,90'],
            'destino_longitud' => [$hasSavedAddress ? 'nullable' : 'required_if:tipo_entrega,domicilio', 'numeric', 'between:-180,180'],
            'guardar_direccion' => ['nullable', 'boolean'],
            'nombre_direccion' => ['nullable', 'string', 'max:255'],
        ]);

        $carrito = $request->user()->carrito()->with('items.producto')->firstOrCreate();
        if ($carrito->items->isEmpty()) {
            return back()->withErrors('El carrito está vacío.');
        }

        try {
            $pedido = DB::transaction(function () use ($request, $data, $carrito) {
                $pesoTotal = $carrito->items->sum(fn ($item) => $item->cantidad * $item->producto->peso_kg);
                $total = $carrito->items->sum(fn ($item) => $item->cantidad * $item->producto->precio);
                $direccion = null;
                if ($data['tipo_entrega'] === 'domicilio') {
                    if (! empty($data['direccion_cliente_id'])) {
                        $direccion = $request->user()->direcciones()->findOrFail($data['direccion_cliente_id']);
                    } elseif ($request->boolean('guardar_direccion')) {
                        $direccion = $request->user()->direcciones()->create([
                            'nombre' => $data['nombre_direccion'] ?: 'Direccion de pedido',
                            'direccion' => $data['direccion_entrega'],
                            'latitud' => $data['destino_latitud'],
                            'longitud' => $data['destino_longitud'],
                            'principal' => false,
                        ]);
                    }
                }

                $pedido = Pedido::create([
                    'user_id' => $request->user()->id,
                    'tipo_entrega' => $data['tipo_entrega'],
                    'estacion_entrega_id' => $data['estacion_entrega_id'] ?? null,
                    'direccion_cliente_id' => $direccion?->id,
                    'direccion_entrega' => $direccion?->direccion ?? ($data['direccion_entrega'] ?? null),
                    'destino_latitud' => $direccion?->latitud ?? ($data['destino_latitud'] ?? null),
                    'destino_longitud' => $direccion?->longitud ?? ($data['destino_longitud'] ?? null),
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

                $carrito->items()->delete();
                $this->notificar($pedido, 'Pedido creado', 'Tu pedido fue creado. El drone se asignara cuando el pago sea aprobado.');

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

        return view('pedidos.show', ['pedido' => $pedido->load('items.producto', 'pago', 'factura', 'estacionEntrega', 'direccionCliente', 'entrega.drone', 'entrega.trackingPoints')]);
    }

    public function pagar(Pedido $pedido): RedirectResponse
    {
        abort_unless($pedido->user_id === request()->user()->id || request()->user()->hasRole('administrador'), 403);

        try {
            $droneAsignado = $this->pagoService->aprobar($pedido, [
                'metodo' => 'simulado',
                'proveedor_pago' => 'simulado',
                'referencia_externa' => 'SIM-'.str_pad((string) $pedido->id, 6, '0', STR_PAD_LEFT),
            ], 'El pago fue aprobado, la factura fue generada y el drone fue asignado.');

            return back()->with('status', $droneAsignado
                ? 'Pago simulado aprobado. Drone asignado para entrega.'
                : 'Pago simulado aprobado. No hay drone disponible; queda pendiente de asignacion.'
            );
        } catch (\Throwable $exception) {
            Log::error('Error simulando pago', ['pedido_id' => $pedido->id, 'error' => $exception->getMessage()]);
            return back()->withErrors('No fue posible procesar el pago.');
        }
    }

    public function tracking(Request $request, Pedido $pedido): View
    {
        abort_unless($pedido->user_id === $request->user()->id || $request->user()->hasRole('administrador', 'personal_logistico'), 403);
        abort_unless($pedido->estaPagado() && $pedido->entrega()->exists(), 403, 'El tracking estara disponible cuando el pago sea aprobado y el drone sea asignado.');

        return view('tracking.show', ['pedido' => $pedido->load('estacionEntrega', 'direccionCliente', 'entrega.drone', 'entrega.trackingPoints')]);
    }

    private function notificar(Pedido $pedido, string $titulo, string $mensaje): void
    {
        Notificacion::create(['user_id' => $pedido->user_id, 'pedido_id' => $pedido->id, 'titulo' => $titulo, 'mensaje' => $mensaje]);
    }
}
