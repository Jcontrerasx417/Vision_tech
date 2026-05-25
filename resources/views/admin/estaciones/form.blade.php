@extends('layouts.app')

@section('content')
<h1 class="h3 mb-3">{{ $estacion->exists ? 'Editar estación' : 'Nueva estación' }}</h1>
<form class="bg-white border rounded-3 p-3" method="POST" action="{{ $estacion->exists ? route('admin.estaciones.update', $estacion) : route('admin.estaciones.store') }}">
    @csrf @if($estacion->exists) @method('PUT') @endif
    <label class="form-label">Nombre</label><input class="form-control mb-3" name="nombre" value="{{ old('nombre', $estacion->nombre) }}" required>
    <label class="form-label">Dirección</label><input class="form-control mb-3" name="direccion" value="{{ old('direccion', $estacion->direccion) }}" required>
    <div class="row"><div class="col"><label class="form-label">Latitud</label><input class="form-control mb-3" type="number" step="0.0000001" name="latitud" value="{{ old('latitud', $estacion->latitud) }}" required></div>
    <div class="col"><label class="form-label">Longitud</label><input class="form-control mb-3" type="number" step="0.0000001" name="longitud" value="{{ old('longitud', $estacion->longitud) }}" required></div></div>
    <label class="form-check mb-3"><input class="form-check-input" type="checkbox" name="activa" value="1" @checked(old('activa', $estacion->activa ?? true))> Activa</label>
    <button class="btn btn-primary">Guardar</button>
</form>
@endsection
