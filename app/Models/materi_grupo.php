<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class materi_grupo extends Model
{
    protected $table      = 'materi_grupos';
    public    $incrementing = false;
    public    $timestamps   = false;

    protected $fillable = ['codigoG', 'idMateria', 'idHorario', 'idAula', 'codigoDoc'];

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class, 'codigoG', 'codigoG');
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class, 'idMateria', 'idMateria');
    }

    public function horario(): BelongsTo
    {
        return $this->belongsTo(Horario::class, 'idHorario', 'idHorario');
    }

    public function aula(): BelongsTo
    {
        return $this->belongsTo(Aula::class, 'idAula', 'idAula');
    }

    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class, 'codigoDoc', 'codigoDoc');
    }
}
