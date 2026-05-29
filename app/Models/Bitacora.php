<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bitacora extends Model
{
    protected $table      = 'bitacoras';
    protected $primaryKey = 'idBitacora';

    protected $fillable = [
        'descripcion',
        'fecha',
        'hora',
        'direccionIP',
        'idUsuario',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idUsuario', 'idUsuario');
    }
}
