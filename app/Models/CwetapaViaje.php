<?php
// app/Models/CwetapaViaje.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CwetapaViaje extends Model
{
   // use SoftDeletes;

    protected $table = 'cwetapas_viaje';

    protected $fillable = [
        'viaje_id',
        'nombre',
        'descripcion',
        'orden',
        'estado',
        'ubicacion',
        'latitud',
        'longitud',
        'kilometraje_estimado',
        'kilometraje_inicio',
        'kilometraje_real',
        'fecha_estimada_inicio',
        'fecha_estimada_fin',
        'fecha_real_inicio',
        'fecha_real_fin',
        'latitud_inicio',
        'longitud_inicio',
        'ubicacion_texto_inicio',
        'latitud_fin',
        'longitud_fin',
        'ubicacion_texto_fin',
        'foto_evidencia',
        'notas'
    ];

    protected $casts = [
        'fecha_estimada_inicio' => 'datetime',
        'fecha_estimada_fin'    => 'datetime',
        'fecha_real_inicio'     => 'datetime',
        'fecha_real_fin'        => 'datetime',
        'kilometraje_inicio'    => 'decimal:2',
        'kilometraje_estimado'  => 'decimal:2',
        'kilometraje_real'      => 'decimal:2',
        'latitud'               => 'decimal:8',
        'longitud'              => 'decimal:8',
        'latitud_inicio'        => 'decimal:8',
        'longitud_inicio'       => 'decimal:8',
        'latitud_fin'           => 'decimal:8',
        'longitud_fin'          => 'decimal:8',
    ];

    /**
     * Relaciones
     */
    public function viaje(): BelongsTo
    {
        return $this->belongsTo(Cwviaje::class, 'viaje_id');
    }

    public function seguimientos(): HasMany
    {
        return $this->hasMany(CwseguimientoViaje::class, 'etapa_id');
    }

    /**
     * Accesores
     */
    public function getEstadoColorAttribute(): string
    {
        return match($this->estado) {
            'completado' => 'success',
            'en_curso' => 'warning',
            default => 'secondary'
        };
    }

    public function getEstadoBadgeAttribute(): string
    {
        return match($this->estado) {
            'completado' => '<span class="badge bg-success">Completado</span>',
            'en_curso' => '<span class="badge bg-warning text-dark">En Curso</span>',
            default => '<span class="badge bg-secondary">Pendiente</span>'
        };
    }

    public function getDuracionEstimadaAttribute(): ?string
    {
        if (!$this->fecha_estimada_inicio || !$this->fecha_estimada_fin) {
            return null;
        }

        $diferencia = $this->fecha_estimada_inicio->diff($this->fecha_estimada_fin);
        return $diferencia->format('%d días %h horas');
    }

    public function getDuracionRealAttribute(): ?string
    {
        if (!$this->fecha_real_inicio || !$this->fecha_real_fin) {
            return null;
        }

        $diferencia = $this->fecha_real_inicio->diff($this->fecha_real_fin);
        return $diferencia->format('%d días %h horas');
    }

    public function getRetrasoAttribute(): ?string
    {
        if (!$this->fecha_estimada_fin || !$this->fecha_real_fin) {
            return null;
        }

        if ($this->fecha_real_fin > $this->fecha_estimada_fin) {
            $retraso = $this->fecha_estimada_fin->diff($this->fecha_real_fin);
            return "Retraso: " . $retraso->format('%d días %h horas');
        }

        return "A tiempo";
    }

    public function getKilometrajeAttribute(): string
    {
        if ($this->kilometraje_real) {
            return number_format($this->kilometraje_real, 2) . ' km';
        }

        if ($this->kilometraje_estimado) {
            return 'Est. ' . number_format($this->kilometraje_estimado, 2) . ' km';
        }

        return 'N/A';
    }
}
