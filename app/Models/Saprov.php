<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saprov extends Model
{
    use HasFactory;
    protected $table    = 'saprov';
    protected $fillable = [ 'CodProv', 'Descrip', 'TipoPrv', 'TipoID3', 'TipoID', 'ID3', 'DescOrder', 'Clase', 'Activo', 'Represent',
        'Direc1', 'Direc2', 'ZipCode', 'Telef', 'Movil', 'Fax', 'Email', 'FechaE', 'Observa', 'blockDesc' 	];

}
