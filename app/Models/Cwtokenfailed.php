<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cwtokenfailed extends Model
{
    use HasFactory;
    protected $table    = 'cwtokenfailed';
    protected $fillable = ['id', 'tokenfailed', 'codusua', 'status'];



}
