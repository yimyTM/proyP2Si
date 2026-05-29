<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'correo' => ['required', 'email:rfc', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'correo.required' => 'Ingrese su correo electrónico.',
            'correo.email'    => 'Ingrese un correo electrónico válido.',
        ];
    }
}
