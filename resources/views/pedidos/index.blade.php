@extends('layouts.app')

@section('content')
<div class="vt-page-heading">
    <div>
        <div class="vt-kicker">Mis pedidos</div>
        <h1 class="display-6 fw-bold mt-2 mb-1">Historial y seguimiento</h1>
        <p class="text-muted mb-0">El mapa y el dron aparecen cuando el pago queda aprobado.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('catalogo.index') }}">
        <span class="material-symbols-outlined">add</span>
        Comprar mas
    </a>
</div>

@forelse($pedidos as $pedido)
    @php
        $pagado = $pedido->estaPagado();
        $pagoEstado = $pedido->pago?->estado ?? 'pendiente';
    @endphp
    <article class="vt-card p-3 p-md-4 mb-3">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div class="d-flex gap-3">
                <div class="vt-icon-box">
                    <span class="material-symbols-outlined">{{ $pagado ? 'package' : 'pending_actions' }}</span>
                </div>
                <div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        <strong class="fs-5">Pedido #{{ $pedido->id }}</strong>
                        <span class="badge-status {{ $pagado ? 'badge-paid' : 'badge-pending' }}">{{ $pagado ? 'Pagado' : 'Pendiente' }}</span>
                    </div>
                    <div class="text-muted">${{ number_format($pedido->total, 0, ',', '.') }} / {{ $pedido->peso_total_kg }} kg / {{ $pedido->tipo_entrega }}</div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="badge-status badge-muted-soft">{{ str_replace('_', ' ', $pedido->estado) }}</span>
                <span class="small text-muted">Pago: {{ $pagoEstado }}</span>
                <span class="small text-muted">Dron: {{ $pedido->entrega?->drone?->codigo ?? '-' }}</span>
                <a class="btn btn-outline-primary btn-sm" href="{{ route('pedidos.show', $pedido) }}">Detalle</a>
                @if($pagado && $pedido->entrega)
                    <a class="btn btn-primary btn-sm" href="{{ route('pedidos.tracking', $pedido) }}">
                        <span class="material-symbols-outlined" style="font-size: 18px;">location_pin</span>
                        Mapa
                    </a>
                @else
                    <button class="btn btn-outline-secondary btn-sm" disabled>
                        <span class="material-symbols-outlined" style="font-size: 18px;">lock</span>
                        Sin mapa
                    </button>
                @endif
            </div>
        </div>
    </article>
@empty
    <div class="vt-card p-5 text-center">
        <span class="material-symbols-outlined text-yellow mb-3" style="font-size: 48px;">package</span>
        <h2 class="h4 fw-bold">Aun no tienes pedidos</h2>
        <p class="text-muted">Explora el catalogo y crea tu primer pedido con entrega por dron.</p>
        <a class="btn btn-primary" href="{{ route('catalogo.index') }}">Ir al catalogo</a>
    </div>
@endforelse

<div class="mt-3">{{ $pedidos->links() }}</div>
@endsection
