<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Form_Academica extends Model
{
    protected $table      = 'form_academicas';
    protected $primaryKey = 'idForm';

    protected $fillable = ['nroProfesion', 'nombProfesion'];

    public function docentes(): BelongsToMany
    {
        return $this->belongsToMany(
            Docente::class,
            'form_docente',
            'idForm',
            'codigoDoc'
        );
    }
}
