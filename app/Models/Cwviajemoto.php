<?php
// app/Models/Cwviajemoto.php (o como se llame tu modelo)

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cwviajemoto extends Model
{
    protected $table = 'cwviajemotos'; // Asegúrate que este sea el nombre correcto

    protected $fillable = [
        'viaje_id',
        'cliente_codclie',
        'proveedor_paga',
        'proveedor_codprov',
        'monto_transporte_proveedor',
        'retencion_proveedor',
        'descuento_aplicado_cliente',
        'monto_esperado_cliente',
        'monto_real_cliente',
        'diferencia',
        'estado_conciliacion',
        'notas_conciliacion',
        'fecha_conciliacion',
        'conciliado_por',
        'modelo_moto',
        'cantidad',
        'precio_por_moto',
        'facturado',
        'fecha_facturacion'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_por_moto' => 'decimal:2',
        'facturado' => 'boolean',
        'proveedor_paga' => 'boolean',
        'monto_transporte_proveedor' => 'decimal:2',
        'retencion_proveedor' => 'decimal:2',
        'descuento_aplicado_cliente' => 'decimal:2',
        'monto_esperado_cliente' => 'decimal:2',
        'monto_real_cliente' => 'decimal:2',
        'diferencia' => 'decimal:2',
        'fecha_facturacion' => 'date',
        'fecha_conciliacion' => 'date',
    ];

    /**
     * Relación con el viaje
     */
    public function viaje(): BelongsTo
    {
        return $this->belongsTo(Cwviaje::class, 'viaje_id');
    }

    /**
     * Relación con el cliente (saclie)
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Saclie::class, 'cliente_codclie', 'codclie');
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Saprov::class, 'proveedor_codprov', 'codprov');
    }

    /**
     * Relación con el usuario que concilió
     */
    public function conciliador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'conciliado_por');
    }

    /**
     * Accesor para obtener el subtotal
     */
    public function getSubtotalAttribute(): float
    {
        return $this->cantidad * $this->precio_por_moto;
    }

    /**
     * Accesor para saber si es del proveedor específico
     */
    public function getEsProveedorAttribute(): bool
    {
        $proveedorId = config('transporte.proveedor_transporte', 'V15184480');
        return $this->proveedor_codprov === $proveedorId;
    }

    /**
     * Accesor para el monto esperado calculado
     */
    public function getMontoEsperadoCalculadoAttribute(): float
    {
        if (!$this->proveedor_paga) return 0;
        return ($this->monto_transporte_proveedor ?? 0) - ($this->retencion_proveedor ?? 0);
    }

    /**
     * Accesor para el estado de conciliación con color
     */
    public function getEstadoConciliacionColorAttribute(): string
    {
        return match($this->estado_conciliacion) {
            'conciliado' => 'success',
            'discrepancia' => 'danger',
            default => 'warning'
        };
    }
}
