<?php
// app/Models/Satarj.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Satarj extends Model
{
    protected $table = 'satarj';
    protected $fillable = [
        'codtarj', 'descrip', 'clase', 'bs', 'dolares', 'pesos',
        'fk_sucursal', 'comercial', 'activo', 'multiple'
    ];

    public function sucursal(){
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }

    protected $casts = [
        'bs'      => 'boolean',
        'dolares' => 'boolean',
        'pesos'   => 'boolean',
        'activo'  => 'boolean'
    ];

    public function scopeActive($query)
    {
        return $query->where('activo', 1);
    }

    public function scopeByComercial($query, $comercialId)
    {
        return $query->where('comercial', $comercialId);
    }

    public function scopeBs($query)
    {
        return $query->where('bs', 1);
    }

    public function scopeDolares($query)
    {
        return $query->where('dolares', 1);
    }

    public function scopePesos($query)
    {
        return $query->where('pesos', 1);
    }
}
