<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saitemopi extends Model
{
    use HasFactory;
    protected $table    = 'saitemopi';
    protected $fillable = [ 'tipoopi', 'NumeroD', 'NroLinea', 'NroLineaC', 'CodItem', 'CodUbic', 'CodUbic2', 'Descrip1',
        'Refere', 'Signo',  'Cantidad',  'ExistAnt',  'ExistAnt2', 'CantidadC', 'Costo', 'costact', 'TotalItem', 'Precio',  'FechaE',
        'FechaL', 'FechaV', 'EsServ', 'EsUnid', 'EsExento',  'DEsSeri',   'preciod', 'fk_sucursal' ];

    public function operacion  (){
        return $this->belongsTo(Safact::class, 'NumeroD', 'NumeroD')
            ->whereTipofac('Safact.tipofac')
            ->whereFkSucursal('Safact.fk_sucursal')
            ->whereNumerod('Safact.NumeroD');
    }

    public function producto  (){
        $comercial = session('comercialid') ;
        return $this->belongsTo(Saprod::class, 'coditem', 'codprod')->where('comercial',$comercial);
    }

    public function sucursal  (){
        $comercial = session('comercialid') ;
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }

}
