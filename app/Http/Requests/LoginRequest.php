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
            // Sin 'dns': muchos correos institucionales (@ficct.edu.bo, @estudiante.bo)
            // no tienen registros MX, y exigir DNS impediría iniciar sesión a cuentas válidas
            // (además requeriría conexión a internet en cada login).
            'correo'   => ['required', 'email:rfc'],
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
