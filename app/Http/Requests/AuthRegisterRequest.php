<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'telefono' => ['nullable', 'string', 'max:50', 'regex:/^[0-9+()\\s-]{7,50}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['nullable', Rule::in(['cliente', 'administrador', 'personal_logistico', 'proveedor'])],
            'nombre_tienda' => ['nullable', 'required_if:role,proveedor', 'string', 'max:255'],
        ];
    }
}
