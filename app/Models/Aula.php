<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Aula extends Model
{
    protected $table      = 'aulas';
    protected $primaryKey = 'idAula';

    protected $fillable = ['capacidad', 'cantSillas', 'cantMesas'];

    public function grupos(): BelongsToMany
    {
        return $this->belongsToMany(
            Grupo::class,
            'grupo__aulas',
            'idAula',
            'codigoG'
        );
    }
}
