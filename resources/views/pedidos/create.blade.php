@extends('layouts.app')

@section('content')
@php
    $peso = $carrito->items->sum(fn($i) => $i->cantidad * $i->producto->peso_kg);
    $total = $carrito->items->sum(fn($i) => $i->cantidad * $i->producto->precio);
@endphp

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const defaultLat = 7.1253930;
        const defaultLng = -73.1198040;
        const latInput = document.querySelector('[name="destino_latitud"]');
        const lngInput = document.querySelector('[name="destino_longitud"]');
        const mapElement = document.getElementById('delivery-map');
        const deliveryType = document.querySelector('[name="tipo_entrega"]');
        const mapBox = document.getElementById('delivery-map-box');
        const stationBox = document.getElementById('station-box');
        const addressBox = document.getElementById('address-box');
        const stationSelect = document.querySelector('[name="estacion_entrega_id"]');
        const addressInput = document.querySelector('[name="direccion_entrega"]');
        const savedAddress = document.getElementById('saved-address');

        if (!mapElement) return;

        const map = L.map('delivery-map', { scrollWheelZoom: false }).setView([defaultLat, defaultLng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const markerIcon = L.divIcon({
            className: 'vt-map-icon',
            html: '<span class="vt-pin vt-destination">Destino</span>',
            iconSize: [76, 34],
            iconAnchor: [38, 17]
        });

        const marker = L.marker([defaultLat, defaultLng], { draggable: true, icon: markerIcon })
            .addTo(map)
            .bindPopup('Destino seleccionado')
            .openPopup();

        function setCoordinates(lat, lng) {
            latInput.value = Number(lat).toFixed(7);
            lngInput.value = Number(lng).toFixed(7);
        }

        function syncMarker(latlng) {
            marker.setLatLng(latlng);
            setCoordinates(latlng.lat, latlng.lng);
        }

        map.on('click', event => syncMarker(event.latlng));
        marker.on('dragend', event => syncMarker(event.target.getLatLng()));
        setCoordinates(defaultLat, defaultLng);

        function toggleMap() {
            const isHome = deliveryType.value === 'domicilio';
            mapBox.style.display = isHome ? 'block' : 'none';
            stationBox.style.display = isHome ? 'none' : 'block';
            addressBox.style.display = isHome ? 'block' : 'none';
            latInput.required = isHome;
            lngInput.required = isHome;
            addressInput.required = isHome;
            stationSelect.required = !isHome;
            latInput.disabled = !isHome;
            lngInput.disabled = !isHome;
            addressInput.disabled = !isHome;
            stationSelect.disabled = isHome;
            setTimeout(() => map.invalidateSize(), 120);
        }

        if (savedAddress) {
            savedAddress.addEventListener('change', function () {
                if (!this.value) return;
                const selected = this.options[this.selectedIndex];
                addressInput.value = selected.dataset.address || '';
                syncMarker({
                    lat: Number(selected.dataset.lat),
                    lng: Number(selected.dataset.lng)
                });
                map.setView([Number(selected.dataset.lat), Number(selected.dataset.lng)], 15);
            });
        }

        deliveryType.addEventListener('change', toggleMap);
        toggleMap();
    });
</script>
@endpush

<style>
    #delivery-map {
        min-height: 320px;
        border: 4px solid #111;
        border-radius: 8px;
        overflow: hidden;
    }

    .vt-map-icon {
        background: transparent;
        border: 0;
    }

    .vt-pin {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 76px;
        height: 34px;
        border-radius: 999px;
        background: var(--dc-yellow);
        color: #111;
        border: 4px solid #111;
        font-weight: 900;
        box-shadow: 0 10px 20px rgba(0,0,0,.28);
    }
</style>

<div class="admin-section-title">
    <div>
        <span class="badge-status">Checkout</span>
        <h1 class="display-6 fw-bold mt-2 mb-1">Crear pedido</h1>
        <p class="text-muted mb-0">Selecciona el tipo de entrega y marca el destino en Bucaramanga.</p>
    </div>
    <a class="btn btn-outline-primary" href="{{ route('carrito.index') }}">Volver al carrito</a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="surface p-3">
            <h2 class="h5">Productos del pedido</h2>
            @foreach($carrito->items as $item)
                <div class="d-flex gap-3 align-items-center border-bottom py-3">
                    <img src="{{ $item->producto->imagenPrincipalUrl() }}" alt="{{ $item->producto->nombre }}" style="width: 84px; height: 70px; object-fit: cover; border-radius: 8px;">
                    <div class="flex-grow-1">
                        <strong>{{ $item->producto->nombre }}</strong>
                        <div class="text-muted small">{{ $item->cantidad }} unidades / {{ $item->cantidad * $item->producto->peso_kg }} kg</div>
                    </div>
                    <strong>${{ number_format($item->cantidad * $item->producto->precio, 0, ',', '.') }}</strong>
                </div>
            @endforeach
            <div class="d-flex justify-content-between pt-3 fs-5">
                <span>Peso total: <strong>{{ number_format($peso, 2) }} kg</strong></span>
                <span>Total: <strong>${{ number_format($total, 0, ',', '.') }}</strong></span>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <form class="surface p-3" method="POST" action="{{ route('pedidos.store') }}">
            @csrf
            <label class="form-label">Tipo de entrega</label>
            <select class="form-select mb-3" name="tipo_entrega">
                <option value="estacion">Recoger en estacion</option>
                <option value="domicilio">Entrega a domicilio</option>
            </select>

            <div id="station-box">
                <label class="form-label">Estacion de entrega</label>
                <select class="form-select mb-3" name="estacion_entrega_id">
                    <option value="">Seleccionar</option>
                    @foreach($estaciones as $estacion)
                        <option value="{{ $estacion->id }}">{{ $estacion->nombre }}</option>
                    @endforeach
                </select>
                <div class="alert alert-info">Si recoges en estacion no necesitas escribir direccion de domicilio.</div>
            </div>

            <div id="address-box">
                @if($direcciones->isNotEmpty())
                    <label class="form-label">Usar direccion guardada</label>
                    <select class="form-select mb-3" id="saved-address">
                        <option value="">Seleccionar direccion guardada</option>
                        @foreach($direcciones as $dir)
                            <option value="{{ $dir->id }}" data-address="{{ $dir->direccion }}" data-lat="{{ $dir->latitud }}" data-lng="{{ $dir->longitud }}">{{ $dir->nombre }} - {{ $dir->direccion }}</option>
                        @endforeach
                    </select>
                @endif

                <label class="form-label">Direccion domicilio</label>
                <input class="form-control mb-3" name="direccion_entrega" value="{{ old('direccion_entrega') }}" placeholder="Carrera 27 #36-14, Bucaramanga">
            </div>

            <div id="delivery-map-box" class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>Destino en el mapa</strong>
                    <span class="small text-muted">Haz clic o arrastra el marcador</span>
                </div>
                <div id="delivery-map"></div>
                <div class="row g-2 mt-2">
                    <div class="col">
                        <label class="form-label small">Latitud</label>
                        <input class="form-control form-control-sm" name="destino_latitud" value="{{ old('destino_latitud', '7.1253930') }}">
                    </div>
                    <div class="col">
                        <label class="form-label small">Longitud</label>
                        <input class="form-control form-control-sm" name="destino_longitud" value="{{ old('destino_longitud', '-73.1198040') }}">
                    </div>
                </div>
            </div>

            <button class="btn btn-primary w-100">Confirmar pedido</button>
        </form>
    </div>
</div>
@endsection
