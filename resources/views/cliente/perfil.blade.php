@extends('layouts.app')

@section('content')
<div class="admin-section-title">
    <div>
        <span class="badge-status">Cliente</span>
        <h1 class="display-6 fw-bold mt-2 mb-1">Mi cuenta</h1>
        <p class="text-muted mb-0">Datos personales, pedidos realizados y direcciones guardadas.</p>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary" href="{{ route('cliente.pedidos') }}">Mis pedidos</a>
        <a class="btn btn-primary" href="{{ route('cliente.direcciones.index') }}">Direcciones</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="surface admin-metric p-3 mb-3">
            <div class="text-muted">Pedidos realizados</div>
            <div class="fs-2 fw-bold">{{ $pedidosCount }}</div>
        </div>
        <div class="surface admin-metric p-3">
            <div class="text-muted">Direcciones guardadas</div>
            <div class="fs-2 fw-bold">{{ $direccionesCount }}</div>
        </div>
    </div>
    <div class="col-lg-8">
        <form class="surface p-3" method="POST" action="{{ route('cliente.perfil.update') }}">
            @csrf @method('PUT')
            <label class="form-label">Nombre</label>
            <input class="form-control mb-3" name="name" value="{{ old('name', $user->name) }}" required>
            <label class="form-label">Correo</label>
            <input class="form-control mb-3" type="email" name="email" value="{{ old('email', $user->email) }}" required>
            <button class="btn btn-primary">Guardar cambios</button>
        </form>
    </div>
</div>
@endsection
