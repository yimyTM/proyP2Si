<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Requisito_Postulante extends Model
{
    protected $table      = 'requisito__postulantes';
    protected $primaryKey = 'idReqPos';

    protected $fillable = [
        'fecha_entrega',
        'entregado',
        'validado',
        'idReq',
        'idPost',
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

    public function postulante(): BelongsTo
    {
        return $this->belongsTo(Postulante::class, 'idPost', 'idPost');
    }
}
