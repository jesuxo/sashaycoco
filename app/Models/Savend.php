<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Savend extends Model
{
    use HasFactory;

    protected $table    = 'savend';
    protected $fillable = ['codvend', 'descrip', 'email', 'telef'];

    public function user()
    {
        return $this->belongsTo(User::class, 'fk_user', 'id');
    }

    public function factura()
    {
        return $this->belongsTo(Safact::class, 'codvend', 'codvend');
    }

}
