<?php
// app/Models/TransferenciaItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferenciaItem extends Model
{
    protected $table = 'transferencia_item';

    protected $fillable = [
        'fk_proceso', 'codprod', 'descripcion', 'cantidad',
        'fk_sucursal_origen', 'fk_sucursal_destino',
        'precio_unitario', 'costo_unitario', 'observaciones'
    ];

    // Relaciones
    public function proceso()
    {
        return $this->belongsTo(TransferenciaProceso::class, 'fk_proceso');
    }

    public function producto()
    {
        return $this->belongsTo(Saprod::class, 'codprod', 'codprod');
    }

    public function sucursalOrigen()
    {
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal_origen');
    }

    public function sucursalDestino()
    {
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal_destino');
    }
}
