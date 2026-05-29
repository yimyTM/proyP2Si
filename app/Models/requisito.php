<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class requisito extends Model
{
    protected $table      = 'requisitos';
    protected $primaryKey = 'idReq';

    protected $fillable = ['nombre', 'tipo', 'obligatorio'];

    protected function casts(): array
    {
        return ['obligatorio' => 'boolean'];
    }

    public function requisitoPostulantes(): HasMany
    {
        return $this->hasMany(Requisito_Postulante::class, 'idReq', 'idReq');
    }

    public function requisitoDocentes(): HasMany
    {
        return $this->hasMany(Requisito_docente::class, 'idReq', 'idReq');
    }
}
