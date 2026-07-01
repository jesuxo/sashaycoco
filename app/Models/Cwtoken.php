<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cwtoken extends Model
{
    use HasFactory;

    protected $table = 'cwtoken';

    protected $fillable = [
        'id', 'token', 'codusua', 'status', 'obs', 'fksucursal', 'created_at', 'updated_at'
    ];

    protected $appends = ['fechaformat', 'status_text', 'status_class', 'tiempo_restante'];

    // Accessors
    public function getFechaformatAttribute()
    {
        return $this->created_at ? Carbon::parse($this->created_at)->format('d/m/Y H:i') : '';
    }

    public function getStatusTextAttribute()
    {
        return $this->status == 0 ? 'Pendiente' : 'Usado';
    }

    public function getStatusClassAttribute()
    {
        return $this->status == 0 ? 'warning' : 'success';
    }

    public function getTiempoRestanteAttribute()
    {
        if ($this->status == 1) return 'Usado';
        $creado = Carbon::parse($this->created_at);
        $dias = $creado->diffInDays(Carbon::now());
        if ($dias > 7) return 'Expirado';
        return $dias . ' días';
    }

    // Relations
    public function sucursal()
    {
        return $this->belongsTo(Sasucursal::class, 'fksucursal', 'id');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('status', 0);
    }

    public function scopeUsados($query)
    {
        return $query->where('status', 1);
    }

    public function scopePorSucursal($query, $sucursalId)
    {
        if ($sucursalId) {
            return $query->where('fksucursal', $sucursalId);
        }
        return $query;
    }
}
