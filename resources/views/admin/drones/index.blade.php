@extends('layouts.app')

@section('content')
<div class="admin-shell">
    @include('admin.partials.nav')

    <section>
        <div class="admin-section-title">
            <div>
                <span class="badge-status">Flota</span>
                <h1 class="display-6 fw-bold mt-2 mb-1">Drones</h1>
                <p class="text-muted mb-0">Capacidad, bateria, estado operativo y estacion actual.</p>
            </div>
            <a class="btn btn-primary" href="{{ route('admin.drones.create') }}">Nuevo drone</a>
        </div>

        <div class="surface p-3">
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th>Codigo</th>
                            <th>Modelo</th>
                            <th>Capacidad</th>
                            <th>Bateria</th>
                            <th>Estado</th>
                            <th>Estacion</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($drones as $drone)
                            <tr>
                                <td><strong>{{ $drone->codigo }}</strong></td>
                                <td>{{ $drone->modelo }}</td>
                                <td>{{ $drone->capacidad_kg }} kg</td>
                                <td>
                                    <div class="progress" style="height: 8px;"><div class="progress-bar bg-warning" style="width: {{ $drone->bateria }}%"></div></div>
                                    <div class="small text-muted">{{ $drone->bateria }}%</div>
                                </td>
                                <td><span class="badge-status">{{ str_replace('_', ' ', $drone->estado) }}</span></td>
                                <td>{{ $drone->estacionEntrega?->nombre ?? 'Sin estacion' }}</td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.drones.edit', $drone) }}">Editar</a>
                                        <form method="POST" action="{{ route('admin.drones.destroy', $drone) }}">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm">Eliminar</button></form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">{{ $drones->links() }}</div>
    </section>
</div>
@endsection
