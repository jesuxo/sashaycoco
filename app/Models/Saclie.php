<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saclie extends Model
{
    use HasFactory;
    protected $table    = 'saclie';
    protected $fillable = ['codclie', 'direc1', 'direc2', 'id3', 'descrip', 'tipocli', 'represent', 'tipopvp', 'codvend', 'Estado','codzona',
        'clase', 'telef', 'movil', 'email', 'fax', 'Observaciones', 'DescripExt', 'TipoID3', 'activo', 'escredito', 'LimiteCred', 'porcIncrementa'];


}
