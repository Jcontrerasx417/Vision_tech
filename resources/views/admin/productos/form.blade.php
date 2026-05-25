@extends('layouts.app')

@section('content')
<h1 class="h3 mb-3">{{ $producto->exists ? 'Editar producto' : 'Nuevo producto' }}</h1>
<form class="bg-white border rounded-3 p-3" method="POST" action="{{ $producto->exists ? route('admin.productos.update', $producto) : route('admin.productos.store') }}">
    @csrf @if($producto->exists) @method('PUT') @endif
    <label class="form-label">Proveedor</label>
    <select class="form-select mb-3" name="proveedor_id" required>
        @foreach($proveedores as $proveedor)<option value="{{ $proveedor->id }}" @selected(old('proveedor_id', $producto->proveedor_id) == $proveedor->id)>{{ $proveedor->nombre }}</option>@endforeach
    </select>
    <label class="form-label">Nombre</label><input class="form-control mb-3" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required>
    <label class="form-label">Descripción</label><textarea class="form-control mb-3" name="descripcion">{{ old('descripcion', $producto->descripcion) }}</textarea>
    <label class="form-label">URL de imagen</label><input class="form-control mb-3" name="imagen_url" value="{{ old('imagen_url', $producto->imagen_url) }}" placeholder="https://...">
    <div class="row"><div class="col"><label class="form-label">Precio</label><input class="form-control mb-3" type="number" step="0.01" name="precio" value="{{ old('precio', $producto->precio) }}" required></div>
    <div class="col"><label class="form-label">Peso kg</label><input class="form-control mb-3" type="number" step="0.01" name="peso_kg" value="{{ old('peso_kg', $producto->peso_kg) }}" required></div>
    <div class="col"><label class="form-label">Stock</label><input class="form-control mb-3" type="number" name="stock" value="{{ old('stock', $producto->stock ?? 0) }}" required></div></div>
    <label class="form-check mb-3"><input class="form-check-input" type="checkbox" name="activo" value="1" @checked(old('activo', $producto->activo ?? true))> Activo</label>
    <button class="btn btn-primary">Guardar</button>
</form>
@endsection
