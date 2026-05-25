<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('administrador');
    }

    public function rules(): array
    {
        return [
            'proveedor_id' => ['required', 'exists:proveedores,id'],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'imagen_url' => ['nullable', 'url', 'max:2048'],
            'precio' => ['required', 'numeric', 'min:0'],
            'peso_kg' => ['required', 'numeric', 'min:0.01'],
            'stock' => ['required', 'integer', 'min:0'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
}
