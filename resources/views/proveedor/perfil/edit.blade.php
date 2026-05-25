@extends('layouts.app')

@section('content')
<div class="admin-shell">
    @include('proveedor.partials.nav')

    <section>
        <div class="admin-section-title">
            <div>
                <span class="badge-status">Perfil proveedor</span>
                <h1 class="display-6 fw-bold mt-2 mb-1">{{ $proveedor->nombre }}</h1>
                <p class="text-muted mb-0">Completa tus datos y envia la solicitud de aprobacion.</p>
            </div>
        </div>

        @if($solicitud)
            <div class="alert alert-{{ $solicitud->estado === 'aprobada' ? 'success' : ($solicitud->estado === 'rechazada' ? 'danger' : 'warning') }}">
                Solicitud actual: <strong>{{ $solicitud->estado }}</strong>
                @if($solicitud->respuesta_admin) / {{ $solicitud->respuesta_admin }} @endif
            </div>
        @endif

        <form class="surface p-3" method="POST" action="{{ route('proveedor.perfil.update') }}">
            @csrf @method('PUT')
            <label class="form-label">Nombre comercial</label>
            <input class="form-control mb-3" name="nombre_comercial" value="{{ old('nombre_comercial', $perfil->nombre_comercial ?? $proveedor->nombre) }}" required>

            <label class="form-label">Descripcion</label>
            <textarea class="form-control mb-3" name="descripcion" rows="4">{{ old('descripcion', $perfil->descripcion ?? '') }}</textarea>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">NIT</label>
                    <input class="form-control mb-3" name="nit" value="{{ old('nit', $perfil->nit ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Telefono</label>
                    <input class="form-control mb-3" name="telefono" value="{{ old('telefono', $perfil->telefono ?? $proveedor->telefono) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Direccion</label>
                    <input class="form-control mb-3" name="direccion" value="{{ old('direccion', $perfil->direccion ?? $proveedor->direccion) }}">
                </div>
            </div>

            <label class="form-label">Mensaje para el administrador</label>
            <textarea class="form-control mb-3" name="mensaje" rows="3">{{ old('mensaje') }}</textarea>

            <button class="btn btn-primary">Enviar a revision</button>
        </form>
    </section>
</div>
@endsection
