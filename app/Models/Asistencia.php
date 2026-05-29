<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asistencia extends Model
{
    protected $table      = 'asistencias';
    protected $primaryKey = 'idAsistencia';

    protected $fillable = ['fecha', 'observacion', 'codigoG', 'codigoDoc'];

    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class, 'codigoG', 'codigoG');
    }

    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class, 'codigoDoc', 'codigoDoc');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleAsistencia::class, 'idAsistencia', 'idAsistencia');
    }

    public function totalPresentes(): int
    {
        return $this->detalles()->where('estado', 'presente')->count();
    }

    public function totalAusentes(): int
    {
        return $this->detalles()->where('estado', 'ausente')->count();
    }
}
