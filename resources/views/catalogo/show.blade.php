@extends('layouts.app')

@section('content')
<div class="row g-4 align-items-start">
    <div class="col-lg-6">
        <div class="surface overflow-hidden">
            <img src="{{ $producto->imagenPrincipalUrl() }}" alt="{{ $producto->nombre }}" class="w-100" style="height: 460px; object-fit: cover;">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="surface p-4">
            <span class="badge-status">{{ $producto->proveedor->nombre }}</span>
            <h1 class="display-6 fw-bold mt-3">{{ $producto->nombre }}</h1>
            <p class="text-muted fs-5">{{ $producto->descripcion }}</p>
            <div class="row g-3 my-3">
                <div class="col-4"><div class="border rounded p-3"><div class="small text-muted">Precio</div><strong>${{ number_format($producto->precio, 0, ',', '.') }}</strong></div></div>
                <div class="col-4"><div class="border rounded p-3"><div class="small text-muted">Peso</div><strong>{{ $producto->peso_kg }} kg</strong></div></div>
                <div class="col-4"><div class="border rounded p-3"><div class="small text-muted">Stock</div><strong>{{ $producto->stock }}</strong></div></div>
            </div>
            <p class="mb-1"><strong>Proveedor:</strong> {{ $producto->proveedor->nombre }}</p>
            <p class="mb-4"><strong>Entrega:</strong> compatible con recogida en estacion o domicilio con drone disponible.</p>
            @auth
                @if(auth()->user()->hasRole('cliente'))
                <form method="POST" action="{{ route('carrito.add', $producto) }}" class="d-flex gap-2">
                    @csrf
                    <input class="form-control form-control-lg" style="max-width: 120px;" type="number" name="cantidad" value="1" min="1" max="{{ $producto->stock }}">
                    <button class="btn btn-primary btn-lg">Agregar al carrito</button>
                </form>
                @else
                    <div class="alert alert-warning mb-0">Solo las cuentas cliente pueden agregar productos al carrito.</div>
                @endif
            @else
                <a class="btn btn-primary btn-lg" href="{{ route('login') }}">Ingresar para comprar</a>
            @endauth
        </div>
    </div>
</div>

@if($relacionados->isNotEmpty())
    <h2 class="h4 fw-bold mt-5 mb-3">Mas productos del proveedor</h2>
    <div class="row g-3">
        @foreach($relacionados as $relacionado)
            <div class="col-md-4">
                <a href="{{ route('catalogo.show', $relacionado) }}" class="surface d-flex align-items-center gap-3 p-3 text-decoration-none text-dark">
                    <img src="{{ $relacionado->imagenPrincipalUrl() }}" alt="{{ $relacionado->nombre }}" style="width: 84px; height: 84px; object-fit: cover; border-radius: 6px;">
                    <div>
                        <strong>{{ $relacionado->nombre }}</strong>
                        <div class="text-muted">${{ number_format($relacionado->precio, 0, ',', '.') }}</div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endif
@endsection
