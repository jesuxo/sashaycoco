@extends('layouts.master')
@section('title')
    Resultado General
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
        td.casilladollar::before {
            content: "$";
            margin-right: 2px;
        }
        td.casillaporc::after {
            content: "%";
            margin-left: 2px;
        }
        .report-table {
            font-size: 12px;
            border-collapse: collapse;
            width: 100%;
        }
        .report-table th {
            background-color: #0072c5;
            color: white;
            padding: 8px;
            text-align: center;
            border: 1px solid #0056a3;
        }
        .report-table td {
            padding: 6px;
            border: 1px solid #dee2e6;
        }
        .report-table tr:hover {
            background-color: #f8f9fa;
        }
        .casilladollar::before {
            content: "$";
            margin-right: 2px;
        }
        .casillaporc::after {
            content: "%";
            margin-left: 2px;
        }
        .filter-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .btn-consultar {
            background-color: #0072c5;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-consultar:hover {
            background-color: #0056a3;
        }

        .tdline{
            border:1px solid #0072c5 !important;
            font-size: 12px;
        }
        .tdlineff{
            border-left:1px solid #fff !important;
            font-size: 12px;
            color: white !important;
            background-color: #0072c5 !important;
        }
        #idsucu,.form-select, .choices__list,.choices__item,.choices__item--selectable{
            font-size: 12px !important;
        }
    </style>
