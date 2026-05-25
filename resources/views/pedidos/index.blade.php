@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <span class="badge-status">Mis pedidos</span>
        <h1 class="display-6 fw-bold mt-2 mb-0">Historial y seguimiento</h1>
    </div>
    <a class="btn btn-primary" href="{{ route('catalogo.index') }}">Comprar mas</a>
</div>

@forelse($pedidos as $pedido)
    <div class="surface p-3 mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <strong class="fs-5">Pedido #{{ $pedido->id }}</strong>
                <div class="text-muted">${{ number_format($pedido->total, 0, ',', '.') }} / {{ $pedido->peso_total_kg }} kg / {{ $pedido->tipo_entrega }}</div>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="badge-status">{{ str_replace('_', ' ', $pedido->estado) }}</span>
                <span class="small text-muted">Pago: {{ $pedido->pago?->estado ?? 'pendiente' }}</span>
                <span class="small text-muted">Drone: {{ $pedido->entrega?->drone?->codigo ?? '-' }}</span>
                <a class="btn btn-outline-primary btn-sm" href="{{ route('pedidos.show', $pedido) }}">Detalle</a>
                <a class="btn btn-primary btn-sm" href="{{ route('pedidos.tracking', $pedido) }}">Mapa</a>
            </div>
        </div>
    </div>
@empty
    <div class="surface p-4 text-center">
        <h2 class="h4">Aun no tienes pedidos</h2>
        <p class="text-muted">Explora el catalogo y crea tu primer pedido con entrega por drone.</p>
        <a class="btn btn-primary" href="{{ route('catalogo.index') }}">Ir al catalogo</a>
    </div>
@endforelse
{{ $pedidos->links() }}
@endsection
