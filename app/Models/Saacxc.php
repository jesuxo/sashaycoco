<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saacxc extends Model
{
    use HasFactory;
    protected $table    = 'saacxc';
    protected $fillable = [
        'CodClie', 'NroUnico', 'NroRegi', 'FechaI', 'FechaE', 'FechaV', 'FechaT', 'CodEsta', 'CodUsua', 'CodOper', 'CodVend',
        'NumeroD', 'NumeroN', 'TipoCxc', 'Document', 'Notas1', 'Notas2', 'Notas3', 'Monto', 'MontoNeto', 'MtoTax', 'Saldo', 'SaldoOrg', 'BaseImpo',
        'TExento', 'CancelI', 'CancelA', 'CancelE', 'CancelC', 'CancelT', 'EsUnPago', 'dolares', 'pesos', 'dolar_tranf', 'euros', 'montodolares',
        'tasadolar', 'xdev', 'tasapeso', 'fk_transaccion', 'tasaeuro', 'peso_tranf', 'cancelaUSD', 'descargar'
    ];

    public function sucursal  (){
        $comercial = session('comercialid') ;
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id')
            ->where('sasucursal.fk_comercial', $comercial);
    }

    public function sucursalcli  (){
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }

    public function cliente  (){
        return $this->belongsTo(Saclie::class, 'CodClie', 'codclie');
    }

    public function getFormattedDateAttribute(){
        return Carbon::parse($this->attributes['created_at'])->format('d/m/Y');
    }

}
