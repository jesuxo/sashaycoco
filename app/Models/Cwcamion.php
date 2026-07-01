<?php
// app/Models/Camion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cwcamion extends Model
{
    //use SoftDeletes; // Usar el trait

    protected $fillable = [
        'id',
        'marca',
        'modelo',
        'placa',
        'capacidad_motos',
        'tipo',
        'costo_alquiler',
        'notas',
        'activo'
    ];

    protected $table = 'cwcamiones';

    protected $casts = [
        'activo' => 'boolean',
        'capacidad_motos' => 'integer',
        'costo_alquiler' => 'decimal:2',
    ];

    protected $dates = ['deleted_at']; // Indicar que deleted_at es una fecha

    // Relaciones
    public function viajes(): HasMany
    {
        return $this->hasMany(Cwviaje::class, 'camion_id'); // Especifica que la llave foránea es 'camion_id'
    }

    public function gastos(): MorphMany
    {
        return $this->morphMany(Cwgasto::class, 'gastable');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePropios($query)
    {
        return $query->where('tipo', 'propio');
    }

    public function scopeAlquilados($query)
    {
        return $query->where('tipo', 'alquilado');
    }

    public function scopeDisponibles($query)
    {
        return $query->where('activo', true)
            ->whereDoesntHave('viajes', function ($q) {
                $q->whereIn('estado', ['en_curso', 'planeado']);
            });
    }

    // Accesores
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->marca} {$this->modelo} - {$this->placa}";
    }

    public function getEstadoColorAttribute(): string
    {
        return $this->activo ? 'success' : 'danger';
    }

    public function getTipoColorAttribute(): string
    {
        return $this->tipo === 'propio' ? 'success' : 'warning';
    }

    public function getTipoBadgeAttribute(): string
    {
        return $this->tipo === 'propio'
            ? '<span class="badge bg-success">Propio</span>'
            : '<span class="badge bg-warning text-dark">Alquilado</span>';
    }

    public function getEstadoBadgeAttribute(): string
    {
        return $this->activo
            ? '<span class="badge bg-success">Activo</span>'
            : '<span class="badge bg-danger">Inactivo</span>';
    }

    // Métodos útiles
    public function estaDisponible(): bool
    {
        return $this->activo && !$this->viajes()
                ->whereIn('estado', ['en_curso', 'planeado'])
                ->exists();
    }

    public function getTotalViajes(): int
    {
        return $this->viajes()->count();
    }

    public function getTotalMotosTransportadas(): int
    {
        return $this->viajes()
            ->with('motosTransportadas')
            ->get()
            ->sum(function ($viaje) {
                return $viaje->motosTransportadas->sum('cantidad');
            });
    }
}
