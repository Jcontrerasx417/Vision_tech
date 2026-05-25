@extends('layouts.app')

@section('content')
<div class="admin-shell">
    @include('admin.partials.nav')

    <section>
        <div class="admin-section-title">
            <div>
                <span class="badge-status">Mantenimiento</span>
                <h1 class="display-6 fw-bold mt-2 mb-1">Mantenimiento de drones</h1>
                <p class="text-muted mb-0">Programacion, ejecucion y control tecnico de la flota.</p>
            </div>
            <a class="btn btn-primary" href="{{ route('admin.mantenimientos.create') }}">Nuevo mantenimiento</a>
        </div>

        <div class="surface p-3">
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th>Drone</th>
                            <th>Tipo</th>
                            <th>Programado</th>
                            <th>Realizado</th>
                            <th>Descripcion</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mantenimientos as $mantenimiento)
                            <tr>
                                <td><strong>{{ $mantenimiento->drone->codigo }}</strong><div class="small text-muted">{{ $mantenimiento->drone->modelo }}</div></td>
                                <td>{{ $mantenimiento->tipo }}</td>
                                <td>{{ $mantenimiento->programado_en?->format('Y-m-d H:i') }}</td>
                                <td>{{ $mantenimiento->realizado_en?->format('Y-m-d H:i') ?? 'Pendiente' }}</td>
                                <td>{{ Str::limit($mantenimiento->descripcion, 60) }}</td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.mantenimientos.edit', $mantenimiento) }}">Editar</a>
                                        <form method="POST" action="{{ route('admin.mantenimientos.destroy', $mantenimiento) }}">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm">Eliminar</button></form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">{{ $mantenimientos->links() }}</div>
    </section>
</div>
@endsection
