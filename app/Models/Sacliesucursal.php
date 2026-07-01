<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sacliesucursal extends Model
{
    use HasFactory;
    protected $table = 'sacliesucursal';

    public function sucursal(){
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }

    public function cliente(){
        return $this->belongsTo(Saclie::class, 'codclie', 'codclie');
    }
}
