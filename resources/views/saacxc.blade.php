{{-- resources/views/saacxc.blade.php --}}

@extends('layouts.master')
@section('title')
    Reporte Cuentas por Cobrar
@endsection
@section('css')
    <style>
        #clearall {
            text-decoration: none !important;
        }

        .botoncal {
            background: transparent;
            border: none;
            color: white;
        }

        .botoncal:hover {
            font-size: 13px;
        }

        /* Estilos mejorados */
        .card-header-custom {
            background: linear-gradient(135deg, #2f4b9a 0%, #448bc9 100%);
            color: white;
        }

        .table-cxc {
            margin-bottom: 0;
        }

        .table-cxc th {
            background-color: #f8f9fa;
            border-top: none;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 8px;
        }

        .table-cxc td {
            vertical-align: middle;
            padding: 10px 8px;
        }

        .table-cxc tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        .sucursal-link {
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .sucursal-link:hover {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd !important;
            transform: translateX(3px);
        }

        .cliente-link {
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }

        .cliente-link:hover {
            color: #0d6efd !important;
            text-decoration: underline;
        }

        .badge-cxc {
            font-size: 0.7rem;
            padding: 4px 8px;
            border-radius: 20px;
        }

        .total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .total-row td {
            font-weight: bold;
            background-color: #e9ecef;
        }

        .card-proceso {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card-proceso:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .search-cliente {
            position: relative;
        }
        .search-cliente .form-control {
            padding-right: 35px;
        }
        .search-cliente .search-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            pointer-events: none;
        }
        .clear-search {
            position: absolute;
            right: 35px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #dc3545;
            display: none;
            z-index: 10;
            background: white;
            padding: 0 5px;
        }
        .clear-search:hover {
            color: #a71d2a;
        }
        .table-cxc tbody tr {
            transition: all 0.2s ease;
        }
        .table-cxc tbody tr.hidden-row {
            display: none;
        }
        .highlight-text {
            background-color: #fff3cd;
            font-weight: bold;
            padding: 0 2px;
            border-radius: 3px;
        }
        .result-count {
            font-size: 0.85rem;
            margin-left: 10px;
        }

        .list-group-item {
            transition: all 0.2s ease;
        }
        .list-group-item:hover {
            background-color: rgba(13, 110, 253, 0.03);
        }
        .badge i {
            font-size: 0.7rem;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-receipt me-2"></i>Cuentas por Cobrar
                    </h4>
                    <p class="text-white-50 mb-0 small">
                        Resumen de saldos pendientes por sucursal y cliente - Filtra las cuentas por fecha de factura
                    </p>
                </div>
                <div class="card-body">
                    <form method="post" name="form1" id="form1" action="/cxc{{(isset($id) and $id > 0)? '/'.$id : ''}}">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <div class="col-md-5">

                                <div class="input-group">
                                    <input type="text" class="form-control" data-provider="flatpickr"
                                           placeholder="Seleccione rango de fechas"
                                           data-range-date="true" data-date-format="d/m/Y"
                                           data-deafult-date="" name="fechasreport"
                                           readonly="readonly" value="{{$fechasreport}}"
                                           style="background-color: #fff;">
                                    <div class="input-group-text bg-primary border-primary text-white">
                                        <button type="submit" class="botoncal">
                                            <i class="bi bi-search me-1"></i>Consultar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <!-- Panel izquierdo - Resumen por Sucursal -->
        <div class="col-xl-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="bi bi-building me-2"></i>Resumen por Sucursal
                    </h6>
                </div>
                <div class="card-body p-0">
                    @php
                        $nn = $tcanti = $tmonto = $tabona = $tdivis = 0;
                        $datafechas = '';
                        if(isset($fecha1) and isset($fecha2) and $fecha1 != '' and $fecha2 != ''){
                            $datafechas = " and (c.fechat >= '$fecha1 00:00:00.00' and c.fechat <= '$fecha2 23:59:22') ";
                        }
                    @endphp

                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-cxc mb-0">
                            <thead>

                            <th>  Sucursal</th>
                            <th class="text-center">  Facturas</th>
                            <th class="text-center">    Bs</th>
                            <th class="text-center">    USD</th>

                            </thead>
                            <tbody>
                            @foreach($sucursales as $index => $sucu)
                                @php
                                    $sql = "
                                        SELECT
                                            COUNT(*) AS cant,
                                            SUM(c.montodolares) AS credito,
                                            SUM(c.montodolares - (c.saldo / c.tasadolar)) AS abonado,
                                            SUM(c.saldo / c.tasadolar) AS saldo,
                                            sum(IFNULL(
                                                (select sum(totalmontodivisa)
                                                 from safact g
                                                 where g.fk_sucursal = ".$sucu->id."
                                                 and g.numerod = c.numerod
                                                 and c.tipocxc = '10'
                                                 and g.fk_sucursal = c.fk_sucursal
                                                 and g.codclie = a.codclie
                                                 and g.tipofac in ('A','Z')
                                                ) * ((c.saldo/c.tasadolar)/c.montodolares), 0
                                            )) as saldodivisa
                                        FROM saclie AS a
                                        JOIN saacxc AS c ON c.codclie = a.codclie
                                        WHERE c.Saldo > 10
                                            $datafechas
                                            AND c.tipocxc IN (20, 10)
                                            AND c.tasadolar > 0
                                            AND c.fk_sucursal = ".$sucu->id;

                                    $saldocxc = \Illuminate\Support\Facades\DB::select($sql);

                                    if($saldocxc[0]->saldo != 0){
                                        $nn++;
                                        $tcanti += $saldocxc[0]->cant;
                                        $tmonto += $saldocxc[0]->saldo;
                                        $tdivis += $saldocxc[0]->saldodivisa;
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{route('saacxc',['id'=>$sucu->id, 'fechasreport'=>$fechasreport])}}"
                                           class="sucursal-link text-dark" style="font-size: 12px">
                                            <i class="bi bi-shop me-1"></i>{{ str_replace("SARA","",$sucu->descrip) }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                                <span class="badge bg-info">
                                                    {{ number_format($saldocxc[0]->cant, 0, ',', '.') }}
                                                </span>
                                    </td>
                                    <td class="text-end text-danger fw-bold">
                                        {{ number_format($saldocxc[0]->saldo, 2, ',', '.') }}
                                    </td>
                                    <td class="text-end text-primary fw-bold">
                                        {{ number_format($saldocxc[0]->saldodivisa, 2, ',', '.') }}
                                    </td>
                                </tr>
                                @php } @endphp
                            @endforeach
                            </tbody>
                            @if($tcanti > 0)
                                <tfoot class="total-row">
                                <tr>
                                    <td class="fw-bold">TOTAL GENERAL</td>
                                    <td class="text-center fw-bold">{{ number_format($tcanti, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold text-danger"> {{ number_format($tmonto, 2, ',', '.') }}</td>
                                    <td class="text-end fw-bold text-primary"> {{ number_format($tdivis, 2, ',', '.') }}</td>
                                </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            <!-- Últimos Procesos (solo cuando hay sucursal seleccionada) -->
            @if(isset($sucursalselected) && isset($sucursalselected->descrip))
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="bi bi-clock-history me-2"></i>
                            Últimos Movimientos
                        </h6>
                        <small class="text-muted">Cobranzas y descuentos recientes</small>
                    </div>
                    <div class="card-body p-0">
                        @php
                            // Obtener últimos procesos de cobranza (tipo 99) y descuentos (tipo 98) de la sucursal seleccionada
                            $movimientosRecientes = \App\Models\Saacxc::with(['cliente'])
                                ->whereIn('tipocxc', [98, 99])
                                ->orderBy('created_at', 'desc')
                                ->limit(10)
                                ->get();

                        @endphp

                        @if(count($movimientosRecientes) > 0)
                            <div class="list-group list-group-flush">
                                @foreach($movimientosRecientes as $movimiento)
                                    <div class="list-group-item list-group-item-action px-3 py-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="  small">
                                                        {{ $movimiento->cliente->descrip ?? 'N/A' }}
                                                    </span>
                                                </div>
                                                <div class="mt-1">
                                                    <span class="text-muted small">
                                                        Monto {{ ($movimiento->TipoCxc == 99)? "Pago" : "Desc" }}:
                                                        <span class=" text-primary">
                                                            ${{ number_format(abs($movimiento->montodolares), 2, ',', '.') }}
                                                        </span>
                                                    </span>
                                                    <span class="text-muted small ms-2">
                                                        {{ \Carbon\Carbon::parse($movimiento->created_at)->format('d/m/Y H:i') }}
                                                    </span>
                                                    <br>
                                                    Observacion: {{$movimiento->Document}} {{$movimiento->Notas1}}
                                                </div>
                                                @if($movimiento->document && $movimiento->document != '')
                                                    <div class="small text-muted mt-1">
                                                        <i class="bi bi-chat-text"></i> {{ \Illuminate\Support\Str::limit($movimiento->document, 50) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ms-2">
                                                <span class="badge {{ $movimiento->descargar == 1 ? 'bg-warning text-white' : 'bg-success text-white' }}">
                                                    @if($movimiento->TipoCxc == 99)
                                                        Pago   {!!  $movimiento->descargar == 1 ? 'Pend' : '&radic;'  !!}
                                                    @else
                                                        Desc   {!!  $movimiento->descargar == 1 ? 'Pend' : '&radic;'  !!}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if(count($movimientosRecientes) >= 5)
                                <div class="card-footer bg-transparent text-center py-2">
                                    <small class="text-muted">Mostrando últimos 10 movimientos</small>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-4"></i>
                                <p class="small mb-0">No hay movimientos recientes</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Panel derecho - Detalle por Cliente o Procesos Recientes -->
        <div class="col-xl-8">
            @if(isset($sucursalselected) && isset($sucursalselected->descrip))
                <!-- Detalle de clientes por sucursal -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">
                                <i class="bi bi-people me-2"></i>
                                Clientes con Deuda - {{ $sucursalselected->descrip }}
                            </h6>
                            <small class="text-muted">Detalle de saldos por cliente</small>
                        </div>
                        <div class="d-flex gap-2 mt-2 mt-sm-0">
                            <!-- Buscador de clientes -->
                            <div class="search-cliente">
                                <input type="text"
                                       id="buscarCliente"
                                       class="form-control form-control-sm"
                                       placeholder="Buscar cliente..."
                                       style="width: 250px;">
                                <i class="bi bi-search search-icon"></i>
                                <i class="bi bi-x-circle clear-search" id="clearSearch" style="display: none;"></i>
                            </div>

                        </div>
                    </div>
                    <div class="card-body p-0">
                        @php
                            $fk_sucursal = $sucursalselected->id;
                            $sqlcostoinv = "
                                SELECT
                                    COUNT(*) AS deudas,
                                    a.descrip AS cliente,
                                    SUM(c.montodolares) AS credito,
                                    SUM(c.montodolares - (c.saldo / c.tasadolar)) AS abonado,
                                    a.codclie,
                                    SUM(c.saldo / c.tasadolar) AS saldo,
                                    sum(IFNULL(
                                        (select sum(totalmontodivisa)
                                         from safact g
                                         where g.fk_sucursal = ".$sucursalselected->id."
                                         and g.numerod = c.numerod
                                         and c.tipocxc = '10'
                                         and g.fk_sucursal = c.fk_sucursal
                                         and g.codclie = a.codclie
                                         and g.tipofac in ('A','Z')
                                        ) * ((c.saldo/c.tasadolar)/c.montodolares), 0
                                    )) as saldodivisa
                                FROM saclie AS a
                                JOIN saacxc AS c ON c.codclie = a.codclie
                                WHERE c.Saldo > 10
                                    $datafechas
                                    AND c.tipocxc IN (20, 10)
                                    AND c.tasadolar > 0
                                    AND c.fk_sucursal = ".$sucursalselected->id."
                                GROUP BY a.descrip, a.codclie
                                ORDER BY saldo DESC
                            ";

                            $saldocxc = \Illuminate\Support\Facades\DB::select($sqlcostoinv);
                            $tmonto = $tabona = $tsaldo = $tdivis = 0;
                        @endphp

                        <div class="px-3 pt-2 pb-2 d-flex justify-content-between align-items-center border-bottom">
                            <div>
                                <span class="badge bg-info" id="totalClientes">{{ count($saldocxc) }}</span>
                                <span class="small text-muted">clientes encontrados</span>
                            </div>
                            <div id="resultadoBusqueda" class="small text-muted"></div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-cxc mb-0">
                                <thead>
                                <tr>
                                    <th> Cliente</th>
                                    <th class="text-center">  Facturas</th>
                                    <th class="text-end">  Facturado</th>
                                    <th class="text-end">  Abonado</th>
                                    <th class="text-end"> Saldo Bs</th>
                                    <th class="text-end">  Saldo USD</th>
                                    <th class="text-center">  Acción</th>
                                </tr>
                                </thead>
                                <tbody id="tbodyClientes">
                                @foreach($saldocxc as $index => $cxc)
                                    @php
                                        $tmonto += $cxc->credito;
                                        $tabona += $cxc->abonado;
                                        $tsaldo += $cxc->saldo;
                                        $tdivis += $cxc->saldodivisa;
                                    @endphp
                                    <tr class="cliente-row" data-cliente="{{ strtolower($cxc->cliente) }}" data-codclie="{{ $cxc->codclie }}">
                                        <td>
                                            <a href="/clientes/{{$cxc->codclie}}" class="cliente-link text-dark">
                                                {{ $cxc->cliente }}
                                            </a>
                                            <br>
                                            <small class="text-muted">{{ $cxc->codclie }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ number_format($cxc->deudas, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="text-end"> {{ number_format($cxc->credito, 2, ',', '.') }}</td>
                                        <td class="text-end text-success"> {{ number_format($cxc->abonado, 2, ',', '.') }}</td>
                                        <td class="text-end text-danger fw-bold">{{ number_format($cxc->saldo, 2, ',', '.') }}</td>
                                        <td class="text-end text-primary fw-bold"> {{ number_format($cxc->saldodivisa, 2, ',', '.') }}</td>
                                        <td class="text-center">
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-info cxcmodal"
                                                    data-codclie="{{ $cxc->codclie }}"
                                                    data-fecha1="{{ $fecha1 ?? '' }}"
                                                    data-fecha2="{{ $fecha2 ?? '' }}"
                                                    data-cliente="{{ $cxc->cliente }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#cxcmodal">
                                                <i class="bi bi-list-ul me-1"></i> Ver
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                @if(count($saldocxc) > 0)
                                    <tfoot class="total-row" id="footerTotales">
                                    <tr>
                                        <td class="fw-bold">TOTALES</td>
                                        <td class="text-center fw-bold">{{ number_format(count($saldocxc), 0, ',', '.') }} clientes</td>
                                        <td class="text-end">{{ number_format($tmonto, 2, ',', '.') }}</td>
                                        <td class="text-end text-success">{{ number_format($tabona, 2, ',', '.') }}</td>
                                        <td class="text-end text-danger fw-bold">{{ number_format($tsaldo, 2, ',', '.') }}</td>
                                        <td class="text-end text-primary fw-bold">{{ number_format($tdivis, 2, ',', '.') }}</td>
                                        <td class="text-center"></td>
                                    </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Modal para detalle de facturas -->
                <div class="modal fade" id="cxcmodal" aria-hidden="true" tabindex="-1">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title text-white" id="titulolistado">
                                    <i class="bi bi-receipt me-2"></i>Facturas a Crédito
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body bg-light" id="contentcxcreport">
                                <div class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <p class="mt-3 text-muted">Cargando facturas...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Procesos Recientes -->
                @if(isset($cxcprocesos) && count($cxcprocesos) > 0)
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="bi bi-clock-history me-2"></i>
                                Últimos Procesos de Cobranza
                            </h6>
                            <small class="text-muted">Actividad reciente en cuentas por cobrar</small>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach($cxcprocesos as $proceso)
                                    <div class="col-md-6"   >
                                        <div class="card card-proceso border-{{($proceso->descargar == 1)? 'warning' : 'success'}} border-opacity-50 shadow-sm mb-1 ">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <h6 class="mb-1">
                                                            <i class="bi bi-person-circle me-1"></i>
                                                            <span style="font-size: 11px">{{ $proceso->cliente->descrip }}</span>
                                                        </h6>
                                                        <small class="text-muted">
                                                            <i class="bi bi-building me-1"></i>
                                                            {{ $proceso->sucursalcli->descrip }}
                                                        </small>
                                                    </div>
                                                    <div class="badge bg-{{($proceso->descargar == 1)? 'warning' : 'success'}}">
                                                        @if($proceso->TipoCxc == 99)
                                                            Pago {!! ($proceso->descargar == 1)? 'Pend' : '&radic;' !!}
                                                        @else
                                                            Desc {!! ($proceso->descargar == 1)? 'Pend' : '&radic;' !!}
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="mt-2 pt-2 border-top">
                                                    <div class="d-flex justify-content-between">
                                                        <span class="text-muted">Monto
                                                            @if($proceso->TipoCxc == 99)
                                                                Pago
                                                            @else
                                                                Desc
                                                            @endif:
                                                        </span>
                                                        <span class="fw-bold text-primary">${{ number_format($proceso->montodolares, 2, ',', '.') }}</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mt-1">
                                                        <span class="text-muted">Fecha:</span>
                                                        <span>{{ $proceso->formattedDate }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card shadow-sm">
                        <div class="card-body text-center text-muted py-5">
                            <i class="bi bi-receipt fs-1 d-block mb-3 text-primary"></i>
                            <h5>Selecciona una sucursal</h5>
                            <p class="mb-0">Haz clic en cualquier sucursal del panel izquierdo para ver el detalle de clientes con deuda</p>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <div class="modal fade" id="descuentoModal" tabindex="-1" aria-labelledby="descuentoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content" id="contentdescuento">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="descuentoModalLabel">
                        <i class="bi bi-percent me-2"></i>Registrar Descuento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" >
                    <div class="alert alert-info small">
                        <strong>Factura:</strong> <span id="facturaNumero"></span><br>
                        <strong>Saldo actual:</strong>   <span id="saldoActualUSD"></span> USD
                    </div>
                    <form id="formDescuento">
                        <input type="hidden" id="descuentoNumerod"  name="numerod">
                        <input type="hidden" id="descuentonrounico" name="nrounico">
                        <input type="hidden" id="descuentoFksucu"   name="fksucu">

                        <div class="mb-3">
                            <label class="form-label">Tipo de Descuento</label>
                            <select class="form-select" id="tipoDescuento" name="tipoDescuento" required>
                                <option value="monto">Monto fijo</option>
                                <option value="porcentaje">Porcentaje (%)</option>
                            </select>
                        </div>

                        <div class="mb-3" id="montoDescuentoDiv">
                            <label class="form-label">Monto a descontar (USD)</label>
                            <input type="number" step="0.01" class="form-control" id="montoDescuento" name="montoDescuento" placeholder="0.00">
                            <small class="text-muted">Monto en USD a restar de esta factura</small>
                        </div>

                        <div class="mb-3" id="porcentajeDescuentoDiv" style="display: none;">
                            <label class="form-label">Porcentaje de Descuento (%)</label>
                            <input type="number" step="0.01" class="form-control" id="porcentajeDescuento" name="porcentajeDescuento" placeholder="0.00">
                            <small class="text-muted">Porcentaje a descontar del saldo actual</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Motivo del Descuento</label>
                            <textarea class="form-control" id="motivoDescuento" name="motivoDescuento" rows="2" placeholder="Ej: Descuento por pronto pago, Devolución parcial, etc." required></textarea>
                        </div>

                        <div class="alert alert-success" id="previewDescuento" style="display: none;">
                            <strong>Vista previa:</strong><br>
                            Se descontará: <span id="previewMonto"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-warning" id="btnAplicarDescuento">
                        <i class="bi bi-list-ul"></i> Aplicar Descuento
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        // Función para resaltar texto
        function highlightText(text, searchTerm) {
            if (!searchTerm || searchTerm.length < 2) return text;
            const regex = new RegExp(`(${searchTerm})`, 'gi');
            return text.replace(regex, '<span class="highlight-text">$1</span>');
        }

        // Función para buscar clientes en tiempo real
        function buscarCliente() {
            const searchTerm = $('#buscarCliente').val().trim().toLowerCase();
            let visibleCount = 0;
            let totalSaldo = 0;
            let totalFacturado = 0;
            let totalAbonado = 0;
            let totalSaldoUSD = 0;
            let totalFacturas = 0;

            if (searchTerm.length === 0) {
                // Mostrar todas las filas
                $('.cliente-row').each(function() {
                    $(this).removeClass('hidden-row').show();
                    visibleCount++;

                    // Recolectar totales
                    totalSaldo += parseFloat($(this).find('td:eq(4)').text().replace(/\./g, '').replace(',', '.')) || 0;
                    totalFacturado += parseFloat($(this).find('td:eq(2)').text().replace(/\./g, '').replace(',', '.')) || 0;
                    totalAbonado += parseFloat($(this).find('td:eq(3)').text().replace(/\./g, '').replace(',', '.')) || 0;
                    totalSaldoUSD += parseFloat($(this).find('td:eq(5)').text().replace(/\./g, '').replace(',', '.')) || 0;
                    totalFacturas += parseInt($(this).find('td:eq(1) .badge').text().replace(/\./g, '')) || 0;
                });

                $('#clearSearch').hide();
                $('#resultadoBusqueda').html('');

                // Restaurar textos originales
                $('.cliente-row .cliente-link').each(function() {
                    const originalText = $(this).data('original-text') || $(this).text();
                    $(this).text(originalText);
                });

                // Actualizar totales originales
                actualizarTotales(visibleCount, totalFacturado, totalAbonado, totalSaldo, totalSaldoUSD, totalFacturas);

            } else if (searchTerm.length >= 2) {
                $('#clearSearch').show();

                $('.cliente-row').each(function() {
                    const $row = $(this);
                    const clienteNombre = $row.data('cliente');
                    // Convertir codclie a string antes de usar toLowerCase
                    const codclie = String($row.data('codclie') || '');
                    const clienteLink = $row.find('.cliente-link');

                    // Guardar texto original si no está guardado
                    if (!clienteLink.data('original-text')) {
                        clienteLink.data('original-text', clienteLink.text());
                    }

                    // Buscar coincidencia (asegurar que clienteNombre existe)
                    const nombreMatch = clienteNombre ? clienteNombre.includes(searchTerm) : false;
                    const codigoMatch = codclie.toLowerCase().includes(searchTerm);

                    if (nombreMatch || codigoMatch) {
                        $row.removeClass('hidden-row').show();
                        visibleCount++;

                        // Recolectar totales
                        totalSaldo += parseFloat($row.find('td:eq(4)').text().replace(/\./g, '').replace(',', '.')) || 0;
                        totalFacturado += parseFloat($row.find('td:eq(2)').text().replace(/\./g, '').replace(',', '.')) || 0;
                        totalAbonado += parseFloat($row.find('td:eq(3)').text().replace(/\./g, '').replace(',', '.')) || 0;
                        totalSaldoUSD += parseFloat($row.find('td:eq(5)').text().replace(/\./g, '').replace(',', '.')) || 0;
                        totalFacturas += parseInt($row.find('td:eq(1) .badge').text().replace(/\./g, '')) || 0;

                        // Resaltar texto
                        const highlightedText = highlightText(clienteLink.data('original-text'), searchTerm);
                        clienteLink.html(highlightedText);

                        // También resaltar código si aplica
                        const codigoSpan = $row.find('td:first small');
                        if (codigoSpan.length && codigoMatch) {
                            const highlightedCod = highlightText(codclie, searchTerm);
                            codigoSpan.html(highlightedCod);
                        } else if (codigoSpan.length) {
                            // Restaurar código original si no hay coincidencia
                            codigoSpan.text(codclie);
                        }
                    } else {
                        $row.addClass('hidden-row').hide();
                        // Restaurar texto original
                        clienteLink.text(clienteLink.data('original-text'));
                        // Restaurar código original
                        const codigoSpan = $row.find('td:first small');
                        if (codigoSpan.length) {
                            codigoSpan.text(codclie);
                        }
                    }
                });

                const resultText = visibleCount === 1 ? '1 cliente encontrado' : `${visibleCount} clientes encontrados`;
                $('#resultadoBusqueda').html(`<i class="bi bi-filter"></i> ${resultText}`);

                // Actualizar totales con los filtrados
                actualizarTotales(visibleCount, totalFacturado, totalAbonado, totalSaldo, totalSaldoUSD, totalFacturas);

            } else if (searchTerm.length === 1) {
                $('#resultadoBusqueda').html('<i class="bi bi-info-circle"></i> Ingrese al menos 2 caracteres para buscar');
                $('#clearSearch').show();
            }
        }

        // Función para actualizar los totales en el footer
        function actualizarTotales(clientes, facturado, abonado, saldo, saldoUSD, facturas) {
            const $footer = $('#footerTotales');
            if ($footer.length) {
                $footer.find('td:eq(0)').text('TOTALES');
                $footer.find('td:eq(1)').html(`<span class="fw-bold">${clientes.toLocaleString('es-VE')}</span> clientes`);
                $footer.find('td:eq(2)').text(formatearNumero(facturado));
                $footer.find('td:eq(3)').html(`<span class="text-success">${formatearNumero(abonado)}</span>`);
                $footer.find('td:eq(4)').html(`<span class="text-danger fw-bold">${formatearNumero(saldo)}</span>`);
                $footer.find('td:eq(5)').html(`<span class="text-primary fw-bold">${formatearNumero(saldoUSD)}</span>`);
            }

            // Actualizar el badge de total de clientes
            $('#totalClientes').text(clientes);
        }

        // Función para formatear números
        function formatearNumero(numero) {
            if (isNaN(numero)) return '0,00';
            return numero.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Función para limpiar búsqueda
        function limpiarBusqueda() {
            $('#buscarCliente').val('');
            buscarCliente();
            $('#buscarCliente').focus();
        }

        // Eventos de búsqueda en tiempo real
        $(document).ready(function() {
            // Buscar mientras escribe
            $('#buscarCliente').unbind('keyup').bind('keyup',function () {
                buscarCliente();
            });

            // Limpiar búsqueda
            $('#clearSearch').unbind('click').bind('click',function () {
                limpiarBusqueda();
            });

            // Enfocar el buscador automáticamente cuando se carga la página de sucursal
            if ($('#buscarCliente').length) {
                $('#buscarCliente').focus();
            }
        });

        let facturaSeleccionada = {
            numerod  : '',
            nrounico : '',
            fksucu   : '',
            saldoBs  : 0,
            saldoUsd : 0
        };


        function activarparadescuentocxc(){

            inicializarEventos();

// Mostrar modal de descuento
            $(document).off('click', '.btn-descuento').on('click', '.btn-descuento', function() {
                facturaSeleccionada.numerod = $(this).data('numerod');
                facturaSeleccionada.nrounico = $(this).data('nrounico');
                facturaSeleccionada.fksucu = $(this).data('fksucu');
                facturaSeleccionada.saldoUsd = $(this).data('saldo-usd') || 0;
                facturaSeleccionada.saldoBs = $(this).data('saldo') || 0;

                $('#facturaNumero').text(facturaSeleccionada.numerod);
                $('#saldoActualUSD').text('$' + facturaSeleccionada.saldoUsd.toLocaleString('es-VE', {minimumFractionDigits: 2}));

                $('#descuentoNumerod').val(facturaSeleccionada.numerod);
                $('#descuentonrounico').val(facturaSeleccionada.nrounico);
                $('#descuentoFksucu').val(facturaSeleccionada.fksucu);

                // Resetear formulario
                $('#montoDescuento').val('');
                $('#porcentajeDescuento').val('');
                $('#motivoDescuento').val('');
                $('#previewDescuento').hide();
                $('#tipoDescuento').val('monto');
                $('#montoDescuentoDiv').show();
                $('#porcentajeDescuentoDiv').hide();
            });

// Cambiar entre monto y porcentaje
            $(document).off('click', '#tipoDescuento').on('change', '#tipoDescuento', function() {
                if ($(this).val() === 'monto') {
                    $('#montoDescuentoDiv').show();
                    $('#porcentajeDescuentoDiv').hide();
                } else {
                    $('#montoDescuentoDiv').hide();
                    $('#porcentajeDescuentoDiv').show();
                }
                calcularPreview();
            });

// Calcular preview del descuento


            $(document).off('keyup change', '#montoDescuento, #porcentajeDescuento').on('keyup change', '#montoDescuento, #porcentajeDescuento', function() {
                calcularPreview();
            });

// Aplicar descuento
            // Aplicar descuento
            $('#btnAplicarDescuento').unbind('click').bind('click',function () {
                let tipo = $('#tipoDescuento').val();
                let monto = 0;

                if (tipo === 'monto') {
                    monto = parseFloat($('#montoDescuento').val()) || 0;
                    if (monto <= 0) {
                        alert('Debe ingresar un monto válido');
                        return;
                    }
                    if (monto > facturaSeleccionada.saldoUsd) {
                        alert('El monto del descuento no puede exceder el saldo de la factura ($' + facturaSeleccionada.saldoUsd + ')');
                        return;
                    }
                } else {
                    let porcentaje = parseFloat($('#porcentajeDescuento').val()) || 0;
                    if (porcentaje <= 0 || porcentaje > 100) {
                        alert('Debe ingresar un porcentaje válido entre 1 y 100');
                        return;
                    }
                    monto = facturaSeleccionada.saldoUsd * (porcentaje / 100);
                }

                let motivo = $('#motivoDescuento').val().trim();
                if (!motivo) {
                    alert('Debe ingresar un motivo para el descuento');
                    return;
                }

                if (!confirm('¿Está seguro de aplicar este descuento?\n\nFactura: ' + facturaSeleccionada.numerod + '\nMonto: $' + monto.toLocaleString('es-VE', {minimumFractionDigits: 2}) + '\nMotivo: ' + motivo)) {
                    return;
                }

                // Mostrar loading dentro del modal principal
                $('#contentdescuento').html(`
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Procesando...</span>
                            </div>
                            <p class="mt-3 text-muted">Procesando descuento...</p>
                        </div>
                    `);

                $.ajax({
                    type: 'POST',
                    data: {
                        nrounico: facturaSeleccionada.nrounico,
                        numerod : facturaSeleccionada.numerod,
                        fksucu  : facturaSeleccionada.fksucu,
                        monto   : monto,
                        motivo  : motivo,
                        tipo    : tipo,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/cxcdescuento',
                    success: function(response) {
                        if (response.success) {
                            window.location.href = "/cxc/"+facturaSeleccionada.fksucu;
                        } else {
                            $('#contentdescuento').html(`
                                <div class="alert alert-danger text-center">
                                    <i class="bi bi-exclamation-triangle fs-4"></i>
                                    <h5 class="mt-2">Error al aplicar descuento</h5>
                                    <p>${response.message}</p>
                                    <button type="button" class="btn btn-secondary mt-3" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            `);
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Error al procesar el descuento';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        $('#contentdescuento').html(`
                                <div class="alert alert-danger text-center">
                                    <i class="bi bi-exclamation-triangle fs-4"></i>
                                    <h5 class="mt-2">Error</h5>
                                    <p>${msg}</p>
                                    <button type="button" class="btn btn-secondary mt-3" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            `);
                    }
                });
            });
        }

        // Función para cargar detalle de facturas por cliente (se mantiene igual)
        $('.cxcmodal').off('click').on('click', function() {
            var codclie = $(this).data('codclie');
            var fecha1 = $(this).data('fecha1');
            var fecha2 = $(this).data('fecha2');
            var cliente = $(this).data('cliente');


            codclieActual = codclie;
            fecha1Actual  = fecha1;
            fecha2Actual  = fecha2;
            clienteActual = cliente;


            $('#titulolistado').html(`<i class="bi bi-receipt me-2"></i>Facturas a Crédito de ${cliente}`);

            $('#contentcxcreport').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Cargando facturas del cliente...</p>
                </div>
            `);

            $.ajax({
                type: 'post',
                data:{codclie: (codclie)? codclie : '',fecha1: (fecha1)? fecha1 : '',fecha2: (fecha2)? fecha2 : '' },
                url: '/cxclist',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.vista) {
                        $('#contentcxcreport').html(response.vista);
                    } else {
                        $('#contentcxcreport').html(`
                            <div class="alert alert-warning text-center">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                No se encontraron facturas para este cliente
                            </div>
                        `);
                    }
                },
                error: function() {
                    $('#contentcxcreport').html(`
                        <div class="alert alert-danger text-center">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Error al cargar las facturas. Por favor, intente nuevamente.
                        </div>
                    `);
                }
            });
        });

        function calcularPreview() {
            let tipo = $('#tipoDescuento').val();
            let monto = 0;

            if (tipo === 'monto') {
                monto = parseFloat($('#montoDescuento').val()) || 0;
                if (monto > 0) {
                    $('#previewMonto').text('$' + monto.toLocaleString('es-VE', {minimumFractionDigits: 2}));
                    $('#previewDescuento').show();
                } else {
                    $('#previewDescuento').hide();
                }
            } else {
                let porcentaje = parseFloat($('#porcentajeDescuento').val()) || 0;
                if (porcentaje > 0 && porcentaje <= 100) {
                    monto = facturaSeleccionada.saldoUsd * (porcentaje / 100);
                    $('#previewMonto').text('$' + monto.toLocaleString('es-VE', {minimumFractionDigits: 2}) + ' (' + porcentaje + '% del saldo)');
                    $('#previewDescuento').show();
                } else {
                    $('#previewDescuento').hide();
                }
            }
        }

        // Función para abonar (se mantiene igual)
        function cxcabonarweb(codclie, fecha1, fecha2) {
            var montoabonar = parseFloat(document.getElementById("montoabonar").value);
            document.getElementById("alertmontoabonar").textContent = '';

            if (montoabonar && !isNaN(montoabonar) && montoabonar > 0) {
                if (confirm(`¿Está seguro que desea abonar $${montoabonar.toLocaleString('es-VE')} a este cliente?`)) {
                    $('#contentcxcreport').html(`
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Procesando...</span>
                            </div>
                            <p class="mt-3 text-muted">Procesando abono...</p>
                        </div>
                    `);

                    $.ajax({
                        type: 'post',
                        data: {
                            codclie: codclie || '',
                            fecha1: fecha1 || '',
                            fecha2: fecha2 || '',
                            montoabonar: montoabonar
                        },
                        url: '/cxcabonarweb',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            window.location.href = "/cxc";
                        },
                        error: function(xhr, status, error) {
                            console.error('Error en el abono:', error);
                            $('#contentcxcreport').html(`
                                <div class="alert alert-danger text-center">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    Error al procesar el abono. Por favor, intente nuevamente.
                                </div>
                            `);
                        }
                    });
                }
            } else {
                document.getElementById("alertmontoabonar").textContent = 'Debe ingresar un monto válido mayor a 0';
            }
        }


        let instrumentosData = {
            bs : [],
            usd: [],
            cop: []
        };

        let instrumentoIndex = {
            bs : 0,
            usd: 0,
            cop: 0
        };

        // Cargar instrumentos de pago al abrir el modal
        function cargarInstrumentos() {
            $.ajax({
                url: '{{ route("cxcweb.instrumentos") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        instrumentosData.bs = response.instrumentos_bs;
                        instrumentosData.usd = response.instrumentos_usd;
                        instrumentosData.cop = response.instrumentos_pesos;

                        $('#tasa_abono').val(response.tasa_cambio);
                        $('#tasa_peso').val(response.tasa_peso);
                    }
                },
                error: function() {
                    console.error('Error al cargar instrumentos');
                }
            });
        }

        // Generar options para select de instrumentos
        function generarOptions(instrumentos, selectedValue = '') {
            let options = '<option value="">Seleccionar...</option>';
            instrumentos.forEach(function(inst) {
                let selected = (selectedValue === inst.codtarj) ? 'selected' : '';
                options += `<option value="${inst.codtarj}" ${selected}>${inst.codtarj} - ${inst.descrip}</option>`;
            });
            return options;
        }

        // Agregar fila de instrumento
        function agregarInstrumento(currency) {
            const container = $(`#instrumentos${currency.charAt(0).toUpperCase() + currency.slice(1)}Container`);
            const options = generarOptions(instrumentosData[currency]);
            const idx = instrumentoIndex[currency]++;

            const row = `
        <div class="row mb-2 instrumento-row" data-currency="${currency}" data-idx="${idx}">
            <div class="col-md-4">
                <select class="form-select form-select-sm instrumento-select" data-currency="${currency}" data-idx="${idx}">
                    ${options}
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control form-control-sm instrumento-ref" placeholder="Referencia">
            </div>
            <div class="col-md-3">
                <input type="number" step="0.01" class="form-control form-control-sm instrumento-monto payment-amount" data-currency="${currency}" value="0">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm remove-instrumento">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;

            container.append(row);
        }

        // Calcular total en USD
        function calcularTotalUSD() {
            let total = 0;
            const tasaBs   = parseFloat($('#tasa_abono').val()) || 0;
            const tasaPeso = parseFloat($('#tasa_peso').val()) || 4000;

            const saldo    = parseFloat($('#tsaldolistaod').val()) || 0;

            if(tasaBs > 0){

                // Efectivo
                total += parseFloat($('#efectivo_usd').val()) || 0;
                total += parseFloat($('#efectivo_eur').val()) || 0;
                total += (parseFloat($('#efectivo_bs').val()) || 0) / tasaBs;
                total += (parseFloat($('#efectivo_pesos').val()) || 0) / tasaPeso;

                // Instrumentos Bs
                $('.instrumento-row[data-currency="bs"] .instrumento-monto').each(function() {
                    total += (parseFloat($(this).val()) || 0) / tasaBs;
                });

                // Instrumentos USD
                $('.instrumento-row[data-currency="usd"] .instrumento-monto').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });

                // Instrumentos COP
                $('.instrumento-row[data-currency="cop"] .instrumento-monto').each(function() {
                    total += (parseFloat($(this).val()) || 0) / tasaPeso;
                });

                $('#totalUSD').text('$' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));

                if (total > saldo) {
                    $('#totalUSD').addClass('text-danger');
                    $('#alertMonto').text('El monto total excede el saldo de la factura -- total = '+total + ' Saldo ='+ saldo);
                } else if (total <= 0) {
                    $('#alertMonto').text('Debe ingresar al menos un monto para pagar  -- total = '+total);
                } else {
                    $('#totalUSD').removeClass('text-danger');
                    $('#alertMonto').text('');
                }

                return total;

            }else{
                $('#alertMonto').text('Debe colocar una tasa de cambio de BS');
            }
        }

        // Procesar pago
        function procesarPago(codclie, fecha1, fecha2) {
            const totalUSD = parseFloat($('#totalUSD').text().replace('$', '').replace(/,/g, ''));
            const saldo    = parseFloat($('#tsaldolistaod').val()) || 0;

            if (totalUSD <= 0) {
                $('#alertMonto').text('Debe ingresar al menos un monto para pagar');
                return;
            }

            if (totalUSD > saldo) {
                $('#alertMonto').text('El monto total excede el saldo de la factura totalUSD ='+totalUSD+' > Saldo = '+saldo);
                return;
            }

            if (!$('#observacion').val()) {
                $('#alertMonto').text('Debe ingresar una observación');
                return;
            }

            // Recolectar datos del pago
            const pagoData = {
                codclie        : codclie,
                fecha1         : fecha1,
                fecha2         : fecha2,
                efectivo_bs    : parseFloat($('#efectivo_bs').val()) || 0,
                efectivo_usd   : parseFloat($('#efectivo_usd').val()) || 0,
                efectivo_pesos : parseFloat($('#efectivo_pesos').val()) || 0,
                efectivo_eur   : parseFloat($('#efectivo_eur').val()) || 0,
                tasa_abono     : parseFloat($('#tasa_abono').val()) || 1,
                tasa_peso      : parseFloat($('#tasa_peso').val()) || 4000,
                observacion    : $('#observacion').val(),
                instrumentos_bs   : [],
                instrumentos_usd  : [],
                instrumentos_pesos: []
            };

            // Recolectar instrumentos Bs
            $('.instrumento-row[data-currency="bs"]').each(function() {
                const codPago = $(this).find('.instrumento-select').val();
                const monto = parseFloat($(this).find('.instrumento-monto').val()) || 0;
                const referencia = $(this).find('.instrumento-ref').val();
                if (codPago && monto > 0) {
                    pagoData.instrumentos_bs.push({
                        cod_pago: codPago,
                        monto: monto,
                        referencia: referencia
                    });
                }
            });

            // Recolectar instrumentos USD
            $('.instrumento-row[data-currency="usd"]').each(function() {
                const codPago = $(this).find('.instrumento-select').val();
                const monto = parseFloat($(this).find('.instrumento-monto').val()) || 0;
                const referencia = $(this).find('.instrumento-ref').val();
                if (codPago && monto > 0) {
                    pagoData.instrumentos_usd.push({
                        cod_pago: codPago,
                        monto: monto,
                        referencia: referencia
                    });
                }
            });

            // Recolectar instrumentos Pesos
            $('.instrumento-row[data-currency="cop"]').each(function() {
                const codPago = $(this).find('.instrumento-select').val();
                const monto = parseFloat($(this).find('.instrumento-monto').val()) || 0;
                const referencia = $(this).find('.instrumento-ref').val();
                if (codPago && monto > 0) {
                    pagoData.instrumentos_pesos.push({
                        cod_pago: codPago,
                        monto: monto,
                        referencia: referencia
                    });
                }
            });

            $('#contentcxcreport').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Procesando...</span>
            </div>
            <p class="mt-3 text-muted">Procesando pago...</p>
        </div>
    `);

            $.ajax({
                url: '{{ route("cxcweb.procesar.pago.web") }}',
                method: 'POST',
                data: {
                    ...pagoData,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#contentcxcreport').html(`
                    <div class="alert alert-success text-center">
                        <i class="bi bi-check-circle me-2"></i>
                        ${response.message}

                    </div>
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                `);
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        $('#contentcxcreport').html(`
                    <div class="alert alert-danger text-center">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        ${response.message}
                    </div>
                `);
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Error al procesar el pago';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $('#contentcxcreport').html(`
                <div class="alert alert-danger text-center">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    ${errorMsg}
                </div>
            `);
                }
            });
        }


        // Variables para navegación entre documentos y listado
        let codclieActual = '';
        let fecha1Actual = '';
        let fecha2Actual = '';
        let clienteActual = '';

        // Función para cargar el listado de facturas (cuando se presiona Volver)
        function cargarListadoFacturas() {
            $('#titulolistado').html(`<i class="bi bi-receipt me-2"></i>Facturas a Crédito de ${clienteActual}`);

            $('#contentcxcreport').html(`
                                    <div class="text-center py-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                        <p class="mt-3 text-muted">Recargando facturas del cliente...</p>
                                    </div>
                                `);

            $.ajax({
                type: 'post',
                data: { codclie: codclieActual, fecha1: fecha1Actual, fecha2: fecha2Actual },
                url: '/cxclist',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    if (response.vista) {
                        $('#contentcxcreport').html(response.vista);
                        // Re-inicializar eventos para los nuevos enlaces
                        inicializarEventos();
                    } else {
                        $('#contentcxcreport').html(`<div class="alert alert-warning text-center">No se encontraron facturas para este cliente</div>`);
                    }
                },
                error: function() {
                    $('#contentcxcreport').html(`<div class="alert alert-danger text-center">Error al cargar las facturas</div>`);
                }
            });
        }

        function inicializarEventos() {
            // Evento para abrir documento
            $(document).off('click', '.openDocumentoEnLinea').on('click', '.openDocumentoEnLinea', function(e) {
                e.preventDefault();
                e.stopPropagation();
                verDocumento($(this).data('fksucu'), $(this).data('numerod'), $(this).data('tipofac'), $(this).data('cliente'));
            });

            // Evento para descuento
            $(document).off('click', '.btn-descuento').on('click', '.btn-descuento', function() {
                facturaSeleccionada.numerod = $(this).data('numerod');
                facturaSeleccionada.nrounico = $(this).data('nrounico');
                facturaSeleccionada.fksucu = $(this).data('fksucu');
                facturaSeleccionada.saldoUsd = $(this).data('saldo-usd') || 0;
                facturaSeleccionada.saldoBs = $(this).data('saldo') || 0;

                $('#facturaNumero').text(facturaSeleccionada.numerod);
                $('#saldoActualUSD').text('$' + facturaSeleccionada.saldoUsd.toLocaleString('es-VE', {minimumFractionDigits: 2}));

                $('#descuentonrounico').val(facturaSeleccionada.nrounico);
                $('#descuentoNumerod').val(facturaSeleccionada.numerod);
                $('#descuentoFksucu').val(facturaSeleccionada.fksucu);

                $('#montoDescuento').val('');
                $('#porcentajeDescuento').val('');
                $('#motivoDescuento').val('');
                $('#previewDescuento').hide();
                $('#tipoDescuento').val('monto');
                $('#montoDescuentoDiv').show();
                $('#porcentajeDescuentoDiv').hide();
            });
        }

        // Función para ver documento
        function verDocumento(fksucu, numerod, tipofac, cliente) {
            clienteActual = cliente;

            $('#titulolistado').html(`
        <i class="bi bi-file-text me-2"></i>
        Documento: ${numerod} - ${cliente}
        <button type="button" class="btn btn-sm btn-outline-light ms-3" id="btnVolverListado">
            <i class="bi bi-arrow-left"></i> Volver a Facturas
        </button>
    `);

            $('#contentcxcreport').html(`<div class="text-center py-5"><div class="spinner-border text-primary"><span class="visually-hidden">Cargando...</span></div><p>Cargando documento ${numerod}...</p></div>`);

            $.ajax({
                type: 'POST',
                data: { tipofac: tipofac || 'Z', numerod: numerod, fksucu: fksucu || '' },
                url: '/openDoc',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    $('#contentcxcreport').html(response);
                    $('#contentcxcreport').find('table').addClass('table table-sm');
                },
                error: function() {
                    $('#contentcxcreport').html(`<div class="alert alert-danger text-center">Error al cargar el documento</div>`);
                }
            });
        }

        // Evento para volver al listado
        $(document).off('click', '#btnVolverListado').on('click', '#btnVolverListado', function() {
            cargarListadoFacturas();
        });


        $(document).off('click', '.openDocumentoEnLinea').on('click', '.openDocumentoEnLinea', function(e) {
            e.preventDefault();
            e.stopPropagation();
            verDocumento(
                $(this).data('fksucu'),
                $(this).data('numerod'),
                $(this).data('tipofac'),
                $(this).data('cliente')
            );
        });
    </script>
@endsection
