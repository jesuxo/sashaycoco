<?php
// app/Models/TipoGasto.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cwtipogasto extends Model
{
    protected $fillable = ['nombre', 'categoria', 'descripcion', 'activo'];

    protected $table = 'cwtipogastos';
    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relación con los gastos
    public function gastos()
    {
        return $this->hasMany(Cwgasto::class, 'tipo_gasto_id');
    }

    // Scope para tipos activos
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
