<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ExpedienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->esPostulante();
    }

    public function rules(): array
    {
        return [
            // Datos personales
            'nombre'              => ['required', 'string', 'max:100'],
            'apellidos'           => ['required', 'string', 'max:100'],
            'ci'                  => ['required', 'string', 'max:20'],
            'fecha_nacimiento'    => ['required', 'date', 'before:-17 years'],
            'sexo'                => ['required', 'in:M,F'],
            'nroTelefono'         => ['nullable', 'string', 'max:20'],
            'ciudad'              => ['required', 'string', 'max:100'],
            'direccion'           => ['nullable', 'string', 'max:255'],
            'colegio_procedencia' => ['required', 'string', 'max:150'],

            // Opciones de carrera (1ra y 2da opción)
            'carrera_primera'     => ['required', 'integer', 'exists:carreras,codCarrera'],
            'carrera_segunda'     => ['nullable', 'integer', 'exists:carreras,codCarrera', 'different:carrera_primera'],

            // Confirmación de tenencia del Título de Bachiller (documento físico)
            'titulo_confirmado'   => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_nacimiento.before'         => 'Debes tener al menos 17 años.',
            'carrera_segunda.different'       => 'La segunda opción debe ser diferente a la primera.',
            'titulo_confirmado.required'      => 'Debes confirmar que tienes el Título de Bachiller.',
            'titulo_confirmado.accepted'      => 'Debes confirmar que tienes el Título de Bachiller.',
        ];
    }
}
