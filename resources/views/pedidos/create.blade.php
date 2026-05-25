@extends('layouts.app')

@section('content')
@php
    $peso = $carrito->items->sum(fn($i) => $i->cantidad * $i->producto->peso_kg);
    $total = $carrito->items->sum(fn($i) => $i->cantidad * $i->producto->precio);
    $direccionInicial = $direcciones->first();
@endphp

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const defaultLat = Number(@json($direccionInicial?->latitud ?? 7.1253930));
        const defaultLng = Number(@json($direccionInicial?->longitud ?? -73.1198040));
        const mapElement = document.getElementById('delivery-map');
        const deliveryType = document.querySelector('[name="tipo_entrega"]');
        const mapBox = document.getElementById('delivery-map-box');
        const stationBox = document.getElementById('station-box');
        const addressBox = document.getElementById('address-box');
        const stationSelect = document.querySelector('[name="estacion_entrega_id"]');
        const addressSelect = document.querySelector('[name="direccion_cliente_id"]');
        const addressInput = document.querySelector('[name="direccion_entrega"]');
        const latInput = document.querySelector('[name="destino_latitud"]');
        const lngInput = document.querySelector('[name="destino_longitud"]');
        const searchButton = document.getElementById('search-delivery-address');
        const saveAddress = document.querySelector('[name="guardar_direccion"]');
        const saveName = document.querySelector('[name="nombre_direccion"]');
        const selectedAddressText = document.getElementById('selected-address-text');

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

        function setDestination(lat, lng, label = 'Destino seleccionado', updateAddress = false) {
            latInput.value = Number(lat).toFixed(7);
            lngInput.value = Number(lng).toFixed(7);
            marker.setLatLng([lat, lng]).bindPopup(label).openPopup();
            map.setView([lat, lng], 16);
            selectedAddressText.textContent = label;
            if (updateAddress) {
                addressInput.value = label;
            }
        }

        async function reverseAddress(lat, lng) {
            selectedAddressText.textContent = 'Buscando direccion aproximada del punto marcado...';
            try {
                const url = new URL('https://nominatim.openstreetmap.org/reverse');
                url.searchParams.set('format', 'json');
                url.searchParams.set('lat', lat);
                url.searchParams.set('lon', lng);
                url.searchParams.set('zoom', '18');
                const response = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                const result = await response.json();
                setDestination(lat, lng, result.display_name || 'Destino marcado manualmente', true);
            } catch (error) {
                setDestination(lat, lng, 'Destino marcado manualmente', false);
            }
        }

        async function searchAddress() {
            const query = addressInput.value.trim();
            if (!query) return;

            searchButton.disabled = true;
            searchButton.textContent = 'Buscando...';
            try {
                const url = new URL('https://nominatim.openstreetmap.org/search');
                url.searchParams.set('format', 'json');
                url.searchParams.set('limit', '1');
                url.searchParams.set('countrycodes', 'co');
                url.searchParams.set('q', query.includes('Bucaramanga') ? query : `${query}, Bucaramanga, Colombia`);
                const response = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                const results = await response.json();
                if (results.length > 0) {
                    setDestination(Number(results[0].lat), Number(results[0].lon), results[0].display_name, true);
                } else {
                    alert('No encontre esa direccion. Puedes arrastrar el marcador o hacer clic en el mapa.');
                }
            } catch (error) {
                alert('No fue posible buscar la direccion. Puedes arrastrar el marcador o hacer clic en el mapa.');
            } finally {
                searchButton.disabled = false;
                searchButton.textContent = 'Buscar en mapa';
            }
        }

        function syncSelectedAddress() {
            if (!addressSelect || !addressSelect.value) return;

            const selected = addressSelect.options[addressSelect.selectedIndex];
            setDestination(Number(selected.dataset.lat), Number(selected.dataset.lng), selected.dataset.address || selected.textContent, true);
        }

        function toggleDeliveryType() {
            const isHome = deliveryType.value === 'domicilio';
            mapBox.style.display = isHome ? 'block' : 'none';
            stationBox.style.display = isHome ? 'none' : 'block';
            addressBox.style.display = isHome ? 'block' : 'none';
            stationSelect.required = !isHome;
            stationSelect.disabled = isHome;
            if (addressSelect) {
                addressSelect.disabled = !isHome;
            }
            addressInput.required = isHome;
            addressInput.disabled = !isHome;
            latInput.required = isHome;
            lngInput.required = isHome;
            latInput.disabled = !isHome;
            lngInput.disabled = !isHome;
            saveAddress.disabled = !isHome;
            saveName.disabled = !isHome || !saveAddress.checked;
            if (isHome) {
                setTimeout(() => map.invalidateSize(), 120);
            }
        }

        addressSelect?.addEventListener('change', syncSelectedAddress);
        searchButton.addEventListener('click', searchAddress);
        addressInput.addEventListener('keydown', event => {
            if (event.key === 'Enter') {
                event.preventDefault();
                searchAddress();
            }
        });
        map.on('click', event => reverseAddress(event.latlng.lat, event.latlng.lng));
        marker.on('dragend', event => {
            const position = event.target.getLatLng();
            reverseAddress(position.lat, position.lng);
        });
        saveAddress.addEventListener('change', () => {
            saveName.disabled = !saveAddress.checked;
        });
        deliveryType.addEventListener('change', toggleDeliveryType);
        if (addressSelect?.value) {
            syncSelectedAddress();
        } else {
            setDestination(defaultLat, defaultLng, 'Escribe una direccion o marca el punto exacto en el mapa');
        }
        toggleDeliveryType();
    });
