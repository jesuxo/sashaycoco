<?php

namespace App\Http\Controllers;

use App\Models\Saacxc;
use App\Models\Safact;
use App\Models\Sapagcxc;
use App\Models\Saipavta;
use App\Models\Saitemfac;
use App\Models\Saprod;
use App\Models\Saseprfac;
use App\Models\Sasucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SafactController extends Controller
{
    public function buscarFacturaPorNumero($tipo, $numero)
    {
        $comercial = session('comercialid');

        // Obtener las sucursales del comercial
        $sucursales = Sasucursal::where('fk_comercial', $comercial)
            ->orderBy('descrip')
            ->get();

        $busqueda = $numero;
        $busqueda = str_replace("\"", "", $busqueda);
        $busqueda = str_replace("'", "", $busqueda);
        $busqueda = str_replace("*", " ", $busqueda);
        $vector = explode(" ", $busqueda);

        $cadena   = '';

        if ($vector) {
            $numerito = 0;

            foreach ($vector as $value) {
                if ($numerito > 0) {
                    $cadena  .= ' AND ';
                }
                $cadena  .= "(safact.numerod like '%$value%' or safact.descrip like '%$value%' or safact.id3 like '%$value%' or safact.codclie like '%$value%')";
                $numerito++;
            }
        }

        try {
            $results = [];

            // Buscar en todas las sucursales
            $documentos = DB::table('safact')
                ->select(
                    'safact.NumeroD',
                    'safact.fechat',
                    'safact.descrip as cliente_nombre',
                    'safact.id3',
                    'safact.fk_sucursal',
                    'safact.contado',
                    'safact.numeror',
                    'safact.codclie',
                    'safact.credito',
                    'safact.mtototal',
                    'safact.tasa_dolar',
                    'safact.TipoFac',
                    DB::raw('ROUND((safact.contado + safact.credito) / safact.tasa_dolar, 2) as total'),
                    'sasucursal.descrip as sucursal_nombre'
                )
                ->join('sasucursal', 'safact.fk_sucursal', '=', 'sasucursal.id')
                ->where('safact.TipoFac', $tipo)
                ->where('safact.tasa_dolar','>',0)
                ->whereRaw($cadena)
                ->whereIn('safact.fk_sucursal',$sucursales->pluck('id'))
                ->orderBy('safact.nrounico','desc')
                ->limit(50)
                ->get();

            // Si hay múltiples resultados, preparar la lista
            if ($documentos->count() > 0) {
                foreach ($documentos as $doc) {
                    // Formatear fecha
                    $fecha = $doc->fechat ? date('d/m/Y', strtotime($doc->fechat)) : 'Fecha no disponible';
                    $hora = $doc->fechat ? date('h:i A', strtotime($doc->fechat)) : '';

                    $results[] = [
                        'tipo'            => $doc->TipoFac,
                        'numerod'         => $doc->NumeroD,
                        'sucursal_id'     => $doc->fk_sucursal,
                        'numeror'         => $doc->numeror,
                        'sucursal_nombre' => $doc->sucursal_nombre,
                        'credito'         => $doc->credito,
                        'fecha'           => $fecha,
                        'hora'            => $hora,
                        'cliente_nombre'  => $doc->cliente_nombre,
                        'codclie'         => $doc->codclie,
                        'total'           => $doc->total
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'results' => $results,
                'count' => count($results)
            ]);

        } catch (\Exception $e) {
            \Log::error('Error buscando factura: ' . $e->getMessage() . ' en línea: ' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar la factura: ' . $e->getMessage()
            ], 500);
        }
    }

    public function documentoSafact(Request $request)
    {
        $ajax    = 0;
        $tipofac = $request->tipofac;
        $numerod = $request->numerod;
        $fk_sucu = $request->fksucu;

        $documentoId = Safact::where('NumeroD', $numerod)
            ->where('TipoFac', $tipofac)
            ->where('fk_sucursal', $fk_sucu)
            ->value('id');

        if (!$documentoId) {
            abort(404, 'Documento no encontrado');
        }

        // Obtener el documento con sus relaciones filtradas correctamente
        $documento = Safact::selectRaw("
            nrounico,
            date_format(fechat, '%d/%m/%Y') as fecha,
            date_format(fechat, '%h:%i %p') as hora,
            notas1, notas2, notas3, safact.NumeroD, safact.TipoFac, safact.fk_sucursal,
            cancelausd, codesta, descrip, id3, dolares, pesos,
            (cancele - efectivosumado) as cancele,
            (cancelt - tarjetasumado) as cancelt,
            vuelto_cancele, vuelto_dolares, vuelto_pesos,
            dolar_transf as transf,
            (credito/tasa_dolar) as credito,
            (contado/tasa_dolar) as contado,
            tasa_dolar, tasa_peso,
            texento, mtotax, tgravable, mtototal,
            Signo, numeror, credendolar, euros, peso_tranf,
            igtf_monto, igtf_cancele, igtf_cancelt, igtf_dolares, igtf_pesos,
            porcdesconado
        ")
            ->with(['sucursal'])
            ->where('id', $documentoId)
            ->first();

        // Cargar items con condición exacta
        $documento->load(['items' => function($query) use ($tipofac, $numerod, $fk_sucu) {
            $query->where('TipoFac', $tipofac)
                ->where('NumeroD', $numerod)
                ->where('fk_sucursal', $fk_sucu)
                ->with(['producto.instancia']);
        }]);

        // Cargar seriales con condición exacta
        $documento->load(['seriales' => function($query) use ($tipofac, $numerod, $fk_sucu) {
            $query->where('TipoFac', $tipofac)
                ->where('NumeroD', $numerod)
                ->where('fk_sucursal', $fk_sucu);
        }]);

        // ========== OBTENER FACTURA ANTERIOR Y SIGUIENTE ==========
        $anterior  = null;
        $siguiente = null;
        $nrounicod = $documento->nrounico;

        if ($documento) {
            $anterior = Safact::where('TipoFac', $tipofac)
                ->where('fk_sucursal', $fk_sucu)
                ->where('nrounico', '<', $nrounicod)
                ->orderBy('nrounico', 'desc')
                ->value('NumeroD');

            $siguiente = Safact::where('TipoFac', $tipofac)
                ->where('fk_sucursal', $fk_sucu)
                ->where('nrounico', '>', $nrounicod)
                ->orderBy('nrounico', 'asc')
                ->value('NumeroD');
        }

        // Instrumentos de pago
        $instpago = [];
        if ($documento && ($documento->cancelt > 0 || $documento->transf > 0 || $documento->dolares > 0 || $documento->pesos > 0)) {
            $instpago = Saipavta::with('satarj')
                ->where('NumeroD', $numerod)
                ->where('TipoFac', $tipofac)
                ->where('fk_sucursal', $fk_sucu)
                ->get();
        }

        // Descuentos a crédito (tipocxc = 31)
        $descuentosCredito = [];
        if ($documento && $documento->credito > 0) {
            $descuentosCredito = Saacxc::selectRaw("
                id, NroUnico, NumeroD, Document, NumeroN,
                date_format(FechaT, '%d/%m/%Y') as fecha,
                (montodolares) as monto_usd,
                (montodolares * tasadolar) as monto_bs,
                tasadolar, notas1, TipoCxc
            ")
                ->where('NumeroN', $numerod)
                ->where('NumeroD', '<>', $numerod)
                ->where('fk_sucursal', $fk_sucu)
                ->where('TipoCxc', 31)
                ->orderBy('FechaT', 'desc')
                ->get();
        }

        // Pagos realizados (tipocxc = 41)
        $pagosAfectados = [];
        if ($documento && $documento->credito > 0) {
            $pagosAfectados = Sapagcxc::selectRaw("
                sapagcxc.id, sapagcxc.NroPpal, sapagcxc.NumeroD, sapagcxc.Monto as monto_bs,
                sapagcxc.montodolar as monto_usd,
                sapagcxc.Descrip, sapagcxc.FechaE,
                a.Document, a.Notas1,
                date_format(sapagcxc.created_at, '%d/%m/%Y') as fecha
            ")
                ->join('saacxc as a', 'a.NroUnico', '=', 'sapagcxc.NroPpal')
                ->where('sapagcxc.NumeroD', $numerod)
                ->where('a.NumeroD', '<>', $numerod)
                ->where('sapagcxc.fk_sucursal', $fk_sucu)
                ->where('a.fk_sucursal', $fk_sucu)
                ->where('a.TipoCxc', 41)
                ->orderBy('sapagcxc.created_at', 'desc')
                ->get();
        }

        // Devoluciones relacionadas (solo para facturas tipo A)
        $devoluciones = [];
        if ($tipofac == 'A'  ) {
            $devoluciones = Safact::selectRaw("
                NumeroD, date_format(fechat, '%d/%m/%Y') as fecha,
                (mtototal/tasa_dolar) as monto_usd, numeror, fk_sucursal, TipoFac,
                notas1
            ")
                ->where('numeror', $numerod)
                ->where('TipoFac', 'B')
                ->where('fk_sucursal', $fk_sucu)
                ->orderBy('fechat', 'asc')
                ->get();
        }

        if ( $tipofac == 'Z') {
            $devoluciones = Safact::selectRaw("
                NumeroD, date_format(fechat, '%d/%m/%Y') as fecha,
                (mtototal/tasa_dolar) as monto_usd, numeror, fk_sucursal, TipoFac,
                notas1
            ")
                ->where('numeror', $numerod)
                ->where('TipoFac', 'W')
                ->where('fk_sucursal', $fk_sucu)
                ->orderBy('fechat', 'asc')
                ->get();
        }


        // Si es devolución, obtener factura original
        $facturaOriginal = null;
        if (($tipofac == 'B' ) && $documento && $documento->numeror) {
            $facturaOriginal = Safact::selectRaw("
                NumeroD, date_format(fechat, '%d/%m/%Y') as fecha,
                (mtototal/tasa_dolar) as monto_usd,
                descrip, id3
            ")
                ->where('NumeroD', $documento->numeror)
                ->where('TipoFac', 'A')
                ->where('fk_sucursal', $fk_sucu)
                ->first();
        }

        if ((  $tipofac == 'W') && $documento && $documento->numeror) {
            $facturaOriginal = Safact::selectRaw("
                NumeroD, date_format(fechat, '%d/%m/%Y') as fecha,
                (mtototal/tasa_dolar) as monto_usd,
                descrip, id3
            ")
                ->where('NumeroD', $documento->numeror)
                ->where('TipoFac', 'Z')
                ->where('fk_sucursal', $fk_sucu)
                ->first();
        }

        // Debug: Verificar si hay items duplicados (opcional, remover en producción)
       /* if ($documento && $documento->items) {
            \Log::info("Documento {$tipofac}-{$numerod} tiene " . $documento->items->count() . " items");
            foreach ($documento->items as $item) {
                \Log::info("Item: TipoFac={$item->TipoFac}, NumeroD={$item->NumeroD}, Producto={$item->CodProd}");
            }
        }*/

        return view('documentoventa', compact(
            'ajax', 'instpago', 'numerod', 'tipofac', 'documento',
            'descuentosCredito', 'pagosAfectados', 'devoluciones', 'facturaOriginal',
            'anterior', 'siguiente'
        ));
    }

    public function buscarFactura(Request $request)
    {
        $tipofac = $request->tipofac;
        $numerod = $request->numerod;
        $fk_sucu = $request->fksucu;

        // Validar que el número no esté vacío
        if (empty($numerod)) {
            return redirect()->back()->with('error', 'Por favor ingrese un número de factura');
        }

        // Verificar si la factura existe
        $existe = Safact::where('NumeroD', $numerod)
            ->where('TipoFac', $tipofac)
            ->where('fk_sucursal', $fk_sucu)
            ->exists();

        if ($existe) {
            return redirect()->route('facturaver', [
                'tipofac' => $tipofac,
                'numerod' => $numerod,
                'fksucu' => $fk_sucu
            ]);
        }

        return redirect()->back()->with('error', "Factura N° {$numerod} no encontrada");
    }

    public function documentoAjax(Request $request)
    {
        $ajax    = 1;
        $tipofac = $request->tipofac;
        $numerod = $request->numerod;
        $fk_sucu = $request->fksucu;

        $documentoId = Safact::where('NumeroD', $numerod)
            ->where('TipoFac', $tipofac)
            ->where('fk_sucursal', $fk_sucu)
            ->value('id');

        $documento = Safact::selectRaw("
                date_format(fechat, '%d/%m/%Y') as fecha,
                date_format(fechat, '%h:%i %p') as hora,
                notas1, notas2, notas3, safact.NumeroD, safact.TipoFac, safact.fk_sucursal,
                cancelausd, codesta, descrip, id3, dolares, pesos,
                (cancele - efectivosumado) as cancele,
                (cancelt - tarjetasumado) as cancelt,
                vuelto_cancele, vuelto_dolares, vuelto_pesos,
                dolar_transf as transf,
                (credito/tasa_dolar) as credito,
                (contado/tasa_dolar) as contado,
                tasa_dolar, tasa_peso,
                texento, mtotax, tgravable, mtototal,
                Signo, numeror, credendolar, euros, peso_tranf,
                igtf_monto, igtf_cancele, igtf_cancelt, igtf_dolares, igtf_pesos,
                porcdesconado
            ")
            ->with(['items.producto.instancia', 'sucursal'])
            ->whereHas('items', function ($q) use ($fk_sucu, $tipofac, $numerod) {
                $q->whereRaw("saitemfac.TipoFac = '$tipofac' and saitemfac.NumeroD='$numerod' and saitemfac.fk_sucursal = $fk_sucu");
            })
            ->where('id', $documentoId)
            ->first();

        // ========== OBTENER FACTURA ANTERIOR Y SIGUIENTE (OPTIMIZADO) ==========
        $anterior = null;
        $siguiente = null;

        if ($documento) {
            $anterior = Safact::where('TipoFac', $tipofac)
                ->where('fk_sucursal', $fk_sucu)
                ->where('NumeroD', '<', $numerod)
                ->orderBy('NumeroD', 'desc')
                ->value('NumeroD');

            $siguiente = Safact::where('TipoFac', $tipofac)
                ->where('fk_sucursal', $fk_sucu)
                ->where('NumeroD', '>', $numerod)
                ->orderBy('NumeroD', 'asc')
                ->value('NumeroD');
        }
        // ========== FIN OPTIMIZADO ==========

        $instpago = [];
        if ($documento and ($documento->cancelt > 0 or $documento->transf > 0 or $documento->dolares > 0 or $documento->pesos > 0)) {
            $instpago = Saipavta::with('satarj')
                ->where([
                    'NumeroD' => $numerod,
                    'TipoFac' => $tipofac,
                    'fk_sucursal' => $fk_sucu
                ])
                ->get();
        }

        $descuentosCredito = [];
        if ($documento and $documento->credito > 0) {
            $descuentosCredito = Saacxc::selectRaw("
                    id, NroUnico, NumeroD, Document, NumeroN,
                    date_format(FechaT, '%d/%m/%Y') as fecha,
                    (montodolares) as monto_usd,
                    (montodolares * tasadolar) as monto_bs,
                    tasadolar, notas1, TipoCxc
                ")
                ->where('NumeroN', $numerod)
                ->where('NumeroD', '<>', $numerod)
                ->where('fk_sucursal', $fk_sucu)
                ->where('TipoCxc', 31)
                ->orderBy('FechaT', 'desc')
                ->get();
        }

        $pagosAfectados = [];
        if ($documento and $documento->credito > 0) {
            $pagosAfectados = Sapagcxc::selectRaw("
                    sapagcxc.id, sapagcxc.NroPpal, sapagcxc.NumeroD, sapagcxc.Monto as monto_bs,
                    sapagcxc.montodolar as monto_usd,
                    sapagcxc.Descrip, sapagcxc.FechaE,
                    a.Document, a.Notas1,
                    date_format(sapagcxc.fechae, '%d/%m/%Y') as fecha
                ")
                ->join('saacxc as a', 'a.NroUnico', '=', 'sapagcxc.NroPpal')
                ->where('sapagcxc.NumeroD', $numerod)
                ->where('a.NumeroD','<>', $numerod)
                ->where('sapagcxc.fk_sucursal', $fk_sucu)
                ->where('a.fk_sucursal', $fk_sucu)
                ->where('a.TipoCxc', 41)
                ->orderBy('sapagcxc.fechae', 'desc')
                ->get();
        }

        $devoluciones = [];
        if ($tipofac == 'A') {
            $devoluciones = Safact::selectRaw("
                    NumeroD, date_format(fechat, '%d/%m/%Y') as fecha,
                    (mtototal/tasa_dolar) as monto_usd, numeror, fk_sucursal, TipoFac,
                    notas1
                ")
                ->where('numeror'    , $numerod)
                ->where('TipoFac'    , 'B')
                ->where('fk_sucursal', $fk_sucu)
                ->orderBy('fechat'   , 'desc')
                ->get();
        }

        if ($tipofac == 'Z') {
            $devoluciones = Safact::selectRaw("
                    NumeroD, date_format(fechat, '%d/%m/%Y') as fecha,
                    (mtototal/tasa_dolar) as monto_usd, numeror, fk_sucursal, TipoFac,
                    notas1
                ")
                ->where('numeror'    , $numerod)
                ->where('TipoFac'    , 'W')
                ->where('fk_sucursal', $fk_sucu)
                ->orderBy('fechat'   , 'desc')
                ->get();
        }

        $facturaOriginal = null;
        if ($tipofac == 'B' and $documento and $documento->numeror) {
            $facturaOriginal = Safact::selectRaw("
                    NumeroD, date_format(fechat, '%d/%m/%Y') as fecha,
                    (mtototal/tasa_dolar) as monto_usd,
                    descrip, id3
                ")
                ->where('NumeroD', $documento->numeror)
                ->where('TipoFac', 'A')
                ->where('fk_sucursal', $fk_sucu)
                ->first();
        }

        if ($tipofac == 'W' and $documento and $documento->numeror) {
            $facturaOriginal = Safact::selectRaw("
                    NumeroD, date_format(fechat, '%d/%m/%Y') as fecha,
                    (mtototal/tasa_dolar) as monto_usd,
                    descrip, id3
                ")
                ->where('NumeroD', $documento->numeror)
                ->where('TipoFac', 'Z')
                ->where('fk_sucursal', $fk_sucu)
                ->first();
        }

        return view('layouts.documento', compact(
            'ajax', 'instpago', 'numerod', 'tipofac', 'documento',
            'descuentosCredito', 'pagosAfectados', 'devoluciones', 'facturaOriginal',
            'anterior', 'siguiente'
        ))->render();
    }

    public function documento(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $facturas = $request->facturas;
        $facturas = json_decode($facturas);
        $vector   = [];

        if(isset($facturas)){
            foreach ($facturas as $fac){
                if(isset($fac->nrounico)){
                    $record = Safact::where([
                        'TipoFac'=>  $fac->TipoFac,
                        'NumeroD'=>  $fac->NumeroD,
                        'fk_sucursal'=> $sucursalid])->first();

                    $additems = 1;
                    $aux = (array) $fac;

                    if(isset($record) and isset($record->id) and $record->id > 0){
                        $record = Safact::find($record->id);

                    }else{
                        $record = new Safact();
                    }

                    $record->fill($aux) ;
                    $additems = 1;

                    $record->fk_sucursal = $sucursalid ;

                    if($additems and isset($fac->allitems)){

                        if ($fac->NumeroD) {
                            $chunkSize = 1000;
                            Saitemfac::where([
                                'TipoFac' => $fac->TipoFac,
                                'NumeroD' => $fac->NumeroD,
                                'fk_sucursal' => $sucursalid
                            ])->chunkById($chunkSize, function ($records) {
                                $records->each->delete();
                            });
                        }


                        $itemsData = [];

                        foreach ($fac->allitems as $allitem){
                            $newitem = new Saitemfac();
                            $auxitem = (array) $allitem;
                            $newitem->fill($auxitem);
                            $newitem->fk_sucursal = $sucursalid ;
                            $newitem->FechaE = $fac->FechaE;
                            $newitem->save();
                        }

                    }

                    if($additems and isset($fac->tarjetas)){

                        if ($fac->NumeroD) {
                            $chunkSize = 1000;
                            Saipavta::where([
                                'TipoFac' => $fac->TipoFac,
                                'NumeroD' => $fac->NumeroD,
                                'fk_sucursal' => $sucursalid
                            ])->chunkById($chunkSize, function ($records) {
                                $records->each->delete();
                            });
                        }

                        if ($fac->tarjetas and count($fac->tarjetas) > 0) {
                            $tarjetasData = [];

                            foreach ($fac->tarjetas as $tarjeta){
                                $newtar  = new Saipavta();
                                $auxitem = (array) $tarjeta;
                                $newtar->fill($auxitem);
                                $newtar->fk_sucursal = $sucursalid ;
                                $newtar->FechaE = $fac->FechaE;
                                $newtar->save();
                            }

                        }
                    }

                    if($additems and isset($fac->seriales)){

                        if ($fac->NumeroD) {
                            $chunkSize = 1000;
                            Saseprfac::where([
                                'TipoFac' => $fac->TipoFac,
                                'NumeroD' => $fac->NumeroD,
                                'fk_sucursal' => $sucursalid
                            ])->chunkById($chunkSize, function ($records) {
                                $records->each->delete();
                            });
                        }

                        if (isset($fac->seriales) and count($fac->seriales) > 0) {

                            foreach ($fac->seriales as $seriales){
                                $newser  = new Saseprfac();
                                $auxser = (array) $seriales;
                                $newser->fill($auxser);
                                $newser->fk_sucursal = $sucursalid ;
                                $newser->save();
                            }

                        }
                    }

                    $record->save();

                    $vector[$fac->nrounico] = 1;
                }
            }
            return response()->json(['success' => 'success', 'vector' => $vector], 200);
        }

        return response()->json(['json' => 'json', ], 200);
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Safact $safact)
    {
        //
    }

    public function edit(Safact $safact)
    {
        //
    }

    public function update(Request $request, Safact $safact)
    {
        //
    }

    public function destroy(Safact $safact)
    {
        //
    }
}
