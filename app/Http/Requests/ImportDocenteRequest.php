<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportDocenteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'archivo' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'archivo.required' => 'Debe seleccionar un archivo CSV.',
            'archivo.mimes'    => 'El archivo debe ser formato CSV (.csv).',
            'archivo.max'      => 'El archivo no debe superar los 5 MB.',
        ];
    }
}
