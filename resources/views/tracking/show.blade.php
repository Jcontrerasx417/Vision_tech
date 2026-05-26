@extends('layouts.app')

@section('content')
@php
    $estadoActual = $pedido->estado;
    $steps = [
        'pendiente' => 'Pedido recibido',
        'en_preparacion' => 'En preparacion',
        'pagado' => 'Pago aprobado',
        'enviado' => 'En camino',
        'proximo_a_llegar' => 'Proximo a llegar',
        'entregado' => 'Entregado',
    ];
    $keys = array_keys($steps);
    $currentIndex = array_search($estadoActual, $keys, true);
    $currentIndex = $currentIndex === false ? 0 : $currentIndex;
    $points = $pedido->entrega?->trackingPoints ?? collect();
    $entregaFinalizada = $pedido->entrega?->estado === 'finalizada' || $pedido->estado === 'entregado';
    $mapPoints = $points->map(fn ($point) => [
        'lat' => (float) $point->latitud,
        'lng' => (float) $point->longitud,
        'alt' => $point->altitud_m,
        'time' => optional($point->registrado_en)->format('Y-m-d H:i'),
    ])->values();
    if ($mapPoints->isEmpty()) {
        $mapPoints = collect([
            ['lat' => 7.1150670, 'lng' => -73.1086300, 'alt' => 985, 'time' => 'Origen demo Bucaramanga'],
            ['lat' => 7.1193490, 'lng' => -73.1227410, 'alt' => 972, 'time' => 'Ruta demo Bucaramanga'],
            ['lat' => 7.1232000, 'lng' => -73.1208000, 'alt' => 968, 'time' => 'Drone en vuelo Bucaramanga'],
        ]);
    }
    $destinationPoint = $pedido->tipo_entrega === 'domicilio'
        ? [
            'lat' => (float) ($pedido->destino_latitud ?? $pedido->direccionCliente?->latitud ?? 7.1253930),
            'lng' => (float) ($pedido->destino_longitud ?? $pedido->direccionCliente?->longitud ?? -73.1198040),
            'label' => $pedido->direccion_entrega ?? $pedido->direccionCliente?->direccion ?? 'Destino domicilio',
        ]
        : [
            'lat' => (float) ($pedido->estacionEntrega?->latitud ?? 7.1253930),
            'lng' => (float) ($pedido->estacionEntrega?->longitud ?? -73.1198040),
            'label' => $pedido->estacionEntrega?->nombre ?? 'Destino estacion',
        ];
