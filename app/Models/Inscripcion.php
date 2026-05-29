<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inscripcion extends Model
{
    protected $table      = 'inscripcions';
    protected $primaryKey = 'idInscripcion';

    protected $fillable = ['fecha', 'estado', 'idPost', 'idGestion'];

    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    public function postulante(): BelongsTo
    {
        return $this->belongsTo(Postulante::class, 'idPost', 'idPost');
    }

    public function gestion(): BelongsTo
    {
        return $this->belongsTo(Gestion::class, 'idGestion', 'idGestion');
    }

    public function carrerasInscritas(): HasMany
    {
        return $this->hasMany(Carrera_Inscrito::class, 'idInscripcion', 'idInscripcion');
    }

    public function carreras(): BelongsToMany
    {
        return $this->belongsToMany(
            Carrera::class,
            'carrera__inscritos',
            'idInscripcion',
            'codCarrera'
        )->withPivot('prioridad')->orderByPivot('prioridad');
    }
}
