<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saprodsucursal extends Model
{
    use HasFactory;
    protected $table = 'saprodsucursal';

    public function sucursal(){
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }

    public function producto(){
        $comercial = session('comercialid') ;
        return $this->belongsTo(Saprod::class, 'codprod', 'codprod')->where('comercial',$comercial);
    }
}
