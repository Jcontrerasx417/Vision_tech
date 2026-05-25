<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EstacionEntregaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('administrador', 'personal_logistico');
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'direccion' => ['required', 'string', 'max:255'],
            'latitud' => ['required', 'numeric', 'between:-90,90'],
            'longitud' => ['required', 'numeric', 'between:-180,180'],
            'activa' => ['nullable', 'boolean'],
        ];
    }
}
