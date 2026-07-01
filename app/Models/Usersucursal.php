<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usersucursal extends Model
{
    use HasFactory;

    protected $table = 'usersucursal';
    protected $fillable = [ 'fk_sucursal', 'fk_user'];

    public function sucursal  (){
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }
    public function user(){
        return $this->belongsTo(User::class, 'fk_user', 'id');
    }
}
