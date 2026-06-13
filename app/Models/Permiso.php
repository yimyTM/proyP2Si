<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permiso extends Model
{
    protected $table      = 'permisos';
    protected $primaryKey = 'idPermiso';

    protected $fillable = ['nombrePermiso', 'idModulo'];

    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulo::class, 'idModulo', 'idModulo');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Rol::class,
            'rol_permisos',
            'idPermiso',
            'idRol'
        );
    }
}
