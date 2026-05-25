@extends('layouts.app')

@section('content')
<h1 class="h3 mb-3">{{ $mantenimiento->exists ? 'Editar mantenimiento' : 'Nuevo mantenimiento' }}</h1>
<form class="bg-white border rounded-3 p-3" method="POST" action="{{ $mantenimiento->exists ? route('admin.mantenimientos.update', $mantenimiento) : route('admin.mantenimientos.store') }}">
    @csrf @if($mantenimiento->exists) @method('PUT') @endif
    <label class="form-label">Drone</label>
    <select class="form-select mb-3" name="drone_id" required>
        @foreach($drones as $drone)<option value="{{ $drone->id }}" @selected(old('drone_id', $mantenimiento->drone_id) == $drone->id)>{{ $drone->codigo }} · {{ $drone->modelo }}</option>@endforeach
    </select>
    <label class="form-label">Tipo</label><input class="form-control mb-3" name="tipo" value="{{ old('tipo', $mantenimiento->tipo) }}" required>
    <label class="form-label">Descripción</label><textarea class="form-control mb-3" name="descripcion">{{ old('descripcion', $mantenimiento->descripcion) }}</textarea>
    <label class="form-label">Programado en</label><input class="form-control mb-3" type="datetime-local" name="programado_en" value="{{ old('programado_en', optional($mantenimiento->programado_en)->format('Y-m-d\\TH:i')) }}" required>
    <label class="form-label">Realizado en</label><input class="form-control mb-3" type="datetime-local" name="realizado_en" value="{{ old('realizado_en', optional($mantenimiento->realizado_en)->format('Y-m-d\\TH:i')) }}">
    <button class="btn btn-primary">Guardar</button>
</form>
@endsection
