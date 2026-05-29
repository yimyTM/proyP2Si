<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Grupo extends Model
{
    protected $table      = 'grupos';
    protected $primaryKey = 'codigoG';

    protected $fillable = ['capacidad', 'codeModalidad', 'idTurno'];

    public function modalidad(): BelongsTo
    {
        return $this->belongsTo(Modalidad::class, 'codeModalidad', 'codeModalidad');
    }

    public function turno(): BelongsTo
    {
        return $this->belongsTo(Turno::class, 'idTurno', 'idTurno');
    }

    public function docentes(): BelongsToMany
    {
        return $this->belongsToMany(
            Docente::class,
            'docente__grupos',
            'codigoG',
            'codigoDoc'
        );
    }

    public function horarios(): BelongsToMany
    {
        return $this->belongsToMany(
            Horario::class,
            'grupo__horarios',
            'codigoG',
            'idHorario'
        );
    }

    public function aulas(): BelongsToMany
    {
        return $this->belongsToMany(
            Aula::class,
            'grupo__aulas',
            'codigoG',
            'idAula'
        );
    }

    public function materias(): BelongsToMany
    {
        return $this->belongsToMany(
            Materia::class,
            'materi_grupos',
            'codigoG',
            'idMateria'
        );
    }
}
