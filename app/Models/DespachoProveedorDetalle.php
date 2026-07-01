<?php
// app/Models/DespachoProveedorDetalle.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DespachoProveedorDetalle extends Model
{
    protected $table = 'despachos_proveedores_detalles';

    public $timestamps = false;

    protected $fillable = [
        'despacho_id',
        'pago_detalle_id',
        'cantidad_recibida'
    ];

    public function despacho()
    {
        return $this->belongsTo(DespachoProveedor::class, 'despacho_id');
    }

    public function pagoDetalle()
    {
        return $this->belongsTo(PagoProveedorDetalle::class, 'pago_detalle_id');
    }
}
