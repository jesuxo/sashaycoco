<?php
// app/Models/Gasto.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cwgasto extends Model
{
   // use SoftDeletes;

    protected $fillable = [
        'tipo_gasto_id', 'concepto', 'descripcion', 'monto', 'moneda_original',
        'monto_original', 'tasa_cambio', 'fecha_gasto',
        'gastable_id', 'gastable_type', 'comprobante', 'proveedor', 'es_viatico',
        'metodo_pago', 'referencia_pago', 'deducible_impuestos', 'registrado_por'
    ];

    protected $table = 'cwgastos';

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_gasto' => 'date',
        'deducible_impuestos' => 'boolean',
    ];

    // Relación polimórfica: Este gasto pertenece a un Viaje, Camión, Chofer, etc.
    public function gastable(): MorphTo
    {
        return $this->morphTo();
    }

    // Relación con el tipo de gasto
    public function tipoGasto(): BelongsTo
    {
        return $this->belongsTo(Cwtipogasto::class);
    }

    // Relación con el usuario que registró el gasto
    public function registrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    // Scope para filtrar por tipo de entidad (Viaje, Camión, etc.)
    public function scopeOfType($query, $type)
    {
        return $query->where('gastable_type', $type);
    }

    // Scope para filtrar por rango de fechas
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('fecha_gasto', [$startDate, $endDate]);
    }

    public function scopeViaticos($query)
    {
        return $query->where('es_viatico', true);
    }

    public function scopeGastosNormales($query)
    {
        return $query->where('es_viatico', false);
    }


    // Accessor para mostrar monto formateado según moneda
    public function getMontoFormateadoAttribute()
    {
        if ($this->moneda_original === 'VES' && $this->tasa_cambio) {
            return "Bs. " . number_format($this->monto_original, 2) .
                " (USD $" . number_format($this->monto, 2) . " @ {$this->tasa_cambio})";
        }
        return "$" . number_format($this->monto, 2);
    }

// Accessor para saber si es en bolívares
    public function getEsBolivaresAttribute()
    {
        return $this->moneda_original === 'VES';
    }
}
