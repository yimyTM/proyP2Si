<?php

namespace App\Http\Requests;

use App\Rules\StrongPassword;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token'                 => ['required', 'string'],
            'correo'                => ['required', 'email:rfc', 'max:150'],
            'password'              => ['required', 'string', 'confirmed', new StrongPassword],
            'password_confirmation' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'correo.required'                => 'El correo es obligatorio.',
            'password.required'              => 'La nueva contraseña es obligatoria.',
            'password.confirmed'             => 'Las contraseñas no coinciden.',
            'password_confirmation.required' => 'Confirme la nueva contraseña.',
        ];
    }
}
