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
            'archivo' => ['required', 'file', 'mimes:csv,txt,xlsx,ods', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'archivo.required' => 'Debe seleccionar un archivo.',
            'archivo.mimes'    => 'Formato no permitido. Use CSV o Excel.',
            'archivo.max'      => 'El archivo no debe superar los 5 MB.',
        ];
    }
}
