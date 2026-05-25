@extends('layouts.app')

@section('content')
@php($pedido = $factura->pedido)
<div class="vt-page-heading">
    <div>
        <div class="vt-kicker">Factura {{ $factura->numero }}</div>
        <h1 class="display-6 fw-bold mt-2 mb-1">Factura de pedido #{{ $pedido->id }}</h1>
        <p class="text-muted mb-0">Emitida {{ optional($factura->emitida_en)->format('Y-m-d H:i') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary" href="{{ route('facturas.index') }}">Volver</a>
        <button class="btn btn-primary" onclick="window.print()">Imprimir</button>
    </div>
</div>

<div class="vt-card p-4">
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="vt-kicker">Cliente</div>
            <h2 class="h5 fw-bold mb-1">{{ $pedido->user->name }}</h2>
            <div class="text-muted">{{ $pedido->user->email }}</div>
            <div class="text-muted">{{ $pedido->user->telefono ?? 'Sin telefono' }}</div>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="vt-kicker">Pago</div>
            <h2 class="h5 fw-bold mb-1">{{ $pedido->pago?->proveedor_pago ?? $pedido->pago?->metodo }}</h2>
            <div class="text-muted">Referencia: {{ $pedido->pago?->referencia_externa ?? '-' }}</div>
            <div class="text-muted">Dron: {{ $pedido->entrega?->drone?->codigo ?? 'Sin asignar' }}</div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Peso</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedido->items as $item)
                    <tr>
                        <td>{{ $item->producto->nombre }}</td>
                        <td>{{ $item->cantidad }}</td>
                        <td>{{ $item->cantidad * $item->peso_unitario_kg }} kg</td>
                        <td class="text-end">${{ number_format($item->cantidad * $item->precio_unitario, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end">
        <div style="min-width: 280px;">
            <div class="d-flex justify-content-between border-bottom py-2"><span>Subtotal</span><strong>${{ number_format($factura->subtotal, 0, ',', '.') }}</strong></div>
            <div class="d-flex justify-content-between border-bottom py-2"><span>Impuestos</span><strong>${{ number_format($factura->impuestos, 0, ',', '.') }}</strong></div>
            <div class="d-flex justify-content-between fs-4 fw-bold py-2"><span>Total</span><span>${{ number_format($factura->total, 0, ',', '.') }}</span></div>
        </div>
    </div>
</div>
@endsection
