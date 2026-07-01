@extends('layouts.master')
@section('title')
    Lista de productos - {{$busqueda}}
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css">

    <!--Swiper slider css-->
    <link href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css">
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

    <div class="row">
        <form  method="post" name="form1" id="form1" action="/ventas/productos/sucursales">
            @csrf
            @method('POST')
            <div class="col-md-3 order-last">
                <div class="input-group">
                    <input type="text" class="form-control"  name="busqueda" placeholder="Busqueda de productos"
                           value="{{$busqueda}}" />
                    <div class="input-group-text bg-primary border-primary text-white">
                        <button type="submit" class="botoncal" >Buscar</button>
                    </div>
                </div>
            </div>
            <div class="col-md-12 ">
                <table  border="0" width="100%"  style="border-radius: 5px !important;" class="mt-3" >

                    @php  $ttt = 0; $tttsuc=[]; $n=0;  @endphp
                    @foreach($itemventas as $index => $item)
                        @php   $vectorsuc = [];  @endphp
                        <tr bgcolor="#f5f5f5">
                            <td align="center" height="30px" class="tdline" colspan="2"> Producto</td>

                            @foreach($sucursales as $indexs  => $vals)
                                <td width="50px"   align="center" class="tdlineff titulo"   >
                                    {{$vals}}
                                </td>
                            @endforeach
                            <td width="50px"   align="center" class="tdline"  > </td>
                            <td width="50px"   align="center" class="tdline"   >Totales        </td>
                        </tr>

                        @foreach($item as $index2 => $productos)
                            @php  $totalline  = 0;
                            $exdecimal = $productos['exdecimal'];
                            @endphp
                            <tr @php if(($n%2)!=0){  echo 'bgcolor="#eeeeee"';} @endphp>
                                <td align="left" class="tdline" colspan="2">{{ $productos['descrip'] }}</td>
                                <td width="50px"   align="right" class="tdline" style="font-size:11px" > </td>
                                <td width="50px"   align="right" class="tdline" style="font-size:11px" >
                                    @if(isset($totalline) and $totalline>0)
                                    {{($exdecimal)? number_format($totalline,3,',','.'): number_format($totalline,0,',','.')}}
                                    @endif
                                </td>
                            </tr>
                            @php    $n++; @endphp
                        @endforeach
                            <tr @php if(($n%2)!=0){  echo 'bgcolor="#eeeeee"';} @endphp>
                                <td align="left" class="tdline" colspan="2">Total {{ $index }}</td>
                                <td width="50px"   align="right" class="tdline"   > </td>
                                <td width="50px"   align="right" class="tdline"  >{{ number_format($totalline ,3,',','.') }} </td>
                            </tr>
                            <tr >
                                <td align="left"  colspan="2"> </td>
                                <td width="50px"   align="center" > </td>
                                <td width="50px"   align="center" > </td>
                            </tr>

                    @endforeach

                    <tr bgcolor="#f5f5f5">
                        <td align="center" height="30px" class="tdline" colspan="2">  </td>
                        <td width="50px"   align="center" class="tdline"  > </td>
                        <td width="50px"   align="center" class="tdline"   >Totales        </td>
                    </tr>
                    <tr >
                        <td align="left"  colspan="2" class="tdline">Totales </td>
                        <td width="50px"   align="center" class="tdline" > </td>
                        <td width="50px"   align="center" class="tdline" > {{ number_format($ttt ,3,',','.') }}</td>
                    </tr>
                </table>
            </div>
        </form>
    </div>

@endsection
@section('scripts')
    <!-- apexcharts -->
    <script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- Vector map-->
    <script src="{{ URL::asset('build/libs/jsvectormap/jsvectormap.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/jsvectormap/world-merc.js') }}"></script>

    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>

    <!--Swiper slider js-->
    <script src="{{ URL::asset('build/libs/swiper/swiper-bundle.min.js') }}"></script>

    <!-- Dashboard init -->
    <script src="{{ URL::asset('build/js/pages/dashboard-ecommerce.init.js') }}"></script>

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

@endsection
