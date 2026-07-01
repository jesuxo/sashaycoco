<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sasepropi extends Model
{
    use HasFactory;
    protected $table    = 'sasepropi';
    protected $fillable = ['tipoopi', 'NumeroD', 'NroSerial','NroLinea','NroLineaC', 'CodItem', 'CodUbic','fk_sucursal'];
}
