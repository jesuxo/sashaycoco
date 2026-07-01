<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sacomercial extends Model
{
    use HasFactory;

    protected $table    = 'sacomercial';
    protected $fillable = ['descrip', 'short'];


    public function productos  (){
        return $this->hasMany(Saprod::class, 'comercial', 'id');
    }

    public function instancias  (){
        return $this->hasMany(Sainsta::class, 'comercial', 'id');
    }

    public function sucursales(): HasMany
    {
        return $this->hasMany(Sasucursal::class, 'fk_comercial', 'id');
    }
}
