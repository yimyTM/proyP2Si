<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Postulante extends Model
{
    protected $table      = 'postulantes';
    protected $primaryKey = 'idPost';

    protected $fillable = [
        'nombre',
        'apellidos',
        'ci',
        'nroTelefono',
        'direccion',
        'sexo',
        'estado',
        'fecha_nacimiento',
        'ciudad',
        'colegio_procedencia',
        'correo',
        'contrasena',
        'idUsuario',
    ];

    protected $hidden = ['contrasena'];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idUsuario', 'idUsuario');
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class, 'idPost', 'idPost');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'idPost', 'idPost');
    }

    public function requisitos(): HasMany
    {
        return $this->hasMany(Requisito_Postulante::class, 'idPost', 'idPost');
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellidos}";
    }

    public function tienePagoAprobado(): bool
    {
        return $this->pagos()->where('estado', 'aprobado')->exists();
    }
}
