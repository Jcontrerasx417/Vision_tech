@extends('layouts.app')

@section('content')
@php
    $pagado = $pedido->estaPagado();
    $pagoEstado = $pedido->pago?->estado ?? 'pendiente';
    $destino = $pedido->tipo_entrega === 'domicilio'
        ? ($pedido->direccion_entrega ?? 'Direccion de domicilio')
        : ($pedido->estacionEntrega?->nombre ?? 'Estacion de entrega');
    $originPoint = [
        'lat' => (float) ($pedido->entrega?->drone?->estacionEntrega?->latitud ?? $pedido->estacionEntrega?->latitud ?? 7.1150670),
        'lng' => (float) ($pedido->entrega?->drone?->estacionEntrega?->longitud ?? $pedido->estacionEntrega?->longitud ?? -73.1086300),
        'label' => $pedido->entrega?->drone?->estacionEntrega?->nombre ?? 'Base del dron',
    ];
    $destinationPoint = $pedido->tipo_entrega === 'domicilio'
        ? [
            'lat' => (float) ($pedido->destino_latitud ?? $pedido->direccionCliente?->latitud ?? 7.1253930),
            'lng' => (float) ($pedido->destino_longitud ?? $pedido->direccionCliente?->longitud ?? -73.1198040),
            'label' => $destino,
        ]
        : [
            'lat' => (float) ($pedido->estacionEntrega?->latitud ?? 7.1253930),
            'lng' => (float) ($pedido->estacionEntrega?->longitud ?? -73.1198040),
            'label' => $destino,
        ];
@endphp

@if($pagado && $pedido->entrega)
    @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const mapElement = document.getElementById('order-mini-map');
                if (!mapElement) return;

                const origin = @json($originPoint);
                const destination = @json($destinationPoint);
                const map = L.map('order-mini-map', {
                    scrollWheelZoom: false,
                    zoomControl: false,
                    attributionControl: false
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19
                }).addTo(map);

                const route = L.polyline([[origin.lat, origin.lng], [destination.lat, destination.lng]], {
                    color: '#ffd700',
                    weight: 5,
                    opacity: .95,
                    dashArray: '8, 10'
                }).addTo(map);

                L.marker([origin.lat, origin.lng]).addTo(map).bindPopup(origin.label);
                L.marker([destination.lat, destination.lng]).addTo(map).bindPopup(destination.label).openPopup();
                map.fitBounds(route.getBounds(), { padding: [30, 30] });
            });
        </script>
    @endpush
@endif

<style>
    #order-mini-map {
        min-height: 290px;
        border: 1px solid var(--dc-line);
        border-radius: 8px;
        overflow: hidden;
    }

    .order-mini-map-wrap {
        position: relative;
    }

    .order-mini-map-action {
        position: absolute;
        left: 1rem;
        right: 1rem;
        bottom: 1rem;
        z-index: 500;
    }
</style>

