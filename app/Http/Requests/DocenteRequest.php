<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocenteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $docente   = $this->route('docente');
        $docenteId = $docente?->codigoDoc;

        return [
            'nombre'        => ['required', 'string', 'max:100'],
            'apellido'      => ['required', 'string', 'max:100'],
            'ci'            => ['required', 'string', 'max:20', Rule::unique('docentes', 'ci')->ignore($docenteId, 'codigoDoc')],
            'nroTelefono'   => ['nullable', 'string', 'max:20'],
            'direccion'     => ['nullable', 'string', 'max:255'],
            'carga_horaria' => ['nullable', 'integer', 'min:0', 'max:40'],

            // CU15 — formación académica y requisitos documentales (solo en registro)
            'formaciones'                  => ['nullable', 'array'],
            'formaciones.*'                => ['integer', 'exists:form_academicas,idForm'],
            'nuevas_profesiones'           => ['nullable', 'array'],
            'nuevas_profesiones.*.nombProfesion' => ['nullable', 'string', 'max:100'],
            'nuevas_profesiones.*.nroProfesion'  => ['nullable', 'string', 'max:50'],
            'requisitos'                   => ['nullable', 'array'],
            'requisitos.*.fecha_entrega'   => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'   => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'ci.required'       => 'La cédula de identidad es obligatoria.',
            'ci.unique'         => 'Ya existe un docente con esa CI.',
        ];
    }
}
