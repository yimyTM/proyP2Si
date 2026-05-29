<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pago extends Model
{
    protected $table      = 'pagos';
    protected $primaryKey = 'nroPago';

    protected $fillable = ['monto', 'fecha', 'estado', 'idPost'];

    protected function casts(): array
    {
        return ['fecha' => 'date', 'monto' => 'decimal:2'];
    }

    public function postulante(): BelongsTo
    {
        return $this->belongsTo(Postulante::class, 'idPost', 'idPost');
    }

    public function comprobante(): HasOne
    {
        return $this->hasOne(Comprobante::class, 'nroPago', 'nroPago');
    }

    public function estaAprobado(): bool
    {
        return $this->estado === 'aprobado';
    }
}
