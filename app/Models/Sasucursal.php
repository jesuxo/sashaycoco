<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sasucursal extends Model
{
    use HasFactory;

    protected $table    = 'sasucursal';
    protected $fillable = [ 'descrip', 'direccion'];

    public function saprodsucursales (){
        return $this->hasMany(Saprodsucursal::class, 'fk_sucursal', 'id');
    }

    public function comercial (){
        return $this->belongsTo(Sacomercial::class, 'fk_comercial', 'id');
    }

    public function sacliesucursales (){
        return $this->hasMany(Sacliesucursal::class, 'fk_sucursal', 'id');
    }

    public function saprovsucursales (){
        return $this->hasMany(Saprovsucursal::class, 'fk_sucursal', 'id');
    }

    public function saexis()
    {
        return $this->hasMany(Saexis::class, 'fk_sucursal', 'id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'usersucursal', 'fk_sucursal', 'fk_user')
            ->withTimestamps();
    }
}
