<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaprodImagen extends Model
{
    use HasFactory;

    protected $table = 'saprod_imagenes';

    protected $fillable = [
        'codprod',
        'comercial',
        'nombre_original',
        'nombre_archivo',
        'ruta',
        'tipo',
        'orden',
        'activo'
    ];

    // Relación con el producto
    public function producto()
    {
        return $this->belongsTo(Saprod::class, 'codprod', 'codprod')
            ->where('comercial', $this->comercial);
    }

    // Scope para obtener imágenes principales
    public function scopePrincipal($query)
    {
        return $query->where('tipo', 'principal');
    }

    // Scope para obtener imágenes secundarias
    public function scopeSecundarias($query)
    {
        return $query->where('tipo', 'secundaria');
    }

    // Scope para obtener thumbnails
    public function scopeThumbnail($query)
    {
        return $query->where('tipo', 'thumbnail');
    }

    // Scope para obtener iconos
    public function scopeIcono($query)
    {
        return $query->where('tipo', 'icono');
    }

    // Scope para imágenes activas
    public function scopeActivas($query)
    {
        return $query->where('activo', 1);
    }
}
