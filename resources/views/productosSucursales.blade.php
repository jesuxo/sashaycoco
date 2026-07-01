@extends('layouts.master')
@section('title')
    VENTAS POR SUCURSAL
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
    </style>
@endsection
@section('content')
    <style>
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
        .choices__list,.choices__item,.choices__item--selectable{
            font-size: 12px !important;
        }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0"> <i class="bi bi-diagram-3 me-2"></i>Ventas de productos por sucursales</h4>
                    <p class="text-white-50 mb-0 small">
                        @if($fecha1 ?? '')
                            DESDE {{$fecha1}} HASTA {{$fecha2}}
                        @endif </p>
                </div>
                <div class="card-body   ">

                    <form  method="post" name="form1" id="form1" action="/ventas/productos/sucursales">
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
                            @method('POST')
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

                    @php  $ttt = 0; $tttc = 0; $tttsuc=[];$tttsucc=[]; $n = $ttexisten = 0; @endphp
                    @foreach($itemventas as $index => $item)

                        @php   $vectorsuc = [];  $vectorsucc = [];  $subttexisten = 0; @endphp
                        <tr bgcolor="#f5f5f5">
                            <td align="center" height="30px" class="tdline" colspan="2"> Producto</td>

                            @foreach($sucursales as $indexs  => $vals)
                                <td align="center" class="tdlineff titulo"   >
                                    {{$vals}}
                                </td>
                            @endforeach
                            @if(count($sucursales)>1)
                                <td align="center" class=" tdline"  > </td>
                                <td align="center" class="tdline"   >Totales        </td>
                            @endif
                            @if(isset($fechasreport2) and $fechasreport2 != '')
                                <td align="center" class="tdline"  > </td>
                                <td align="center" class="tdline"   >Comparaci&oacute;n        </td>
                            @endif

                            @if($existenciaact == 'si')
                                <td align="center" class="tdline"  > </td>
                                <td align="center" class="tdlineff"  >ExAct</td>
                            @endif
                        </tr>

                        @foreach($item as $index2 => $productos)
                            @php  $totalline  = 0;
                            $exdecimal = $productos['exdecimal'];
                            @endphp
                            <tr @php if(($n%2)!=0){  echo 'bgcolor="#eeeeee"';} @endphp>
                                <td align="left" class="tdline" colspan="2">{{ $productos['descrip'] }}</td>

                                @foreach($sucursales as $indexs => $vals)
                                    @php
                                        $key = $index2.$indexs;
                                        $totalline += (isset($cantidadprod[$key]))?$cantidadprod[$key]:0;
                                        $cantsuc = (isset($cantidadprod[$key]))?$cantidadprod[$key]:0;
                                        if(!isset($vectorsuc[$indexs]))
                                                $vectorsuc[$indexs] = 0;
                                        $vectorsuc[$indexs] += $cantsuc;
                                    @endphp
                                    <td align="center" class="tdline  " style="font-size:11px"  >
                                        @if(isset($cantsuc) and $cantsuc>0)
                                            {{($exdecimal)? number_format($cantsuc,3,',','.'): number_format($cantsuc,0,',','.')}}
                                        @endif
                                    </td>
                                @endforeach
                                @if(count($sucursales)>1)
                                    <td align="right" class="tdline" style="font-size:11px" > </td>
                                    <td align="center" class="tdline" style="font-size:11px" >
                                        @if(isset($totalline) and $totalline>0)
                                            {{($exdecimal)? number_format($totalline,3,',','.'): number_format($totalline,0,',','.')}}
                                        @endif
                                    </td>
                                @endif
                                @if(isset($fechasreport2) and $fechasreport2 != '' and isset($indexs))
                                    <td align="center" class="tdline"  > </td>
                                    <td align="center" class="tdline"  style="font-size:11px"  >
                                        @php
                                            $key = $index2;
                                            $cantsucc = (isset($cantidadprod2[$key]))?$cantidadprod2[$key]:0;
                                            if(!isset($vectorsucc[$indexs]))
                                                    $vectorsucc[$indexs] = 0;
                                            $vectorsucc[$indexs] += $cantsucc;
                                        @endphp
                                        {{($exdecimal)? number_format($cantsucc,3,',','.'): number_format($cantsucc,0,',','.')}}
                                    </td>
                                @endif
                                @if($existenciaact == 'si')
                                    @php
                                        $ttexisten    +=  (isset($productos['existen']))?$productos['existen']:0;
                                        $subttexisten +=  (isset($productos['existen']))?$productos['existen']:0;
                                    @endphp
                                    <td align="center" class="tdline"  > </td>
                                    <td align="center" class="tdline"  >{{ (isset($productos['existen']))?$productos['existen']:'--'}}</td>
                                @endif
                            </tr>
                            @php    $n++; @endphp
                        @endforeach
                        <tr @php if(($n%2)!=0){  echo 'bgcolor="#eeeeee"';} @endphp>
                            <td align="left" class="tdline" colspan="2">Total {{ $index }}</td>
                            @php  $totalline  = 0; @endphp
                            @foreach($sucursales as $indexs => $vals)
                                <td  align="center" class="tdline titulo"  >
                                    @if(isset($vectorsuc[$indexs]) and $vectorsuc[$indexs]>0)
                                        {{ $vectorsuc[$indexs]  }}
                                        @php
                                            if(!isset($tttsuc[$indexs]))
                                                    $tttsuc[$indexs] =0;
                                            $tttsuc[$indexs] +=$vectorsuc[$indexs];
                                            $totalline+=$vectorsuc[$indexs]; $ttt+=$vectorsuc[$indexs];
                                        @endphp
                                    @endif
                                </td>
                            @endforeach
                            @if(count($sucursales)>1)
                                <td align="right" class="tdline"> </td>
                                <td align="center" class="tdline">{{$totalline +0 }} </td>
                            @endif
                            @if(isset($fechasreport2) and $fechasreport2 != '' and isset($indexs))
                                <td align="center" class=" tdline"  > </td>
                                <td align="center" class="tdline"   >
                                    @php
                                        if(!isset($tttsucc[$indexs]))
                                                $tttsucc[$indexs] =0;
                                        $tttsucc[$indexs] += $vectorsucc[$indexs];
                                        $tttc += $vectorsucc[$indexs];
                                    @endphp
                                    {{ $vectorsucc[$indexs] +0}}
                                </td>
                            @endif
                            @if($existenciaact == 'si')
                                <td align="center" class="tdline"  > </td>
                                <td align="center" class="tdline"  >{{$subttexisten}}</td>
                            @endif
                        </tr>
                        <tr >
                            <td align="left"  colspan="2"> </td>

                            @foreach($sucursales as $indexs => $vals)
                                <td align="center" class="  titulo"  >
                                    &nbsp;
                                </td>
                            @endforeach
                            @if(count($sucursales)>1)
                                <td align="center" > </td>
                                <td align="center" > </td>
                            @endif
                            @if(isset($fechasreport2) and $fechasreport2 != '')
                                <td align="center" class="  "  > </td>
                                <td align="center" class=" "   >         </td>
                            @endif
                            @if($existenciaact == 'si')
                                <td align="center" class=" "  > </td>
                                <td align="center" class=" "  > </td>
                            @endif
                        </tr>

                    @endforeach

                    <tr bgcolor="#f5f5f5">
                        <td align="center" height="30px" class="tdline" colspan="2">  </td>

                        @foreach($sucursales as $indexs  => $vals)
                            <td align="center" class="tdlineff titulo"   >
                                {{$vals}}
                            </td>
                        @endforeach
                        @if(count($sucursales)>1)
                            <td align="center" class="tdline"  > </td>
                            <td align="center" class="tdline"   >Totales        </td>
                        @endif
                        @if(isset($fechasreport2) and $fechasreport2 != '' and isset($indexs))
                            <td align="center" class="tdline "  > </td>
                            <td align="center" class="tdline"   >Total Comparado        </td>
                        @endif
                        @if($existenciaact == 'si')
                            <td align="center" class="tdline"  > </td>
                            <td align="center" class="tdlineff"  >ExAct</td>
                        @endif
                    </tr>
                    <tr >
                        <td align="left"  colspan="2" class="tdline">Totales </td>

                        @foreach($sucursales as $indexs => $vals)
                            <td align="center" class=" tdline titulo"  >
                                {{ (isset($tttsuc[$indexs]))?  $tttsuc[$indexs]+0 : '' }}
                            </td>
                        @endforeach
                        @if(count($sucursales)>1)
                            <td align="center" class="tdline " > </td>
                            <td align="center" class="tdline" > {{ $ttt  +0 }}</td>
                        @endif
                        @if(isset($fechasreport2) and $fechasreport2 != '' and isset($indexs))
                            <td align="center" class="tdline "  >  </td>
                            <td align="center" class="tdline"   >   {{ (isset($tttsucc[$indexs]))? $tttsucc[$indexs]+0 : '' }}      </td>
                        @endif
                        @if($existenciaact == 'si')
                            <td align="center" class="tdline"  > </td>
                            <td align="center" class="tdline"  > {{$ttexisten}}</td>
                        @endif
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
