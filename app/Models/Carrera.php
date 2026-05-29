<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Carrera extends Model
{
    protected $table      = 'carreras';
    protected $primaryKey = 'codCarrera';

    protected $fillable = ['nombre', 'codeModalidad'];

    public function modalidad(): BelongsTo
    {
        return $this->belongsTo(Modalidad::class, 'codeModalidad', 'codeModalidad');
    }

    public function inscripciones(): BelongsToMany
    {
        return $this->belongsToMany(
            Inscripcion::class,
            'carrera__inscritos',
            'codCarrera',
            'idInscripcion'
        )->withPivot('prioridad');
    }

    public function gestiones(): BelongsToMany
    {
        return $this->belongsToMany(
            Gestion::class,
            'gestion_carreras',
            'codCarrera',
            'idGestion'
        )->withPivot('cupos')->withTimestamps();
    }

    /** Inscritos validados en una gestión específica. */
    public function inscritosValidados(int $idGestion): int
    {
        return $this->inscripciones()
            ->where('estado', 'validada')
            ->where('idGestion', $idGestion)
            ->count();
    }
}
