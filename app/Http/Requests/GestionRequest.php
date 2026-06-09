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
            'nombre'           => ['required', 'string', 'max:100'],
            'fecha_ini'        => ['required', 'date'],
            'fecha_fin'        => ['required', 'date', 'after_or_equal:fecha_ini'],
            'capacidad_maxima' => ['required', 'integer', 'min:1'],
            'estado'           => ['nullable', Rule::in(['Abierta', 'Cerrada'])],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'            => 'El nombre de la gestión es obligatorio.',
            'fecha_ini.required'         => 'La fecha de inicio es obligatoria.',
            'fecha_fin.required'         => 'La fecha de fin es obligatoria.',
            'fecha_fin.after_or_equal'   => 'La fecha de fin debe ser posterior o igual a la de inicio.',
            'capacidad_maxima.required'  => 'La capacidad máxima es obligatoria.',
            'capacidad_maxima.integer'   => 'La capacidad máxima debe ser un número entero.',
            'capacidad_maxima.min'       => 'La capacidad máxima debe ser al menos 1.',
        ];
    }
}
