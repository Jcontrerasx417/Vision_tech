@extends('layouts.app')

@section('content')
<div class="admin-shell">
    @include('admin.partials.nav')

    <section>
        <div class="admin-section-title">
            <div>
                <span class="badge-status">Logistica</span>
                <h1 class="display-6 fw-bold mt-2 mb-1">Estaciones de entrega</h1>
                <p class="text-muted mb-0">Puntos de salida, recogida y soporte operativo en Bucaramanga.</p>
            </div>
            <a class="btn btn-primary" href="{{ route('admin.estaciones.create') }}">Nueva estacion</a>
        </div>

        <div class="surface p-3">
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Direccion</th>
                            <th>Coordenadas</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($estaciones as $estacion)
                            <tr>
                                <td><strong>{{ $estacion->nombre }}</strong></td>
                                <td>{{ $estacion->direccion }}</td>
                                <td>{{ $estacion->latitud }}, {{ $estacion->longitud }}</td>
                                <td><span class="badge-status">{{ $estacion->activa ? 'Activa' : 'Inactiva' }}</span></td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.estaciones.edit', $estacion) }}">Editar</a>
                                        <form method="POST" action="{{ route('admin.estaciones.destroy', $estacion) }}">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm">Eliminar</button></form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">{{ $estaciones->links() }}</div>
    </section>
</div>
@endsection
