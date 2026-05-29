<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha_ini' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_ini'],
            'estado'    => ['nullable', Rule::in(['Abierta', 'Cerrada'])],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_ini.required'       => 'La fecha de inicio es obligatoria.',
            'fecha_fin.required'       => 'La fecha de fin es obligatoria.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la de inicio.',
        ];
    }
}
