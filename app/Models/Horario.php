<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Horario extends Model
{
    protected $table      = 'horarios';
    protected $primaryKey = 'idHorario';

    protected $fillable = ['hora_ini', 'hora_fin', 'dia'];

    protected function casts(): array
    {
        return [
            'hora_ini' => 'datetime',
            'hora_fin' => 'datetime',
        ];
    }

    public function grupos(): BelongsToMany
    {
        return $this->belongsToMany(
            Grupo::class,
            'grupo__horarios',
            'idHorario',
            'codigoG'
        );
    }

    /**
     * Verifica si este horario se superpone con otro en el mismo día.
     * Fórmula: dos intervalos [A,B] y [C,D] se solapan si A < D AND C < B.
     */
    public function seSuperponecon(Horario $otro): bool
    {
        if ($this->dia !== $otro->dia) {
            return false;
        }

        $iniA = $this->hora_ini;
        $finA = $this->hora_fin;
        $iniB = $otro->hora_ini;
        $finB = $otro->hora_fin;

        return $iniA < $finB && $iniB < $finA;
    }

    public function getLabelAttribute(): string
    {
        return "{$this->dia} {$this->hora_ini->format('H:i')}–{$this->hora_fin->format('H:i')}";
    }
}
