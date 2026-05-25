@extends('layouts.app')

@section('content')
<h1 class="h3 mb-3">{{ $proveedor->exists ? 'Editar proveedor' : 'Nuevo proveedor' }}</h1>
<form class="bg-white border rounded-3 p-3" method="POST" action="{{ $proveedor->exists ? route('admin.proveedores.update', $proveedor) : route('admin.proveedores.store') }}">
    @csrf @if($proveedor->exists) @method('PUT') @endif
    <label class="form-label">Nombre</label><input class="form-control mb-3" name="nombre" value="{{ old('nombre', $proveedor->nombre) }}" required>
    <label class="form-label">Contacto</label><input class="form-control mb-3" name="contacto" value="{{ old('contacto', $proveedor->contacto) }}">
    <label class="form-label">Email</label><input class="form-control mb-3" type="email" name="email" value="{{ old('email', $proveedor->email) }}">
    <label class="form-label">Teléfono</label><input class="form-control mb-3" name="telefono" value="{{ old('telefono', $proveedor->telefono) }}">
    <label class="form-label">Dirección</label><input class="form-control mb-3" name="direccion" value="{{ old('direccion', $proveedor->direccion) }}">
    <button class="btn btn-primary">Guardar</button>
</form>
@endsection
