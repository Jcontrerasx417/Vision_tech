@extends('layouts.app')

@section('content')
<div class="vt-page-heading">
    <div>
        <div class="vt-kicker">Facturas</div>
        <h1 class="display-6 fw-bold mt-2 mb-1">Facturacion</h1>
        <p class="text-muted mb-0">Consulta las facturas generadas por pagos aprobados.</p>
    </div>
</div>

<div class="vt-card overflow-hidden">
    <div class="table-responsive">
        <table class="table admin-table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Factura</th>
                    <th>Pedido</th>
                    <th>Cliente</th>
                    <th>Emitida</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($facturas as $factura)
                    <tr>
                        <td><strong>{{ $factura->numero }}</strong></td>
                        <td>#{{ $factura->pedido_id }}</td>
                        <td>{{ $factura->pedido->user->name }}</td>
                        <td>{{ optional($factura->emitida_en)->format('Y-m-d H:i') }}</td>
                        <td class="fw-bold">${{ number_format($factura->total, 0, ',', '.') }}</td>
                        <td class="text-end"><a class="btn btn-outline-primary btn-sm" href="{{ route('facturas.show', $factura) }}">Ver factura</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-muted p-4">Aun no hay facturas generadas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $facturas->links() }}</div>
@endsection
