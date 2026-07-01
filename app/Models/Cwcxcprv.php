<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cwcxcprv extends Model
{
    use HasFactory;

    protected $table = 'cwcxcprv';

    protected $fillable = [
        'concepto',
        'monto',
        'signo',
        'tasabs',
        'monedapago',
        'conversion',
        'archived',
        'numerod',
        'tipo',
        'fecha',
        'codprov',
        'tipocomp',
        'fk_estructura',
        'abonado',
        'compras',
        'gastos',
        'anticipos',
        'contabilidad',
        'NROUNICOCXP',
        'id',
        'fechav',
        'codoper',
        'fk_sucursal'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'tasabs' => 'decimal:2',
        'monedapago' => 'decimal:2',
        'conversion' => 'decimal:2',
        'abonado' => 'decimal:2',
        'fecha' => 'date',
        'fechav' => 'date',
    ];

    // Relación con proveedor (asumiendo que tienes tabla saprov)
    public function proveedor()
    {
        return $this->belongsTo(Saprov::class, 'codprov', 'CodProv');
    }
}