<div class="vt-page-heading">
    <div>
        <div class="vt-kicker">Pedido #{{ $pedido->id }}</div>
        <h1 class="display-6 fw-bold mt-2 mb-1">{{ $pagado ? 'Entrega en preparacion' : 'Accion requerida' }}</h1>
        <p class="text-muted mb-0">{{ $pagado ? 'Pago aprobado. La operacion logistica ya puede iniciar.' : 'Completa el pago para activar la asignacion del dron.' }}</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <span class="badge-status {{ $pagado ? 'badge-paid' : 'badge-pending' }}">{{ $pagado ? 'Pago aprobado' : 'Pendiente de pago' }}</span>
        @if($pagado && $pedido->entrega)
            <a class="btn btn-primary" href="{{ route('pedidos.tracking', $pedido) }}">
                <span class="material-symbols-outlined">location_pin</span>
                Ver mapa
            </a>
        @else
            <button class="btn btn-outline-secondary" disabled>
                <span class="material-symbols-outlined">lock</span>
                Mapa al pagar
            </button>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        @if(!$pagado)
            <section class="vt-card p-4 mb-4 border-start border-5" style="border-left-color: var(--dc-yellow) !important;">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-4">
                    <div>
                        <div class="vt-kicker">Total a pagar</div>
                        <div class="display-6 fw-bold">${{ number_format($pedido->total, 0, ',', '.') }}</div>
                        <p class="text-muted mb-0">El dron queda bloqueado hasta que el pago sea aprobado.</p>
                    </div>
                    <div class="d-grid gap-2">
                        <form method="POST" action="{{ route('pedidos.mercado-pago', $pedido) }}">
                            @csrf
                            <button class="btn btn-primary btn-lg w-100">
                                <span class="material-symbols-outlined">payments</span>
                                Pagar con Mercado Pago
                            </button>
                        </form>
                        <form method="POST" action="{{ route('pedidos.pagar', $pedido) }}">
                            @csrf
                            <button class="btn btn-outline-primary w-100">
                                Simular pago
                            </button>
                        </form>
                    </div>
                </div>
            </section>
        @endif

        <section class="vt-card overflow-hidden">
            <div class="px-4 py-3 border-bottom" style="background: var(--dc-surface-low);">
                <h2 class="h5 fw-bold mb-0">Detalle de productos</h2>
            </div>
            @foreach($pedido->items as $item)
                <div class="d-flex gap-3 align-items-center border-bottom p-4">
                    <img src="{{ $item->producto->imagen_url }}" alt="{{ $item->producto->nombre }}" style="width: 76px; height: 70px; object-fit: cover; border-radius: 8px; border: 1px solid var(--dc-line);">
                    <div class="flex-grow-1">
                        <strong>{{ $item->producto->nombre }}</strong>
                        <div class="text-muted small">{{ $item->cantidad }} unidades / {{ $item->peso_unitario_kg * $item->cantidad }} kg</div>
                    </div>
                    <strong class="font-monospace">${{ number_format($item->cantidad * $item->precio_unitario, 0, ',', '.') }}</strong>
                </div>
            @endforeach
            <div class="p-4 d-flex justify-content-end">
                <div style="min-width: 260px;">
                    <div class="d-flex justify-content-between text-muted mb-2"><span>Subtotal</span><span>${{ number_format($pedido->total, 0, ',', '.') }}</span></div>
                    <div class="d-flex justify-content-between text-muted mb-2"><span>Envio dron</span><span>Incluido</span></div>
                    <div class="d-flex justify-content-between fw-bold border-top pt-2"><span>Total</span><span>${{ number_format($pedido->total, 0, ',', '.') }}</span></div>
                </div>
            </div>
        </section>
    </div>

    <div class="col-lg-5">
        <section class="vt-card p-4 mb-4">
            <h2 class="h5 fw-bold mb-4 d-flex align-items-center gap-2">
                <span class="material-symbols-outlined text-yellow">local_shipping</span>
                Estado logistico
            </h2>

            <div class="d-flex gap-3 mb-4">
                <div class="vt-icon-box"><span class="material-symbols-outlined">drone</span></div>
                <div>
                    <div class="vt-kicker">Dron asignado</div>
                    <strong>{{ $pedido->entrega?->drone?->codigo ?? 'Sin asignar' }}</strong>
                    <p class="text-muted small mb-0">{{ $pagado ? 'Unidad disponible para la entrega.' : 'Pendiente de confirmacion de pago.' }}</p>
                </div>
            </div>

            <div class="mb-4">
                @if($pagado && $pedido->entrega)
                    <div class="order-mini-map-wrap">
                        <div id="order-mini-map"></div>
                        <div class="order-mini-map-action">
                            <a class="btn btn-primary w-100" href="{{ route('pedidos.tracking', $pedido) }}">
                                <span class="material-symbols-outlined">location_pin</span>
                                Ver mapa / Tracking
                            </a>
                        </div>
                    </div>
                @else
                    <div class="vt-map-preview is-locked">
                        <div class="vt-map-lock">
                            <div>
                                <div class="bg-white text-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                                    <span class="material-symbols-outlined fs-2">lock</span>
                                </div>
                                <h3 class="h5 fw-bold">Ruta en tiempo real</h3>
                                <p class="mb-0">Disponible cuando el pago sea aprobado</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="vt-timeline">
                <div class="vt-timeline-rail">
                    <span class="vt-dot"></span>
                    <span class="vt-line"></span>
                    <span class="vt-dot {{ $pagado ? 'active' : '' }}"></span>
                </div>
                <div class="d-grid gap-4">
                    <div>
                        <div class="vt-kicker">Pedido recibido</div>
                        <strong>{{ str_replace('_', ' ', $pedido->estado) }}</strong>
                    </div>
                    <div>
                        <div class="vt-kicker">Destino</div>
                        <strong>{{ $destino }}</strong>
                        <p class="text-muted small mb-0">{{ $pedido->tipo_entrega }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="vt-card p-4">
            <h2 class="h5 fw-bold mb-3">Operacion</h2>
            <div class="d-flex justify-content-between border-bottom py-2"><span>Estado pedido</span><strong>{{ str_replace('_', ' ', $pedido->estado) }}</strong></div>
            <div class="d-flex justify-content-between border-bottom py-2"><span>Pago</span><strong>{{ $pagoEstado }}</strong></div>
            <div class="d-flex justify-content-between border-bottom py-2"><span>Entrega</span><strong>{{ $pedido->entrega?->estado ?? 'Sin entrega' }}</strong></div>
            <div class="d-flex justify-content-between py-2"><span>Factura</span><strong>{{ $pedido->factura?->numero ?? 'No generada' }}</strong></div>
            @if($pedido->factura)
                <a class="btn btn-outline-primary w-100 mt-2" href="{{ route('facturas.show', $pedido->factura) }}">Ver factura</a>
            @endif
        </section>
    </div>
</div>
@endsection
