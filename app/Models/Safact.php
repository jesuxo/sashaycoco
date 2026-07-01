<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Safact extends Model
{
    use HasFactory;
    protected $table    = 'safact';

    protected $fillable = ['nrounico','fk_sucursal', 'TipoFac', 'NumeroD', 'numeror', 'EsCorrel','FechaT','FechaI','FechaE','FechaV','fechac', 'totalmontodivisa',
        'igtf_monto','igtf_cancele','igtf_cancelt','igtf_dolares','igtf_dolar_transf','igtf_pesos','igtf_bcv','CodUsua','CodEsta',
        'Signo','Factor','CodOper','CodClie','CodVend','CodUbic','Descrip','Direc1','Direc2','Telef','ID3','Monto','TotalPrd','TGravable','MtoTax',
        'CancelE','CancelA','MtoTotal','Contado','Credito','CancelC','CancelT','DetalChq','Notas1','Notas2','Notas3','Notas8','Notas5',
        'TExento','credendolar','dolares','pesos','euros','peso_tranf','dolar_transf','dolar_pagado','tarjetasumado','efectivosumado',
        'tasa_dolar','tasa_peso','porcdesconado', 'vuelto_dolares', 'vuelto_cancele', 'vuelto_pesos', 'cancelaUSD','creddolar_pagado'];

    public function vendedor  (){
        return $this->belongsTo(Savend::class, 'CodVend', 'CodVend');
    }

    public function cliente   (){
        return $this->belongsTo(Saclie::class, 'CodClie', 'CodClie');
    }

    public function seriales     (){
        return $this->hasMany(Saseprfac::class, 'numerod', 'NumeroD')
            ->whereColumn('TipoFac','tipofac');
    }

    public function sucursal  (){
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }

    public function items     (){
        return $this->hasMany(Saitemfac::class, 'NumeroD', 'NumeroD')
            ->whereColumn('fk_sucursal', 'fk_sucursal')
            ->whereColumn('TipoFac', 'TipoFac');
    }

    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->format('d/m/Y');
    }

}
