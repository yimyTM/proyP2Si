<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'correo'   => ['required', 'email:rfc,dns'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'correo.required'   => 'El correo electrónico es obligatorio.',
            'correo.email'      => 'Ingrese un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'La contraseña debe tener al menos 6 caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'correo'   => 'correo electrónico',
            'password' => 'contraseña',
        ];
    }
}
