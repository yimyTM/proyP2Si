<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Modalidad extends Model
{
    protected $table      = 'modalidads';
    protected $primaryKey = 'codeModalidad';

    protected $fillable = ['nombModalidad'];

    public function carreras(): HasMany
    {
        return $this->hasMany(Carrera::class, 'codeModalidad', 'codeModalidad');
    }

    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class, 'codeModalidad', 'codeModalidad');
    }
}
