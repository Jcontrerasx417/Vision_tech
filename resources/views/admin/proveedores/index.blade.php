@extends('layouts.app')

@section('content')
<div class="admin-shell">
    @include('admin.partials.nav')

    <section>
        <div class="admin-section-title">
            <div>
                <span class="badge-status">Abastecimiento</span>
                <h1 class="display-6 fw-bold mt-2 mb-1">Proveedores</h1>
                <p class="text-muted mb-0">Contactos comerciales y origen de productos.</p>
            </div>
            <a class="btn btn-primary" href="{{ route('admin.proveedores.create') }}">Nuevo proveedor</a>
        </div>

        <div class="surface p-3">
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th>Proveedor</th>
                            <th>Contacto</th>
                            <th>Email</th>
                            <th>Telefono</th>
                            <th>Direccion</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($proveedores as $proveedor)
                            <tr>
                                <td><strong>{{ $proveedor->nombre }}</strong></td>
                                <td>{{ $proveedor->contacto ?? '-' }}</td>
                                <td>{{ $proveedor->email ?? '-' }}</td>
                                <td>{{ $proveedor->telefono ?? '-' }}</td>
                                <td>{{ $proveedor->direccion ?? '-' }}</td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.proveedores.edit', $proveedor) }}">Editar</a>
                                        <form method="POST" action="{{ route('admin.proveedores.destroy', $proveedor) }}">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm">Eliminar</button></form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">{{ $proveedores->links() }}</div>
    </section>
</div>
@endsection
