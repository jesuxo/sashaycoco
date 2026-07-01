<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sapagcxc extends Model
{
    use HasFactory;

    protected $table = 'sapagcxc';

    protected $fillable = [
        'CodSucu',
        'CodClie',
        'NroPpal',
        'NroUnico',
        'TipoCxc',
        'MontoDocA',
        'Monto',
        'NumeroD',
        'Descrip',
        'FechaE',
        'CodOper',
        'montodolar',
        'fk_sucursal',
    ];

    protected $casts = [
        'FechaE' => 'datetime',
        'MontoDocA' => 'decimal:4',
        'Monto' => 'decimal:4',
        'montodolar' => 'decimal:4',
    ];

    // Relación con saacxc (pago principal)
    public function pago()
    {
        return $this->belongsTo(Saacxc::class, 'NroPpal', 'NroUnico');
    }

    // Relación con safact (factura afectada)
    public function factura()
    {
        return $this->belongsTo(Safact::class, 'NumeroD', 'NumeroD')
            ->where('fk_sucursal', $this->fk_sucursal);
    }

    // Relación con cliente
    public function cliente()
    {
        return $this->belongsTo(Saclie::class, 'CodClie', 'codclie');
    }
}
