<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saacxcw extends Model
{
    use HasFactory;
    protected $table    = 'saacxcw';
    protected $fillable = [
        'CodClie', 'NroUnico', 'NroRegi', 'FechaI', 'FechaE', 'FechaV', 'FechaT', 'CodEsta', 'CodUsua', 'CodOper', 'CodVend',
        'NumeroD', 'NumeroN', 'TipoCxc', 'Document', 'Notas1', 'Notas2', 'Notas3', 'Monto', 'MontoNeto', 'MtoTax', 'Saldo', 'SaldoOrg', 'BaseImpo',
        'TExento', 'CancelI', 'CancelA', 'CancelE', 'CancelC', 'CancelT', 'EsUnPago', 'dolares', 'pesos', 'dolar_tranf', 'euros', 'montodolares',
        'tasadolar', 'xdev', 'tasapeso', 'fk_transaccion', 'tasaeuro', 'peso_tranf'
    ];

}
