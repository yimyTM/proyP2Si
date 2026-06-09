<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grupo extends Model
{
    protected $table      = 'grupos';
    protected $primaryKey = 'codigoG';

    protected $fillable = ['capacidad', 'numero_grupo', 'codeModalidad', 'idTurno', 'idGestion'];

    public function gestion(): BelongsTo
    {
        return $this->belongsTo(Gestion::class, 'idGestion', 'idGestion');
    }

    public function modalidad(): BelongsTo
    {
        return $this->belongsTo(Modalidad::class, 'codeModalidad', 'codeModalidad');
    }

    public function turno(): BelongsTo
    {
        return $this->belongsTo(Turno::class, 'idTurno', 'idTurno');
    }

    public function materiGrupos(): HasMany
    {
        return $this->hasMany(materi_grupo::class, 'codigoG', 'codigoG');
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class, 'codigoG', 'codigoG');
    }
}
