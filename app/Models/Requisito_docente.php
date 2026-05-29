<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Requisito_docente extends Model
{
    protected $table      = 'requisito_docente';
    protected $primaryKey = 'idReqDoc';

    protected $fillable = [
        'fecha_entrega',
        'entregado',
        'validado',
        'idReq',
        'codigoDoc',
    ];

    protected function casts(): array
    {
        return [
            'fecha_entrega' => 'date',
            'entregado'     => 'boolean',
            'validado'      => 'boolean',
        ];
    }

    public function requisito(): BelongsTo
    {
        return $this->belongsTo(requisito::class, 'idReq', 'idReq');
    }

    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class, 'codigoDoc', 'codigoDoc');
    }
}
