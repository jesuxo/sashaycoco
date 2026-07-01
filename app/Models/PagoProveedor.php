<?php
// app/Models/PagoProveedor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PagoProveedor extends Model
{
    protected $table = 'pagos_proveedores';

    protected $fillable = [
        'folio',
        'fecha_pago',
        'codprov',
        'numero_aprobacion',
        'monto_total',
        'notas',
        'estado'
    ];

    protected $casts = [
        'fecha_pago'  => 'date',
        'monto_total' => 'decimal:2',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime'
    ];

    protected $withCount = ['comprobantes'];

    // Estados
    const ESTADO_PENDIENTE  = 'pendiente';
    const ESTADO_PARCIAL    = 'parcial';
    const ESTADO_COMPLETADO = 'completado';
    const ESTADO_APROBADO   = 'aprobado';

    public function proveedor()
    {
        return $this->belongsTo(Saprov::class, 'codprov', 'codprov');
    }

    public function detalles()
    {
        return $this->hasMany(PagoProveedorDetalle::class, 'pago_id');
    }

    public function comprobantes()
    {
        return $this->hasMany(PagoComprobante::class, 'pago_id');
    }

    public function despachos()
    {
        return $this->hasMany(DespachoProveedor::class, 'pago_id');
    }

    public function getTotalProductosAttribute()
    {
        return $this->detalles->sum('cantidad');
    }

    public function getTotalRecibidoAttribute()
    {
        return $this->detalles->sum('cantidad_recibida');
    }

    public function getMontoPendienteAttribute()
    {
        return $this->detalles->sum(function($detalle) {
            return $detalle->pendiente * $detalle->precio_unitario;
        });
    }

    public function getTotalPendienteAttribute()
    {
        return $this->total_productos - $this->total_recibido;
    }

    public function getPorcentajeRecibidoAttribute()
    {
        if ($this->total_productos == 0) return 0;
        return round(($this->total_recibido / $this->total_productos) * 100, 2);
    }

    public function actualizarEstado()
    {
        $totalPendiente = $this->total_pendiente;

        if ($totalPendiente == 0) {
            $this->estado = self::ESTADO_COMPLETADO;
        } elseif ($this->total_recibido > 0) {
            $this->estado = self::ESTADO_PARCIAL;
        } else {
            $this->estado = self::ESTADO_PENDIENTE;
        }

        $this->saveQuietly();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pago) {
            if (empty($pago->folio)) {
                $year = date('Y');
                $last = self::whereYear('created_at', $year)->count() + 1;
                $pago->folio = 'PED-' . $year . '-' . str_pad($last, 4, '0', STR_PAD_LEFT);
            }
        });
    }

}