@endphp

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const points = @json($mapPoints);
        const destination = @json($destinationPoint);
        const routeCoords = [...points.map(point => [point.lat, point.lng]), [destination.lat, destination.lng]];
        const map = L.map('tracking-map-real', {
            scrollWheelZoom: false,
            zoomControl: true
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const route = L.polyline(routeCoords, {
            color: '#f8c900',
            weight: 6,
            opacity: .95,
            dashArray: '10, 12'
        }).addTo(map);

        const originIcon = L.divIcon({
            className: 'vt-map-icon',
            html: '<span class="vt-pin vt-origin">A</span>',
            iconSize: [34, 34],
            iconAnchor: [17, 17]
        });

        const destinationIcon = L.divIcon({
            className: 'vt-map-icon',
            html: '<span class="vt-pin vt-destination">🏁</span>',
            iconSize: [42, 42],
            iconAnchor: [21, 21]
        });

        const droneIcon = L.divIcon({
            className: 'vt-map-icon',
            html: '<span class="vt-drone">✈</span>',
            iconSize: [48, 48],
            iconAnchor: [24, 24]
        });

        points.forEach((point, index) => {
            if (index === 0) {
                L.marker([point.lat, point.lng], {
                    icon: originIcon
                }).addTo(map).bindPopup(`Origen<br>Lat ${point.lat}<br>Lng ${point.lng}`);
            } else {
                L.circleMarker([point.lat, point.lng], {
                    radius: 7,
                    color: '#111',
                    weight: 3,
                    fillColor: '#f8c900',
                    fillOpacity: 1
                }).addTo(map).bindPopup(`Punto GPS<br>${point.time}<br>Alt ${point.alt ?? '-'} m`);
            }
        });

        L.marker([destination.lat, destination.lng], { icon: destinationIcon })
            .addTo(map)
            .bindPopup(`${destination.label}<br>Lat ${destination.lat}<br>Lng ${destination.lng}`)
            .bindTooltip('Destino', {
                permanent: true,
                direction: 'top',
                offset: [0, -22],
                className: 'vt-destination-label'
            });

        const lastPoint = points[points.length - 1];
        const droneMarker = L.marker([lastPoint.lat, lastPoint.lng], { icon: droneIcon })
            .addTo(map)
            .bindPopup('Drone {{ $pedido->entrega?->drone?->codigo ?? 'sin asignar' }}<br>Estado: {{ str_replace('_', ' ', $pedido->estado) }}<br>Ultimo punto GPS')
            .openPopup();

        map.fitBounds(route.getBounds(), { padding: [38, 38] });

        const playButton = document.getElementById('play-flight');
        if (playButton && routeCoords.length > 1) {
            const completionUrl = @json(route('pedidos.tracking.completar', $pedido));
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            const setVisualStatus = (statusKey) => {
                const statusOrder = ['pendiente', 'en_preparacion', 'pagado', 'enviado', 'proximo_a_llegar', 'entregado'];
                const activeIndex = statusOrder.indexOf(statusKey);
                if (activeIndex === -1) return;

                document.querySelectorAll('[data-tracking-step]').forEach((step) => {
                    const stepIndex = statusOrder.indexOf(step.dataset.trackingStep);
                    const helper = step.querySelector('[data-tracking-helper]');
                    step.classList.toggle('done', stepIndex < activeIndex);
                    step.classList.toggle('active', stepIndex === activeIndex);
                    if (helper) {
                        helper.textContent = stepIndex <= activeIndex ? 'Completado o en progreso' : 'Pendiente';
                    }
                });

                const statusLabel = document.querySelector('[data-live-order-status]');
                if (statusLabel) {
                    statusLabel.textContent = statusKey.replaceAll('_', ' ');
                }

                droneMarker.setPopupContent(
                    'Drone {{ $pedido->entrega?->drone?->codigo ?? 'sin asignar' }}<br>' +
                    `Estado: ${statusKey.replaceAll('_', ' ')}<br>` +
                    'Simulacion GPS en vivo'
                );
            };

            const persistDeliveryCompletion = async () => {
                const response = await fetch(completionUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                if (!response.ok) {
                    throw new Error('No fue posible sincronizar la entrega.');
                }

                const data = await response.json();
                document.querySelectorAll('[data-live-order-status]').forEach((element) => {
                    element.textContent = (data.pedido_estado ?? 'entregado').replaceAll('_', ' ');
                });
                document.querySelectorAll('[data-live-delivery-status]').forEach((element) => {
                    element.textContent = (data.entrega_estado ?? 'finalizada').replaceAll('_', ' ');
                });
                document.querySelectorAll('[data-live-drone-battery]').forEach((element) => {
                    element.textContent = data.drone_bateria ?? '-';
                });
                document.querySelectorAll('[data-live-drone-status]').forEach((element) => {
                    element.textContent = (data.drone_estado ?? 'disponible').replaceAll('_', ' ');
                });
                document.querySelectorAll('[data-live-gps-count]').forEach((element) => {
                    element.textContent = data.gps_count ?? 0;
                });

                const gpsList = document.querySelector('[data-gps-list]');
                if (gpsList && Array.isArray(data.gps_points) && data.gps_points.length > 0) {
                    gpsList.innerHTML = data.gps_points.map((point) => `
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span>${point.registrado_en ?? 'Ahora'} / Lat ${Number(point.latitud).toFixed(7)} / Lng ${Number(point.longitud).toFixed(7)}</span>
                            <strong>${point.altitud_m ?? '-'} m</strong>
                        </div>
                    `).join('');
                }
            };

            playButton.addEventListener('click', function () {
                let index = 0;
                this.disabled = true;
                this.textContent = 'Reproduciendo vuelo...';
                droneMarker.setLatLng(routeCoords[0]);
                setVisualStatus('enviado');

                const timer = setInterval(() => {
                    index++;
                    if (index >= routeCoords.length) {
                        setVisualStatus('entregado');
                        clearInterval(timer);
                        this.textContent = 'Sincronizando entrega...';
                        persistDeliveryCompletion()
                            .then(() => {
                                this.textContent = 'Vuelo completado';
                                droneMarker.openPopup();
                            })
                            .catch(() => {
                                this.disabled = false;
                                this.textContent = 'Reintentar sincronizacion';
                            });
                        return;
                    }

                    if (index >= Math.max(1, routeCoords.length - 2)) {
                        setVisualStatus('proximo_a_llegar');
                    } else {
                        setVisualStatus('enviado');
                    }

                    droneMarker.setLatLng(routeCoords[index]);
                    map.panTo(routeCoords[index], { animate: true, duration: .35 });
                }, 700);
            });
        }
    });
</script>
@endpush

<style>
    .tracking-map {
        position: relative;
        min-height: 420px;
        border-radius: 8px;
        overflow: hidden;
        background:
            linear-gradient(90deg, rgba(0,0,0,.06) 1px, transparent 1px) 0 0 / 44px 44px,
            linear-gradient(rgba(0,0,0,.06) 1px, transparent 1px) 0 0 / 44px 44px,
            linear-gradient(135deg, #2d2d2d, #111);
        border: 4px solid #111;
    }

    .tracking-route {
        position: absolute;
        inset: 70px 70px;
        border-top: 6px dashed var(--dc-yellow);
        transform: rotate(-12deg);
        transform-origin: center;
    }

    .map-pin {
        position: absolute;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: var(--dc-yellow);
        border: 4px solid #111;
        box-shadow: 0 0 0 8px rgba(248, 201, 0, .22);
    }

    .map-pin.start { left: 12%; top: 70%; }
    .map-pin.mid { left: 50%; top: 45%; }
    .map-pin.end { right: 12%; top: 25%; }

    .drone-marker {
        position: absolute;
        left: {{ min(76, 18 + ($currentIndex * 11)) }}%;
        top: {{ max(20, 68 - ($currentIndex * 7)) }}%;
        background: #fff;
        color: #111;
        border: 3px solid var(--dc-yellow);
        border-radius: 999px;
        padding: .55rem .9rem;
        font-weight: 800;
        box-shadow: 0 18px 30px rgba(0,0,0,.28);
    }

    .tracking-step {
        display: flex;
        gap: .75rem;
        align-items: flex-start;
        padding: .75rem 0;
        border-bottom: 1px solid #eee;
    }

    .tracking-dot {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 28px;
        background: #ddd;
        color: #555;
        font-size: .8rem;
        font-weight: 800;
    }

    .tracking-step.done .tracking-dot,
    .tracking-step.active .tracking-dot {
        background: var(--dc-yellow);
        color: #111;
    }

    .tracking-step.active strong {
        color: #111;
    }

    #tracking-map-real {
        min-height: 460px;
        border: 4px solid #111;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 18px 36px rgba(0,0,0,.16);
    }

    .map-overlay {
        margin-top: -66px;
        position: relative;
        z-index: 500;
        background: rgba(17, 17, 17, .92);
        color: #fff;
        border-radius: 0 0 8px 8px;
        padding: 1rem;
    }

    .vt-map-icon {
        background: transparent;
        border: 0;
    }

    .vt-pin,
    .vt-drone {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: var(--dc-yellow);
        color: #111;
        border: 4px solid #111;
        font-weight: 900;
        box-shadow: 0 10px 20px rgba(0,0,0,.28);
    }

    .vt-destination {
        background: #fff;
    }

    .vt-destination-label {
        background: #111;
        border: 2px solid var(--dc-yellow);
        color: var(--dc-yellow);
        border-radius: 999px;
        font-weight: 800;
        padding: .25rem .55rem;
    }

    .vt-drone {
        width: 48px;
        height: 48px;
        font-size: 1.25rem;
        transform: rotate(45deg);
        box-shadow: 0 0 0 9px rgba(248, 201, 0, .25), 0 12px 24px rgba(0,0,0,.32);
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <span class="badge-status">Seguimiento GPS</span>
        <h1 class="display-6 fw-bold mt-2 mb-1">Pedido #{{ $pedido->id }}</h1>
        <p class="text-muted mb-0">Estado del pedido: <strong data-live-order-status>{{ str_replace('_', ' ', $pedido->estado) }}</strong></p>
    </div>
    <a class="btn btn-outline-primary" href="{{ route('pedidos.show', $pedido) }}">Ver detalle</a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="surface p-2">
            <div id="tracking-map-real"></div>
            <div class="map-overlay">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex flex-wrap gap-3">
                        <span><strong>Drone:</strong> {{ $pedido->entrega?->drone?->codigo ?? 'Sin asignar' }}</span>
                        <span><strong>Bateria:</strong> <span data-live-drone-battery>{{ $pedido->entrega?->drone?->bateria ?? '-' }}</span>%</span>
                        <span><strong>Dron:</strong> <span data-live-drone-status>{{ str_replace('_', ' ', $pedido->entrega?->drone?->estado ?? 'sin estado') }}</span></span>
                        <span><strong>Entrega:</strong> <span data-live-delivery-status>{{ str_replace('_', ' ', $pedido->entrega?->estado ?? 'sin entrega') }}</span></span>
                        <span><strong>Tipo:</strong> {{ $pedido->tipo_entrega }}</span>
                        <span><strong>Puntos GPS:</strong> <span data-live-gps-count>{{ $points->count() }}</span></span>
                    </div>
                    <button id="play-flight" class="btn btn-primary btn-sm" type="button" @disabled($mapPoints->count() < 2)>
                        Reproducir vuelo
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="surface p-3 h-100">
            <h2 class="h5">Linea de estado</h2>
            @foreach($steps as $key => $label)
                @php $index = array_search($key, $keys, true); @endphp
                <div class="tracking-step {{ $index < $currentIndex ? 'done' : '' }} {{ $index === $currentIndex ? 'active' : '' }}" data-tracking-step="{{ $key }}">
                    <span class="tracking-dot">{{ $index + 1 }}</span>
                    <div>
                        <strong>{{ $label }}</strong>
                        <div class="small text-muted" data-tracking-helper>{{ $index <= $currentIndex ? 'Completado o en progreso' : 'Pendiente' }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-8">
        <div class="surface p-3">
            <h2 class="h5">Puntos GPS registrados</h2>
            <div data-gps-list>
                @forelse($points as $point)
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span>{{ $point->registrado_en }} / Lat {{ $point->latitud }} / Lng {{ $point->longitud }}</span>
                        <strong>{{ $point->altitud_m ?? '-' }} m</strong>
                    </div>
                @empty
                    <p class="text-muted mb-0">Aun no hay puntos GPS registrados. Al completar el vuelo se guardara la ruta simulada.</p>
                @endforelse
            </div>
        </div>
    </div>
    @if(auth()->user()?->hasRole('administrador', 'personal_logistico') && $pedido->entrega)
        <div class="col-lg-4">
            @if(!$entregaFinalizada)
                <div class="surface p-3 mb-3">
                    <h2 class="h5">Simulacion operativa</h2>
                    <p class="text-muted small">Genera puntos GPS, altitud y consumo realista de bateria segun distancia y peso.</p>
                    <form method="POST" action="{{ route('admin.pedidos.simular-vuelo', $pedido) }}" class="mb-2">
                        @csrf
                        <button class="btn btn-primary w-100">
                            <span class="material-symbols-outlined">flight_takeoff</span>
                            Simular vuelo
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.entregas.finalizar', $pedido->entrega) }}">
                        @csrf
                        <button class="btn btn-outline-success w-100">
                            <span class="material-symbols-outlined">home_pin</span>
                            Finalizar y volver a base
                        </button>
                    </form>
                </div>
                <form class="surface p-3" method="POST" action="{{ route('admin.entregas.tracking', $pedido->entrega) }}">
                    @csrf
                    <h2 class="h5">Registrar punto GPS</h2>
                    <label class="form-label">Latitud</label>
                    <input class="form-control mb-2" type="number" step="0.0000001" name="latitud" value="7.1193490" required>
                    <label class="form-label">Longitud</label>
                    <input class="form-control mb-2" type="number" step="0.0000001" name="longitud" value="-73.1227410" required>
                    <label class="form-label">Altitud m</label>
                    <input class="form-control mb-3" type="number" step="0.01" name="altitud_m" value="972">
                    <button class="btn btn-primary w-100">Guardar GPS</button>
                </form>
            @else
                <div class="surface p-3 mb-3">
                    <h2 class="h5">Entrega finalizada</h2>
                    <p class="text-muted mb-0">Este pedido ya fue entregado. Ya no se pueden simular nuevos vuelos ni registrar GPS; solo puedes reproducir el recorrido en el mapa.</p>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
