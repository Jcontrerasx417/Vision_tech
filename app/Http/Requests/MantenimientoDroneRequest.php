<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MantenimientoDroneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('administrador', 'personal_logistico');
    }

    public function rules(): array
    {
        return [
            'drone_id' => ['required', 'exists:drones,id'],
            'tipo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'programado_en' => ['required', 'date'],
            'realizado_en' => ['nullable', 'date', 'after_or_equal:programado_en'],
        ];
    }
}
