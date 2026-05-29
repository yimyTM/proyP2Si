<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permiso extends Model
{
    protected $table      = 'permisos';
    protected $primaryKey = 'idPermiso';

    protected $fillable = ['nombrePermiso', 'categoria'];

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
