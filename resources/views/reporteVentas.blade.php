@extends('layouts.master')
@section('title')
    Venta de productos por sucursal
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
    </style>

@endsection
@section('content')
    <style>
        .tdline{
            border:1px solid #0072c5 !important;

        }
        .tdlineff{
            border-left:1px solid #fff !important;

            color: white !important;
            background-color: #0072c5 !important;
        }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <br />

                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">Reporte de Ventas </h4>
                    <p class="text-white-50 mb-0 small">  Desde  {{$fecha1}} Hasta {{$fecha2}}</p>
                </div>
                <div class="card-body   ">

                    <form  method="post" name="form1" id="form1" action="/reporte/venta">

                        <div class="row">
                            <div class="col-md-3  ">

                                <select class="form-select" data-choices  onchange="$('#form1').submit()"
                                        id="idsucu" name="fksucursal">
                                    <option  {{($fksucursal == '' or $fksucursal == 0 )?'selected':''}} value="0">Todas las Sucursales</option>
                                    @foreach($allsucursales as $sucu)
                                        <option  {{($sucu->id == $fksucursal)?'selected':''}} value="{{$sucu->id}}">{!! $sucu->descrip !!}</option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="col-md-3  ">

                                <div class="input-group">
                                    <input type="text" class="form-control" data-provider="flatpickr"
                                           data-range-date="true" data-date-format="d/m/Y"
                                           data-deafult-date="" name="fechasreport" readonly="readonly" value="{{$fechasreport}}"
                                    >
                                    <div class="input-group-text bg-primary border-primary text-white">
                                        <button type="submit" class="botoncal" >Consultar</button>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-3  ">
                                <div class="input-group">
                                    <div class="input-group-text bg-primary border-primary text-white">
                                        <button onclick="loadingreport('/resumenVentas')" type="button" class="botoncal" >VER Resumen</button>
                                    </div>
                                </div>
                            </div>
                        @csrf
                        @method('POST')
                    </form>


                </div>
            </div>
        </div>
    </div>


    <div id="contentReport"></div>
        <div class="row">
            <div class="col-md-12 mt-3 ">
                <table width="100%" border="0" class="" >

                    <tr>
                        <td width="30%" valign="top">
                            <table width="100%" border="0"  class="table table-borderless table-centered align-middle table-nowrap mb-0 " style="  color:#333; " >
                                <tr bgcolor="#fff">
                                    <td width="39%" height="30"align="center" class="tdline" >SUCURSAL</td>
                                    <td width="6%" align="center" class="tdlineff" > BS</td>
                                    <td width="5%" align="center" class="tdlineff" > BS.T</td>
                                    <td width="6%" align="center" class="tdlineff" > USD</td>
                                    <td width="5%" align="center" class="tdlineff" > USD.T</td>
                                    <td width="6%" align="center" class="tdlineff" > COP</td>
                                    <td width="5%" align="center" class="tdlineff" > COP.T</td>
                                    <td width="5%" align="center" class="tdlineff" > CREDITO </td>
                                    <td width="6%" align="center" class="tdlineff" > TOTAL USD</td>
                                </tr>
                                @if(isset($sucursales))
                                    @php $n=0;
                                        $tcancele    =0;
                                        $tcancelt    =0;
                                        $tdolares    =0;
                                        $ttransf     =0;
                                        $tpesos      =0;
                                        $tpeso_tranf =0;
                                        $teuros      =0;
                                        $tcredito    =0;
                                        $ttotalventa =0;
                                    @endphp
                                    @foreach($sucursales as $indexsuc => $sucursal)
                                        @php
                                            $tcancele    += $listado[$indexsuc]['cancele'];
                                            $tcancelt    += $listado[$indexsuc]['cancelt'];
                                            $tdolares    += $listado[$indexsuc]['dolares'];
                                            $ttransf     += $listado[$indexsuc]['transf'];
                                            $tpesos      += $listado[$indexsuc]['pesos'];
                                            $tpeso_tranf += $listado[$indexsuc]['peso_tranf'];
                                            $teuros      += $listado[$indexsuc]['euros'];
                                            $tcredito    += $listado[$indexsuc]['credito'];
                                            $ttotalventa += $listado[$indexsuc]['totalventa'];
                                        @endphp
                                        <tr @php if(($n%2)==0){echo 'bgcolor="#ddd"'; }else{echo 'bgcolor="#fff"';} @endphp>
                                            <td width="39%" height="30"align="left" class="tdline" >{{$sucursal}}</td>
                                            <td width="6%" align="right" class="tdline" >  {{($listado[$indexsuc]['cancele']    !=0)?number_format($listado[$indexsuc]['cancele']    ,2,',','.') : ''}}</td>
                                            <td width="5%" align="right" class="tdline" >  {{($listado[$indexsuc]['cancelt']    !=0)?number_format($listado[$indexsuc]['cancelt']    ,2,',','.') : ''}}</td>
                                            <td width="6%" align="right" class="tdline" >  {{($listado[$indexsuc]['dolares']    !=0)?number_format($listado[$indexsuc]['dolares']    ,2,',','.') : ''}}</td>
                                            <td width="5%" align="right" class="tdline" >  {{($listado[$indexsuc]['transf']     !=0)?number_format($listado[$indexsuc]['transf']     ,2,',','.') : ''}}</td>
                                            <td width="6%" align="right" class="tdline" >  {{($listado[$indexsuc]['pesos']      !=0)?number_format($listado[$indexsuc]['pesos']      ,2,',','.') : ''}}</td>
                                            <td width="5%" align="right" class="tdline" >  {{($listado[$indexsuc]['peso_tranf'] !=0)?number_format($listado[$indexsuc]['peso_tranf'] ,2,',','.') : ''}}</td>
                                            <td width="5%" align="right" class="tdline" >  {{($listado[$indexsuc]['credito']    !=0)?number_format($listado[$indexsuc]['credito']    ,2,',','.') : ''}}</td>
                                            <td width="6%" align="right" class="tdline" >  {{($listado[$indexsuc]['totalventa'] !=0)?number_format($listado[$indexsuc]['totalventa'] ,2,',','.') : ''}}</td>
                                        </tr>
                                        @php $n++; @endphp
                                    @endforeach
                                @endif

                                <tr bgcolor="#ddd">
                                    <td width="39%" height="30"align="left" class="tdline" >TOTALES</td>
                                    <td width="6%" align="right" class="tdline" >{{ ($tcancele    !=0 )? number_format($tcancele  ,2,',','.'): ''}} </td>
                                    <td width="5%" align="right" class="tdline" >{{ ($tcancelt    !=0 )?number_format($tcancelt   ,2,',','.'): ''}} </td>
                                    <td width="6%" align="right" class="tdline" >{{ ($tdolares    !=0 )?number_format($tdolares   ,2,',','.'): ''}} </td>
                                    <td width="5%" align="right" class="tdline" >{{ ($ttransf     !=0 )?number_format($ttransf    ,2,',','.'): ''}} </td>
                                    <td width="6%" align="right" class="tdline" >{{ ($tpesos      !=0 )?number_format($tpesos     ,2,',','.'): ''}} </td>
                                    <td width="5%" align="right" class="tdline" >{{ ($tpeso_tranf !=0 )?number_format($tpeso_tranf,2,',','.'): ''}} </td>
                                    <td width="5%" align="right" class="tdline" >{{ ($tcredito    !=0 )?number_format($tcredito   ,2,',','.'): ''}} </td>
                                    <td width="6%" align="right" class="tdline" >{{ ($ttotalventa !=0 )?number_format($ttotalventa,2,',','.'): ''}} </td>
                                </tr>

                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-12 mt-3">

                <table width="100%" border="0" class=" table table-borderless table-centered align-middle table-nowrap mb-0 " style="  color:#333; ">
                    <tr>
                        <td width="30%" height="53" align="center" class="titulo">
                            VENTA POR INSTANCIAS <br />
                            DESDE  {{$fecha1}} HASTA {{$fecha2}}
                            @php $tbasesuma = $costoxcantidad = $restaxcantidad = $tprecioventa= 0; @endphp
                        </td>
                    </tr>
                    <tr>
                        <td width="30%" valign="top">

                            <table width="100%" border="0"   style="  border:1px solid #69ABBF; ">
                                <tr  >
                                    <td width="19%" height="30"    align="center" class="tdline" style="font-size:12px">INSTANCIA DE INVENTARIO</td>
                                    <td width="7%"  align="center" class="tdline"   style="font-size:12px; background-color:#ddd" > CANTIDAD VENDIDA</td>
                                    <td width="18%" align="center" class="tdlineff" style="font-size:12px" > TOTAL VENTA</td>

                                    <td width="17%" align="center" class="tdlineff" style="font-size:12px" > TOTAL BASE VENTA</td>
                                    <td width="18%" align="center" class="tdlineff" style="font-size:12px" > TOTAL COSTO</td>
                                    <td width="12%" align="center" class="tdlineff" style="font-size:12px" > TOTAL UTILIDAD</td>
                                    <td width="9%"  align="center" class="tdlineff" style="font-size:12px" > % UTILIDAD</td>

                                </tr>

                                @php

                                    $dataesta      = '';
                                    $item          = 0;
                                    $restaxcantidad= 0;
                                    $totalcant     = 0;
                                    $sumautilidad  = 0;
                                    $utilidad      = 0;
                                    $sql           = '';
                                    list($d1,$m1,$y1) = explode("/",$fecha1);
                                    list($d2,$m2,$y2) = explode("/",$fecha2);

                                    $tantos = 0;
                                @endphp
                                @if(isset($arrayinsta) )
                                    @foreach ($arrayinsta as $listavend)
                                        @php

                                            $totalcant       += $listavend['cant'];
                                            $tprecioventa    += $listavend['precioventa'];
                                            $tbasesuma       += $listavend['basesuma'];
                                            $costoxcantidad  += $listavend['preciod'];
                                            $restaxcantidad  += $listavend['resta'];
                                            $utilidad         = 0;
                                            if($listavend['preciod'] != 0 and $listavend['preciod']  >0 )
                                                $utilidad = $listavend['resta'] / $listavend['preciod'];

                                            $sumautilidad  += $utilidad*100

                                        @endphp
                                        @if( $listavend['cant'] != 0)
                                            @php $tantos ++;@endphp
                                            <tr   @php if(($tantos%2)==0){echo 'bgcolor="#ddd"'; }else{echo 'bgcolor="#fff"';} @endphp>
                                                <td align="left" class="tdline" >{{$listavend['descrip']}}</td>
                                                <td align="center" class="tdline"  style=" background-color:#ddd"> {{number_format($listavend['cant'],0,',','.')}}</td>
                                                <td align="right"  class="tdline"  >{{number_format($listavend['precioventa'],2,',','.')}}</td>
                                                <td align="right"  class="tdline"  > {{number_format($listavend['basesuma'],2,',','.')}}</td>
                                                <td align="right"  class="tdline"  > {{number_format($listavend['preciod'],2,',','.')}} </td>
                                                <td align="right"  class="tdline"  > {{number_format($listavend['resta'],2,',','.')}}   </td>
                                                <td align="right"  class="tdline"  > {{number_format($utilidad*100,2,',','.')}} % </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                                <tr  >
                                    <td height="25" align="left"  >TOTALES</td>
                                    <td align="center" class="tdline"  style=" background-color:#ddd" > {{number_format($totalcant,0,',','.')}}</td>
                                    <td align="right"  class="tdline"  >$  {{ number_format($tprecioventa,2,',','.') }}</td>
                                    <td align="right"  class="tdline"  >$  {{number_format($tbasesuma,2,',','.') }}    </td>
                                    <td align="right"  class="tdline"  >$  {{number_format($costoxcantidad,2,',','.')}}</td>
                                    <td align="right"  class="tdline"  >$  {{number_format($restaxcantidad,2,',','.')}}</td>
                                    <td align="right"  class="tdline"  >
                                        @php
                                            if($costoxcantidad>0) echo number_format(($restaxcantidad/$costoxcantidad)*100,2,',','.');
                                        @endphp %
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                </table>

            </div>
            <div class="col-md-6 mt-3">
                <table width="100%" border="0" class=" table table-borderless table-centered align-middle table-nowrap mb-0 " style="  color:#333; ">
                    <tr>
                        <td width="30%" height="53" align="center" class="titulo">
                            VENTA AGRUPADAS DE VENDEDORES <br />
                            DESDE  {{$fecha1}} HASTA {{$fecha2}}
                        </td>
                    </tr>
                    <tr>
                        <td width="30%" valign="top">
                            <table width="100%" border="0"   class="table table-borderless table-centered align-middle table-nowrap mb-0 " style="  color:#333; ">
                                <tr bgcolor="#ddd">
                                    <td width="50%" align="center" class="tdlineff" style="font-size:12px"> VENDEDOR </td>
                                    <td width="25%" align="center" class="tdlineff" style="font-size:12px"> CANTIDAD </td>
                                    <td width="25%" align="center" class="tdlineff" style="font-size:12px"> TOTAL    </td>
                                </tr>
                                @php
                                    $tantos = $tcant  = $tventa   =0;
                                @endphp
                                @if(isset($vendedores) )
                                    @foreach($vendedores as $index => $vendedor)
                                        @php
                                            $tantos ++;
                                            $tcant  +=  $vendedor['cant'] ;
                                            $tventa += $vendedor['venta'] ;
                                        @endphp
                                        <tr @php if(($tantos%2)==0){echo 'bgcolor="#ddd"'; }else{echo 'bgcolor="#fff"';} @endphp>

                                            <td align="left"  class="tdline">{{ $vendedor['descrip'] }}</td>
                                            <td align="center"class="tdline">{{ $vendedor['cant'] }} </td>
                                            <td align="right" class="tdline">{{ number_format($vendedor['venta'],2,',','.') }} </td>
                                        </tr>
                                    @endforeach
                                @endif
                                <tr  >
                                    <td align="right" class="titulo tdline"  >&nbsp; </td>
                                    <td align="center"class="titulo tdline"  > {{$tcant}}</td>
                                    <td align="right" class="titulo tdline"  > {{ number_format($tventa,2,',','.')}}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6 mt-3">
                <table width="100%" border="0" class=" table table-borderless table-centered align-middle table-nowrap mb-0 " style="  color:#333; ">
                    <tr>
                        <td width="30%" height="53" align="center" class="titulo">
                            VENTA AGRUPADAS CADA SUCURSAL <br />
                            DESDE  {{$fecha1}} HASTA {{$fecha2}}
                        </td>
                    </tr>
                    <tr>
                        <td width="30%" valign="top">
                            <table width="100%" border="0"   class="table table-borderless table-centered align-middle table-nowrap mb-0 " style="  color:#333; ">
                                <tr bgcolor="#ddd">
                                    <td width="50%" align="center" class="tdlineff" style="font-size:12px">SUCURSAL</td>
                                    <td width="25%" align="center" class="tdlineff" style="font-size:12px"> CANTIDAD </td>
                                    <td width="25%" align="center" class="tdlineff"style="font-size:12px"> TOTAL    </td>
                                </tr>
                                @php
                                    $tantos = $tcant  = $tventa   =0;
                                @endphp
                                @if(isset($vsucursal) )
                                    @foreach($vsucursal as $index => $vendedor)
                                        @php
                                            $tantos ++;
                                            $tcant  +=  $vendedor['cant'] ;
                                            $tventa += $vendedor['venta'] ;
                                        @endphp
                                        <tr @php if(($tantos%2)==0){echo 'bgcolor="#ddd"'; }else{echo 'bgcolor="#fff"';} @endphp>

                                            <td align="left"  class="tdline">{{ $vendedor['descrip'] }}</td>
                                            <td align="center"class="tdline">{{ $vendedor['cant'] }} </td>
                                            <td align="right" class="tdline">{{ number_format($vendedor['venta'],2,',','.') }} </td>
                                        </tr>
                                    @endforeach
                                @endif
                                <tr  >
                                    <td align="right" class="titulo tdline"  >&nbsp; </td>
                                    <td align="center"class="titulo tdline"  > {{$tcant}}</td>
                                    <td align="right" class="titulo tdline"  > {{ number_format($tventa,2,',','.')}}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>


@endsection
@section('scripts')


    <script>
        function loadingreport(action){
            $('#form1').attr('action',action);
            $('#contentReport').html('<button class="btn btn-outline-primary btn-load"><span class="d-flex align-items-center"><span class="spinner-border flex-shrink-0" role="status"> <span class="visually-hidden"> Cargando...</span> </span> <span class="flex-grow-1 ms-2">Cargando... </span> </span> </button>');
            $('#form1').submit()
        }
    </script>

    <script src="{{ URL::asset('build/js/app.js') }}"></script>

@endsection
