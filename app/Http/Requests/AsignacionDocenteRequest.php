<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AsignacionDocenteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigoG'    => ['required', 'integer', 'exists:grupos,codigoG'],
            'codigoDoc'  => ['required', 'integer', 'exists:docentes,codigoDoc'],
            'idAula'     => ['required', 'integer', 'exists:aulas,idAula'],
            'idHorario'  => ['required', 'integer', 'exists:horarios,idHorario'],
            'idMateria'  => ['required', 'integer', 'exists:materias,idMateria'],
        ];
    }

    public function messages(): array
    {
        return [
            'codigoG.required'   => 'Seleccione un grupo.',
            'codigoDoc.required' => 'Seleccione un docente.',
            'idAula.required'    => 'Seleccione un aula.',
            'idHorario.required' => 'Seleccione un horario.',
            'idMateria.required' => 'Seleccione una materia.',
        ];
    }
}
