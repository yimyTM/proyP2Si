<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rol extends Model
{
    protected $table      = 'rols';
    protected $primaryKey = 'idRol';

    protected $fillable = ['nombre_Rol'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'idRol', 'idRol');
    }

    public function permisos(): BelongsToMany
    {
        return $this->belongsToMany(
            Permiso::class,
            'rol_permisos',
            'idRol',
            'idPermiso'
        );
    }
}
