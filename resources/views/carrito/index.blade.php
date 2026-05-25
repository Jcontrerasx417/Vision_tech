@extends('layouts.app')

@section('content')
@php
    $subtotal = $carrito->items->sum(fn($i) => $i->cantidad * $i->producto->precio);
    $peso = $carrito->items->sum(fn($i) => $i->cantidad * $i->producto->peso_kg);
@endphp

<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <span class="badge-status">Carrito</span>
        <h1 class="display-6 fw-bold mt-2 mb-0">Tu pedido en preparacion</h1>
    </div>
    <a class="btn btn-outline-primary" href="{{ route('catalogo.index') }}">Seguir comprando</a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="surface p-3">
            @forelse($carrito->items as $item)
                <div class="d-flex gap-3 align-items-center border-bottom py-3">
                    <img src="{{ $item->producto->imagen_url }}" alt="{{ $item->producto->nombre }}" style="width: 96px; height: 96px; object-fit: cover; border-radius: 8px;">
                    <div class="flex-grow-1">
                        <a class="fw-bold text-dark text-decoration-none" href="{{ route('catalogo.show', $item->producto) }}">{{ $item->producto->nombre }}</a>
                        <div class="text-muted small">{{ $item->producto->peso_kg * $item->cantidad }} kg / unidad: ${{ number_format($item->producto->precio, 0, ',', '.') }}</div>
                        <form method="POST" action="{{ route('carrito.update', $item->id) }}" class="d-flex gap-2 mt-2">
                            @csrf @method('PATCH')
                            <input class="form-control form-control-sm" style="max-width: 90px;" type="number" name="cantidad" min="1" max="{{ $item->producto->stock }}" value="{{ $item->cantidad }}">
                            <button class="btn btn-outline-primary btn-sm">Actualizar</button>
                        </form>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold">${{ number_format($item->cantidad * $item->producto->precio, 0, ',', '.') }}</div>
                        <form method="POST" action="{{ route('carrito.remove', $item->id) }}" class="mt-2">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm">Quitar</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-muted mb-0">El carrito esta vacio.</p>
            @endforelse
        </div>
    </div>
    <div class="col-lg-4">
        <div class="surface p-4">
            <h2 class="h5">Resumen</h2>
            <div class="d-flex justify-content-between border-bottom py-2"><span>Subtotal</span><strong>${{ number_format($subtotal, 0, ',', '.') }}</strong></div>
            <div class="d-flex justify-content-between border-bottom py-2"><span>Peso total</span><strong>{{ number_format($peso, 2) }} kg</strong></div>
            <div class="d-flex justify-content-between py-3 fs-5"><span>Total</span><strong>${{ number_format($subtotal, 0, ',', '.') }}</strong></div>
            <a class="btn btn-primary w-100 {{ $carrito->items->isEmpty() ? 'disabled' : '' }}" href="{{ route('pedidos.create') }}">Crear pedido</a>
        </div>
    </div>
</div>
@endsection
