@extends('layouts.app')

@section('content')
<div class="admin-shell">
    @include('admin.partials.nav')

    <section>
        <div class="admin-section-title">
            <div>
                <span class="badge-status">Proveedores</span>
                <h1 class="display-6 fw-bold mt-2 mb-1">Solicitudes de proveedores</h1>
                <p class="text-muted mb-0">Solo se muestran solicitudes pendientes. Las aprobadas o rechazadas salen de esta lista.</p>
            </div>
        </div>

        <div class="surface p-3">
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead><tr><th>Proveedor</th><th>Usuario</th><th>Estado</th><th>Mensaje</th><th>Activo</th><th>Acciones</th></tr></thead>
                    <tbody>
                    @foreach($solicitudes as $solicitud)
                        <tr>
                            <td><strong>{{ $solicitud->proveedor?->nombre ?? '-' }}</strong><div class="small text-muted">{{ $solicitud->proveedor?->perfil?->nit }}</div></td>
                            <td>{{ $solicitud->user->name }}<div class="small text-muted">{{ $solicitud->user->email }}</div></td>
                            <td><span class="badge-status">{{ $solicitud->estado }}</span></td>
                            <td>{{ Str::limit($solicitud->mensaje, 70) }}</td>
                            <td>{{ $solicitud->proveedor?->activo ? 'Si' : 'No' }}</td>
                            <td style="min-width: 280px;">
                                <div class="d-flex flex-wrap gap-2">
                                    <form method="POST" action="{{ route('admin.solicitudes.aprobar', $solicitud) }}">@csrf @method('PATCH')<button class="btn btn-primary btn-sm">Aprobar</button></form>
                                    <form method="POST" action="{{ route('admin.solicitudes.rechazar', $solicitud) }}">@csrf @method('PATCH')<button class="btn btn-outline-danger btn-sm">Rechazar</button></form>
                                    <form method="POST" action="{{ route('admin.solicitudes.activar', $solicitud) }}">@csrf @method('PATCH')<button class="btn btn-outline-primary btn-sm">Activar</button></form>
                                    <form method="POST" action="{{ route('admin.solicitudes.desactivar', $solicitud) }}">@csrf @method('PATCH')<button class="btn btn-outline-secondary btn-sm">Desactivar</button></form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">{{ $solicitudes->links() }}</div>
    </section>
</div>
@endsection
