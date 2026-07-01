<?php

namespace App\Http\Controllers;

//$monto_formateado = FormatoHelper::moneda($transf->monto, $transf->currency);

use App\Models\Cwbancos;
use App\Models\Cwtransferencia;
use App\Models\Sasucursal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use Vinkla\Hashids\Facades\Hashids;
use App\Exports\TransferenciasExport;
use App\Exports\EstadisticasTransferenciasExport;
use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\ImageManager;

class CwtransferenciasController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->to('index');
    }

    /**
     * Exportar listado de transferencias a Excel
     */
    public function exportarExcel(Request $request)
    {
        // Reutilizar la misma lógica de filtros del reporte
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $sucursal_filter = $request->sucursal_filter ?? 0;
        $selectbanco = $request->selectbanco ?? 0;
        $status = $request->status ?? '';
        $fechasreport = $request->fechasreport;
        $busquedatransf = $request->busquedatransf ?? '';
        $moneda = $request->moneda ?? '';
        $ordenar_por = $request->ordenar_por ?? 'created_at';
        $orden_direccion = $request->orden_direccion ?? 'desc';
        $limite = $request->limite ?? 0; // 0 = sin límite para exportación

        // Procesar fechas
        $fechasaux = str_replace(' ', '', $fechasreport);
        $fec1 = $fec2 = '';

        if (strpos($fechasaux, "to")) {
            list($fec1, $fec2) = explode("to", $fechasaux);
        } else {
            if ($fechasreport != '') {
                $fec1 = $fec2 = $fechasreport;
            }
        }

        if ($fec1 != '') {
            list($d1, $m1, $y1) = explode("/", $fec1);
            list($d2, $m2, $y2) = explode("/", $fec2);
            $fec1 = "$y1-$m1-$d1";
            $fec2 = "$y2-$m2-$d2";
        }

        // Construir query
        $query = Cwtransferencia::query()
            ->whereRaw("fksucursal in ($arraysucursales)")
            ->with(['sucursal', 'banco']);

        if ($fec1 != '' and $fec2 != '') {
            $query = $query->whereBetween('created_at', ["$fec1 00:00:00", "$fec2 23:59:59"]);
        }

        if ($busquedatransf != '') {
            $busquedatransf = str_replace('*', ' ', $busquedatransf);
            $vector         = explode(" ", $busquedatransf);

            $query = $query->where(function($q) use ($vector) {

                foreach ($vector as $item) {
                    $q->where(function($subq) use ($item) {
                        $subq->where('numero', 'like', "%$item%")
                            ->orWhere('observacion', 'like', "%$item%")
                            ->orWhere('titular', 'like', "%$item%")
                            ->orWhere('monto', 'like', "%$item%")
                            ->orWhereHas('banco', function($bq) use ($item) {
                                $bq->where('descrip', 'like', "%$item%");
                            });
                    });
                }

            });
        }

        if ($sucursal_filter > 0) {
            $query = $query->where('fksucursal', $sucursal_filter);
        }

        if ($selectbanco > 0) {
            $query = $query->where('fkbanco', $selectbanco);
        }

        if ($status !== '') {
            $query = $query->where('status', $status);
        }

        if ($moneda) {
            switch($moneda) {
                case 'bs':
                    $query = $query->where('bs', 1);
                    break;
                case 'usd':
                    $query = $query->where('dolares', 1);
                    break;
                case 'cop':
                    $query = $query->where('pesos', 1);
                    break;
            }
        }

        // Si hay límite y es > 0, aplicarlo
        if ($limite > 0) {
            $query->limit($limite);
        }

        $transferencias = $query->orderBy($ordenar_por, $orden_direccion)->get();

        $filtros = [
            'fechas' => $fechasreport,
            'sucursal' => $sucursal_filter ? Sasucursal::find($sucursal_filter)?->descrip : null,
            'moneda' => $moneda,
            'status' => $status,
            'busqueda' => $busquedatransf
        ];

        return Excel::download(
            new TransferenciasExport($transferencias, $filtros),
            'transferencias_' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }

    /**
     * Exportar estadísticas a Excel
     */
    public function exportarEstadisticas(Request $request)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $fechasreport = $request->fechasreport ?? '';
        $sucursal_filter = $request->sucursal_filter ?? 0;
        $moneda = $request->moneda ?? '';

        // Procesar fechas
        $fechasaux = str_replace(' ', '', $fechasreport);
        $fec1 = $fec2 = '';

        if (strpos($fechasaux, "to")) {
            list($fec1, $fec2) = explode("to", $fechasaux);
        } else {
            if ($fechasreport != '') {
                $fec1 = $fec2 = $fechasreport;
            }
        }

        if ($fec1 != '') {
            list($d1, $m1, $y1) = explode("/", $fec1);
            list($d2, $m2, $y2) = explode("/", $fec2);
            $fec1 = "$y1-$m1-$d1";
            $fec2 = "$y2-$m2-$d2";
        }

        // Obtener transferencias
        $query = Cwtransferencia::query()
            ->whereRaw("fksucursal in ($arraysucursales)")
            ->with(['sucursal', 'banco']);

        if ($fec1 != '' and $fec2 != '') {
            $query->whereBetween('created_at', ["$fec1 00:00:00", "$fec2 23:59:59"]);
        }

        if ($sucursal_filter > 0) {
            $query->where('fksucursal', $sucursal_filter);
        }

        $transferencias = $query->get();

        // Calcular estadísticas
        $estadisticas = $this->calcularEstadisticas($transferencias);

        $filtros = [
            'fechas' => $fechasreport,
            'sucursal' => $sucursal_filter ? Sasucursal::find($sucursal_filter)?->descrip : null,
            'moneda' => $moneda
        ];

        return Excel::download(
            new EstadisticasTransferenciasExport($estadisticas, $filtros),
            'estadisticas_transferencias_' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }

    /**
     * Calcular todas las estadísticas
     */
    private function calcularEstadisticas($transferencias)
    {
        // Si no hay transferencias, retornar estructura vacía
        if ($transferencias->isEmpty()) {
            return [
                'generales' => [
                    'total' => 0,
                    'pendientes' => 0,
                    'aprobadas' => 0,
                    'rechazadas' => 0,
                    'monto_total' => 0,
                    'tasa_aprobacion' => 0,
                    'tasa_rechazo' => 0,
                    'tiempo_promedio_aprobacion' => 0,
                ],
                'por_moneda' => [
                    'bs' => ['cantidad' => 0, 'monto' => 0, 'aprobadas' => 0, 'pendientes' => 0, 'rechazadas' => 0, 'promedio' => 0, 'max' => 0, 'min' => 0],
                    'usd' => ['cantidad' => 0, 'monto' => 0, 'aprobadas' => 0, 'pendientes' => 0, 'rechazadas' => 0, 'promedio' => 0, 'max' => 0, 'min' => 0],
                    'cop' => ['cantidad' => 0, 'monto' => 0, 'aprobadas' => 0, 'pendientes' => 0, 'rechazadas' => 0, 'promedio' => 0, 'max' => 0, 'min' => 0],
                ],
                'tendencia_diaria' => [],
                'tendencia_semanal' => [],
                'tendencia_mensual' => [],
                'top_bancos' => [],
                'top_sucursales' => [],
                'top_clientes' => [],
                'proyecciones' => [
                    'proyeccion_mensual' => 0,
                    'meta_aprobacion' => 80,
                    'dias_para_meta' => 0,
                ],
                'horas_pico' => array_fill(0, 24, 0),
                'dias_semana' => array_fill(0, 7, 0),
            ];
        }

        // Calcular montos por moneda
        $montoBs = $transferencias->where('bs', 1)->sum('monto');
        $montoUsd = $transferencias->where('dolares', 1)->sum('monto');
        $montoCop = $transferencias->where('pesos', 1)->sum('monto');
        $montoTotal = $montoBs + $montoUsd + $montoCop;

        return [
            'generales' => [
                'total' => $transferencias->count(),
                'pendientes' => $transferencias->where('status', 0)->count(),
                'aprobadas' => $transferencias->where('status', 1)->count(),
                'rechazadas' => $transferencias->where('status', 2)->count(),
                'monto_total' => $montoTotal,
                'tasa_aprobacion' => $transferencias->count() > 0 ? ($transferencias->where('status', 1)->count() / $transferencias->count() * 100) : 0,
                'tasa_rechazo' => $transferencias->count() > 0 ? ($transferencias->where('status', 2)->count() / $transferencias->count() * 100) : 0,
                'tiempo_promedio_aprobacion' => $this->calcularTiempoPromedioAprobacion($transferencias),
            ],

            'por_moneda' => [
                'bs' => [
                    'cantidad' => $transferencias->where('bs', 1)->count(),
                    'monto' => $montoBs,
                    'aprobadas' => $transferencias->where('bs', 1)->where('status', 1)->count(),
                    'pendientes' => $transferencias->where('bs', 1)->where('status', 0)->count(),
                    'rechazadas' => $transferencias->where('bs', 1)->where('status', 2)->count(),
                    'promedio' => $transferencias->where('bs', 1)->avg('monto') ?? 0,
                    'max' => $transferencias->where('bs', 1)->max('monto') ?? 0,
                    'min' => $transferencias->where('bs', 1)->min('monto') ?? 0,
                ],
                'usd' => [
                    'cantidad' => $transferencias->where('dolares', 1)->count(),
                    'monto' => $montoUsd,
                    'aprobadas' => $transferencias->where('dolares', 1)->where('status', 1)->count(),
                    'pendientes' => $transferencias->where('dolares', 1)->where('status', 0)->count(),
                    'rechazadas' => $transferencias->where('dolares', 1)->where('status', 2)->count(),
                    'promedio' => $transferencias->where('dolares', 1)->avg('monto') ?? 0,
                    'max' => $transferencias->where('dolares', 1)->max('monto') ?? 0,
                    'min' => $transferencias->where('dolares', 1)->min('monto') ?? 0,
                ],
                'cop' => [
                    'cantidad' => $transferencias->where('pesos', 1)->count(),
                    'monto' => $montoCop,
                    'aprobadas' => $transferencias->where('pesos', 1)->where('status', 1)->count(),
                    'pendientes' => $transferencias->where('pesos', 1)->where('status', 0)->count(),
                    'rechazadas' => $transferencias->where('pesos', 1)->where('status', 2)->count(),
                    'promedio' => $transferencias->where('pesos', 1)->avg('monto') ?? 0,
                    'max' => $transferencias->where('pesos', 1)->max('monto') ?? 0,
                    'min' => $transferencias->where('pesos', 1)->min('monto') ?? 0,
                ],
            ],

            'tendencia_diaria' => $this->obtenerTendenciaDiaria($transferencias),
            'tendencia_semanal' => $this->obtenerTendenciaSemanal($transferencias),
            'tendencia_mensual' => $this->obtenerTendenciaMensual($transferencias),

            'top_bancos' => $this->obtenerTopBancos($transferencias, 5),
            'top_sucursales' => $this->obtenerTopSucursales($transferencias, 5),
            'top_clientes' => $this->obtenerTopClientes($transferencias, 5),

            'proyecciones' => [
                'proyeccion_mensual' => $this->calcularProyeccionMensual($transferencias),
                'meta_aprobacion' => 80,
                'dias_para_meta' => $this->calcularDiasParaMeta($transferencias),
            ],

            'horas_pico' => $this->obtenerHorasPico($transferencias),
            'dias_semana' => $this->obtenerDistribucionPorDiaSemana($transferencias),
        ];
    }

    public function json($busquedatransf, $status, $fechas)
    {
        $transferencias = [];
        return response()->json($transferencias);
    }

    public function list(Request $request)
    {
        $sucursalid     = str_replace("300","",$request->sucursal);
        $transferencias = Cwtransferencia::where(["fksucursal" => $sucursalid, "status"=>1, "tipo"=>"venta", "descargada" => 0])->limit('30')->get();

        return response()->json(['success'=>'success', 'newtransfer' => $transferencias]);
    }

    public function verificarTiempoReal(Request $request)
    {
        try {
            $numero = $request->numero;

            if (!$numero ) {
                return response()->json(['valid' => 1]);
            }

            $query = Cwtransferencia::whereRaw("numero like '%$numero%'");

            $existente = $query->with(['sucursal', 'banco'])->first();

            if ($existente) {
                // Preparar datos para mostrar en la alerta
                return response()->json([
                    'valid' => 0,
                    'existente' => [
                        'id'           => $existente->id,
                        'numero'       => $existente->numero,
                        'monto'        => $existente->monto,
                        'fecha'        => $existente->fecha,
                        'fechaformat'  => $existente->fechaformat,
                        'status'       => $existente->status,
                        'tipo'         => $existente->tipo,
                        'tipo_texto'   => $existente->tipo_texto,
                        'currency'     => $existente->currency,
                        'banco_nombre' => $existente->banco ? $existente->banco->descrip : null,
                        'sucursal'     => $existente->sucursal ? [
                            'id'       => $existente->sucursal->id,
                            'descrip'  => $existente->sucursal->descrip
                        ] : null
                    ]
                ]);
            }

            return response()->json(['valid' => 1]);

        } catch (\Exception $e) {
            \Log::error('Error en verificarTiempoReal: ' . $e->getMessage());
            return response()->json(['valid' => 1, 'error' => $e->getMessage()]);
        }
    }

    public function buscarNumerosSimilares(Request $request)
    {

        try {
            $query = $request->numero;
            $limit = $request->limit ?? 8;

            if (!$query or strlen("$query") < 2) {
                return response()->json(['numeros' => []]);
            }

            // Buscar números que contengan la query (sin importar mayúsculas/minúsculas)
            $transferencias = Cwtransferencia::where('numero', 'LIKE', "%{$query}%")
                ->with(['banco', 'sucursal'])
                ->orderByRaw("CASE
                                WHEN numero = '{$query}' THEN 1
                                WHEN numero LIKE '{$query}%' THEN 2
                                ELSE 3
                            END")
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            $resultados = [];
            foreach ($transferencias as $transf) {
                $resultados[] = [
                    'id'              => $transf->id,
                    'numero'          => $transf->numero,
                    'monto'           => $transf->monto,
                    'fecha'           => $transf->fecha,
                    'fechaformat'     => $transf->fechaformat,
                    'status'          => $transf->status,
                    'titular'         => $transf->titular,
                    'currency'        => $transf->currency,
                    'banco_nombre'    => $transf->banco ? $transf->banco->descrip : null,
                    'sucursal_nombre' => $transf->sucursal ? $transf->sucursal->descrip : null,
                ];
            }

            return response()->json(['numeros' => $resultados]);

        } catch (\Exception $e) {
            \Log::error('Error en buscarNumerosSimilares: ' . $e->getMessage());
            return response()->json(['numeros' => [], 'error' => $e->getMessage()]);
        }
    }

    public function reportetransferencias(Request $request)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $moneda          = $request->moneda ?? '';
        $tipo            = $request->tipo ?? '';
        $categoria       = $request->categoria ?? '';
        $proveedor_id    = $request->proveedor_id ?? '';
        $ahorro_id       = $request->ahorro_id ?? '';
        $ordenar_por     = $request->ordenar_por ?? 'created_at';
        $orden_direccion = $request->orden_direccion ?? 'desc';
        $sucursal_filter = $request->sucursal_filter ?? 0;
        $selectbanco     = $request->selectbanco ?? 0;
        $status          = $request->status ?? '';  // Puede ser '', '0', '1', '2'
        $fechasreport    = ($request->fechasreport && strlen($request->fechasreport) > 0) ? $request->fechasreport : '';
        $busquedatransf  = $request->busquedatransf ?? '';
        $limite          = $request->limite ?? 150;

        // Procesar fechas
        $fechasaux = str_replace(' ', '', $fechasreport);
        $fec1 = $fec2 = $fecha1 = $fecha2 = '';

        if(strpos($fechasaux, "to")) {
            list($fec1, $fec2) = explode("to", $fechasaux);
        } else {
            if($fechasreport != '') {
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = "$d1/$m1/$y1";
                $fechasreport = "$fec1 to $fec2";
            }
        }

        if($fec1 != '') {
            $fecha1 = $fec1;
            $fecha2 = $fec2;

            list($d1, $m1, $y1) = explode("/", $fec1);
            list($d2, $m2, $y2) = explode("/", $fec2);

            $fec1 = "$y1-$m1-$d1";
            $fec2 = "$y2-$m2-$d2";
        }

        $transferencias = collect([]);

        // Determinar si debemos buscar - CORREGIDO
        $debeBuscar = false;

        // Verificar cada filtro individualmente
        if(($fec1 != '' && $fec2 != '')) $debeBuscar = true;
        if($status !== '' && $status !== null) $debeBuscar = true;
        if($selectbanco > 0) $debeBuscar = true;
        if($busquedatransf != '') $debeBuscar = true;
        if($moneda != '') $debeBuscar = true;
        if($tipo != '') $debeBuscar = true;
        if($categoria != '') $debeBuscar = true;
        if($sucursal_filter > 0) $debeBuscar = true;

        if($debeBuscar) {
            $query = Cwtransferencia::query()
                ->whereRaw("fksucursal in ($arraysucursales)")
                ->with(['sucursal', 'banco', 'usuarioValidador']);

            // Filtro de fechas - CORREGIDO (usar && en lugar de and)
            if($fec1 != '' && $fec2 != '') {
                $query->whereBetween('created_at', ["$fec1 00:00:00", "$fec2 23:59:59"]);
            }

            // Búsqueda por texto - CORREGIDO
            if($busquedatransf != '') {
                $busquedatransf = str_replace('*', ' ', $busquedatransf);
                $vector = explode(" ", $busquedatransf);

                $query->where(function($q) use ($vector) {
                    foreach ($vector as $item) {
                        $q->where(function($subq) use ($item) {
                            $subq->where('numero', 'like', "%$item%")
                                ->orWhere('observacion', 'like', "%$item%")
                                ->orWhere('titular', 'like', "%$item%")
                                ->orWhere('monto', 'like', "%$item%")
                                ->orWhere('referencia', 'like', "%$item%")
                                ->orWhere('categoria', 'like', "%$item%")
                                ->orWhereHas('banco', function($bq) use ($item) {
                                    $bq->where('descrip', 'like', "%$item%");
                                });
                        });
                    }
                });
            }

            // Filtro de sucursal
            if($sucursal_filter > 0) {
                $query->where('fksucursal', $sucursal_filter);
            }

            // Filtro de banco
            if($selectbanco > 0) {
                $query->where('fkbanco', $selectbanco);
            }

            // Filtro de status - CORREGIDO (ahora maneja '0' correctamente)
            if($status !== '' && $status !== null) {
                $query->where('status', $status);
            }

            // Filtro de tipo
            if($tipo !== '') {
                $query->where('tipo', $tipo);
            }

            // Filtro de categoría
            if($categoria !== '') {
                $query->where('categoria', 'like', "%$categoria%");
            }

            // Filtros adicionales
            if($proveedor_id !== '') {
                $query->where('proveedor_id', $proveedor_id);
            }

            if($ahorro_id !== '') {
                $query->where('ahorro_id', $ahorro_id);
            }

            // Filtro de moneda
            if($moneda != '') {
                switch($moneda) {
                    case 'bs':
                        $query->where('bs', 1);
                        break;
                    case 'usd':
                        $query->where('dolares', 1);
                        break;
                    case 'cop':
                        $query->where('pesos', 1);
                        break;
                }
            }

            // Ordenar y limitar
            $query->orderBy($ordenar_por, $orden_direccion);

            if($limite > 0) {
                $query->limit($limite);
            }

            $transferencias = $query->get();

        } elseif($status == '' && $busquedatransf == '' && $fechasreport == '') {
            // Si no hay filtros, mostrar solo pendientes por defecto
            $transferencias = Cwtransferencia::query()
                ->whereRaw("fksucursal in ($arraysucursales)")
                ->where('status', 0)
                ->with(['sucursal', 'banco'])
                ->orderBy($ordenar_por, $orden_direccion)
                ->limit($limite)
                ->get();

            // Asegurar que $status tenga el valor correcto para la vista
            $status = 0;
        }

        // Resto del código igual...
        $arraytransf    = [];
        $arraybstransf  = [];
        $arrayusdtransf = [];
        $arraycoptransf = [];
        $pendientes     = [];
        $aprobadas      = [];
        $rechazadas     = [];
        $bancos         = [];
        $sucursales     = [];
        $arraysucu      = [];

        // Agrupar por tipo
        $porTipo = [
            'venta'     => [],
            'pago'      => [],
            'ahorro'    => [],
            'proveedor' => [],
            'gasto'     => [],
            'otro'      => []
        ];

        foreach($transferencias as $transf) {
            $arraytransf[] = $transf;

            if(!isset($sucursales[$transf->fksucursal])) {
                $sucursales[$transf->fksucursal] = $transf->sucursal->descrip ?? 'N/A';
            }

            if($transf->status == 0) $pendientes[]     = $transf;
            if($transf->status == 1) $aprobadas[]      = $transf;
            if($transf->status == 2) $rechazadas[]     = $transf;
            if($transf->bs == 1) $arraybstransf[]  = $transf;
            if($transf->dolares == 1) $arrayusdtransf[] = $transf;
            if($transf->pesos == 1) $arraycoptransf[] = $transf;

            if(isset($porTipo[$transf->tipo])) {
                $porTipo[$transf->tipo][] = $transf;
            }

            if(!isset($bancos[$transf->fkbanco])) {
                $bancos[$transf->fkbanco] = [
                    'cant' => 0,
                    'descrip' => $transf->banco->descrip ?? 'N/A'
                ];
            }

            if(!isset($arraysucu[$transf->fksucursal])) {
                $arraysucu[$transf->fksucursal] = ['cant' => 0];
            }

            $arraysucu[$transf->fksucursal]['cant']++;
            $bancos[$transf->fkbanco]['cant']++;
        }

        $sucursales_list = Sasucursal::whereRaw("id in ($arraysucursales)")
            ->orderBy('descrip', 'asc')
            ->get();

        $allbancos = Cwbancos::whereRaw("recibetransf = 1 and activo = 1 and (bs=1 or dolares = 1 or pesos =1) ")
            ->orderBy('descrip')
            ->select('id', 'descrip')
            ->get()->toArray();

        $montoTotal = 0;
        $montoBs = 0;
        $montoUsd = 0;
        $montoCop = 0;
        foreach($arraytransf as $t) {
            $montoTotal += $t->monto;
            if($t->bs)      $montoBs  += $t->monto;
            if($t->dolares) $montoUsd += $t->monto;
            if($t->pesos)   $montoCop += $t->monto;
        }

        $listadobancos = [];
        foreach ($allbancos as $banco) {
            if(!isset($listadobancos[$banco['id']])) {
                $listadobancos[$banco['id']] = $banco['descrip'];
            }
        }

        $estadisticas = $this->calcularEstadisticas($transferencias);
        $insights = [];

        if(isset($estadisticas['generales'])) {
            $insights = $this->generarInsights($estadisticas);
        }

        return view('reporteTransferencias', compact(
            'estadisticas',
            'ordenar_por',
            'orden_direccion',
            'fechasreport',
            'bancos',
            'listadobancos',
            'status',
            'insights',
            'arraybstransf',
            'arraycoptransf',
            'arrayusdtransf',
            'arraytransf',
            'pendientes',
            'aprobadas',
            'rechazadas',
            'arraysucu',
            'sucursales',
            'transferencias',
            'fecha1',
            'fecha2',
            'selectbanco',
            'busquedatransf',
            'sucursales_list',
            'sucursal_filter',
            'limite',
            'moneda',
            'tipo',
            'categoria',
            'porTipo',
            'montoTotal',
            'montoBs',
            'montoUsd',
            'montoCop'
        ));
    }
    /**
     * Generar insights automáticos basados en las estadísticas
     */
    private function generarInsights($estadisticas)
    {
        $insights = [];

        // Mejor día
        $diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $mejorDia = array_search(max($estadisticas['dias_semana']), $estadisticas['dias_semana']);
        $insights['mejor_dia'] = [
            'nombre' => $diasSemana[$mejorDia] ?? 'N/A',
            'cantidad' => $estadisticas['dias_semana'][$mejorDia] ?? 0
        ];

        // Hora pico
        $horaPico = array_search(max($estadisticas['horas_pico']), $estadisticas['horas_pico']);
        $insights['hora_pico'] = [
            'inicio' => $horaPico,
            'fin' => ($horaPico + 1) % 24,
            'cantidad' => $estadisticas['horas_pico'][$horaPico] ?? 0
        ];

        // Bancos destacados por moneda
        $bancosDestacados = [];
        foreach (['bs', 'usd', 'cop'] as $moneda) {
            if (!empty($estadisticas['top_bancos'][$moneda])) {
                $bancosDestacados[$moneda] = $estadisticas['top_bancos'][$moneda][0];

                // Buscar el de mejor tasa de aprobación (mínimo 3 transferencias)
                $mejorTasa = null;
                $maxTasa = 0;
                foreach ($estadisticas['top_bancos'][$moneda] as $banco) {
                    $tasa = $banco['cantidad'] > 0 ? ($banco['aprobadas'] / $banco['cantidad'] * 100) : 0;
                    if ($tasa > $maxTasa and $banco['cantidad'] >= 3) {
                        $maxTasa = $tasa;
                        $mejorTasa = $banco;
                    }
                }
                if ($mejorTasa) {
                    $bancosDestacados[$moneda . '_mejor_tasa'] = $mejorTasa;
                }
            }
        }
        $insights['bancos_destacados'] = $bancosDestacados;

        // Métricas clave
        $insights['metricas'] = [
            'total' => $estadisticas['generales']['total'],
            'aprobadas' => $estadisticas['generales']['aprobadas'],
            'tasa_aprobacion' => $estadisticas['generales']['tasa_aprobacion'],
            'proyeccion' => $estadisticas['proyecciones']['proyeccion_mensual'],
            'dias_para_meta' => $estadisticas['proyecciones']['dias_para_meta'],
        ];

        return $insights;
    }

    /**
     * Calcular tiempo promedio de aprobación
     */
    public function calcularTiempoPromedioAprobacion($transferencias)
    {
        $aprobadas = $transferencias->where('status', 1)->filter(function($t) {
            return $t->created_at and $t->fecha;
        });

        if ($aprobadas->isEmpty()) return 0;

        $totalHoras = 0;
        foreach ($aprobadas as $t) {
            $creacion = Carbon::parse($t->created_at);
            $fechaTransf = Carbon::parse($t->fecha);
            $totalHoras += $creacion->diffInHours($fechaTransf);
        }
        return round($totalHoras / $aprobadas->count(), 1);
    }

    /**
     * Obtener tendencia diaria
     */
    function obtenerTendenciaDiaria($transferencias)
    {
        $tendencia = [];
        foreach ($transferencias as $t) {
            $fecha = $t->created_at->format('Y-m-d');
            if (!isset($tendencia[$fecha])) {
                $tendencia[$fecha] = ['cantidad' => 0, 'monto' => 0, 'aprobadas' => 0];
            }
            $tendencia[$fecha]['cantidad']++;
            $tendencia[$fecha]['monto'] += $t->monto;
            if ($t->status == 1) $tendencia[$fecha]['aprobadas']++;
        }
        ksort($tendencia);
        return array_slice($tendencia, -30);
    }

    /**
     * Obtener tendencia semanal
     */
    function obtenerTendenciaSemanal($transferencias)
    {
        $tendencia = [];
        foreach ($transferencias as $t) {
            $semana = $t->created_at->format('Y-W');
            if (!isset($tendencia[$semana])) {
                $tendencia[$semana] = [
                    'cantidad' => 0,
                    'monto' => 0,
                    'semana_inicio' => $t->created_at->copy()->startOfWeek()->format('d/m')
                ];
            }
            $tendencia[$semana]['cantidad']++;
            $tendencia[$semana]['monto'] += $t->monto;
        }
        return array_slice($tendencia, -12);
    }

    /**
     * Obtener tendencia mensual
     */
    function obtenerTendenciaMensual($transferencias)
    {
        $tendencia = [];
        foreach ($transferencias as $t) {
            $mes = $t->created_at->format('Y-m');
            if (!isset($tendencia[$mes])) {
                $tendencia[$mes] = [
                    'cantidad' => 0,
                    'monto' => 0,
                    'mes_nombre' => $t->created_at->format('M Y')
                ];
            }
            $tendencia[$mes]['cantidad']++;
            $tendencia[$mes]['monto'] += $t->monto;
        }
        return array_slice($tendencia, -12);
    }

    /**
     * Obtener top bancos
     */
    function obtenerTopBancos($transferencias, $limite)
    {
        $bancos = [
            'bs'  => [],
            'usd' => [],
            'cop' => []
        ];

        foreach ($transferencias as $t) {
            $id = $t->fkbanco;
            $moneda = $t->bs ? 'bs' : ($t->dolares ? 'usd' : 'cop');

            if (!isset($bancos[$moneda][$id])) {
                $bancos[$moneda][$id] = [
                    'id' => $id,
                    'nombre' => $t->banco->descrip ?? 'Desconocido',
                    'cantidad' => 0,
                    'monto' => 0,
                    'aprobadas' => 0,
                    'moneda' => $moneda,
                    'simbolo' => $t->currency
                ];
            }

            $bancos[$moneda][$id]['cantidad']++;
            $bancos[$moneda][$id]['monto'] += $t->monto;
            if ($t->status == 1) {
                $bancos[$moneda][$id]['aprobadas']++;
            }
        }

        // Ordenar cada grupo por monto descendente
        foreach (['bs', 'usd', 'cop'] as $moneda) {
            uasort($bancos[$moneda], fn($a, $b) => $b['monto'] <=> $a['monto']);
            $bancos[$moneda] = array_slice($bancos[$moneda], 0, $limite);
        }

        return $bancos;
    }

    /**
     * Obtener top sucursales
     */
    function obtenerTopSucursales($transferencias, $limite)
    {
        $sucursales = [
            'bs' => [],
            'usd' => [],
            'cop' => []
        ];

        foreach ($transferencias as $t) {
            $id = $t->fksucursal;
            $moneda = $t->bs ? 'bs' : ($t->dolares ? 'usd' : 'cop');

            if (!isset($sucursales[$moneda][$id])) {
                $sucursales[$moneda][$id] = [
                    'id' => $id,
                    'nombre' => $t->sucursal->descrip ?? 'Desconocido',
                    'cantidad' => 0,
                    'monto' => 0,
                    'aprobadas' => 0,
                    'moneda' => $moneda,
                    'simbolo' => $t->currency
                ];
            }

            $sucursales[$moneda][$id]['cantidad']++;
            $sucursales[$moneda][$id]['monto'] += $t->monto;
            if ($t->status == 1) {
                $sucursales[$moneda][$id]['aprobadas']++;
            }
        }

        // Ordenar cada grupo por monto descendente
        foreach (['bs', 'usd', 'cop'] as $moneda) {
            uasort($sucursales[$moneda], fn($a, $b) => $b['monto'] <=> $a['monto']);
            $sucursales[$moneda] = array_slice($sucursales[$moneda], 0, $limite);
        }

        return $sucursales;
    }

    /**
     * Obtener top clientes
     */
    function obtenerTopClientes($transferencias, $limite)
    {
        $clientes = [
            'bs' => [],
            'usd' => [],
            'cop' => []
        ];

        foreach ($transferencias as $t) {
            $nombre = $t->titular;
            $moneda = $t->bs ? 'bs' : ($t->dolares ? 'usd' : 'cop');

            if (!isset($clientes[$moneda][$nombre])) {
                $clientes[$moneda][$nombre] = [
                    'titular' => $nombre,
                    'cantidad' => 0,
                    'monto' => 0,
                    'aprobadas' => 0,
                    'moneda' => $moneda,
                    'simbolo' => $t->currency
                ];
            }

            $clientes[$moneda][$nombre]['cantidad']++;
            $clientes[$moneda][$nombre]['monto'] += $t->monto;
            if ($t->status == 1) {
                $clientes[$moneda][$nombre]['aprobadas']++;
            }
        }

        // Ordenar cada grupo por monto descendente
        foreach (['bs', 'usd', 'cop'] as $moneda) {
            uasort($clientes[$moneda], fn($a, $b) => $b['monto'] <=> $a['monto']);
            $clientes[$moneda] = array_slice($clientes[$moneda], 0, $limite);
        }

        return $clientes;
    }
    /**
     * Obtener horas pico
     */
    function obtenerHorasPico($transferencias)
    {
        $horas = array_fill(0, 24, 0);
        foreach ($transferencias as $t) {
            $hora = (int)$t->created_at->format('H');
            $horas[$hora]++;
        }
        return $horas;
    }

    /**
     * Obtener distribución por día de semana
     */
    function obtenerDistribucionPorDiaSemana($transferencias)
    {
        $distribucion = array_fill(0, 7, 0);
        foreach ($transferencias as $t) {
            $dia = (int)$t->created_at->format('w');
            $distribucion[$dia]++;
        }
        return $distribucion;
    }

    /**
     * Calcular proyección mensual
     */
    function calcularProyeccionMensual($transferencias)
    {
        if ($transferencias->isEmpty()) return 0;

        $primera = $transferencias->first();
        $ultima = $transferencias->last();

        $diasTranscurridos = Carbon::parse($primera->created_at)->diffInDays(Carbon::parse($ultima->created_at)) ?: 1;
        $promedioDiario = $transferencias->count() / $diasTranscurridos;
        $diasRestantes = Carbon::now()->endOfMonth()->diffInDays(Carbon::now());

        return round($promedioDiario * ($diasTranscurridos + $diasRestantes));
    }

    /**
     * Calcular días para alcanzar la meta
     */
    function calcularDiasParaMeta($transferencias)
    {
        if ($transferencias->isEmpty()) return 0;

        $aprobadas = $transferencias->where('status', 1)->count();
        $total = $transferencias->count();
        $tasaActual = ($aprobadas / $total) * 100;
        $meta = 80;

        if ($tasaActual >= $meta) return 0;

        $necesarias = ceil(($meta * $total / 100) - $aprobadas);
        $promedioDiario = $transferencias->count() / 30 ?: 1;

        return ceil($necesarias / $promedioDiario);
    }

    public function validar($id)
    {
        if($id != '') {
            $hashid = Hashids::connection(Cwtransferencia::class)->decode($id)[0] ?? null;
            if (!$hashid) {
                return redirect()->route('login');
            }
            $transf = Cwtransferencia::with('banco')->find($hashid);
            if(isset($transf)){
                if($transf->status < 1) {
                    return view('transferencias-validar', compact('transf'));
                } else {
                    return view('transferencias-validada', compact('transf'));
                }
            }
        }
        return redirect()->route('login');
    }

    public function cambiarstatus(Request $request)
    {
        $id = $request->id;
        $va = $request->va;
        $comentario = $request->comentario ?? '';

        $hashid = Hashids::connection(Cwtransferencia::class)->decode($id)[0] ?? null;
        if (!$hashid) {
            return response()->json(['cambiado' => 0]);
        }

        $transf = Cwtransferencia::find($hashid);
        if($transf and $transf->status < 1){
            $transf->status = $va;
            $transf->comentario_validacion = $comentario;
            $transf->fecha_validacion = now();
            $transf->usuario_valida = auth()->id();
            $transf->save();

            return response()->json(['cambiado' => 1]);
        }

        return response()->json(['cambiado' => 0]);
    }

    public function create()
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);
        $sucursales = Sasucursal::whereRaw("id in ($arraysucursales)")->orderBy('descrip', 'asc')->get();

        $bancos = Cwbancos::whereRaw("fksucursal in ($arraysucursales)")->orderBy('descrip')->get();
        return view('transferencias-create', compact('sucursales', 'bancos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha'         => 'required',
            'numero'        => 'required|string|max:50',
            'titular'       => 'required|string|max:120',
            'monto'         => 'required|numeric|min:0.01',
            'bancosucursal' => 'required|exists:cwbancos,id',
            'fksucursal'    => 'required|exists:sasucursal,id',
            'imagen'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Aumentado a 10MB
            'tipo'          => 'required|in:venta,pago,ahorro,proveedor,gasto,otro',
            'categoria'     => 'nullable|string|max:100',
            'referencia'    => 'nullable|string|max:100',
            'proveedor_id'  => 'nullable|integer',
            'ahorro_id'     => 'nullable|integer'
        ]);

        $fecha = $request->fecha;
        list($d, $m, $y) = explode('/', $fecha);

        $bancosel = Cwbancos::find($request->bancosucursal);

        // Verificar si ya existe
        $busqueda = Cwtransferencia::where('numero', $request->numero);

        if ($bancosel) {
            if ($bancosel->bs == 1) $busqueda->where('bs', 1);
            if ($bancosel->pesos == 1) $busqueda->where('pesos', 1);
            if ($bancosel->dolares == 1) $busqueda->where('dolares', 1);
        }

        $existente = $busqueda->first();

        if ($existente) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ya existe una transferencia con el número ' . $request->numero);
        }

        $new = new Cwtransferencia();

        $new->fecha = "$y-$m-$d";
        $new->numero = strtoupper($request->numero);
        $new->observacion = strtoupper($request->observacion ?? '');
        $new->titular = strtoupper($request->titular);
        $new->monto = $request->monto;
        $new->fkbanco = $request->bancosucursal;
        $new->fksucursal = $request->fksucursal;
        $new->fk_usuario = auth()->id();
        $new->status = 0;

        // Nuevos campos
        $new->tipo = $request->tipo;
        $new->categoria = $request->categoria;
        $new->referencia = $request->referencia;
        $new->proveedor_id = $request->proveedor_id;
        $new->ahorro_id = $request->ahorro_id;

        // Procesar imagen con compresión
        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen');
            $extension = strtolower($imagen->getClientOriginalExtension());
            $nombre_imagen = time() . '_' . uniqid() . '.' . $extension;

            $uploadPath = public_path('uploads/transferencias');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $rutaCompleta = $uploadPath . '/' . $nombre_imagen;

            // Comprimir la imagen antes de guardar
            $this->comprimirImagenTransferencia($imagen->getPathname(), $rutaCompleta, $extension);

            $new->imagen = 'uploads/transferencias/' . $nombre_imagen;
            $new->imagen_original = $imagen->getClientOriginalName();
        }

        if ($bancosel) {
            if ($bancosel->bs == 1) $new->bs = 1;
            if ($bancosel->pesos == 1) $new->pesos = 1;
            if ($bancosel->dolares == 1) $new->dolares = 1;
        }

        try {
            $new->save();
            return redirect()->route('reportetransferencias')
                ->with('success', 'Transferencia creada correctamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    /**
     * Comprimir imagen de transferencia antes de guardar
     */
    private function comprimirImagenTransferencia($rutaOrigen, $rutaDestino, $extension)
    {
        try {
            // Usar ImageManagerStatic de Intervention Image 2.x
            $img = Image::make($rutaOrigen);

            // Obtener dimensiones
            $anchoOriginal = $img->width();
            $altoOriginal  = $img->height();

            // Calcular nuevas dimensiones (máximo 1920px)
            $maxLado = 1920;

            if ($anchoOriginal > $maxLado || $altoOriginal > $maxLado) {
                $img->resize($maxLado, $maxLado, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Guardar con calidad 75%
            $img->save($rutaDestino, 75);

        } catch (\Exception $e) {
            \Log::warning('Error al comprimir imagen: ' . $e->getMessage());
            copy($rutaOrigen, $rutaDestino);
        }
    }


    public function getCategorias(Request $request)
    {
        $query = $request->q;
        $categoriasQuery = Cwtransferencia::
        whereNotNull('categoria')
            ->where('categoria', 'LIKE', "%{$query}%")
            ->select('categoria')
            ->distinct()
            ->orderBy('categoria')
            ->limit(10)
            ->pluck('categoria');

        return response()->json($categoriasQuery);

    }

    public function pendienteAgain(Request $request)
    {
        $idtran = $request->id ?? 0;
        $transf = Cwtransferencia::find($idtran);

        if($transf and $transf->id > 0) {
            $transf->status = 0;
            $transf->save();
            return response()->json(['error' => 1]);
        }
        return response()->json(['error' => 0]);
    }

    public function verificar(Request $request)
    {
        try {
            $numero = $request->numero;
            $fkbanco = $request->fkbanco;

            if (!$numero or !$fkbanco) {
                return response()->json(['valid' => 1]);
            }

            $banco = Cwbancos::find($fkbanco);
            $query = Cwtransferencia::where('numero', $numero);

            if ($banco) {
                if ($banco->bs == 1) $query->where('bs', 1);
                if ($banco->pesos == 1) $query->where('pesos', 1);
                if ($banco->dolares == 1) $query->where('dolares', 1);
            }

            $existente = $query->first();
            return response()->json(['valid' => $existente ? 0 : 1]);

        } catch (\Exception $e) {
            return response()->json(['valid' => 1, 'error' => $e->getMessage()]);
        }
    }

    public function filtrarstatus(Request $request, $status)
    {
        $fechas = $request->fechas ?? '';
        $busquedatransf = $request->busquedatransf ?? '';
        $status = $request->status ?? '';

        return view('transferencias', compact('status', 'fechas', 'busquedatransf'));
    }

    public function informacion(Request $request)
    {
        $urlencode = '';
        $options = $request->options;

        if($options) {
            list($id, $fksucursal) = explode("-", $options);
            $banco = Cwbancos::where(['id' => $id, 'fksucursal' => $fksucursal])->first();
            if ($banco) {
                $urlencode = urlencode($banco->texto ?? '');
            }
        }

        $telefono = $request->telefono;
        $error = 0;
        $listado = Cwbancos::whereRaw("texto <> ''")->get();

        if (!preg_match('/^\+?[0-9]{12}$/i', $telefono)) {
            $error = 1;
        }

        return view('transf-send', compact('listado', 'telefono', 'error', 'options', 'urlencode'));
    }

    public function apiStatus(Request $request)
    {
        $array = $request->array;
        $array = json_decode($array);
        $descargadas = 0;

        if (isset($array)) {
            foreach ($array as $item) {
                $aux = Cwtransferencia::find($item->id);
                if ($aux) {
                    $aux->descargada = 1;
                    $aux->save();
                    $descargadas++;
                }
            }
        }

        if ($descargadas > 0) {
            return response()->json(['success' => 'success']);
        }
        return response()->json(['error' => '1']);
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        $transf = Cwtransferencia::find($id);

        if (!$transf) {
            return response()->json(['deleted' => 0, 'error' => 'Transferencia no encontrada']);
        }

        try {
            // Eliminar la imagen física si existe
            if ($transf->imagen) {
                $rutaImagen = public_path($transf->imagen);
                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen);
                }
            }

            $transf->delete();
            return response()->json(['deleted' => 1]);

        } catch (\Exception $e) {
            return response()->json(['deleted' => 0, 'error' => $e->getMessage()]);
        }
    }
}
