<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cwcuentas extends Model
{
    use HasFactory;
    protected $table    = 'cwcuentas';
    protected $fillable = ['id', 'numero', 'descrip', 'nivel', 'numpadre', 'detalle', 'banco', 'noeliminar'];

    public function padre()
    {
        return $this->belongsTo(Cwcuentas::class, 'numpadre', 'numero');
    }

    public function hijos  (){
        return $this->hasMany(Cwcuentas::class, 'numpadre', 'numero');
    }


}
