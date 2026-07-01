<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cwcuentasucursal extends Model
{
    use HasFactory;
    protected $table = 'cwcuentasucursal';

    public function sucursal(){
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }

    public function cuenta(){
        return $this->belongsTo(Cwcuentas::class, 'fk_cuenta', 'id');
    }
}
