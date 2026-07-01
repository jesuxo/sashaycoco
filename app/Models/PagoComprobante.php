<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoComprobante extends Model
{
    protected $table = 'pagos_comprobantes';

    protected $fillable = [
        'pago_id',
        'tipo_comprobante',
        'numero_comprobante',
        'monto',
        'fecha_comprobante',
        'archivo_path',
        'notas'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_comprobante' => 'date'
    ];

    public function pago()
    {
        return $this->belongsTo(PagoProveedor::class, 'pago_id');
    }
}
