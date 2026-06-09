<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ExpedienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->esPostulante();
    }

    public function rules(): array
    {
        $user       = $this->user();
        $postulante = $user?->postulante;

        return [
            // Datos personales
            'nombre'              => ['required', 'string', 'max:100'],
            'apellidos'           => ['required', 'string', 'max:100'],
            'ci'                  => [
                'required', 'string', 'max:20',
                Rule::unique('postulantes', 'ci')->ignore($postulante?->idPost, 'idPost'),
            ],
            'correo'              => [
                'required', 'email', 'max:150',
                Rule::unique('users', 'correo')->ignore($user?->idUsuario, 'idUsuario'),
            ],
            'fecha_nacimiento'    => ['required', 'date', 'before:-17 years'],
            'sexo'                => ['required', 'in:M,F'],
            'nroTelefono'         => ['nullable', 'string', 'max:20'],
            'ciudad'              => ['required', 'string', 'max:100'],
            'direccion'           => ['nullable', 'string', 'max:255'],
            'colegio_procedencia' => ['required', 'string', 'max:150'],

            // Opciones de carrera
            'carrera_primera'     => ['required', 'integer', 'exists:carreras,codCarrera'],
            'carrera_segunda'     => ['nullable', 'integer', 'exists:carreras,codCarrera', 'different:carrera_primera'],

            // Documentos requeridos (PDF, JPG o PNG — máx. 5 MB cada uno)
            'doc_titulo_bachiller'          => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'doc_libreta_escolar'           => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'doc_cedula_identidad'          => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'doc_formulario_preinscripcion' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'ci.unique'                             => 'Ya existe un postulante registrado con esta cédula de identidad.',
            'correo.unique'                         => 'Este correo electrónico ya se encuentra registrado.',
            'fecha_nacimiento.before'               => 'Debes tener al menos 17 años.',
            'carrera_segunda.different'             => 'La segunda opción debe ser diferente a la primera.',
            'doc_titulo_bachiller.required'         => 'Debes adjuntar el Título de Bachiller.',
            'doc_libreta_escolar.required'          => 'Debes adjuntar la Libreta Escolar.',
            'doc_cedula_identidad.required'         => 'Debes adjuntar la Cédula de Identidad.',
            'doc_formulario_preinscripcion.required'=> 'Debes adjuntar el Formulario de Preinscripción.',
            'doc_titulo_bachiller.mimes'            => 'Solo se permiten archivos PDF, JPG o PNG.',
            'doc_libreta_escolar.mimes'             => 'Solo se permiten archivos PDF, JPG o PNG.',
            'doc_cedula_identidad.mimes'            => 'Solo se permiten archivos PDF, JPG o PNG.',
            'doc_formulario_preinscripcion.mimes'   => 'Solo se permiten archivos PDF, JPG o PNG.',
            'doc_titulo_bachiller.max'              => 'El archivo no debe superar los 5 MB.',
            'doc_libreta_escolar.max'               => 'El archivo no debe superar los 5 MB.',
            'doc_cedula_identidad.max'              => 'El archivo no debe superar los 5 MB.',
            'doc_formulario_preinscripcion.max'     => 'El archivo no debe superar los 5 MB.',
        ];
    }
}
