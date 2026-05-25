@extends('layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <span class="badge-status">Catalogo inteligente</span>
        <h1 class="display-6 fw-bold mt-2 mb-1">Productos listos para despegar</h1>
        <p class="text-muted mb-0">Compra productos livianos compatibles con entregas por drone.</p>
    </div>
    @auth
        @if(auth()->user()->hasRole('cliente'))
            <a class="btn btn-outline-primary" href="{{ route('carrito.index') }}">Ver carrito</a>
        @endif
    @endauth
</div>

<div class="row g-4">
    @foreach($productos as $producto)
        <div class="col-md-6 col-xl-3">
            <div class="surface h-100 overflow-hidden">
                <a href="{{ route('catalogo.show', $producto) }}" class="text-decoration-none text-dark">
                    <img src="{{ $producto->imagenPrincipalUrl() }}" alt="{{ $producto->nombre }}" class="w-100" style="height: 190px; object-fit: cover;">
                </a>
                <div class="p-3">
                    <div class="small text-muted mb-1">{{ $producto->proveedor->nombre }}</div>
                    <h2 class="h5 mb-2">
                        <a href="{{ route('catalogo.show', $producto) }}" class="text-dark text-decoration-none">{{ $producto->nombre }}</a>
                    </h2>
                    <p class="text-muted small mb-3" style="min-height: 42px;">{{ Str::limit($producto->descripcion, 80) }}</p>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold fs-5">${{ number_format($producto->precio, 0, ',', '.') }}</span>
                        <span class="badge text-bg-dark">{{ $producto->peso_kg }} kg</span>
                    </div>
                    <div class="small text-muted mb-3">Stock disponible: {{ $producto->stock }}</div>
                    @auth
                        @if(auth()->user()->hasRole('cliente'))
                        <form method="POST" action="{{ route('carrito.add', $producto) }}" class="d-flex gap-2">
                            @csrf
                            <input class="form-control" type="number" name="cantidad" value="1" min="1" max="{{ $producto->stock }}">
                            <button class="btn btn-primary">Agregar</button>
                        </form>
                        @else
                            <div class="alert alert-warning mb-0 py-2">Solo las cuentas cliente pueden comprar.</div>
                        @endif
                    @else
                        <a class="btn btn-primary w-100" href="{{ route('login') }}">Ingresar para comprar</a>
                    @endauth
                </div>
            </div>
        </div>
    @endforeach
</div>
<div class="mt-4">{{ $productos->links() }}</div>
@endsection
