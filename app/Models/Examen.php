<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Examen extends Model
{
    protected $table      = 'examens';
    protected $primaryKey = 'idExamen';
    protected $fillable   = ['descripcion', 'fecha', 'ponderacion', 'nroParcial', 'idGestion'];

    protected function casts(): array
    {
        return ['fecha' => 'datetime'];
    }

    public function gestion(): BelongsTo
    {
        return $this->belongsTo(Gestion::class, 'idGestion', 'idGestion');
    }

    public function examMaterias(): HasMany
    {
        return $this->hasMany(Exam_Materia::class, 'idExamen', 'idExamen');
    }
}
