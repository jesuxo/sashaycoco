<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saprod extends Model
{
    use HasFactory;

    protected $table    = 'saprod';
    protected $fillable = ['codprod','descrip','descrip2','descrip3',
                          'marca','refere','codinst','observaciones','activo',
                          'esexento','exdecimal','cantxempaq','volumen','peso','unidad',
                          'preciod',  'preciod2','costod','costod2','costod3'];

    public function instancia(){
        $comercial = session('comercialid') ;
        return $this->belongsTo(Sainsta::class, 'codinst', 'codinst')
            ->where('comercial', $comercial);
    }

    public function instanciatres(){

        return $this->belongsTo(Sainsta::class, 'codinst', 'codinst')
            ->where('comercial', '=', 3);
    }

    public function existencias(){
        return $this->hasMany(Saexis::class, 'codprod', 'codprod');
    }

    public function sucursales  (){
        return $this->hasMany(Saprodsucursal::class, 'codprod', 'codprod');
    }

    public function comercial  (){
        return $this->belongsTo(Sacomercial::class, 'comercial', 'id');
    }

    public function imagenes()
    {
        return $this->hasMany(SaprodImagen::class, 'codprod', 'codprod')
            ->where('comercial', $this->comercial)
            ->orderBy('orden', 'asc');
    }

    public function toApiArray()
    {
        $data = $this->getAttributes();

        $imagenes = $this->imagenes()->get();

        $principal = $this->imagenPrincipal;
        if ($principal) {
            $principal->url = asset($principal->ruta);
        }

        $data['imagen_url'] = !empty($principal->url)
            ? $principal->url
            : null;

        return $data;
    }

    public function imagenPrincipal()
    {
        return $this->hasOne(SaprodImagen::class, 'codprod', 'codprod')
            ->where('comercial', $this->comercial)
            ->where('tipo', 'principal')
            ->where('activo', 1);
    }

    public function imagenesSecundarias()
    {
        return $this->hasMany(SaprodImagen::class, 'codprod', 'codprod')
            ->where('comercial', $this->comercial)
            ->where('tipo', 'secundaria')
            ->where('activo', 1)
            ->orderBy('orden', 'asc');
    }

    public function thumbnail()
    {
        return $this->hasOne(SaprodImagen::class, 'codprod', 'codprod')
            ->where('comercial', $this->comercial)
            ->where('tipo', 'thumbnail')
            ->where('activo', 1);
    }

    public function icono()
    {
        return $this->hasOne(SaprodImagen::class, 'codprod', 'codprod')
            ->where('comercial', $this->comercial)
            ->where('tipo', 'icono')
            ->where('activo', 1);
    }
}
