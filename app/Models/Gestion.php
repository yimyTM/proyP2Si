<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gestion extends Model
{
    protected $table      = 'gestions';
    protected $primaryKey = 'idGestion';

    protected $fillable = ['fecha_ini', 'fecha_fin', 'estado'];

    protected function casts(): array
    {
        return [
            'fecha_ini' => 'date',
            'fecha_fin' => 'date',
        ];
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class, 'idGestion', 'idGestion');
    }

    public function carreras(): BelongsToMany
    {
        return $this->belongsToMany(
            Carrera::class,
            'gestion_carreras',
            'idGestion',
            'codCarrera'
        )->withPivot('cupos')->withTimestamps();
    }

    public function gestionCarreras(): HasMany
    {
        return $this->hasMany(GestionCarrera::class, 'idGestion', 'idGestion');
    }

    public function estaAbierta(): bool
    {
        return $this->estado === 'Abierta';
    }

    /** Total de inscritos validados en esta gestión. */
    public function totalInscritosValidados(): int
    {
        return $this->inscripciones()->where('estado', 'validada')->count();
    }
}
