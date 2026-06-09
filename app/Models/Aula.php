<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aula extends Model
{
    protected $table      = 'aulas';
    protected $primaryKey = 'idAula';

    protected $fillable = ['capacidad'];

    public function materiGrupos(): HasMany
    {
        return $this->hasMany(materi_grupo::class, 'idAula', 'idAula');
    }
}
