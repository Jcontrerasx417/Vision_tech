@extends('layouts.app')

@section('content')
@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const defaultLat = Number(@json(old('latitud', $direccion->latitud)));
        const defaultLng = Number(@json(old('longitud', $direccion->longitud)));
        const hasInitialCoordinates = @json(old('latitud') !== null && old('longitud') !== null);
        const mapElement = document.getElementById('address-map');
        const addressInput = document.querySelector('[name="direccion"]');
        const latInput = document.querySelector('[name="latitud"]');
        const lngInput = document.querySelector('[name="longitud"]');
        const searchButton = document.getElementById('search-address');
        const locationHint = document.getElementById('location-hint');

        if (!mapElement) return;

        const map = L.map('address-map', { scrollWheelZoom: false }).setView([defaultLat, defaultLng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map).bindPopup('Direccion seleccionada');

        function setDestination(lat, lng) {
            latInput.value = Number(lat).toFixed(7);
            lngInput.value = Number(lng).toFixed(7);
            marker.setLatLng([lat, lng]);
            map.setView([lat, lng], 16);
        }

        async function fillAddressFromCoordinates(lat, lng) {
            locationHint.textContent = 'Buscando direccion aproximada...';

            try {
                const url = new URL('https://nominatim.openstreetmap.org/reverse');
                url.searchParams.set('format', 'json');
                url.searchParams.set('lat', lat);
                url.searchParams.set('lon', lng);
                url.searchParams.set('zoom', '18');
                url.searchParams.set('addressdetails', '1');

                const response = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                const result = await response.json();

                if (result.display_name) {
                    addressInput.value = result.display_name;
                    marker.bindPopup(result.display_name).openPopup();
                    locationHint.textContent = 'Direccion tomada desde el punto marcado en el mapa.';
                } else {
                    locationHint.textContent = 'Punto guardado. Escribe una referencia de direccion si el mapa no la reconoce.';
                }
            } catch (error) {
                locationHint.textContent = 'Punto guardado. No pude convertirlo a direccion automaticamente.';
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
                    setDestination(Number(results[0].lat), Number(results[0].lon));
                    addressInput.value = results[0].display_name;
                    marker.bindPopup(results[0].display_name).openPopup();
                    locationHint.textContent = 'Direccion ubicada desde el buscador.';
                } else {
                    alert('No encontre esa direccion. Puedes hacer clic en el mapa para ubicarla.');
                }
            } catch (error) {
                alert('No fue posible buscar la direccion. Puedes hacer clic en el mapa para ubicarla.');
            } finally {
                searchButton.disabled = false;
                searchButton.textContent = 'Ubicar en mapa';
            }
        }

        map.on('click', event => {
            setDestination(event.latlng.lat, event.latlng.lng);
            fillAddressFromCoordinates(event.latlng.lat, event.latlng.lng);
        });
        marker.on('dragend', event => {
            const position = event.target.getLatLng();
            setDestination(position.lat, position.lng);
            fillAddressFromCoordinates(position.lat, position.lng);
        });
        searchButton.addEventListener('click', searchAddress);
        addressInput.addEventListener('keydown', event => {
            if (event.key === 'Enter') {
                event.preventDefault();
                searchAddress();
            }
        });
        if (hasInitialCoordinates) {
            setDestination(defaultLat, defaultLng);
        }
    });
</script>
@endpush

<style>
    #address-map {
        min-height: 280px;
        border: 1px solid var(--dc-line);
        border-radius: 8px;
        overflow: hidden;
    }
</style>

<div class="vt-page-heading">
    <div>
        <div class="vt-kicker">Direcciones</div>
        <h1 class="display-6 fw-bold mt-2 mb-1">Mis direcciones</h1>
        <p class="text-muted mb-0">Guarda destinos frecuentes. El mapa del pedido usara la direccion que elijas aqui.</p>
    </div>
    <a class="btn btn-outline-primary" href="{{ route('cliente.perfil') }}">Mi cuenta</a>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <form class="vt-card p-4" method="POST" action="{{ route('cliente.direcciones.store') }}">
            @csrf
            <h2 class="h5 fw-bold">Nueva direccion</h2>
            <label class="form-label">Nombre</label>
            <input class="form-control mb-3" name="nombre" value="{{ old('nombre') }}" placeholder="Casa, oficina..." required>

            <label class="form-label">Direccion</label>
            <div class="input-group mb-3">
                <input class="form-control" name="direccion" value="{{ old('direccion') }}" placeholder="Carrera 27 #36-14, Bucaramanga" required>
                <button class="btn btn-outline-primary" type="button" id="search-address">Ubicar en mapa</button>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>Ubicacion del destino</strong>
                    <span class="small text-muted">Busca, haz clic o arrastra el marcador</span>
                </div>
                <div id="address-map"></div>
                <div id="location-hint" class="small text-muted mt-2">Si el buscador no encuentra la direccion, marca el punto exacto en el mapa.</div>
            </div>

            <input type="hidden" name="latitud" value="{{ old('latitud') }}">
            <input type="hidden" name="longitud" value="{{ old('longitud') }}">
            <div class="alert alert-info small">Antes de guardar, ubica la direccion con el boton o marcala en el mapa.</div>

            <label class="form-check mb-3"><input class="form-check-input" type="checkbox" name="principal" value="1" @checked(old('principal'))> Usar como principal</label>
            <button class="btn btn-primary w-100">Guardar direccion</button>
        </form>
    </div>
    <div class="col-lg-7">
        <div class="vt-card p-4">
            <h2 class="h5 fw-bold">Guardadas</h2>
            @forelse($direcciones as $dir)
                <div class="d-flex justify-content-between gap-3 border-bottom py-3">
                    <div>
                        <strong>{{ $dir->nombre }}</strong>
                        @if($dir->principal)<span class="badge-status ms-2">Principal</span>@endif
                        <div class="text-muted">{{ $dir->direccion }}</div>
                        <div class="small text-muted">Ubicacion guardada para el mapa del pedido</div>
                    </div>
                    <form method="POST" action="{{ route('cliente.direcciones.destroy', $dir) }}">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm">Eliminar</button>
                    </form>
                </div>
            @empty
                <p class="text-muted mb-0">Todavia no tienes direcciones guardadas.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
