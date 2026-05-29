<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostulanteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $postulante   = $this->route('postulante');
        $postulanteId = $postulante?->idPost;
        $userId       = $postulante?->idUsuario;

        return [
            'nombre'              => ['required', 'string', 'max:100'],
            'apellidos'           => ['required', 'string', 'max:100'],
            'ci'                  => ['required', 'string', 'max:20', Rule::unique('postulantes', 'ci')->ignore($postulanteId, 'idPost')],
            'correo'              => ['nullable', 'email', 'max:100', Rule::unique('users', 'correo')->ignore($userId, 'idUsuario')],
            'nroTelefono'         => ['nullable', 'string', 'max:20'],
            'direccion'           => ['nullable', 'string', 'max:255'],
            'sexo'                => ['nullable', Rule::in(['M', 'F'])],
            'estado'              => ['required', Rule::in(['activo', 'inactivo'])],
            'fecha_nacimiento'    => ['nullable', 'date', 'before:today'],
            'ciudad'              => ['nullable', 'string', 'max:100'],
            'colegio_procedencia' => ['nullable', 'string', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'    => 'El nombre es obligatorio.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'ci.required'        => 'La cédula de identidad es obligatoria.',
            'ci.unique'          => 'Ya existe un postulante con esa CI.',
            'correo.email'       => 'Ingrese un correo electrónico válido.',
            'correo.unique'      => 'Ese correo ya está registrado en el sistema.',
            'estado.required'    => 'El estado es obligatorio.',
        ];
    }
}
