<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleAsistencia extends Model
{
    protected $table      = 'detalle_asistencias';
    protected $primaryKey = 'idDetalle';

    protected $fillable = ['idAsistencia', 'idPost', 'estado'];

    public function asistencia(): BelongsTo
    {
        return $this->belongsTo(Asistencia::class, 'idAsistencia', 'idAsistencia');
    }

    public function postulante(): BelongsTo
    {
        return $this->belongsTo(Postulante::class, 'idPost', 'idPost');
    }
}
