@extends('layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <span class="badge-status">Pedido #{{ $pedido->id }}</span>
        <h1 class="display-6 fw-bold mt-2 mb-0">Detalle del pedido</h1>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-primary" href="{{ route('pedidos.tracking', $pedido) }}">Ver mapa</a>
        @if(!$pedido->pago)
            <form method="POST" action="{{ route('pedidos.pagar', $pedido) }}">@csrf <button class="btn btn-success">Simular pago</button></form>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="surface p-3">
            <h2 class="h5">Productos</h2>
            @foreach($pedido->items as $item)
                <div class="d-flex gap-3 align-items-center border-bottom py-3">
                    <img src="{{ $item->producto->imagen_url }}" alt="{{ $item->producto->nombre }}" style="width: 86px; height: 76px; object-fit: cover; border-radius: 8px;">
                    <div class="flex-grow-1">
                        <strong>{{ $item->producto->nombre }}</strong>
                        <div class="text-muted small">{{ $item->cantidad }} unidades / {{ $item->peso_unitario_kg * $item->cantidad }} kg</div>
                    </div>
                    <strong>${{ number_format($item->cantidad * $item->precio_unitario, 0, ',', '.') }}</strong>
                </div>
            @endforeach
        </div>
    </div>
    <div class="col-lg-4">
        <div class="surface p-3 mb-3">
            <h2 class="h5">Resumen</h2>
            <div class="d-flex justify-content-between border-bottom py-2"><span>Estado</span><strong>{{ str_replace('_', ' ', $pedido->estado) }}</strong></div>
            <div class="d-flex justify-content-between border-bottom py-2"><span>Entrega</span><strong>{{ $pedido->tipo_entrega }}</strong></div>
            <div class="d-flex justify-content-between border-bottom py-2"><span>Peso</span><strong>{{ $pedido->peso_total_kg }} kg</strong></div>
            <div class="d-flex justify-content-between py-2"><span>Total</span><strong>${{ number_format($pedido->total, 0, ',', '.') }}</strong></div>
        </div>
        <div class="surface p-3">
            <h2 class="h5">Operacion</h2>
            <p class="mb-1"><strong>Drone:</strong> {{ $pedido->entrega?->drone?->codigo ?? 'Sin asignar' }}</p>
            <p class="mb-1"><strong>Estado entrega:</strong> {{ $pedido->entrega?->estado ?? 'Sin entrega' }}</p>
            <p class="mb-1"><strong>Pago:</strong> {{ $pedido->pago?->estado ?? 'pendiente' }}</p>
            <p class="mb-0"><strong>Factura:</strong> {{ $pedido->factura?->numero ?? 'No generada' }}</p>
        </div>
    </div>
</div>
@endsection
