<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Materia extends Model
{
    protected $table      = 'materias';
    protected $primaryKey = 'idMateria';

    protected $fillable = ['nombMateria'];

    public function grupos(): BelongsToMany
    {
        return $this->belongsToMany(
            Grupo::class,
            'materi_grupos',
            'idMateria',
            'codigoG'
        );
    }
}
