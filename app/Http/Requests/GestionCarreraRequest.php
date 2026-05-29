<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GestionCarreraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cupos'   => ['required', 'array'],
            'cupos.*' => ['nullable', 'integer', 'min:1', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'cupos.required' => 'Debe indicar los cupos para al menos una carrera.',
            'cupos.*.min'    => 'Los cupos por grupo deben ser al menos 1.',
            'cupos.*.max'    => 'Los cupos por grupo no pueden superar 500.',
        ];
    }
}
