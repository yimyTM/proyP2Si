<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Turno extends Model
{
    protected $table      = 'turnos';
    protected $primaryKey = 'idTurno';

    protected $fillable = ['nombTurno'];

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class, 'idTurno', 'idTurno');
    }
}
