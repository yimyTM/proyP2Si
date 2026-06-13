<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Modulo extends Model
{
    protected $table      = 'modulos';
    protected $primaryKey = 'idModulo';

    protected $fillable = ['nombreModulo'];

    public function permisos(): HasMany
    {
        return $this->hasMany(Permiso::class, 'idModulo', 'idModulo');
    }
}
