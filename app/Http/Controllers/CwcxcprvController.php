<?php

namespace App\Http\Controllers;

use App\Models\Cwcxcprv;
use App\Models\Saprov;
use App\Models\Sasucursal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CwcxcprvController extends Controller
{
    /**
     * Vista principal de cuentas por pagar
     */
    public function index()
    {
        $comercial = session('comercialid');

        if (!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",", $arraysucursales);

        $sucursales = Sasucursal::where("fk_comercial", $comercial)
            ->whereRaw("id in ($arraysucursales)")
            ->orderBy('descrip')
            ->get();


        return view('cwcxcprv', compact(  'sucursales', 'comercial'));
    }

    /**
     * Sincronizar cuentas por pagar desde el sistema local
     * Este método recibe los datos enviados desde verificarCwcxcprv.php
     */
    public function cuentasxpagar(Request $request)
    {
        try {

            $dato = $request->dato;
            $tokenEsperado = '$xjsSwN5xEX4nY2U@';

            if ($dato !== $tokenEsperado) {
                return response()->json(['error' => 'Token de seguridad inválido'], 401);
            }

            $cuentasxpagar = $request->cuentasxpagar;
            $sucursalId    = str_replace("300", "", $request->sucursal);

            if (!$cuentasxpagar) {

                return response()->json(['error' => 'No se recibieron cuentas por pagar'], 400);
            }

            $cuentasxpagar = json_decode($cuentasxpagar);

            if (!is_array($cuentasxpagar) and !is_object($cuentasxpagar)) {

                return response()->json(['error' => 'Formato de datos inválido'], 400);
            }

            $updatedCount = 0;
            $lineascxp = [];

            if (count($cuentasxpagar) > 0) {
                foreach ($cuentasxpagar as $cxp) {
                    if (isset($cxp->id) and $cxp->id > 0) {

                        try {
                            // Buscar si ya existe el registro
                            $record = Cwcxcprv::where([
                                'id' => $cxp->id,
                                'fk_sucursal' => $sucursalId
                            ])->first();

                            // Guardar línea para el reporte
                            $lineascxp[$cxp->id] = [
                                'numerod' => $cxp->numerod ?? '',
                                'codprov' => $cxp->codprov ?? '',
                                'actions' => 'Sincronizada'
                            ];

                            if (!$record) {
                                $record = new Cwcxcprv();
                                $updatedCount++;
                            }

                            // Asignar todos los campos (convertir a array para evitar problemas)
                            $recordData = [
                                'concepto'      => $cxp->concepto ?? '',
                                'monto'         => $cxp->monto ?? 0,
                                'signo'         => $cxp->signo ?? 0,
                                'tasabs'        => $cxp->tasabs ?? 0,
                                'monedapago'    => $cxp->monedapago ?? 0,
                                'conversion'    => $cxp->conversion ?? 0,
                                'archived'      => $cxp->archived ?? 0,
                                'numerod'       => $cxp->numerod ?? '',
                                'tipo'          => $cxp->tipo ?? '',
                                'codprov'       => $cxp->codprov ?? '',
                                'tipocomp'      => $cxp->tipocomp ?? '',
                                'fk_estructura' => $cxp->fk_estructura ?? 0,
                                'abonado'       => $cxp->abonado ?? 0,
                                'compras'       => $cxp->compras ?? 0,
                                'gastos'        => $cxp->gastos ?? 0,
                                'anticipos'     => $cxp->anticipos ?? 0,
                                'contabilidad'  => $cxp->contabilidad ?? 0,
                                'id'            => $cxp->id ?? 0,
                                'NROUNICOCXP'   => $cxp->NROUNICOCXP ?? 0,
                                'codoper'       => $cxp->codoper ?? 'CXP',
                                'fk_sucursal'   => $sucursalId,
                            ];

                            // Procesar fechas
                            if (isset($cxp->fecha) and $cxp->fecha) {
                                $recordData['fecha'] = Carbon::parse($cxp->fecha);
                            }
                            if (isset($cxp->fechav) and $cxp->fechav) {
                                $recordData['fechav'] = Carbon::parse($cxp->fechav);
                            }

                            $record->fill($recordData);
                            $record->save();

                        } catch (\Exception $e) {
                            Log::error('Error al guardar registro', [
                                'id' => $cxp->id ?? 'null',
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            }

            // Generar HTML de respuesta
            $html = '';
            if (count($lineascxp) > 0) {
                $html = '<table style="width: 92%; margin: auto" width="92%" border="0" cellpadding="5" cellspacing="0">
                            <tr style="background-color: #f0f0f0;">
                                <th width="20%" align="left">Número</th>
                                <th width="70%" align="left">Proveedor</th>
                                <th width="10%" align="left">Estado</th>
                            </tr>';

                foreach ($lineascxp as $index => $line) {
                    $html .= '<tr>
                                <td width="20%" align="left">' . htmlspecialchars($line['numerod']) . '</td>
                                <td width="70%" align="left">' . htmlspecialchars($line['codprov']) . '</td>
                                <td width="10%" align="left">' . htmlspecialchars($line['actions']) . '</td>
                              </tr>';
                }
                $html .= '</table>';
            }

            Log::info('Sincronización completada', [
                'sucursal' => $sucursalId,
                'registros_sincronizados' => $updatedCount,
                'total_recibidos' => count($cuentasxpagar)
            ]);

            return response()->json([
                'success' => true,
                'updated' => true,
                'count' => $updatedCount,
                'html' => $html,
                'lineas' => $lineascxp
            ]);

        } catch (\Exception $e) {
            Log::error('Error en cuentasxpagar', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener detalle de una cuenta por pagar específica
     */
    public function show($id)
    {
        $cxp = Cwcxcprv::find($id);

        if (!$cxp) {
            return response()->json(['error' => 'Cuenta por pagar no encontrada'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $cxp
        ]);
    }

    /**
     * Obtener cuentas por pagar de un proveedor específico
     */
    public function porProveedor(Request $request, $codprov)
    {
        $sucursalId = $request->sucursal ? str_replace("300", "", $request->sucursal) : null;

        $query = Cwcxcprv::where('codprov', $codprov);

        if ($sucursalId) {
            $query->where('fk_sucursal', $sucursalId);
        }

        $cuentas = $query->orderBy('fecha', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $cuentas,
            'count' => $cuentas->count()
        ]);
    }

    /**
     * Obtener resumen de cuentas por pagar por proveedor
     */
    public function resumenPorProveedor(Request $request)
    {
        $sucursalId = $request->sucursal ? str_replace("300", "", $request->sucursal) : null;

        $query = Cwcxcprv::select(
            'codprov',
            DB::raw('COUNT(*) as total_documentos'),
            DB::raw('SUM(monto) as monto_total'),
            DB::raw('SUM(abonado) as abonado_total'),
            DB::raw('SUM(monto - abonado) as saldo_total')
        );

        if ($sucursalId) {
            $query->where('fk_sucursal', $sucursalId);
        }

        $resumen = $query->groupBy('codprov')
            ->havingRaw('SUM(monto - abonado) > 0')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $resumen
        ]);
    }


    /**
     * Eliminar cuenta por pagar (solo si no está sincronizada)
     */
    public function destroy($id)
    {
        $cxp = Cwcxcprv::find($id);

        if (!$cxp) {
            return response()->json(['error' => 'Cuenta por pagar no encontrada'], 404);
        }

        $cxp->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cuenta por pagar eliminada correctamente'
        ]);
    }

    /**
     * Estadísticas de sincronización
     */
    public function estadisticas(Request $request)
    {
        $sucursalId = $request->sucursal ? str_replace("300", "", $request->sucursal) : null;

        $query = Cwcxcprv::query();

        if ($sucursalId) {
            $query->where('fk_sucursal', $sucursalId);
        }

        $estadisticas = [
            'total' => $query->count(),
            'por_sucursal' => Cwcxcprv::select('fk_sucursal', DB::raw('COUNT(*) as total') )
                ->groupBy('fk_sucursal')
                ->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }
}
