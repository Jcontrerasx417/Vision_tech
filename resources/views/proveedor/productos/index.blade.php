@extends('layouts.app')

@section('content')
<div class="admin-shell">
    @include('proveedor.partials.nav')

    <section>
        <div class="admin-section-title">
            <div>
                <span class="badge-status">Catalogo proveedor</span>
                <h1 class="display-6 fw-bold mt-2 mb-1">Mis productos</h1>
                <p class="text-muted mb-0">Productos publicados por {{ $proveedor->nombre }}.</p>
            </div>
            <a class="btn btn-primary" href="{{ route('proveedor.productos.create') }}">Nuevo producto</a>
        </div>

        <div class="surface p-3">
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Peso</th>
                            <th>Stock</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productos as $producto)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $producto->imagenPrincipalUrl() }}" alt="{{ $producto->nombre }}" style="width: 74px; height: 58px; object-fit: cover; border-radius: 6px;">
                                        <div><strong>{{ $producto->nombre }}</strong><div class="small text-muted">{{ Str::limit($producto->descripcion, 48) }}</div></div>
                                    </div>
                                </td>
                                <td>${{ number_format($producto->precio, 0, ',', '.') }}</td>
                                <td>{{ $producto->peso_kg }} kg</td>
                                <td>{{ $producto->stock }}</td>
                                <td>
                                    <span class="badge-status {{ $producto->estadoBadgeClass() }}">{{ $producto->estadoLabel() }}</span>
                                    @if($producto->estado === 'pendiente_revision')
                                        <div class="small text-muted">Esperando aprobacion del administrador.</div>
                                    @endif
                                    @if($producto->estado === 'rechazado' && $producto->motivo_rechazo)
                                        <div class="small text-danger">Motivo: {{ Str::limit($producto->motivo_rechazo, 80) }}</div>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('proveedor.productos.edit', $producto) }}">Editar</a>
                                        @if($producto->activo)
                                            <form method="POST" action="{{ route('proveedor.productos.desactivar', $producto) }}">@csrf @method('PATCH')<button class="btn btn-outline-primary btn-sm">Desactivar</button></form>
                                        @elseif($producto->estado === 'aprobado')
                                            <form method="POST" action="{{ route('proveedor.productos.activar', $producto) }}">@csrf @method('PATCH')<button class="btn btn-primary btn-sm">Activar</button></form>
                                        @endif
                                        <form method="POST" action="{{ route('proveedor.productos.destroy', $producto) }}">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm">Eliminar</button></form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">{{ $productos->links() }}</div>
    </section>
</div>
@endsection
