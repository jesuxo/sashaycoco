<?php
// app/Models/Cwchofer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cwchofer extends Model
{
    //use SoftDeletes;

    protected $table = 'cwchoferes';

    protected $fillable = [
        'nombre',
        'apellido',
        'licencia',
        'telefono',
        'email',
        'direccion',
        'fecha_nacimiento',
        'fecha_ingreso',
        'tipo_sangre',
        'contacto_emergencia_nombre',
        'contacto_emergencia_telefono',
        'foto',
        'observaciones_medicas',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_nacimiento' => 'date',
        'fecha_ingreso' => 'date',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Relaciones
     */
    public function viajes(): HasMany
    {
        return $this->hasMany(Cwviaje::class, 'chofer_id');
    }

    public function gastos(): MorphMany
    {
        return $this->morphMany(Cwgasto::class, 'gastable');
    }

    /**
     * Scopes - Filtros
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeDisponibles($query)
    {
        return $query->where('activo', true)
            ->whereDoesntHave('viajes', function ($q) {
                $q->whereIn('estado', ['en_curso', 'planeado']);
            });
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
                ->orWhere('apellido', 'like', "%{$termino}%")
                ->orWhere('licencia', 'like', "%{$termino}%")
                ->orWhere('telefono', 'like', "%{$termino}%")
                ->orWhere('email', 'like', "%{$termino}%");
        });
    }

    /**
     * Accesores - Atributos calculados
     */
    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombre} {$this->apellido}");
    }

    public function getEdadAttribute(): ?int
    {
        if (!$this->fecha_nacimiento) {
            return null;
        }
        return $this->fecha_nacimiento->age;
    }

    public function getAntiguedadAttribute(): ?string
    {
        if (!$this->fecha_ingreso) {
            return null;
        }
        return $this->fecha_ingreso->diffForHumans(null, true);
    }

    public function getEstadoColorAttribute(): string
    {
        return $this->activo ? 'success' : 'danger';
    }

    public function getEstadoBadgeAttribute(): string
    {
        return $this->activo
            ? '<span class="badge bg-success">Activo</span>'
            : '<span class="badge bg-danger">Inactivo</span>';
    }

    public function getInicialesAttribute(): string
    {
        $iniciales = '';
        if ($this->nombre) {
            $iniciales .= substr($this->nombre, 0, 1);
        }
        if ($this->apellido) {
            $iniciales .= substr($this->apellido, 0, 1);
        }
        return strtoupper($iniciales);
    }

    /**
     * Métodos útiles
     */
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

    public function getViajesCompletados(): int
    {
        return $this->viajes()->where('estado', 'completado')->count();
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

    public function getUltimoViaje()
    {
        return $this->viajes()->latest()->first();
    }

    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }
        return null;
    }
}
