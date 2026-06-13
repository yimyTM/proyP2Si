<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class solicitud_materia extends Model
{
    protected $table = 'solicitud_materias';

    public $timestamps   = false;
    public $incrementing = false;
    protected $primaryKey = null;

    protected $guarded = [];

    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class, 'codigoDoc', 'codigoDoc');
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class, 'idMateria', 'idMateria');
    }
}
