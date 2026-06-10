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
        'carga_horaria',
        'idUsuario',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idUsuario', 'idUsuario');
    }

    public function grupos(): BelongsToMany
    {
        return $this->belongsToMany(
            Grupo::class,
            'materi_grupos',
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

    public function gestiones(): BelongsToMany
    {
        return $this->belongsToMany(
            Gestion::class,
            'docente_gestion',
            'codigoDoc',
            'idGestion'
        )->withPivot('fecha_contrato', 'estado')->withTimestamps();
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }

    /** ¿El docente está contratado en la gestión indicada? */
    public function estaContratadoEn(int $idGestion): bool
    {
        return $this->gestiones()
            ->wherePivot('idGestion', $idGestion)
            ->wherePivot('estado', 'Contratado')
            ->exists();
    }

    /**
     * ¿Tiene todos los requisitos documentales obligatorios validados?
     * Compara los requisitos tipo 'D' obligatorios contra los validados del docente.
     */
    public function tieneRequisitosValidados(): bool
    {
        $obligatorios = requisito::where('tipo', 'D')
            ->where('obligatorio', true)
            ->pluck('idReq');

        if ($obligatorios->isEmpty()) {
            return false;
        }

        $validados = $this->requisitosDocente()
            ->where('validado', true)
            ->pluck('idReq');

        return $obligatorios->diff($validados)->isEmpty();
    }
}
