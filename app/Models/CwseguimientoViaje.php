<?php
// app/Models/CwseguimientoViaje.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CwseguimientoViaje extends Model
{
   // use SoftDeletes;

    protected $table = 'cwseguimiento_viaje';

    protected $fillable = [
        'viaje_id',
        'etapa_id',
        'latitud',
        'longitud',
        'ubicacion_texto',
        'velocidad',
        'kilometraje_total',
        'nivel_combustible',
        'estado_motor',
        'tipo_punto',
        'etapa_asociada_id',
        'fecha_hora'
    ];

    protected $casts = [
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
        'velocidad' => 'decimal:2',
        'kilometraje_total' => 'decimal:2',
        'nivel_combustible' => 'decimal:2',
        'fecha_hora' => 'datetime',
    ];

    public function viaje(): BelongsTo
    {
        return $this->belongsTo(Cwviaje::class, 'viaje_id');
    }

    public function etapa(): BelongsTo
    {
        return $this->belongsTo(CwetapaViaje::class, 'etapa_id');
    }

    public function etapaAsociada(): BelongsTo
    {
        return $this->belongsTo(CwetapaViaje::class, 'etapa_asociada_id');
    }

    public function getColorMarkerAttribute(): string
    {
        return match($this->tipo_punto) {
            'inicio_etapa' => '#10b981', // Verde
            'fin_etapa' => '#ef4444',     // Rojo
            'manual' => '#3b82f6',         // Azul
            'automatico' => '#f59e0b',     // Naranja
            default => '#6b7280'            // Gris
        };
    }

    public function getIconMarkerAttribute(): string
    {
        return match($this->tipo_punto) {
            'inicio_etapa' => '▶',
            'fin_etapa' => '◼',
            'manual' => '📍',
            'automatico' => '⚡',
            default => '•'
        };
    }

    public function getTipoTextoAttribute(): string
    {
        return match($this->tipo_punto) {
            'inicio_etapa' => 'Inicio de Etapa',
            'fin_etapa' => 'Fin de Etapa',
            'manual' => 'Reporte Manual',
            'automatico' => 'Reporte Automático',
            default => 'Desconocido'
        };
    }
}
