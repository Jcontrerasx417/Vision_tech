@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="surface p-4">
            <span class="badge-status">VisionTech</span>
            <h1 class="h4 mt-3 mb-3">Crear cuenta</h1>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <label class="form-label">Nombre</label>
                <input class="form-control mb-3" name="name" value="{{ old('name') }}" required>

                <label class="form-label">Correo</label>
                <input class="form-control mb-3" type="email" name="email" value="{{ old('email') }}" required>

                <label class="form-label">Telefono</label>
                <input class="form-control mb-3" name="telefono" value="{{ old('telefono') }}" placeholder="Ej: 3001234567">

                <label class="form-label">Rol</label>
                <select class="form-select mb-3" name="role">
                    <option value="cliente">Cliente</option>
                    <option value="proveedor">Proveedor</option>
                    <option value="administrador">Administrador</option>
                    <option value="personal_logistico">Personal logistico</option>
                </select>

                <label class="form-label">Nombre de tienda o marca</label>
                <input class="form-control mb-3" name="nombre_tienda" value="{{ old('nombre_tienda') }}" placeholder="Solo si te registras como proveedor">

                <label class="form-label">Contrasena</label>
                <input class="form-control mb-3" type="password" name="password" required>

                <label class="form-label">Confirmar contrasena</label>
                <input class="form-control mb-3" type="password" name="password_confirmation" required>

                <button class="btn btn-primary w-100">Crear cuenta</button>
            </form>
        </div>
    </div>
</div>
@endsection
