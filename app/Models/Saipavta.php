<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saipavta extends Model
{
    use HasFactory;
    protected $table    = 'saipavta';
    protected $fillable = ['TipoFac', 'NumeroD', 'CodPago', 'dolares','monto','pesos', 'RetencT', 'Impuesto', 'FechaE', 'Descrip', 'fk_sucursal'];

    public function sucursal  (){
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }

    public function satarj  (){
        $comercial = session('comercialid') ;
        return $this->belongsTo(Satarj::class, 'CodPago', 'codtarj')->where('comercial',$comercial);
    }

    public function factura  (){
        return $this->belongsTo(Safact::class, 'NumeroD', 'NumeroD')
            ->whereColumn('fk_sucursal','=','fk_sucursal')
            ->whereColumn('TipoFac','=','TipoFac');
    }
}
