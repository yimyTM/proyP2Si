<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Docente extends Model
{
    protected $table      = 'docentes';
    protected $primaryKey = 'codigoDoc';

    protected $fillable = [
        'nombre',
        'apellido',
        'ci',
        'nroTelefono',
        'direccion',
        'correo',
        'carga_horaria',
        'contrasena',
        'idUsuario',
    ];

    protected $hidden = ['contrasena'];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idUsuario', 'idUsuario');
    }

    public function grupos(): BelongsToMany
    {
        return $this->belongsToMany(
            Grupo::class,
            'docente__grupos',
            'codigoDoc',
            'codigoG'
        );
    }

    public function formAcademicas(): BelongsToMany
    {
        return $this->belongsToMany(
            Form_Academica::class,
            'form_docente',
            'codigoDoc',
            'idForm'
        );
    }

    public function requisitosDocente(): HasMany
    {
        return $this->hasMany(Requisito_docente::class, 'codigoDoc', 'codigoDoc');
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }
}
