<?php

namespace App\Http\Requests;

use App\Models\Drone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DroneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('administrador', 'personal_logistico');
    }

    public function rules(): array
    {
        $droneId = $this->route('drone')?->id;

        return [
            'codigo' => ['required', 'string', 'max:255', Rule::unique('drones', 'codigo')->ignore($droneId)],
            'modelo' => ['required', 'string', 'max:255'],
            'capacidad_kg' => ['required', 'numeric', 'min:0.01'],
            'bateria' => ['required', 'integer', 'between:0,100'],
            'estado' => ['required', Rule::in(Drone::ESTADOS)],
            'estacion_entrega_id' => ['nullable', 'exists:estacion_entregas,id'],
        ];
    }
}
