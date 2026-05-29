<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GestionCarrera extends Model
{
    protected $table = 'gestion_carreras';

    protected $fillable = [
        'idGestion',
        'codCarrera',
        'cupos',
    ];

    public function gestion(): BelongsTo
    {
        return $this->belongsTo(Gestion::class, 'idGestion', 'idGestion');
    }

    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class, 'codCarrera', 'codCarrera');
    }
}
