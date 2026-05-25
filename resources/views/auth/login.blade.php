@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="bg-white border rounded-3 p-4">
            <h1 class="h4 mb-3">Iniciar sesión</h1>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <label class="form-label">Correo</label>
                <input class="form-control mb-3" type="email" name="email" value="{{ old('email') }}" required>
                <label class="form-label">Contraseña</label>
                <input class="form-control mb-3" type="password" name="password" required>
                <button class="btn btn-primary w-100">Ingresar</button>
            </form>
        </div>
    </div>
</div>
@endsection
