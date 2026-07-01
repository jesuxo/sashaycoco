<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ia extends Model
{
    use HasFactory;

    protected $table = 'ia';

    protected $fillable = [
        'title',
        'text',
        'category',
        'tags',
        'active',
        'order'
    ];

    protected $casts = [
        'tags' => 'array',
        'active' => 'boolean'
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at', 'desc');
    }

    protected $appends  = [ 'fechaformat'];

    public function getFechaformatAttribute(){
        $date = $this->created_at;
        if(isset($date)){
            list($date,$time) = explode(' ',$date);
            list($y,$m,$d) = explode('-',$date);
            return "$d/$m/$y";
        }
    }
}


