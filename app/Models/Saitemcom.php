<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saitemcom extends Model
{
    use HasFactory;
    protected $table    = 'saitemcom';
    protected $fillable = ['tipocom', 'esserv', 'numerod', 'codprov', 'nrolinea', 'nrolineac', 'signo', 'fechae', 'coditem',
        'refere', 'codubic', 'otipo', 'onumero', 'descrip1', 'descrip2', 'descrip3', 'cantidad', 'costo',
        'totalitem', 'precio', 'mtotax', 'costorg', 'nrounicol', 'existantu', 'existant', 'precio1', 'precio2',
        'fechal', 'fechav', 'nrolote', 'esunid', 'esexento', 'usaserv', 'preciod', 'preciod2', 'costod', 'costod2', 'costod3',
        'result', 'precioanterior', 'costoanterior', 'costoorig', 'porc1', 'porc2', 'porc3', 'fk_sucursal' ];


    public function compra  (){
        return $this->belongsTo(Sacomp::class, 'numerod', 'numerod')
            ->whereColumn('tipocom', 'sacomp.tipocom')
            ->whereColumn('fk_sucursal', 'sacomp.fk_sucursal');
    }

    public function producto  (){
        $comercial = session('comercialid') ;
        return $this->belongsTo(Saprod::class, 'coditem', 'codprod')->where('comercial', $comercial);
    }

    public function sucursal  (){
        $comercial = session('comercialid') ;
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }

    public function serialesCompra()
    {
        return $this->hasMany(Saseprcom::class, 'numerod', 'numerod')
            ->whereColumn('tipocom', 'tipocom')
            ->whereColumn('nrolinea', 'nrolinea')
            ->whereColumn('coditem', 'coditem');
    }

}
