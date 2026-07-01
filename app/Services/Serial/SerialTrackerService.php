<?php
// app/Services/Serial/SerialTrackerService.php

namespace App\Services\Serial;

use App\Models\Saseprcom;
use App\Models\Saseprfac;
use App\Models\Sasepropi;
use App\Models\Sasucursal;
use App\Models\Saprod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SerialTrackerService
{
    /**
     * Obtener historial completo de un serial
     */
    public function obtenerHistorialCompleto($serial, $codprod = null)
    {
        // Compras
        $compras = $this->obtenerMovimientosCompra($serial);

        // Ventas
        $ventas = $this->obtenerMovimientosVenta($serial);

        // Operaciones de inventario
        $operaciones = $this->obtenerOperacionesInventario($serial);

        // Combinar y ordenar
        $historial = $this->combinarMovimientos($compras, $ventas, $operaciones);

        // Enriquecer con nombres de sucursal
        $this->enriquecerConSucursales($historial);

        return $historial;
    }

    /**
     * Obtener movimientos de compra
     */
    protected function obtenerMovimientosCompra($serial)
    {
        return DB::table('saseprcom')
            ->select([
                'id',
                'tipocom as tipo',
                'numerod',
                'created_at as fecha_original',
                DB::raw("DATE_FORMAT(created_at, '%d/%m/%Y') as fecha"),
                'fk_sucursal',
                'coditem',
                DB::raw("'COMPRA' as tipo_movimiento"),
                DB::raw("'success' as badge_color")
            ])
            ->where('nroserial', $serial)
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Obtener movimientos de venta
     */
    protected function obtenerMovimientosVenta($serial)
    {
        return DB::table('saseprfac')
            ->select([
                'id',
                'tipofac as tipo',
                'numerod',
                'created_at as fecha_original',
                DB::raw("DATE_FORMAT(created_at, '%d/%m/%Y') as fecha"),
                'fk_sucursal',
                'coditem',
                DB::raw("'VENTA' as tipo_movimiento"),
                DB::raw("CASE WHEN tipofac IN ('A', 'Z') THEN 'primary' ELSE 'warning' END as badge_color")
            ])
            ->where('nroserial', $serial)
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Obtener operaciones de inventario
     */
    protected function obtenerOperacionesInventario($serial)
    {
        return DB::table('sasepropi')
            ->select([
                'id',
                'tipoopi as tipo',
                'NumeroD as numerod',
                'created_at as fecha_original',
                DB::raw("DATE_FORMAT(created_at, '%d/%m/%Y') as fecha"),
                'fk_sucursal',
                'CodItem as coditem',
                DB::raw("'OPERACION' as tipo_movimiento"),
                DB::raw("'info' as badge_color")
            ])
            ->where('NroSerial', $serial)
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Combinar movimientos
     */
    protected function combinarMovimientos(...$colecciones)
    {
        $todos = collect();

        foreach ($colecciones as $coleccion) {
            $todos = $todos->concat($coleccion);
        }

        return $todos->sortBy('fecha_original')->values();
    }

    /**
     * Enriquecer con nombres de sucursal
     */
    protected function enriquecerConSucursales($historial)
    {
        $sucursalesCache = [];

        foreach ($historial as $movimiento) {
            if (!isset($sucursalesCache[$movimiento->fk_sucursal])) {
                $sucursal = Sasucursal::find($movimiento->fk_sucursal);
                $sucursalesCache[$movimiento->fk_sucursal] = $sucursal ? $sucursal->descrip : 'N/A';
            }

            $movimiento->sucursal_nombre = $sucursalesCache[$movimiento->fk_sucursal];
            $movimiento->tipo_descripcion = $this->getTipoDescripcion($movimiento->tipo, $movimiento->tipo_movimiento);
        }
    }

    /**
     * Obtener descripción del tipo de movimiento
     */
    public function getTipoDescripcion($tipo, $tipoMovimiento)
    {
        if ($tipoMovimiento === 'COMPRA') {
            return match($tipo) {
                'U' => 'Compra',
                'Y' => 'Devolución de Compra',
                default => 'Compra'
            };
        }

        if ($tipoMovimiento === 'VENTA') {
            return match($tipo) {
                'A', 'Z' => 'Factura',
                'B', 'W' => 'Devolución de Factura',
                default => 'Venta'
            };
        }

        if ($tipoMovimiento === 'OPERACION') {
            return match($tipo) {
                'P', 'K' => 'Descargo',
                'O', 'T' => 'Cargo',
                'N', 'S' => 'Traslado',
                default => 'Operación'
            };
        }

        return 'Desconocido';
    }

    /**
     * Obtener historial de seriales para una compra - VERSIÓN CORREGIDA
     */
    public function obtenerHistorialSerialesCompra($compraId)
    {
        // Buscar la compra primero
        $compra = \App\Models\Sacomp::find($compraId);

        if (!$compra) {
            return [
                'historial' => [],
                'sucursales_venta' => []
            ];
        }

        // Buscar seriales usando los campos de la compra
        $seriales = Saseprcom::where('numerod', $compra->numerod)
            ->where('tipocom', $compra->tipocom)
            ->with('producto')
            ->get();

        $historial = [];
        $sucursalesVenta = [];

        foreach ($seriales as $serial) {
            $ventas = DB::table('saseprfac')
                ->where('nroserial', $serial->nroserial)
                ->orderBy('created_at', 'desc')
                ->get();

            $sucursalesSerial = [];

            foreach ($ventas as $venta) {
                if (!isset($sucursalesVenta[$venta->fk_sucursal])) {
                    $sucursal = Sasucursal::find($venta->fk_sucursal);
                    $sucursalesVenta[$venta->fk_sucursal] = $sucursal ? $sucursal->descrip : 'Desconocida';
                }

                if (!isset($sucursalesSerial[$venta->fk_sucursal])) {
                    $sucursalesSerial[$venta->fk_sucursal] = [];
                }

                $sucursalesSerial[$venta->fk_sucursal][] = $venta;
            }

            $historial[$serial->nroserial] = [
                'serial' => $serial,
                'ventas' => $ventas,
                'sucursales' => $sucursalesSerial,
                'total_ventas' => $ventas->count()
            ];
        }

        return [
            'historial' => $historial,
            'sucursales_venta' => $sucursalesVenta
        ];
    }

    /**
     * Verificar si un serial está vendido
     */
    public function estaVendido($nroserial)
    {
        return DB::table('saseprfac')
            ->where('nroserial', $nroserial)
            ->whereIn('tipofac', ['A', 'Z'])
            ->exists();
    }

    /**
     * Obtener estadísticas de seriales
     */
    public function obtenerEstadisticasSeriales($seriales = null)
    {
        if ($seriales === null) {
            return [
                'total' => 0,
                'vendidos' => 0,
                'en_stock' => 0,
                'porcentaje_vendido' => 0
            ];
        }

        $total = count($seriales);
        $vendidos = 0;

        foreach ($seriales as $serial) {
            if ($this->estaVendido($serial->nroserial)) {
                $vendidos++;
            }
        }

        return [
            'total' => $total,
            'vendidos' => $vendidos,
            'en_stock' => $total - $vendidos,
            'porcentaje_vendido' => $total > 0 ? round(($vendidos / $total) * 100, 2) : 0
        ];
    }

    /**
     * Buscar serial por texto
     */
    public function buscarSerial($texto)
    {
        $resultados = [
            'compras' => [],
            'ventas' => [],
            'operaciones' => []
        ];

        if (strlen($texto) < 3) {
            return $resultados;
        }

        // Buscar en compras
        $resultados['compras'] = Saseprcom::with('producto')
            ->where('nroserial', 'LIKE', "%{$texto}%")
            ->limit(20)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'serial' => $item->nroserial,
                    'documento' => $item->numerod,
                    'tipo' => $item->tipocom,
                    'producto' => $item->producto->descrip ?? 'N/A',
                    'fecha' => $item->created_at->format('d/m/Y')
                ];
            });

        // Buscar en ventas
        $resultados['ventas'] = DB::table('saseprfac')
            ->where('nroserial', 'LIKE', "%{$texto}%")
            ->limit(20)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'serial' => $item->nroserial,
                    'documento' => $item->numerod,
                    'tipo' => $item->tipofac,
                    'fecha' => date('d/m/Y', strtotime($item->created_at))
                ];
            });

        // Buscar en operaciones
        $resultados['operaciones'] = DB::table('sasepropi')
            ->where('NroSerial', 'LIKE', "%{$texto}%")
            ->limit(20)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'serial' => $item->NroSerial,
                    'documento' => $item->NumeroD,
                    'tipo' => $item->tipoopi,
                    'fecha' => date('d/m/Y', strtotime($item->created_at))
                ];
            });

        return $resultados;
    }
}
