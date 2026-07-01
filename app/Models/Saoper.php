<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saoper extends Model
{
    use HasFactory;
    protected $table    = 'saoper';
    protected $fillable = ['CodOper', 'descrip'];

    public function comercial  (){
        return $this->belongsTo(Sacomercial::class, 'comercial', 'id');
    }

    public function factura()
    {
        return $this->belongsTo(Safact::class, 'CodOper', 'CodOper');
    }
}
