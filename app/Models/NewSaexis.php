<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewSaexis extends Model
{
    use HasFactory;
    protected $table    = 'newsaexis';
    protected $fillable = ['codubic', 'codprod', 'existen', 'fk_sucursal'];

    public function sucursal  (){
        $comercial = session('comercialid') ;
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id')->where('fk_comercial',$comercial);
    }

    public function deposito  (){
        $comercial = session('comercialid') ;
        return $this->belongsTo(Sadepo::class, 'codubic', 'codubic')->where('comercial',$comercial);
    }

    // ============================================================
    // NUEVA RELACIÓN PARA LA TIENDA
    // ============================================================

    /**
     * Relación inversa con el producto
     */
    public function producto()
    {
        return $this->belongsTo(Saprod::class, 'codprod', 'codprod');
    }
}
