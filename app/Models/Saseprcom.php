<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saseprcom extends Model
{
    use HasFactory;
    protected $table    = 'saseprcom';
    protected $fillable = ['codsucu', 'tipocom',  'numerod', 'codprov', 'nroLinea', 'nrolineac', 'nroserial', 'coditem', 'codubic'];

    public function itemCompra()
    {
        return $this->belongsTo(Saitemcom::class,
            ['tipocom', 'numerod', 'nrolinea', 'coditem'], ['tipocom', 'numerod', 'nrolinea', 'coditem']);
    }

}
