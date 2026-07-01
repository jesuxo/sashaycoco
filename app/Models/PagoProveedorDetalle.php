<?php
// app/Models/PagoProveedorDetalle.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoProveedorDetalle extends Model
{
    protected $table = 'pagos_proveedores_detalles';

    protected $fillable = [
        'pago_id',
        'producto_id',
        'producto_codprod',
        'producto_descrip',
        'cantidad',
        'cantidad_recibida',
        'cantidad_facturada',
        'precio_unitario',
        'subtotal'
    ];

    protected $casts = [
        'cantidad'           => 'integer',
        'cantidad_recibida'  => 'integer',
        'cantidad_facturada' => 'integer',
        'precio_unitario'    => 'decimal:2',
        'subtotal'           => 'decimal:2'
    ];

    public function pago()
    {
        return $this->belongsTo(PagoProveedor::class, 'pago_id');
    }

    public function producto()
    {
        return $this->belongsTo(Saprod::class, 'producto_codprod','codprod')
            ->where('comercial', '=',3);
    }

    public function getPendienteAttribute()
    {
        return $this->cantidad - $this->cantidad_recibida;
    }

    public function getPendienteFacturarAttribute()
    {
        return $this->cantidad - $this->cantidad_facturada;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($detalle) {
            $detalle->subtotal = $detalle->cantidad * $detalle->precio_unitario;
        });

        static::saved(function ($detalle) {
            $detalle->pago->actualizarEstado();
        });
    }
}
