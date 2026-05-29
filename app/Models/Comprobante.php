<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comprobante extends Model
{
    protected $table      = 'comprobantes';
    protected $primaryKey = 'idComprobante';

    protected $fillable = ['codigo', 'nroComprobante', 'concepto', 'fecha', 'nroPago'];

    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class, 'nroPago', 'nroPago');
    }
}
