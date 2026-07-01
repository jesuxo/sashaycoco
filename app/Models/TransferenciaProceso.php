<?php
// app/Models/TransferenciaProceso.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TransferenciaProceso extends Model
{
    protected $table = 'transferencia_proceso';

    protected $fillable = [
        'codigo', 'fecha', 'fk_sucursal_origen', 'fk_sucursal_destino',
        'estado', 'observaciones', 'creado_por', 'creado_por_nombre'
    ];

    protected $casts = [
        'fecha'      => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Generar código automático
    public static function generarCodigo()
    {
        $year = date('Y');
        $month = date('m');
        $last = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $numero = str_pad($last + 1, 4, '0', STR_PAD_LEFT);
        return "TR-{$year}{$month}-{$numero}";
    }

    // Relaciones
    public function items()
    {
        return $this->hasMany(TransferenciaItem::class, 'fk_proceso');
    }

    public function sucursalOrigen()
    {
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal_origen');
    }

    public function sucursalDestino()
    {
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal_destino');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    // Scopes
    public function scopeBorradores($query)
    {
        return $query->where('estado', 'borrador');
    }

    public function scopeEnProceso($query)
    {
        return $query->where('estado', 'en_proceso');
    }

    public function scopeCompletados($query)
    {
        return $query->where('estado', 'completado');
    }

    // Métodos
    public function completar()
    {
        DB::beginTransaction();
        try {
            foreach ($this->items as $item) {
                // Restar stock de origen
                $exisOrigen = Saexis::where([
                    'codprod' => $item->codprod,
                    'fk_sucursal' => $item->fk_sucursal_origen
                ])->first();

                if ($exisOrigen) {
                    $exisOrigen->existen -= $item->cantidad;
                    $exisOrigen->save();
                }

                // Sumar stock a destino
                $exisDestino = Saexis::where([
                    'codprod' => $item->codprod,
                    'fk_sucursal' => $item->fk_sucursal_destino
                ])->first();

                if ($exisDestino) {
                    $exisDestino->existen += $item->cantidad;
                    $exisDestino->save();
                } else {
                    // Crear registro si no existe
                    $exisDestino = new Saexis();
                    $exisDestino->codprod = $item->codprod;
                    $exisDestino->fk_sucursal = $item->fk_sucursal_destino;
                    $exisDestino->existen = $item->cantidad;
                    $exisDestino->save();
                }
            }

            $this->estado = 'completado';
            $this->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
