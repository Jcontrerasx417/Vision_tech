@extends('layouts.app')

@section('content')
<div class="admin-shell">
    @include('proveedor.partials.nav')

    <section>
        <div class="admin-section-title">
            <div>
                <span class="badge-status">Pedidos</span>
                <h1 class="display-6 fw-bold mt-2 mb-1">Pedidos con mis productos</h1>
                <p class="text-muted mb-0">Lineas de pedido donde aparecen productos de {{ $proveedor->nombre }}.</p>
            </div>
        </div>

        <div class="surface p-3">
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead><tr><th>Pedido</th><th>Cliente</th><th>Producto</th><th>Cantidad</th><th>Estado pedido</th><th>Total linea</th></tr></thead>
                    <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>#{{ $item->pedido->id }}</td>
                            <td>{{ $item->pedido->user->name }}</td>
                            <td>{{ $item->producto->nombre }}</td>
                            <td>{{ $item->cantidad }}</td>
                            <td><span class="badge-status">{{ str_replace('_', ' ', $item->pedido->estado) }}</span></td>
                            <td>${{ number_format($item->cantidad * $item->precio_unitario, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted">Aun no hay pedidos con tus productos.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">{{ $items->links() }}</div>
    </section>
</div>
@endsection
