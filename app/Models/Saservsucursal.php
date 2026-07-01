<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saservsucursal extends Model
{
    use HasFactory;
    protected $table = 'saservsucursal';

    public function sucursal(){
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }

    public function servicio(){
        return $this->belongsTo(Saserv::class, 'codserv', 'codserv');
    }
}
