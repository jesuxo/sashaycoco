<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saprovsucursal extends Model
{
    use HasFactory;
    protected $table = 'saprovsucursal';

    public function sucursal(){
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }

    public function proveedor(){
        return $this->belongsTo(Saprov::class, 'codprov', 'codprov');
    }
}
