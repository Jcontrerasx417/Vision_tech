@extends('layouts.app')

@section('content')
<div class="admin-section-title">
    <div>
        <span class="badge-status">Direcciones</span>
        <h1 class="display-6 fw-bold mt-2 mb-1">Mis direcciones</h1>
        <p class="text-muted mb-0">Guarda destinos frecuentes para usarlos al crear pedidos a domicilio.</p>
    </div>
    <a class="btn btn-outline-primary" href="{{ route('cliente.perfil') }}">Mi cuenta</a>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <form class="surface p-3" method="POST" action="{{ route('cliente.direcciones.store') }}">
            @csrf
            <h2 class="h5">Nueva direccion</h2>
            <label class="form-label">Nombre</label>
            <input class="form-control mb-3" name="nombre" placeholder="Casa, oficina..." required>
            <label class="form-label">Direccion</label>
            <input class="form-control mb-3" name="direccion" placeholder="Carrera 27 #36-14, Bucaramanga" required>
            <div class="row g-2">
                <div class="col">
                    <label class="form-label">Latitud</label>
                    <input class="form-control mb-3" type="number" step="0.0000001" name="latitud" value="{{ old('latitud', $direccion->latitud) }}" required>
                </div>
                <div class="col">
                    <label class="form-label">Longitud</label>
                    <input class="form-control mb-3" type="number" step="0.0000001" name="longitud" value="{{ old('longitud', $direccion->longitud) }}" required>
                </div>
            </div>
            <label class="form-check mb-3"><input class="form-check-input" type="checkbox" name="principal" value="1"> Usar como principal</label>
            <button class="btn btn-primary w-100">Guardar direccion</button>
        </form>
    </div>
    <div class="col-lg-7">
        <div class="surface p-3">
            <h2 class="h5">Guardadas</h2>
            @forelse($direcciones as $dir)
                <div class="d-flex justify-content-between gap-3 border-bottom py-3">
                    <div>
                        <strong>{{ $dir->nombre }}</strong>
                        @if($dir->principal)<span class="badge-status ms-2">Principal</span>@endif
                        <div class="text-muted">{{ $dir->direccion }}</div>
                        <div class="small text-muted">{{ $dir->latitud }}, {{ $dir->longitud }}</div>
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
