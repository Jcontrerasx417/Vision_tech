@extends('layouts.app')

@section('content')
<div class="admin-shell">
    @include('admin.partials.nav')

    <section>
        <div class="admin-section-title">
            <div>
                <span class="badge-status">Inventario</span>
                <h1 class="display-6 fw-bold mt-2 mb-1">Productos</h1>
                <p class="text-muted mb-0">Catalogo, imagen, proveedor, peso para drone y stock disponible.</p>
            </div>
            <a class="btn btn-primary" href="{{ route('admin.productos.create') }}">Nuevo producto</a>
        </div>

        <div class="surface p-3">
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Proveedor</th>
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
                                        <div><strong>{{ $producto->nombre }}</strong><div class="small text-muted">{{ Str::limit($producto->descripcion, 50) }}</div></div>
                                    </div>
                                </td>
                                <td>{{ $producto->proveedor->nombre }}</td>
                                <td>${{ number_format($producto->precio, 0, ',', '.') }}</td>
                                <td>{{ $producto->peso_kg }} kg</td>
                                <td><strong>{{ $producto->stock }}</strong></td>
                                <td>
                                    <span class="badge-status {{ $producto->estadoBadgeClass() }}">{{ $producto->estadoLabel() }}</span>
                                    @if($producto->motivo_rechazo)
                                        <div class="small text-danger">{{ Str::limit($producto->motivo_rechazo, 70) }}</div>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex flex-wrap justify-content-end gap-2">
                                        @if($producto->estado === 'pendiente_revision')
                                            <form method="POST" action="{{ route('admin.productos.aprobar', $producto) }}">
                                                @csrf @method('PATCH')
                                                <button class="btn btn-primary btn-sm">Aprobar</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.productos.rechazar', $producto) }}" class="d-flex gap-1">
                                                @csrf @method('PATCH')
                                                <input class="form-control form-control-sm" name="motivo_rechazo" placeholder="Motivo" required style="max-width: 180px;">
                                                <button class="btn btn-outline-danger btn-sm">Rechazar</button>
                                            </form>
                                        @elseif($producto->estado === 'rechazado')
                                            <form method="POST" action="{{ route('admin.productos.aprobar', $producto) }}">
                                                @csrf @method('PATCH')
                                                <button class="btn btn-primary btn-sm">Aprobar ahora</button>
                                            </form>
                                        @endif
                                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.productos.edit', $producto) }}">Editar</a>
                                        <form method="POST" action="{{ route('admin.productos.destroy', $producto) }}">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm">Eliminar</button></form>
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
