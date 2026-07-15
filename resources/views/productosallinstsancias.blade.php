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

    <div class=" col-lg-12 ">
        <div class=" row ">
            <div class="card card-height-100">
                <div class="card-header align-items-center text-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">  EXISTENCIAS POR DEPOSITO</h4>
                </div>
                <div class="card-body" data-simplebar style="max-height: 490px;">
                    <table width="1000px" border="0" class="table table-borderless table-centered align-middle table-nowrap mb-0 ">
                        <thead  style="position: sticky; top: 0;">
                        <tr>
                            <td width="4%" height="30" align="center" class="titulo tdlineff  "> COD</td>
                            <td width="20%" align="center" class="titulo tdlineff  "> PRODUCTO  </td>
                            <td width="9%"  align="center" class="titulo tdlineff  "> COSTO PRO</td>
                            @foreach($deposito as $indexdep => $descripdepo)
                                <td width="13%"  align="center" class="titulo tdlineff   "> {{ $descripdepo }}</td>
                            @endforeach
                            <td width="9%"  align="center" class="titulo tdlineff  "> Unds</td>
                            <td width="9%"  align="center" class="titulo tdlineff  "> COSTO PRO* Unds</td>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $tantos = 0;
                            $totalcost  = 0;
                            $existdepstt  = 0;
                            $arraycantdep = [];
                        @endphp

                        @foreach($productos as $index => $producto)
                            @php
                                $tantos++;
                                $bgcolor = "#f2f2f2";
                                if(($tantos%2)==0){ $bgcolor = "#ffffff"; }
                                $existdeps = 0;
                            @endphp

                            <tr  bgcolor="{{$bgcolor}}" style="color:#333;">
                                <td align="center" class="titulo "> {{$index}}</td>
                                <td align="left"   class="titulo "> {{$producto['descrip'] }}</td>
                                <td align="right"  class="titulo "> {{ number_format($producto['preciod'],2,',','.') }}</td>
                                @foreach($deposito as $indexdep => $descripdepo)
                                <td align="center" class="titulo ">
                                    @php

                                    if(!isset($arraycantdep[$indexdep]))
                                            $arraycantdep[$indexdep] = 0;

                                    if(isset($existencias[$index][$indexdep]) and $existencias[$index][$indexdep] != 0){
                                        $arraycantdep[$indexdep] = $arraycantdep[$indexdep] +$existencias[$index][$indexdep];
                                        $existdeps +=  $existencias[$index][$indexdep];
                                        $existdepstt +=  $existencias[$index][$indexdep];
                                        $totalcost += $existencias[$index][$indexdep]*$producto['preciod'];
                                    }


                                    @endphp
                                    {{(isset($existencias[$index][$indexdep]))? $existencias[$index][$indexdep] :'' }}</td>
                                @endforeach
                                <td align="center" class="titulo"> {{$existdeps+0 }}</td>
                                <td align="right"  class="titulo"> {{ number_format($existdeps*$producto['preciod'],2,',','.') }} </td>
                            </tr>
                        @endforeach

                        <tr   style="color:#333;">
                            <td align="center" class="titulo ">  </td>
                            <td align="left"   class="titulo  ">  </td>
                            <td align="right"   class="titulo  ">  </td>
                            @foreach($deposito as $indexdep => $descripdepo)
                                <td align="center" class="titulo tdlineff ">  {{($arraycantdep[$indexdep])? $arraycantdep[$indexdep] :'' }}</td>
                            @endforeach
                            <td align="right"   class="titulo  "> {{$existdepstt }} </td>
                            <td align="right"   class="titulo  ">   {{ number_format($totalcost,2,',','.') }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>


