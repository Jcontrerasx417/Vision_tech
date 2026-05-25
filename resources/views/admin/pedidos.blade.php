@extends('layouts.app')

@section('content')
<div class="admin-shell">
    @include('admin.partials.nav')

    <section>
        <div class="admin-section-title">
            <div>
                <span class="badge-status">Operacion</span>
                <h1 class="display-6 fw-bold mt-2 mb-1">Gestion de pedidos</h1>
                <p class="text-muted mb-0">Actualiza estados, asigna drones y revisa el mapa de cada entrega.</p>
            </div>
            <a class="btn btn-outline-primary" href="{{ route('admin.dashboard') }}">Resumen</a>
        </div>

        <div class="surface p-3">
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th>Pedido</th>
                            <th>Cliente</th>
                            <th>Entrega</th>
                            <th>Estado</th>
                            <th>Drone</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pedidos as $pedido)
                            <tr>
                                <td><strong>#{{ $pedido->id }}</strong><div class="small text-muted">{{ $pedido->peso_total_kg }} kg</div></td>
                                <td>{{ $pedido->user->name }}<div class="small text-muted">{{ $pedido->user->email }}</div></td>
                                <td>{{ $pedido->tipo_entrega }}</td>
                                <td><span class="badge-status">{{ str_replace('_', ' ', $pedido->estado) }}</span></td>
                                <td>{{ $pedido->entrega?->drone?->codigo ?? 'Sin asignar' }}</td>
                                <td>${{ number_format($pedido->total, 0, ',', '.') }}</td>
                                <td style="min-width: 360px;">
                                    <div class="d-flex flex-column gap-2">
                                        <form method="POST" action="{{ route('admin.pedidos.estado', $pedido) }}" class="d-flex gap-2">
                                            @csrf @method('PATCH')
                                            <select class="form-select form-select-sm" name="estado">@foreach(\App\Models\Pedido::ESTADOS as $estado)<option @selected($pedido->estado === $estado)>{{ $estado }}</option>@endforeach</select>
                                            <button class="btn btn-primary btn-sm">Estado</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.pedidos.drone', $pedido) }}" class="d-flex gap-2">
                                            @csrf @method('PATCH')
                                            <select class="form-select form-select-sm" name="drone_id">@foreach($drones as $drone)<option value="{{ $drone->id }}">{{ $drone->codigo }} / {{ $drone->capacidad_kg }} kg / {{ $drone->estado }}</option>@endforeach</select>
                                            <button class="btn btn-outline-primary btn-sm">Drone</button>
                                        </form>
                                        <div class="d-flex gap-2">
                                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('pedidos.show', $pedido) }}">Detalle</a>
                                            <a class="btn btn-primary btn-sm" href="{{ route('pedidos.tracking', $pedido) }}">Mapa</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">{{ $pedidos->links() }}</div>
    </section>
</div>
@endsection