</script>
@endpush

<style>
    #delivery-map {
        min-height: 320px;
        border: 1px solid var(--dc-line);
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

<div class="vt-page-heading">
    <div>
        <div class="vt-kicker">Checkout</div>
        <h1 class="display-6 fw-bold mt-2 mb-1">Crear pedido</h1>
        <p class="text-muted mb-0">Para domicilio puedes usar una direccion guardada o escribir una nueva y ubicarla en el mapa.</p>
    </div>
    <a class="btn btn-outline-primary" href="{{ route('carrito.index') }}">Volver al carrito</a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="vt-card p-4">
            <h2 class="h5 fw-bold">Productos del pedido</h2>
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
        <form class="vt-card p-4" method="POST" action="{{ route('pedidos.store') }}">
            @csrf
            <label class="form-label">Tipo de entrega</label>
            <select class="form-select mb-3" name="tipo_entrega">
                <option value="estacion" @selected(old('tipo_entrega') === 'estacion')>Recoger en estacion</option>
                <option value="domicilio" @selected(old('tipo_entrega') === 'domicilio')>Entrega a domicilio</option>
            </select>

            <div id="station-box">
                <label class="form-label">Estacion de entrega</label>
                <select class="form-select mb-3" name="estacion_entrega_id">
                    <option value="">Seleccionar</option>
                    @foreach($estaciones as $estacion)
                        <option value="{{ $estacion->id }}" @selected(old('estacion_entrega_id') == $estacion->id)>{{ $estacion->nombre }}</option>
                    @endforeach
                </select>
                <div class="alert alert-info">Si recoges en estacion, el mapa usara la ubicacion de esa estacion en el tracking.</div>
            </div>

            <div id="address-box">
                @if($direcciones->isNotEmpty())
                    <label class="form-label">Direccion guardada para domicilio</label>
                    <select class="form-select mb-3" name="direccion_cliente_id">
                        <option value="">Escribir otra direccion</option>
                        @foreach($direcciones as $dir)
                            <option value="{{ $dir->id }}" data-address="{{ $dir->direccion }}" data-lat="{{ $dir->latitud }}" data-lng="{{ $dir->longitud }}" @selected(old('direccion_cliente_id') == $dir->id || (!old('direccion_cliente_id') && $dir->principal))>
                                {{ $dir->nombre }} - {{ $dir->direccion }}
                            </option>
                        @endforeach
                    </select>
                @endif

                <label class="form-label">Direccion de entrega</label>
                <div class="input-group mb-2">
                    <input class="form-control" name="direccion_entrega" value="{{ old('direccion_entrega') }}" placeholder="Carrera 27 #36-14, Bucaramanga">
                    <button class="btn btn-outline-primary" type="button" id="search-delivery-address">Buscar en mapa</button>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="guardar_direccion" id="guardar_direccion" value="1" @checked(old('guardar_direccion'))>
                    <label class="form-check-label" for="guardar_direccion">Guardar esta direccion para futuros pedidos</label>
                </div>
                <input class="form-control mb-3" name="nombre_direccion" value="{{ old('nombre_direccion') }}" placeholder="Nombre para guardar: Casa, oficina..." disabled>
            </div>

            <div id="delivery-map-box" class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>Destino en el mapa</strong>
                    <span class="small text-muted">Busca, haz clic o arrastra el marcador</span>
                </div>
                <div id="delivery-map"></div>
                <p id="selected-address-text" class="small text-muted mt-2 mb-0"></p>
                <input type="hidden" name="destino_latitud" value="{{ old('destino_latitud') }}">
                <input type="hidden" name="destino_longitud" value="{{ old('destino_longitud') }}">
            </div>

            <button class="btn btn-primary w-100">Confirmar pedido</button>
        </form>
    </div>
</div>
@endsection
