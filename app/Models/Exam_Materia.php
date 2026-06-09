<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam_Materia extends Model
{
    protected $table      = 'exam_materias';
    protected $primaryKey = 'idEx_materia';
    protected $fillable   = ['puntaje', 'idExamen', 'idMateria'];

    public function examen(): BelongsTo
    {
        return $this->belongsTo(Examen::class, 'idExamen', 'idExamen');
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class, 'idMateria', 'idMateria');
    }

    public function notas(): HasMany
    {
        return $this->hasMany(Nota::class, 'idEx_materia', 'idEx_materia');
    }
}
