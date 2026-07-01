<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saesta extends Model
{
    use HasFactory;
    protected $table    = 'saesta';
    protected $fillable = ['codesta', 'descrip', 'cobranza','facturacion'];
}
