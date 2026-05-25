@extends('layouts.app')

@section('content')
<h1 class="h3 mb-3">{{ $drone->exists ? 'Editar drone' : 'Nuevo drone' }}</h1>
<form class="bg-white border rounded-3 p-3" method="POST" action="{{ $drone->exists ? route('admin.drones.update', $drone) : route('admin.drones.store') }}">
    @csrf @if($drone->exists) @method('PUT') @endif
    <label class="form-label">Código</label><input class="form-control mb-3" name="codigo" value="{{ old('codigo', $drone->codigo) }}" required>
    <label class="form-label">Modelo</label><input class="form-control mb-3" name="modelo" value="{{ old('modelo', $drone->modelo) }}" required>
    <div class="row"><div class="col"><label class="form-label">Capacidad kg</label><input class="form-control mb-3" type="number" step="0.01" name="capacidad_kg" value="{{ old('capacidad_kg', $drone->capacidad_kg) }}" required></div>
    <div class="col"><label class="form-label">Batería</label><input class="form-control mb-3" type="number" name="bateria" value="{{ old('bateria', $drone->bateria ?? 100) }}" required></div></div>
    <label class="form-label">Estado</label>
    <select class="form-select mb-3" name="estado">@foreach(\App\Models\Drone::ESTADOS as $estado)<option @selected(old('estado', $drone->estado) === $estado)>{{ $estado }}</option>@endforeach</select>
    <label class="form-label">Estación</label>
    <select class="form-select mb-3" name="estacion_entrega_id"><option value="">Sin estación</option>@foreach($estaciones as $estacion)<option value="{{ $estacion->id }}" @selected(old('estacion_entrega_id', $drone->estacion_entrega_id) == $estacion->id)>{{ $estacion->nombre }}</option>@endforeach</select>
    <button class="btn btn-primary">Guardar</button>
</form>
@endsection
