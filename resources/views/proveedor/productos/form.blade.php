@extends('layouts.app')

@section('content')
<div class="admin-shell">
    @include('proveedor.partials.nav')

    <section>
        <div class="admin-section-title">
            <div>
                <span class="badge-status">{{ $proveedor->nombre }}</span>
                <h1 class="display-6 fw-bold mt-2 mb-1">{{ $producto->exists ? 'Editar producto' : 'Publicar producto' }}</h1>
                <p class="text-muted mb-0">Este producto aparecera en el catalogo publico de VisionTech.</p>
            </div>
            <a class="btn btn-outline-primary" href="{{ route('proveedor.productos.index') }}">Volver</a>
        </div>

        <form class="surface p-3" method="POST" enctype="multipart/form-data" action="{{ $producto->exists ? route('proveedor.productos.update', $producto) : route('proveedor.productos.store') }}">
            @csrf
            @if($producto->exists) @method('PUT') @endif

            <label class="form-label">Nombre del producto</label>
            <input class="form-control mb-3" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required>

            <label class="form-label">Categoria</label>
            <select class="form-select mb-3" name="categoria_producto_id" required>
                <option value="">Seleccionar categoria</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" @selected(old('categoria_producto_id', $producto->categoria_producto_id) == $categoria->id)>{{ $categoria->nombre }}</option>
                @endforeach
            </select>

            <label class="form-label">Descripcion</label>
            <textarea class="form-control mb-3" name="descripcion" rows="4">{{ old('descripcion', $producto->descripcion) }}</textarea>

            @if($producto->imagen_url)
                <div class="mb-3">
                    <label class="form-label">Imagen actual</label>
                    <div><img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}" style="width: 180px; height: 120px; object-fit: cover; border-radius: 8px; border: 3px solid #111;"></div>
                </div>
            @endif

            <label class="form-label">Subir imagen desde tu equipo</label>
            <input class="form-control mb-2" type="file" name="imagen_archivo" accept="image/png,image/jpeg,image/webp">
            <div class="form-text mb-3">Formatos permitidos: JPG, PNG o WEBP. Maximo 2 MB. Si subes archivo, se usara por encima de la URL.</div>

            <label class="form-label">Imagenes adicionales</label>
            <input class="form-control mb-2" type="file" name="imagenes[]" accept="image/png,image/jpeg,image/webp" multiple>
            <div class="form-text mb-3">Puedes subir hasta 5 imagenes adicionales.</div>

            <label class="form-label">O pegar URL de imagen</label>
            <input class="form-control mb-3" name="imagen_url" value="{{ old('imagen_url', $producto->imagen_url) }}" placeholder="https://...">

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Precio</label>
                    <input class="form-control mb-3" type="number" step="0.01" name="precio" value="{{ old('precio', $producto->precio) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Peso kg</label>
                    <input class="form-control mb-3" type="number" step="0.01" name="peso_kg" value="{{ old('peso_kg', $producto->peso_kg) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Stock</label>
                    <input class="form-control mb-3" type="number" name="stock" value="{{ old('stock', $producto->stock ?? 0) }}" required>
                </div>
            </div>

            <div class="alert alert-success">Como tu proveedor ya fue aprobado, el producto quedara publicado en el catalogo al guardarlo.</div>

            <button class="btn btn-primary">Guardar producto</button>
        </form>
    </section>
</div>
@endsection
