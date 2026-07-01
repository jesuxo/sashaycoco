<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sainsta extends Model
{
    use HasFactory;
    protected $table    = 'sainsta';
    protected $fillable = ['codinst', 'descrip', 'insPadre', 'nivel', 'tipoIns', 'DEsComp', 'codalte', 'desseri', 'comercial'];

    // Relación con padre (filtrada por comercial)
    public function padre()
    {
        return $this->belongsTo(Sainsta::class, 'insPadre', 'codinst')
            ->where('comercial', session('comercialid'));
    }

    // Relación con hijos (filtrada por comercial)
    public function hijos()
    {
        return $this->hasMany(Sainsta::class, 'insPadre', 'id')
            ->where('comercial', session('comercialid'));
    }

    // Relación con productos (filtrada por comercial)
    public function productos()
    {
        return $this->hasMany(Saprod::class, 'codinst', 'codinst')
            ->where('comercial', session('comercialid'));
    }

    // Relación con productos con existencias (filtrada por comercial)
    public function productosexistencias()
    {
        return $this->hasMany(Saprod::class, 'codinst', 'codinst')
            ->where('saprod.existen', '<>', 0)
            ->where('saprod.comercial', session('comercialid'));
    }

    // Relación con servicios (filtrada por comercial)
    public function servicios()
    {
        return $this->hasMany(Saserv::class, 'codinst', 'codinst')
            ->where('comercial', session('comercialid'));
    }

    // Relación con comercial
    public function comercial()
    {
        return $this->belongsTo(Sacomercial::class, 'comercial', 'id');
    }

    // Scope para filtrar por comercial
    public function scopePorComercial($query, $comercialId = null)
    {
        $comercialId = $comercialId ?: session('comercialid');
        return $query->where('comercial', $comercialId);
    }

    // Scope para instancias de nivel 1 (departamentos principales)
    public function scopeNivel1($query)
    {
        $comercial = session('comercialid');
        return $query->where('nivel', 1)->where('comercial', $comercial);
    }

    // Scope para obtener solo las que NO tienen seriales (para dropdown de productos sin serial)
    public function scopeSinSeriales($query)
    {
        $comercial = session('comercialid');
        return $query->where('comercial', $comercial)->where('desseri', 0);
    }

    // Scope para obtener solo las que tienen seriales
    public function scopeConSeriales($query)
    {
        $comercial = session('comercialid');
        return $query->where('comercial', $comercial)->where('desseri', 1);
    }

    // Accessor para datos formateados (con filtro comercial)
    public function getFormattedDataAttribute()
    {
        return [
            'id'          => $this->id,
            'subcategory' => $this->descrip,
            'category'    => $this->padre ? $this->padre->descrip : '',
            'desseri'     => $this->desseri ?? 0,
            'nivel'       => $this->nivel,
            'hijos'       => $this->hijos()->count() > 0,
            'productos'   => $this->productos()->count() > 0,
            'servicios'   => $this->servicios()->count() > 0
        ];
    }

    // Boot del modelo para asegurar siempre el filtro por comercial
    protected static function booted()
    {
        static::addGlobalScope('comercial', function ($builder) {
            if (session()->has('comercialid')) {
                $builder->where('comercial', session('comercialid'));
            }
        });
    }
}
