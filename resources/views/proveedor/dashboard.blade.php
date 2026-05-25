@extends('layouts.app')

@section('content')
<div class="admin-shell">
    @include('proveedor.partials.nav')

    <section>
        <div class="admin-section-title">
            <div>
                <span class="badge-status">Panel proveedor</span>
                <h1 class="display-6 fw-bold mt-2 mb-1">{{ $proveedor->nombre }}</h1>
                <p class="text-muted mb-0">Estado del proveedor: <strong>{{ $proveedor->activo ? 'aprobado' : 'pendiente o inactivo' }}</strong></p>
            </div>
            <a class="btn btn-primary {{ ! $proveedor->activo ? 'disabled' : '' }}" href="{{ route('proveedor.productos.create') }}">Publicar producto</a>
        </div>

        @if(! $proveedor->activo)
            <div class="alert alert-warning">Tu perfil de proveedor aun debe ser aprobado por el administrador antes de publicar productos.</div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="surface admin-metric p-3"><div class="text-muted">Productos publicados</div><div class="fs-2 fw-bold">{{ $totalProductos }}</div></div></div>
            <div class="col-md-4"><div class="surface admin-metric p-3"><div class="text-muted">Activos en catalogo</div><div class="fs-2 fw-bold">{{ $activos }}</div></div></div>
            <div class="col-md-4"><div class="surface admin-metric p-3"><div class="text-muted">Stock total</div><div class="fs-2 fw-bold">{{ $stockTotal }}</div></div></div>
        </div>

        <div class="surface p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">Ultimos productos</h2>
                <a class="btn btn-outline-primary btn-sm" href="{{ route('proveedor.productos.index') }}">Ver todos</a>
            </div>
            <div class="row g-3">
                @forelse($productos as $producto)
                    <div class="col-md-6 col-xl-4">
                        <div class="border rounded p-2 h-100">
                            <img src="{{ $producto->imagenPrincipalUrl() }}" class="w-100 rounded mb-2" style="height: 130px; object-fit: cover;" alt="{{ $producto->nombre }}">
                            <strong>{{ $producto->nombre }}</strong>
                            <div class="text-muted small">${{ number_format($producto->precio, 0, ',', '.') }} / stock {{ $producto->stock }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Aun no tienes productos publicados.</p>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection
