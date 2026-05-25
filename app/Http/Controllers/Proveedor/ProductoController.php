<?php

namespace App\Http\Controllers\Proveedor;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\CategoriaProducto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductoController extends Controller
{
    public function index(Request $request): View
    {
        $proveedor = $this->proveedor($request);

        return view('proveedor.productos.index', [
            'proveedor' => $proveedor,
            'productos' => $proveedor->productos()->latest()->paginate(12),
        ]);
    }

    public function create(Request $request): View
    {
        return view('proveedor.productos.form', [
            'producto' => new Producto(['activo' => true]),
            'proveedor' => $this->proveedor($request),
            'categorias' => CategoriaProducto::where('activa', true)->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $proveedor = $this->proveedor($request);
        $data = $this->productData($request);
        abort_unless($proveedor->activo, 403, 'Tu perfil de proveedor debe ser aprobado antes de publicar productos.');

        $imagenes = $this->storeImages($request);
        if ($imagenes) {
            $data['imagen_url'] = $imagenes[0];
        }
        $producto = $proveedor->productos()->create($data + [
            'activo' => true,
            'estado' => 'aprobado',
        ]);
        $this->syncImages($producto, $imagenes ?: array_filter([$producto->imagen_url]));

        return redirect()->route('proveedor.productos.index')->with('status', 'Producto publicado en el catalogo.');
    }

    public function edit(Request $request, Producto $producto): View
    {
        $proveedor = $this->proveedor($request);
        abort_unless($producto->proveedor_id === $proveedor->id, 403);

        return view('proveedor.productos.form', [
            'producto' => $producto->load('imagenes'),
            'proveedor' => $proveedor,
            'categorias' => CategoriaProducto::where('activa', true)->orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Producto $producto): RedirectResponse
    {
        $proveedor = $this->proveedor($request);
        abort_unless($producto->proveedor_id === $proveedor->id, 403);

        $data = $this->productData($request);
        $imagenes = $this->storeImages($request);
        if ($imagenes) {
            $data['imagen_url'] = $imagenes[0];
        }
        $producto->update($data + [
            'activo' => true,
            'estado' => 'aprobado',
            'motivo_rechazo' => null,
        ]);
        if ($imagenes) {
            $producto->imagenes()->delete();
            $this->syncImages($producto, $imagenes);
        } elseif (! empty($data['imagen_url']) && $producto->imagenes()->count() === 0) {
            $this->syncImages($producto, [$data['imagen_url']]);
        }

        return redirect()->route('proveedor.productos.index')->with('status', 'Producto actualizado.');
    }

    public function destroy(Request $request, Producto $producto): RedirectResponse
    {
        $proveedor = $this->proveedor($request);
        abort_unless($producto->proveedor_id === $proveedor->id, 403);
        $producto->delete();

        return back()->with('status', 'Producto eliminado.');
    }

    public function desactivar(Request $request, Producto $producto): RedirectResponse
    {
        $proveedor = $this->proveedor($request);
        abort_unless($producto->proveedor_id === $proveedor->id, 403);
        $producto->update(['estado' => 'inactivo', 'activo' => false]);

        return back()->with('status', 'Producto desactivado.');
    }

    public function activar(Request $request, Producto $producto): RedirectResponse
    {
        $proveedor = $this->proveedor($request);
        abort_unless($producto->proveedor_id === $proveedor->id, 403);
        abort_unless($proveedor->activo, 403, 'Tu perfil de proveedor debe estar aprobado para activar productos.');
        $producto->update(['estado' => 'aprobado', 'activo' => true, 'motivo_rechazo' => null]);

        return back()->with('status', 'Producto activado y visible en el catalogo.');
    }

    private function proveedor(Request $request): Proveedor
    {
        return Proveedor::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['nombre' => $request->user()->name, 'contacto' => $request->user()->name, 'email' => $request->user()->email]
        );
    }

    private function productData(Request $request): array
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'categoria_producto_id' => ['required', 'exists:categoria_productos,id'],
            'descripcion' => ['nullable', 'string'],
            'imagen_url' => ['nullable', 'url', 'max:2048'],
            'imagen_archivo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'imagenes' => ['nullable', 'array', 'max:5'],
            'imagenes.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'precio' => ['required', 'numeric', 'min:0'],
            'peso_kg' => ['required', 'numeric', 'min:0.01'],
            'stock' => ['required', 'integer', 'min:0'],
            'activo' => ['nullable', 'boolean'],
        ]);

        unset($data['imagen_archivo'], $data['imagenes']);

        return $data;
    }

    private function storeImages(Request $request): array
    {
        $paths = [];

        if ($request->hasFile('imagen_archivo')) {
            $paths[] = Storage::url($request->file('imagen_archivo')->store('productos', 'public'));
        }

        foreach ($request->file('imagenes', []) as $image) {
            $paths[] = Storage::url($image->store('productos', 'public'));
        }

        return array_values(array_unique($paths));
    }

    private function syncImages(Producto $producto, array $urls): void
    {
        foreach (array_values($urls) as $index => $url) {
            $producto->imagenes()->create([
                'url' => $url,
                'principal' => $index === 0,
                'orden' => $index,
            ]);
        }
    }
}
