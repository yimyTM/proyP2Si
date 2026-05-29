<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Carrera_Inscrito extends Model
{
    protected $table      = 'carrera__inscritos';
    protected $primaryKey = 'idCarreraInscrito';

    protected $fillable = ['prioridad', 'idInscripcion', 'codCarrera'];

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class, 'idInscripcion', 'idInscripcion');
    }

    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class, 'codCarrera', 'codCarrera');
    }
}
