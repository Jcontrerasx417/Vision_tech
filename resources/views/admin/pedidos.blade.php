@extends('layouts.app')

@section('content')
<div class="admin-shell">
    @include('admin.partials.nav')

    <section>
        <div class="vt-page-heading">
            <div>
                <div class="vt-kicker">Operacion</div>
                <h1 class="display-6 fw-bold mt-2 mb-1">Gestion de pedidos</h1>
                <p class="text-muted mb-0">Supervision de pagos, estados, drones y tracking.</p>
            </div>
            <a class="btn btn-outline-primary" href="{{ route('admin.dashboard') }}">Resumen</a>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="vt-metric">
                    <div><div class="vt-kicker">Total</div><div class="h3 fw-bold mb-0">{{ $pedidos->total() }}</div></div>
                    <span class="material-symbols-outlined text-yellow fs-1">trending_up</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="vt-metric">
                    <div><div class="vt-kicker">Pagados</div><div class="h3 fw-bold mb-0">{{ $pedidos->getCollection()->filter->estaPagado()->count() }}</div></div>
                    <span class="material-symbols-outlined text-success fs-1">check_circle</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="vt-metric">
                    <div><div class="vt-kicker">Pendientes</div><div class="h3 fw-bold mb-0">{{ $pedidos->getCollection()->reject->estaPagado()->count() }}</div></div>
                    <span class="material-symbols-outlined text-warning fs-1">pending_actions</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="vt-metric">
                    <div><div class="vt-kicker">Drones</div><div class="h3 fw-bold mb-0">{{ $drones->count() }}</div></div>
                    <span class="material-symbols-outlined fs-1" style="color: var(--dc-tertiary);">drone</span>
                </div>
            </div>
        </div>

        <div class="vt-card overflow-hidden">
            <div class="px-4 py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3" style="background: var(--dc-surface-low);">
                <h2 class="h5 fw-bold mb-0">Listado de pedidos</h2>
                <div class="input-group" style="max-width: 320px;">
                    <span class="input-group-text bg-white"><span class="material-symbols-outlined" style="font-size: 18px;">search</span></span>
                    <input class="form-control" type="search" placeholder="Buscar pedido o cliente">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table admin-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Pedido</th>
                            <th>Cliente</th>
                            <th>Pago</th>
                            <th>Estado</th>
                            <th>Dron</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pedidos as $pedido)
                            @php
                                $pagado = $pedido->estaPagado();
                                $pagoEstado = $pedido->pago?->estado ?? 'pendiente';
                                $entregaFinalizada = $pedido->entrega?->estado === 'finalizada' || $pedido->estado === 'entregado';
                                $initials = collect(explode(' ', $pedido->user->name))->filter()->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->join('');
                            @endphp
                            <tr>
                                <td>
                                    <strong class="font-monospace">#{{ $pedido->id }}</strong>
                                    <div class="small text-muted">{{ $pedido->peso_total_kg }} kg / {{ $pedido->tipo_entrega }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold" style="width: 34px; height: 34px; background: var(--dc-surface-high);">{{ $initials }}</span>
                                        <span>
                                            {{ $pedido->user->name }}
                                            <div class="small text-muted">{{ $pedido->user->email }}</div>
                                        </span>
                                    </div>
                                </td>
                                <td><span class="badge-status {{ $pagado ? 'badge-paid' : 'badge-pending' }}">{{ $pagado ? 'Pagado' : $pagoEstado }}</span></td>
                                <td><span class="badge-status badge-muted-soft">{{ str_replace('_', ' ', $pedido->estado) }}</span></td>
                                <td>
                                    @if($pedido->entrega?->drone)
                                        <span class="material-symbols-outlined text-yellow" style="font-size: 18px;">drone</span>
                                        <span class="font-monospace">{{ $pedido->entrega->drone->codigo }}</span>
                                    @else
                                        <span class="text-muted">Sin asignar</span>
                                    @endif
                                </td>
                                <td class="fw-bold font-monospace">${{ number_format($pedido->total, 0, ',', '.') }}</td>
                                <td style="min-width: 390px;">
                                    <div class="d-flex flex-column gap-2">
                                        <form method="POST" action="{{ route('admin.pedidos.estado', $pedido) }}" class="d-flex gap-2">
                                            @csrf @method('PATCH')
                                            <select class="form-select form-select-sm" name="estado">
                                                @foreach(\App\Models\Pedido::ESTADOS as $estado)
                                                    <option @selected($pedido->estado === $estado)>{{ $estado }}</option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-primary btn-sm">Estado</button>
                                        </form>

                                        @if($pagado)
                                            <form method="POST" action="{{ route('admin.pedidos.drone', $pedido) }}" class="d-flex gap-2">
                                                @csrf @method('PATCH')
                                                <select class="form-select form-select-sm" name="drone_id">
                                                    @foreach($drones as $drone)
                                                        <option value="{{ $drone->id }}">{{ $drone->codigo }} / {{ $drone->capacidad_kg }} kg / {{ $drone->estado }}</option>
                                                    @endforeach
                                                </select>
                                                <button class="btn btn-outline-primary btn-sm">
                                                    <span class="material-symbols-outlined" style="font-size: 18px;">battery_android_bolt</span>
                                                </button>
                                            </form>
                                        @else
                                            <div class="d-flex align-items-center gap-2 text-muted small">
                                                <span class="material-symbols-outlined" style="font-size: 18px;">lock</span>
                                                Asignacion de dron bloqueada hasta aprobar pago.
                                            </div>
                                        @endif

                                        <div class="d-flex gap-2">
                                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('pedidos.show', $pedido) }}">Detalle</a>
                                            @if($pagado && $pedido->entrega)
                                                <a class="btn btn-primary btn-sm" href="{{ route('pedidos.tracking', $pedido) }}">
                                                    <span class="material-symbols-outlined" style="font-size: 18px;">location_pin</span>
                                                    Mapa
                                                </a>
                                                @if(!$entregaFinalizada)
                                                    <form method="POST" action="{{ route('admin.pedidos.simular-vuelo', $pedido) }}">
                                                        @csrf
                                                        <button class="btn btn-outline-primary btn-sm" title="Simular vuelo GPS y consumo de bateria">
                                                            <span class="material-symbols-outlined" style="font-size: 18px;">flight_takeoff</span>
                                                            Simular vuelo
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.entregas.finalizar', $pedido->entrega) }}">
                                                        @csrf
                                                        <button class="btn btn-outline-success btn-sm" title="Finalizar entrega y retornar dron a base">
                                                            <span class="material-symbols-outlined" style="font-size: 18px;">assignment_turned_in</span>
                                                            Finalizar
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="badge-status badge-paid">Entregado</span>
                                                @endif
                                            @else
                                                <button class="btn btn-outline-secondary btn-sm" disabled>
                                                    <span class="material-symbols-outlined" style="font-size: 18px;">lock</span>
                                                    Sin mapa
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center" style="background: var(--dc-surface-low);">
                <span class="vt-kicker">Mostrando {{ $pedidos->firstItem() ?? 0 }}-{{ $pedidos->lastItem() ?? 0 }} de {{ $pedidos->total() }}</span>
                {{ $pedidos->links() }}
            </div>
        </div>
        <section class="vt-card p-4 mt-4">
            <div class="d-flex align-items-start gap-3">
                <div class="vt-icon-box"><span class="material-symbols-outlined">sensors</span></div>
                <div>
                    <h3 class="h5 fw-bold mb-1">Operacion real de drones</h3>
                    <p class="text-muted mb-0">Usa las acciones de cada pedido para simular vuelo GPS, consumir bateria, finalizar entrega y retornar el dron a base.</p>
                </div>
            </div>
        </section>
    </section>
</div>
@endsection
