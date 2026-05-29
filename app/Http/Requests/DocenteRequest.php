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
        $userId    = $docente?->idUsuario;

        return [
            'nombre'        => ['required', 'string', 'max:100'],
            'apellido'      => ['required', 'string', 'max:100'],
            'ci'            => ['required', 'string', 'max:20', Rule::unique('docentes', 'ci')->ignore($docenteId, 'codigoDoc')],
            'correo'        => ['nullable', 'email', 'max:100', Rule::unique('users', 'correo')->ignore($userId, 'idUsuario')],
            'nroTelefono'   => ['nullable', 'string', 'max:20'],
            'direccion'     => ['nullable', 'string', 'max:255'],
            'carga_horaria' => ['nullable', 'integer', 'min:0', 'max:40'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'   => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'ci.required'       => 'La cédula de identidad es obligatoria.',
            'ci.unique'         => 'Ya existe un docente con esa CI.',
            'correo.email'      => 'Ingrese un correo electrónico válido.',
            'correo.unique'     => 'Ese correo ya está registrado en el sistema.',
        ];
    }
}
