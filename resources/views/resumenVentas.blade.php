{{-- resources/views/resumenVentas.blade.php --}}

@extends('layouts.master')
@section('title')
    Inicio
@endsection
@section('css')
    <style>
        .botoncal{
            background: transparent;
            border: none;
            color: white;
        }
        .botoncal:hover{
            font-size: 13px;
        }
        .linkunderline:hover{
            text-decoration: underline;
        }

        .tdline{
            border:1px solid #0072c5 !important;
        }
        .tdlineff{
            border-left:1px solid #fff !important;
            color: white !important;
            background-color: #0072c5  !important;
        }

        /* Nuevos estilos para resúmenes */
        .resumen-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            color: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .total-card {
            background: white;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            transition: transform 0.2s;
        }
        .total-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,114,197,0.2);
        }

        .total-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 24px;
        }

        .total-value {
            font-size: 24px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .total-label {
            font-size: 13px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .comparison-card {
            background: #f8fafc;
            border-radius: 10px;
            padding: 15px;
            border-bottom: 4px solid #0072c5;
            margin-bottom: 15px;
        }

        .badge-total {
            background: rgba(0,114,197,0.1);
            color: #0072c5;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .kpi-mini {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 500;
        }
        .kpi-up {
            background: #c6f6d5;
            color: #22543d;
        }
        .kpi-down {
            background: #fed7d7;
            color: #742a2a;
        }
    </style>
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">Resumen de ventas de las sucursales y la cobranza en el periodo</h4>
                    <p class="text-white-50 mb-0 small">{{$fechasreport}}</p>
                </div>
                <div class="card-body  d-flex justify-content-between">

                    <form  method="post" name="form1" id="form1">
                        @csrf
                        @method('POST')

                        <div class="row g-2 align-items-end">
                            <div class="col-auto">
                                <div class="input-group">
                                    <input type="text" class="form-control" data-provider="flatpickr"
                                           data-range-date="true" data-date-format="d/m/Y" id="fechasreport"
                                           data-deafult-date="" name="fechasreport" readonly="readonly" value="{{$fechasreport}}">
                                    <div class="input-group-text bg-primary border-primary text-white">
                                        <button type="submit" class="botoncal">Consultar</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Filtro de Sucursal -->
                            <div class="col-auto">
                                <select name="fksucursal" id="fksucursal" onchange="$('#form1').submit()"
                                        class="form-select" style="min-width: 200px;">
                                    <option value="">Todas las sucursales</option>
                                    @foreach($sucursalesList as $suc)
                                        <option value="{{$suc->id}}" {{ $fksucursal == $suc->id ? 'selected' : '' }}>
                                            {{$suc->descrip}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-auto">
                                <select name="fkestacion" {{(!$fksucursal)? 'disabled': ''}} id="fkestacion"
                                        class="form-select" style="min-width: 200px;"  onchange="$('#form1').submit()">
                                    <option value="">{{(!$fksucursal)? 'Seleccione Sucursal ': 'Todas las Estaciones'}}</option>
                                    @foreach($estaciones as $index => $est)
                                        <option value="{{$est['codesta']}}" {{ $fkestacion == $est['codesta'] ? 'selected' : '' }}>
                                            {{$est['codesta']}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto" style=" height: 40px; display:flex; align-items: center; justify-content: space-between;">
                                @php
                                    $partes = explode(" to ", $fechasreport);
                                    $fecha1 = $partes[0];
                                    $fecha2 = $partes[1] ?? $partes[0];

                                    if($fecha1 != $fecha2){
                                        $fechasreport = "$fecha1 - $fecha2";
                                    }else{
                                        $fechasreport = "$fecha1";
                                    }
                                    if($fecha1 == $fecha2):
                                        list($d, $m, $y) = explode('/', $fecha1);
                                        $fechaanterior = date('d/m/Y', strtotime("$y-$m-$d -1 day"));
                                        $fechaposterior = date('d/m/Y', strtotime("$y-$m-$d +1 day"));
                                @endphp
                                <a href="javascript:;" onclick="$('#fechasreport').val('{{$fechaanterior}}'); loadingreport('/resumenVentas')"> << {{$fechaanterior}}</a>
                                <i class="ri-calendar-2-fill" style="margin: 0px 10px;"></i>
                                <a href="javascript:;" onclick="$('#fechasreport').val('{{$fechaposterior}}'); loadingreport('/resumenVentas')"> {{$fechaposterior}} >> </a>
                                @php
                                    endif;
                                @endphp
                            </div>
                            @if($fkestacion or $fksucursal)
                                <div class="col-auto">
                                    <a href="javascript:;"  onclick="limpiarFiltros()"
                                       class="d-flex align-items-center p-1 m-2 linkunderline" style="text-align: center; border: 1px solid #0072c5; border-radius: 5px;">
                                        <i class="bi bi-eraser"></i> Limpiar
                                    </a>
                                </div>
                                <script>
                                    function limpiarFiltros() {
                                        $('#fksucursal').val('');
                                        $('#fkestacion').val('');
                                        $('#form1').submit();
                                    }
                                </script>
                            @endif
                        </div>

                    </form>

                    <a  href="javascript:;" onclick="loadingreport('/reporte/venta')"
                        class="d-flex align-items-center p-1 m-2 linkunderline" style="text-align: center; border: 1px solid #0072c5; border-radius: 5px;">
                        Reporte Ventas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class=" col-lg-7 ">
            <div class=" row ">
                <div class=" col-12 ">
                    <div class="card card-height-100" style="background-color: #fafafa; border: 1px solid #0072c5;">

                        <div class="card-body" id="contentReport" data-simplebar  style="height: 363px; max-height: 363px;" >

                            @if(isset($sucursales))
                                <div class="table-responsive table-card ">
                                    <table class="table table-borderless table-striped align-middle table-sm fs-14 mb-0">
                                        <thead class="text-muted table-light">
                                        <tr>
                                            <th width="30%" class="tdlineff">  Sucursal  </th>
                                            <th  width="15%"class="tdlineff" style="text-align: center !important" align="center">Total</th>
                                            <th  width="15%"class="tdlineff"style="text-align: center !important" align="center">Contado</th>
                                            <th  width="15%"class="tdlineff" style="text-align: center !important" align="center">Cr&eacute;dito</th>
                                            <th  width="10%"class="tdlineff"style="text-align: center !important" align="center">Facts</th>
                                            <th  width="10%"class="tdlineff" style="text-align: center !important" align="center">Devs</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $porc     = 0;
                                            $tantomto = 0;
                                            $tcontado = 0;
                                            $tcredito = 0;
                                            $tfacs    = 0;
                                            $tdevs    = 0;
                                            if(isset($sucursales)){
                                                foreach ($sucursales as $sucursal){
                                                    $tantomto += $sucursal['contado']+$sucursal['credito'];
                                                    $tcontado += $sucursal['contado'];
                                                    $tcredito += $sucursal['credito'];
                                                    $tfacs    += $sucursal['facturas'];
                                                    $tdevs    += $sucursal['devoluciones'];
                                                }
                                            }
                                        @endphp
                                        @if(isset($sucursales))
                                            @foreach($sucursales as $index => $sucursal)
                                                @php
                                                    $venta = $sucursal['contado']+$sucursal['credito'];
                                                    if($tantomto>0)
                                                        $porc = ($venta / $tantomto) *100;

                                                @endphp

                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <a href="javascript:;"  class="fw-medium fs-14 mb-0 reporteventasucursalmodal"
                                                               data-fksucursal   = "{{$sucursal['id']}}"
                                                               data-fechasreport = "{{$fechasreport}}"
                                                               data-fkestacion   = "{{$fkestacion}}"
                                                               data-contado      = ""
                                                               data-credito      = ""
                                                               onclick="$('#titulorepventasucu').html('REPORTE DE VENTAS DE {{$sucursal['descrip']}}')"
                                                               data-bs-toggle="modal" data-bs-target="#reporteventasucursalmodal"
                                                            >
                                                                {{$sucursal['descrip']}}
                                                            </a>
                                                        </div>
                                                    </td>
                                                    <td align="right">
                                                        $ {{  number_format(($sucursal['contado']+$sucursal['credito']),2,',','.')  }}
                                                    </td>

                                                    <td align="right">
                                                        $ {{  number_format($sucursal['contado'],2,',','.')  }}
                                                    </td>
                                                    <td align="right">
                                                        <a href="javascript:;"  class="fw-medium fs-14 mb-0 reporteventasucursalmodal"
                                                           data-fksucursal   = "{{$sucursal['id']}}"
                                                           data-fechasreport = "{{$fechasreport}}"
                                                           data-contado      = "1"
                                                           data-credito      = "1"
                                                           onclick="$('#titulorepventasucu').html('REPORTE DE VENTAS DE {{$sucursal['descrip']}}')"
                                                           data-bs-toggle="modal" data-bs-target="#reporteventasucursalmodal"
                                                        >
                                                            $ {{number_format($sucursal['credito'],2,',','.') }}
                                                        </a>

                                                    </td>
                                                    <td align="center" class="text-success">
                                                        {{$sucursal['facturas'] }}
                                                    </td>
                                                    <td align="center" class="text-danger">
                                                        {{$sucursal['devoluciones'] }}
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="progress progress-sm" role="progressbar" aria-label="Success example"
                                                             aria-valuenow="{{$porc}}" aria-valuemin="0" aria-valuemax="100">
                                                            <div class="progress-bar bg-success bg-opacity-50 progress-bar-striped progress-bar-animated"
                                                                 style="width: {{$porc}}%">

                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                        @if($tantomto > 0)
                                            <tfoot style="background: #e9ecef; font-weight: bold;">
                                            <tr>
                                                <td  width="30%" >   Totales  </td>
                                                <td  width="15%"  class="text-primary" style="text-align: right !important" align="center">
                                                    {{number_format($tantomto,2,',','.')}}
                                                </td>
                                                <td  width="15%" class="text-primary" style="text-align: right !important" align="center">
                                                    {{number_format($tcontado,2,',','.')}}
                                                </td>
                                                <td  width="15%"  class="text-primary" style="text-align: right !important" align="center">
                                                    {{number_format($tcredito,2,',','.')}}
                                                </td>
                                                <td  width="10%" class="text-primary" style="text-align: center !important" align="center">
                                                    {{$tfacs+0}}
                                                </td>
                                                <td  width="10%" class="text-primary" style="text-align: center !important" align="center">
                                                    {{$tdevs+0}}
                                                </td>
                                            </tr>

                                            </tfoot>
                                        @endif
                                    </table>
                                </div>
                            @endif

                        </div>
                        <div class="card-body mt-4"  style="height: 242px">

                            @if(isset($sucursales))
                                <div class="table-responsive table-card  " data-simplebar  style="height: 234px; max-height: 234px;">
                                    <table class="table table-borderless table-striped align-middle table-sm fs-14 mb-0">
                                        <thead class="text-muted table-light">
                                        <tr>
                                            <th width="30%" class="tdlineff">  Sucursal  </th>
                                            <th  width="20%"class="tdlineff" style="text-align: right !important" align="right">Cobranza ($)</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $cobranzas  = 0;
                                            $tcobranzas = 0;
                                        @endphp
                                        @if(isset($sucursales))
                                            @foreach($sucursales as $index => $sucursal)
                                                @if($sucursal['cobranzas'] >0)
                                                    @php
                                                        $cobranzas  += $sucursal['cobranzas'];
                                                        $tcobranzas += $sucursal['tcobranzas'];
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <a href="javascript:;"  class="fw-medium fs-14 mb-0 reporteventasucursalmodal"
                                                                   data-fksucursal   = "{{$sucursal['id']}}"
                                                                   data-fechasreport = "{{$fechasreport}}"
                                                                   data-fkestacion   = "{{$fkestacion}}"
                                                                   data-contado      = ""
                                                                   data-credito      = ""
                                                                   onclick="$('#titulorepventasucu').html('REPORTE DE VENTAS DE {{$sucursal['descrip']}}')"
                                                                   data-bs-toggle="modal" data-bs-target="#reporteventasucursalmodal"
                                                                >
                                                                    {{$sucursal['descrip']}}
                                                                </a>
                                                            </div>
                                                        </td>
                                                        <td align="right">
                                                            $ {{  number_format($sucursal['cobranzas'],2,',','.')  }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endif
                                        </tbody>
                                        <!-- Total de cobranzas agregado -->
                                        @if($cobranzas > 0)
                                            <tfoot style="background: #e9ecef; font-weight: bold;">
                                                <tr>
                                                    <td align="right">TOTAL COBRANZAS:</td>
                                                    <td align="right" class="text-primary">$ {{ number_format($cobranzas,2,',','.') }}</td>
                                                </tr>
                                            </tfoot>
                                        @endif
                                    </table>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>

                <div class="col-12">

                </div>
            </div>
        </div>
        <div class=" col-lg-5 ">
            <div class="row g-0 text-center"  >
                <div class="card card-animate" style="border: 1px solid #0072c5;">
                    <div class="card-body row">
                        <div class="col-4 col-sm-4 p-0">
                            <div class="p-1 pt-3 pb-3 border border-dashed border-bottom-0">
                                <div class="total-icon" style="background: rgba(72,187,120,0.1); color: #48bb78;">
                                    <i class="bi bi-cash"></i>
                                </div>
                                <h5 class="mb-1">$<span>{{number_format($contado,2,',','.')}}</span></h5>
                                <p class="text-muted mb-0">Contado</p>
                            </div>
                        </div>
                        <div class="col-4 col-sm-4 p-0">
                            <div class="p-1 pt-3 pb-3 border border-dashed border-start-0 border-bottom-0">
                                <div class="total-icon" style="background: rgba(237,137,54,0.1); color: #ed8936;">
                                    <i class="bi bi-credit-card"></i>
                                </div>
                                <h5 class="mb-1">$<span >{{number_format($credito,2,',','.')}}</span>
                                </h5>
                                <p class="text-muted mb-0">Cr&eacute;dito</p>
                            </div>
                        </div>
                        <div class="col-4 col-sm-4 p-0">
                            <div class="p-1 pt-3 pb-3 border border-dashed border-start-0 border-bottom-0">
                                <div class="total-icon" style="background: rgba(159,122,234,0.1); color: #9f7aea;">
                                    <i class="bi bi-bank"></i>
                                </div>
                                <h5 class="mb-1">$<span >{{number_format($cobranzas,2,',','.')}}</span>
                                </h5>
                                <p class="text-muted mb-0">$ Cobrado</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="comparison-card d-flex justify-content-between align-items-center"
                                 style="border-bottom-color: #06d6a0;">
                                <div class="text-center" style="margin: auto;display: block;">
                                    <span class="text-muted">Facturas  </span>
                                    <h3 class="mb-0 text-success">{{ $facturas }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="comparison-card d-flex justify-content-between align-items-center"
                                 style="border-bottom-color: #f56565;">
                                <div class="text-center" style="margin: auto;display: block;">
                                    <span class="text-muted">Devoluciones</span>
                                    <h3 class="mb-0 text-danger">{{ $devoluciones }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="comparison-card d-flex justify-content-between align-items-center"
                                 style="border-bottom-color: #f1be46;">
                                <div class="text-center" style="margin: auto;display: block;">
                                    <span class="text-muted">Cobranzas</span>
                                    <h3 class="mb-0 text-warning">{{ $totalCobranzasCantidad }}</h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-12 p-0">
                            <div class="p-5 pt-3 d-flex justify-content-between pb-3 border border-dashed border-start-0">
                                <p class="text-muted mb-0">Total (Contado+Cr&eacute;dito)</p>
                                <h5 class="mb-1 text-primary">${{number_format($credito+$contado,2,',','.')}}</h5>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="card"  data-simplebar  style="height: 269px; background-color: #fafafa;  border: 1px solid #0072c5;">
                @if(isset($jesusus))
                <div class="card-body mt-4"   >

                    @if(isset($clases))
                        <div class="table-responsive table-card  "  >
                            <table class="table table-borderless table-striped align-middle table-sm fs-14 mb-0">
                                <thead class="text-muted table-light">
                                <tr>
                                    <th width="30%"   class="tdlineff">  Clasificaci&oacute;n  </th>
                                    <th  width="20%"  class="tdlineff" style="text-align: right !important; cursor:pointer;" align="right" onclick="loadingreport('/reporte/instpagobs')"><i class="bi bi-link"></i> Monto (BS)</th>
                                    <th  width="20%"  class="tdlineff" style="text-align: right !important; cursor:pointer;" align="right" onclick="loadingreport('/reporte/instpagodolares')"><i class="bi bi-link"></i> Monto (USD)</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $tbs  = 0;
                                    $usd = 0;
                                @endphp
                                @if(isset($clases))
                                    @foreach($clases as $index => $clase)
                                        @if($index  !='')
                                            @php
                                                $tbs  += ( isset($listado[$index])    and $listado[$index]  >0)? $listado[$index]: 0;
                                                $usd  += ( isset($listadousd[$index]) and $listadousd[$index]  >0)? $listadousd[$index]: 0;
                                            @endphp
                                            <tr>
                                                <td align="left"> {{$index}}</td>
                                                <td align="right">   {{ (isset($listado[$index]))? ' '.number_format($listado[$index],2,',','.'):''  }}  </td>
                                                <td align="right">   {{ (isset($listadousd[$index]))? '$ '.number_format($listadousd[$index],2,',','.'):''  }} </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                                <tr>
                                    <td class="tdlineff">   </td>
                                    <td align="right" class="tdlineff">  {{  (isset($tbs) and $tbs >0)? number_format($tbs ,2,',','.'): ''  }}  </td>
                                    <td align="right" class="tdlineff">  {{  (isset($tbs) and $tbs >0)?'$ '.number_format($usd ,2,',','.') : ''  }}  </td>
                                </tr>
                                </tbody>
                                <!-- Totales de métodos de pago agregados -->

                            </table>
                        </div>
                    @endif

                </div>
                @endif
            </div>
        </div>

    </div>

    <div class="modal fade" id="reporteventasucursalmodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
        <div class="modal-dialog modal-fullscreen modal-dialog-scrollable" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="titulorepventasucu">REPORTE DE VENTAS POR SUCURSAL</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body" id="contentreporteventasucu">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">  CERRAR</button>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <script>
        function loadingreport(action){
            $('#form1').attr('action',action);
            $('#contentReport').html('<button class="btn btn-outline-primary btn-load"><span class="d-flex align-items-center"><span class="spinner-border flex-shrink-0" role="status"> <span class="visually-hidden"> Cargando...</span> </span> <span class="flex-grow-1 ms-2">Cargando... </span> </span> </button>');
            $('#form1').submit()
        }

        $('.reporteventasucursalmodal').unbind('click').bind('click',function () {
            var fksucursal   = $(this).attr('data-fksucursal');
            var contado      = $(this).attr('data-contado');
            var credito      = $(this).attr('data-credito');
            var fechasreport = $(this).attr('data-fechasreport');
            var fkestacion   = $(this).attr('data-fkestacion');  // AGREGAR ESTA LÍNEA

            $('#contentreporteventasucu').html('<button class="btn btn-outline-primary btn-load"><span class="d-flex align-items-center"><span class="spinner-border flex-shrink-0" role="status"> <span class="visually-hidden"> Cargando...</span> </span> <span class="flex-grow-1 ms-2">Cargando... </span> </span> </button>');
            $.ajax({
                type:'post',
                data:{
                    credito: (credito)? credito : '',
                    contado: (contado)? contado : '',
                    fechasreport: (fechasreport)? fechasreport : '',
                    fksucursal: (fksucursal)? fksucursal : '',
                    fkestacion: (fkestacion)? fkestacion : ''   // AGREGAR ESTA LÍNEA
                },
                url:'/reporte/venta/sucu',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function(response) {
                    $('#contentreporteventasucu').html(response);
                }
            });
        });
    </script>
@endsection
