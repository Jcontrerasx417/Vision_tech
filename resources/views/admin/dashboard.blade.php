@extends('layouts.app')

@section('content')
@php($esAdministrador = auth()->user()->hasRole('administrador'))
<div class="admin-shell">
    @include('admin.partials.nav')

    <section>
        <div class="admin-section-title">
            <div>
                <span class="badge-status">Centro de control</span>
                <h1 class="display-6 fw-bold mt-2 mb-1">{{ $esAdministrador ? 'Panel administrador' : 'Panel logistico' }}</h1>
                <p class="text-muted mb-0">
                    {{ $esAdministrador
                        ? 'Vista general de pedidos, pagos, inventario, drones y estaciones.'
                        : 'Vista operativa para seguimiento de pedidos, flota, estaciones y mantenimiento.' }}
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-primary" href="{{ route('admin.pedidos.index') }}">Revisar pedidos</a>
                <a class="btn btn-outline-primary" href="{{ route('admin.drones.index') }}">Flota</a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3"><div class="surface admin-metric p-3"><div class="text-muted">Pedidos pendientes</div><div class="fs-2 fw-bold">{{ $pedidosPendientes }}</div></div></div>
            <div class="col-md-3"><div class="surface admin-metric p-3"><div class="text-muted">Pedidos en ruta</div><div class="fs-2 fw-bold">{{ $pedidosEnRuta }}</div></div></div>
            <div class="col-md-3"><div class="surface admin-metric p-3"><div class="text-muted">Drones disponibles</div><div class="fs-2 fw-bold">{{ $dronesDisponibles }}</div></div></div>
            <div class="col-md-3"><div class="surface admin-metric p-3"><div class="text-muted">Pagos pendientes</div><div class="fs-2 fw-bold">{{ $pagosPendientes }}</div></div></div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-xl-8">
                <div class="surface p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h2 class="h5 mb-0">Pedidos recientes</h2>
                            <div class="text-muted small">Ultimos movimientos de clientes</div>
                        </div>
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.pedidos.index') }}">Ver todos</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table admin-table">
                            <thead>
                                <tr>
                                    <th>Pedido</th>
                                    <th>Cliente</th>
                                    <th>Estado</th>
                                    <th>Drone</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pedidos as $pedido)
                                    <tr>
                                        <td>#{{ $pedido->id }}</td>
                                        <td>{{ $pedido->user->name }}</td>
                                        <td><span class="badge-status">{{ str_replace('_', ' ', $pedido->estado) }}</span></td>
                                        <td>{{ $pedido->entrega?->drone?->codigo ?? '-' }}</td>
                                        <td>${{ number_format($pedido->total, 0, ',', '.') }}</td>
                                        <td class="text-end"><a class="btn btn-outline-secondary btn-sm" href="{{ route('pedidos.show', $pedido) }}">Detalle</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-muted">No hay pedidos registrados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="surface p-3 h-100">
                    <h2 class="h5">Estado de flota</h2>
                    <div class="d-flex justify-content-between border-bottom py-2"><span>Disponibles</span><strong>{{ $dronesDisponibles }}</strong></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span>En vuelo</span><strong>{{ $dronesEnVuelo }}</strong></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span>Mantenimiento</span><strong>{{ $dronesMantenimiento }}</strong></div>
                    <div class="d-flex justify-content-between py-2"><span>Total drones</span><strong>{{ $drones }}</strong></div>
                    <a class="btn btn-primary w-100 mt-3" href="{{ route('admin.mantenimientos.index') }}">Revisar mantenimiento</a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            @if($esAdministrador)
                <div class="col-lg-6">
                    <div class="surface p-3 h-100">
                        <h2 class="h5">Alertas de inventario</h2>
                        @forelse($stockBajo as $producto)
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span>{{ $producto->nombre }}</span>
                                <strong>Stock {{ $producto->stock }}</strong>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No hay productos con stock critico.</p>
                        @endforelse
                    </div>
                </div>
            @endif
            <div class="{{ $esAdministrador ? 'col-lg-6' : 'col-lg-12' }}">
                <div class="surface p-3 h-100">
                    <h2 class="h5">Alertas de drones</h2>
                    @forelse($dronesCriticos as $drone)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span>{{ $drone->codigo }} / {{ $drone->modelo }}</span>
                            <strong>{{ $drone->bateria }}%</strong>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No hay drones con bateria critica.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
