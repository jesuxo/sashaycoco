<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cwbancos extends Model
{
    use HasFactory;
    protected $table    = 'cwbancos';
    protected $fillable = ['id', 'descrip', 'fksucursal', 'fk_cuenta', 'abrev', 'sbs', 'sdolares', 'seuros','spesos'];

    public function cuenta(){
        return $this->belongsTo(Cwcuentas::class, 'id', 'fk_cuenta');
    }

}