@endsection
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0"> <i class="bi bi-diagram-3 me-2"></i> RESULTADO GENERAL</h4>
                    <p class="text-white-50 mb-0 small">
                        @if($fecha1 ?? '')
                          DESDE {{$fecha1}} HASTA {{$fecha2}}
                        @endif </p>
                </div>
                <div class="card-body   ">

                    <form method="post" name="form1" id="form1" action="/ventas/resultado" class="filter-section">
                        <div class="row">

                            <div class="col-md-2 mb-2">
                                <label class="form-label">Sucursal</label>
                                <select class="form-select" onChange="$('#form1').submit()" id="idsucu" name="fksucursal">
                                    <option value="" {{($fksucursal=='' or $fksucursal==0)?'selected':''}}>Seleccionar Sucursal</option>
                                    @foreach($allsucursales as $sucu)
                                        <option value="{{$sucu->id}}" {{($sucu->id == $fksucursal)?'selected':''}}>
                                            {{ $sucu->descrip }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <label class="form-label">Categoría</label>
                                <select class="form-select" onChange="$('#form1').submit()" name="codinst">
                                    <option value="" {{($codinst=='' or $codinst==0)?'selected':''}}>Todas las Categorías</option>
                                    @foreach($instancias as $instancia)
                                        <option value="{{$instancia->codinst}}" {{($instancia->codinst == $codinst)?'selected':''}}>
                                            {{ str_repeat('--', $instancia->nivel-1) }} {{ $instancia->descrip }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-2">
                                <label class="form-label">Período</label>
                                <input type="text" class="form-control" data-provider="flatpickr"
                                       data-range-date="true" data-date-format="d/m/Y"
                                       placeholder="Seleccionar fechas" name="fechasreport"
                                       readonly="readonly" value="{{$fechasreport}}">
                            </div>

                            <div class="col-md-3 mb-2">
                                <label class="form-label">Comparar con</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" data-provider="flatpickr"
                                           data-range-date="true" data-date-format="d/m/Y"
                                           id="fechasreport2" name="fechasreport2" readonly="readonly"
                                           value="{{$fechasreport2 ?? ''}}" placeholder="Opcional">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i> Consultar
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-2 mb-2">
                                <label class="form-label">Existencia Actual</label>
                                <select class="form-select" onChange="$('#form1').submit()" name="existenciaact">
                                    <option value="si" {{( $existenciaact == 'si')?'selected' : ''}}>Si</option>
                                    <option value="no" {{( $existenciaact == 'no')?'selected' : ''}}>NO</option>
                                </select>
                            </div>

                            @csrf
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>


    <div class="row">

        <div class="col-md-12 ">

            <div class="table-responsive table-card mt-3">
                <table  border="0" width="100%"  style="border-radius: 5px !important;" class="table table-borderless table-centered align-middle table-nowrap mb-0 mt-3" >
                    @php $ttexisten = 0; @endphp
                    @php  $ttt =  $tttpreciod = $tttcostod  = $tttc = 0; $tttsuc=[];$tttsucc=[]; $n=0;  @endphp
                    @foreach($itemventas as $index => $item)

                        @php  $subttexisten = 0;  $vectorsuc =  $vectorsucpreciod =  $vectorsuccostod =  $vectorsucc = [];  @endphp
                        <tr bgcolor="#f5f5f5">
                            <td align="center" height="30px" class="tdline" colspan="2"> Producto</td>

                            @foreach($sucursales as $indexs  => $vals)
                                <td align="center" class="tdlineff titulo"  style="display: none"  >
                                    {{$vals}}
                                </td>
                            @endforeach

                            <td align="center" class="tdline"  > </td>
                            <td align="center" class="tdlineff"   > {{(count($sucursales)>1)? 'UNDS TOTALES': 'UNDS TIENDA'}} </td>

                            @if(isset($fechasreport2) and $fechasreport2 != '')
                                <td align="center" class="tdlineff"   >Comparaci&oacute;n        </td>
                                <td align="center" class="tdlineff"  > DIF%</td>

                            @endif
                            @if($existenciaact == 'si')
                                <td align="center" class="tdline"  > </td>
                                <td align="center" class="tdline"  >ExAct</td>
                                <td align="center" class="tdline"  ></td>
                            @endif
                            <td align="center" class="tdlineff"  > COSTO</td>
                            <td align="center" class="tdlineff"  > VENTA </td>
                            <td align="center" class="tdlineff"  > UTILIDAD </td>
                            <td align="center" class="tdlineff"  > % UTILIDAD </td>
                        </tr>

                        @foreach($item as $index2 => $productos)
                            @php  $totalline  = $totallinepreciod = $totallinecostod =  0;
                            $exdecimal = $productos['exdecimal'];
                            @endphp
                            <tr @php if(($n%2)!=0){  echo 'bgcolor="#eeeeee"';} @endphp>
                                <td align="left" class="tdline" colspan="2">{{ $productos['descrip'] }}</td>

                                @foreach($sucursales as $indexs => $vals)
                                    @php
                                        $key = $index2.$indexs;
                                        $totalline        += (isset($cantidadprod[$key]))?$cantidadprod[$key]:0;
                                        $totallinepreciod += (isset($preciodprod[$key]))?$preciodprod[$key]:0;
                                        $totallinecostod  += (isset($costodprod[$key]))?$costodprod[$key]:0;


                                        $cantsuc = (isset($cantidadprod[$key]))?$cantidadprod[$key]:0;
                                        $precsuc = (isset($preciodprod[$key]))?$preciodprod[$key]:0;
                                        $costsuc = (isset($costodprod[$key]))?$costodprod[$key]:0;

                                        if(!isset($vectorsuc[$indexs])) $vectorsuc[$indexs] = 0;
                                        if(!isset($vectorsucpreciod[$indexs])) $vectorsucpreciod[$indexs] = 0;
                                        if(!isset($vectorsuccostod[$indexs])) $vectorsuccostod[$indexs] = 0;

                                        $vectorsuc[$indexs]        += $cantsuc;
                                        $vectorsucpreciod[$indexs] += $precsuc;
                                        $vectorsuccostod[$indexs]  += $costsuc;

                                    @endphp
                                    <td align="right" class="tdline  " style="font-size:11px; display: none"  >
                                        @if(isset($cantsuc) and $cantsuc>0)
                                            {{($exdecimal)?  $cantsuc +0 : number_format($cantsuc,0,',','.')}}
                                        @endif                                    </td>
                                @endforeach

                                <td align="right" class="tdline" style="font-size:11px" > </td>
                                <td align="center" class="tdline" style="font-size:11px" >
                                    @if(isset($totalline) and $totalline>0)
                                        {{($exdecimal)?  $totalline+0: number_format($totalline,0,',','.')}}
                                    @endif                                    </td>
                                @if(isset($fechasreport2) and $fechasreport2 != '' and isset($indexs))

                                    <td align="center" class="tdline"  style="font-size:11px"  >
                                        @php
                                            $key = $index2;
                                            $cantsucc = (isset($cantidadprod2[$key]))?$cantidadprod2[$key]:0;
                                            if(!isset($vectorsucc[$indexs]))
                                                    $vectorsucc[$indexs] = 0;
                                            $vectorsucc[$indexs] += $cantsucc;
                                            $difcant = $totalline-$cantsucc;

                                        @endphp
                                        {{($exdecimal)? number_format($cantsucc,3,',','.'): number_format($cantsucc,0,',','.')}}                                        </td>

                                    <td align="center" style="font-size:11px" st class="tdline  bg-opacity-50  {{($difcant>0)? 'bg-success text-white':'bg-danger text-white'}}" > {{($totalline>0)? number_format(($difcant/$totalline)*100  ,2,',','.').'%' : ''}}</td>

                                @endif
                                @if($existenciaact == 'si')
                                    @php
                                        $ttexisten    +=  (isset($productos['existen']))?$productos['existen']:0;
                                        $subttexisten +=  (isset($productos['existen']))?$productos['existen']:0;
                                    @endphp
                                    <td align="center" class="tdline "  > </td>
                                    <td align="center" class="tdline"  >{{ (isset($productos['existen']))?$productos['existen']:'--'}}</td>
                                    <td align="center" class="tdline"  ></td>
                                @endif
                                <td align="right" class="tdline casilladollar" style="font-size:11px" >
                                    @php
                                        $resta = $totallinecostod-$totallinepreciod;
                                    @endphp
                                    @if(isset($totallinepreciod) and $totallinepreciod>0)
                                        {{  number_format($totallinepreciod,2,',','.') }}
                                    @endif                                    </td>
                                <td align="right" class="tdline casilladollar" style="font-size:11px" >
                                    @if(isset($totallinecostod) and $totallinecostod>0)
                                        {{  number_format($totallinecostod,2,',','.') }}
                                    @endif                                    </td>
                                <td align="right" class="tdline casilladollar" style="font-size:11px" >
                                    @if(isset($totallinecostod) and isset($totallinepreciod))
                                        {{  number_format($totallinecostod-$totallinepreciod,2,',','.') }}
                                    @endif                                    </td>
                                <td align="right" class="tdline casillaporc" style="font-size:11px" >
                                    @if(isset($totallinecostod) and isset($totallinepreciod) and $resta>0 and $totallinepreciod>0)
                                        {{  number_format(($resta/$totallinepreciod)*100,2,',','.') }}
                                    @endif                                    </td>
                            </tr>
                            @php    $n++; @endphp
                        @endforeach
                        <tr @php if(($n%2)!=0){  echo 'bgcolor="#eeeeee"';} @endphp>
                            <td align="left" class="tdline" colspan="2">Total {{ $index }}</td>
                            @php  $totalline  = $totallinepreciod = $totallinecostod =0; @endphp
                            @foreach($sucursales as $indexs => $vals)
                                <td  align="center" class="tdline titulo" style="display: none"    >
                                    @if(isset($vectorsuc[$indexs]) and $vectorsuc[$indexs]>0)
                                        {{  $vectorsuc[$indexs]+0 }}
                                        @php
                                            if(!isset($tttsuc[$indexs]))
                                                    $tttsuc[$indexs] =0;
                                            $tttsuc[$indexs]  += $vectorsuc[$indexs];
                                            $totalline        += $vectorsuc[$indexs];        $ttt        += $vectorsuc[$indexs];
                                            $totallinepreciod += $vectorsucpreciod[$indexs]; $tttpreciod += $vectorsucpreciod[$indexs];
                                            $totallinecostod  += $vectorsuccostod[$indexs];  $tttcostod  += $vectorsuccostod[$indexs];
                                        @endphp
                                    @endif                                </td>
                            @endforeach

                            <td align="right" class="tdline"   > </td>
                            <td align="center" class="tdline"  >{{  $totalline  +0 }} </td>
                            @if(isset($fechasreport2) and $fechasreport2 != '' and isset($indexs))

                                <td align="center" class="tdline"   >
                                    @php
                                        if(!isset($tttsucc[$indexs]))
                                                $tttsucc[$indexs] =0;
                                        $tttsucc[$indexs] += $vectorsucc[$indexs];
                                        $tttc += $vectorsucc[$indexs];


                                    @endphp
                                    {{  $vectorsucc[$indexs] + 0 }}                                    </td>

                                <td align="center" class="tdline"  > </td>

                            @endif
                            @if($existenciaact == 'si')
                                <td align="center" class="tdline"  > </td>
                                <td align="center" class="tdline"  >{{$subttexisten}}</td>
                                <td align="center" class="tdline"  ></td>
                                @php $subttexisten = 0; @endphp
                            @endif
                            <td align="right" class="tdline casilladollar"  >
                                @php  $resta = $totallinecostod-$totallinepreciod;  @endphp
                                {{ number_format($totallinepreciod ,2,',','.') }}                                </td>
                            <td align="right" class="tdline casilladollar"  > {{ number_format($totallinecostod ,2,',','.') }} </td>
                            <td align="right" class="tdline casilladollar"  >{{ number_format($totallinecostod-$totallinepreciod ,2,',','.') }} </td>
                            <td align="right" class="tdline casillaporc"  >{{ ($resta>0)?number_format(($resta/$totallinepreciod)*100 ,2,',','.'):'' }} </td>
                        </tr>
                        <tr >
                            <td align="left"  colspan="2"> </td>

                            @foreach($sucursales as $indexs => $vals)
                                <td align="center" class="  titulo"  style="display: none"   >&nbsp;                                </td>
                            @endforeach

                            <td align="center" > </td>
                            @if(isset($fechasreport2) and $fechasreport2 != '' and isset($indexs))

                                <td align="center" class="tdline"   >     </td>
                                <td align="center" class="tdline"  > </td>

                            @endif

                            @if($existenciaact == 'si')
                                <td align="center" class="tdline"  > </td>
                                <td align="center" class="tdline"  ></td>
                                <td align="center" class="tdline"  ></td>
                            @endif

                            @if(isset($fechasreport2) and $fechasreport2 != '')
                                <td align="center" class="tdline"  > </td>
                                <td align="center" class="tdline"   >         </td>
                                <td align="center" class="tdline"   >         </td>
                            @endif
                            <td align="center" > </td>
                            <td align="center" > </td>
                            <td align="center" > </td>
                            <td align="center" > </td>
                        </tr>

                    @endforeach

                    <tr bgcolor="#f5f5f5">
                        <td align="center" height="30px" class="tdline" colspan="2">  </td>

                        @foreach($sucursales as $indexs  => $vals)
                            <td align="center" class="tdlineff titulo" style="display: none"    >
                                {{$vals}}                            </td>
                        @endforeach

                        <td align="center" class="tdline"  > </td>
                        <td align="center" class="tdlineff"   >{{(count($sucursales)>1)? 'TOTALES TIENDAS': 'TOTAL TIENDA'}}         </td>

                        @if(isset($fechasreport2) and $fechasreport2 != '' and isset($indexs))
                            <td align="center" class="tdline"   >Total Comparado        </td>
                            <td align="center" class="tdline"  ></td>
                        @endif
                        @if($existenciaact == 'si')
                            <td align="center" class="tdline"  > </td>
                            <td align="center" class="tdline"  >ExTotal</td>
                            <td align="center" class="tdline"  ></td>
                        @endif
                        <td align="center" class="tdlineff"  > TTL COSTO</td>
                        <td align="center" class="tdlineff"  > TTL VENTA </td>
                        <td align="center" class="tdlineff"  > TTL UTILIDAD </td>
                        <td align="center" class="tdlineff"  > PROM UTIL </td>
                    </tr>
                    <tr >
                        <td align="left"  colspan="2" class="tdline">Totales </td>

                        @foreach($sucursales as $indexs => $vals)
                            <td align="center" class=" tdline titulo"  style="display: none"  >
                                {{ (isset($tttsuc[$indexs]))?   $tttsuc[$indexs] +0 : '' }}                            </td>
                        @endforeach

                        <td align="center" class="tdline" > </td>
                        <td align="center" class="tdline" > {{  $ttt +0 }}</td>

                        @if(isset($fechasreport2) and $fechasreport2 != '' and isset($indexs))

                            <td align="center" class="tdline"   >   {{ (isset($tttsucc[$indexs]))? $tttsucc[$indexs]+0 : '' }}      </td>
                            <td align="center" class="tdline"  ></td>
                        @endif
                        @if($existenciaact == 'si')
                            <td align="center" class="tdline"  >  </td>
                            <td align="center" class="tdline"  >{{$ttexisten}}</td>
                            <td align="center" class="tdline"  >  </td>
                        @endif

                        <td align="center" class="tdline casilladollar" >
                            @php   $resta = $tttcostod-$tttpreciod;  @endphp
                            {{ number_format($tttpreciod ,2,',','.') }}</td>
                        <td align="center" class="tdline casilladollar" > {{ number_format($tttcostod ,2,',','.') }}</td>
                        <td align="center" class="tdline casilladollar" > {{ number_format($tttcostod-$tttpreciod ,2,',','.') }}</td>
                        <td align="center" class="tdline  casillaporc" > {{ ($resta>0)?number_format(($resta/$tttpreciod)*100 ,2,',','.'):'' }}</td>
                    </tr>
                </table>
            </div>
            <br>
            <br>
            <br>
        </div>
    </div>

@endsection
@section('scripts')

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

@endsection

