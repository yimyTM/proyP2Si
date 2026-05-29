<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Turno extends Model
{
    protected $table      = 'turnos';
    protected $primaryKey = 'idTurno';

    protected $fillable = ['nombTurno'];

    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class, 'idTurno', 'idTurno');
    }
}
