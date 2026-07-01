<?php
// app/Models/TransferenciaSesion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TransferenciaSesion extends Model
{
    protected $table = 'transferencia_sesion';
    public $timestamps = true;

    protected $fillable = [
        'sesion_id', 'fk_sucursal_origen', 'fk_sucursal_destino',
        'codprod', 'cantidad'
    ];

    // Obtener items de la sesión actual
    public static function getItemsSesion()
    {
        $sesionId = Session::getId();
        return self::where('sesion_id', $sesionId)
            ->with(['producto', 'sucursalOrigen', 'sucursalDestino'])
            ->get();
    }

    // Agregar item a la sesión
    public static function agregarItem($origenId, $destinoId, $codprod, $cantidad)
    {
        $sesionId = Session::getId();

        $item = self::where('sesion_id', $sesionId)
            ->where('fk_sucursal_origen', $origenId)
            ->where('fk_sucursal_destino', $destinoId)
            ->where('codprod', $codprod)
            ->first();

        if ($item) {
            $item->cantidad += $cantidad;
            $item->save();
        } else {
            self::create([
                'sesion_id' => $sesionId,
                'fk_sucursal_origen' => $origenId,
                'fk_sucursal_destino' => $destinoId,
                'codprod' => $codprod,
                'cantidad' => $cantidad
            ]);
        }
    }

    // Limpiar sesión
    public static function limpiarSesion()
    {
        $sesionId = Session::getId();
        return self::where('sesion_id', $sesionId)->delete();
    }

    // Relaciones
    public function producto()
    {
        return $this->belongsTo(Saprod::class, 'codprod', 'codprod');
    }

    public function sucursalOrigen()
    {
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal_origen');
    }

    public function sucursalDestino()
    {
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal_destino');
    }
}
