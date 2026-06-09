<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nota extends Model
{
    protected $table      = 'notas';
    protected $primaryKey = 'idCalif';
    protected $fillable   = ['calificacion', 'idInscripcion', 'idEx_materia'];

    protected function casts(): array
    {
        return ['calificacion' => 'decimal:2'];
    }

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class, 'idInscripcion', 'idInscripcion');
    }

    public function examMateria(): BelongsTo
    {
        return $this->belongsTo(Exam_Materia::class, 'idEx_materia', 'idEx_materia');
    }
}
