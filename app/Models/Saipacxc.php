<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saipacxc extends Model
{
    use HasFactory;
    protected $table    = 'saipacxc';
    protected $fillable = [
        'NroPpal', 'NroUnico', 'CodPago', 'codclie', 'Descrip',
        'pesos', 'Monto', 'dolares', 'fk_sucursal'
    ];

    public function sucursal  (){
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }

    public function cxc  (){
        return $this->belongsTo(Saacxc::class, 'NroPpal', 'NroUnico');
    }

    public function satarj  (){
        return $this->belongsTo(Satarj::class, 'codpago', 'codtarj');
    }

    public function cliente  (){
        return $this->belongsTo(Saclie::class, 'codclie', 'codclie');
    }

    public function pago()
    {
        return $this->belongsTo(Saacxc::class, 'NroPpal', 'id');
    }

    public function instrumento()
    {
        return $this->belongsTo(Satarj::class, 'CodPago', 'codtarj');
    }

}
