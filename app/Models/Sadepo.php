<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sadepo extends Model
{
    use HasFactory;

    protected $table    = 'sadepo';
    protected $fillable = ['codubic', 'descrip', 'venta', 'exhibicion', 'servicio'];


}
