<?php
// app/Models/DespachoProveedor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DespachoProveedor extends Model
{
    protected $table = 'despachos_proveedores';

    protected $fillable = [
        'pago_id',
        'fecha_despacho',
        'numero_guia',
        'notas'
    ];

    protected $casts = [
        'fecha_despacho' => 'date'
    ];

    public function pago()
    {
        return $this->belongsTo(PagoProveedor::class, 'pago_id');
    }

    public function detalles()
    {
        return $this->hasMany(DespachoProveedorDetalle::class, 'despacho_id');
    }
}
