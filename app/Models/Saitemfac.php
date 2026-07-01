<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saitemfac extends Model
{
    use HasFactory;
    protected $table    = 'saitemfac';
    protected $fillable = ['codclie', 'TipoFac', 'esserv', 'NumeroD', 'NroLinea', 'NroLineaC', 'Signo', 'FechaE', 'CodItem', 'Refere', 'DEsComp', 'CodUbic', 'OTipo',
        'ONumero', 'Descrip1', 'Descrip2', 'Descrip3', 'Cantidad', 'CantMayor', 'Costo', 'TotalItem', 'Precio', 'PriceO', 'MtoTax',
        'CodVend', 'NroUnicoL', 'ExistAntU', 'ExistAnt', 'FechaL', 'FechaV', 'esfiscal', 'preciod', 'costod',  'EsUnid', 'basecostod',
        'costodoriginal','fk_sucursal' ];

    public function factura  (){
        return $this->belongsTo(Safact::class, 'NumeroD', 'NumeroD')
            ->whereRaw('saitemfac.tipofac= safact.tipofac and safact.fk_sucursal = saitemfac.fk_sucursal');
    }
    public function producto  (){
        $comercial = session('comercialid') ;
        return $this->belongsTo(Saprod::class, 'CodItem', 'codprod')
            ->where('saprod.comercial', $comercial);
    }

    public function sucursal  (){
        $comercial = session('comercialid') ;
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id')
            ->where('fk_comercial',$comercial);;
    }

    public function serialesVenta()
    {
        return $this->hasMany(Saseprfac::class, 'NumeroD', 'NumeroD')
            ->whereColumn('TipoFac', 'TipoFac')
            ->whereColumn('NroLinea', 'NroLinea')
            ->whereColumn('CodItem', 'CodItem');
    }

}
