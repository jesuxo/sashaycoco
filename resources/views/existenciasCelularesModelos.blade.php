
    <style>
        #clearall{
            text-decoration: none !important;
        }

        .tdline{
            border:1px solid #0072c5 !important;

        }
        .tdlineff{
            border-left:1px solid #fff !important;

            color: white !important;
            background-color: #0072c5 !important;
        }
    </style>


    <div class=" col-lg-12 ">
        <div class=" row ">
            <div class="card card-height-100">
                <div class="card-header align-items-center text-center d-flex justify-content-between">
                    <h4 class="card-title text-start mb-0 flex-grow-1">  EXISTENCIAS POR CELULAR</h4>
                    @if($ajax == 0)
                        <script>window.print()</script>
                    @else
                         <a target="_blank" href="/existencia/celulares/modelos/{{$inspadre}}"><i class="bi bi-printer" style="margin-right: 5px"></i> Imprimir</a>
                    @endif
                </div>
                <div class="card-body" data-simplebar  >
                    <table @if($ajax == 0) width="1000px"@endif border="0" class="table table-borderless table-centered align-middle table-nowrap mb-0 ">
                        <thead  style="position: sticky; top: 0;">
                        <tr>
                            <td width="20%" align="center" class="titulo tdlineff  "> Celular  </td>
                            @foreach($arraysucursal as $index => $sucursal)
                                <td @if($ajax == 0) width="13%" @endif  align="center" class="titulo tdlineff   " style="@if($ajax == 0)  font-size: 11px; @endif"> {{ $sucursal }}</td>
                            @endforeach
                            <td width="13%"  align="center" class="titulo tdlineff   "> </td>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $tantos  = $totalline = 0;
                            $totalessucu = [];
                        @endphp

                        @foreach($arrayinstanci as $indexinst => $instancia)
                            @php
                                $tantos++;
                                $bgcolor = "#f2f2f2";
                                if(($tantos%2)==0){ $bgcolor = "#ffffff"; }

                                $tline = 0;
                            @endphp

                            <tr  bgcolor="{{$bgcolor}}" style="color:#333; @if($ajax == 0)font-size: 11px @endif">
                                <td align="left"   class="titulo tdline  " style="@if($ajax == 0)padding: 2px !important; @endif"> {{$instancia}}</td>
                                @foreach($arraysucursal as $index => $sucursal)
                                    <td align="center" class="titulo tdline " style="@if($ajax == 0)padding: 2px !important; @endif">
                                        @php

                                            if(!isset($totalessucu[$index]))
                                                $totalessucu[$index] = 0;

                                            $tline += (isset($arraycantidad[$indexinst][$index]))? $arraycantidad[$indexinst][$index]  : 0;
                                            $totalessucu[$index] += (isset($arraycantidad[$indexinst][$index]))? $arraycantidad[$indexinst][$index]  : 0;

                                        @endphp
                                        {{(isset($arraycantidad[$indexinst][$index]))? $arraycantidad[$indexinst][$index] +0 :'' }}
                                    </td>
                                @endforeach
                                <td align="center" class="titulo tdline text-primary " style="font-weight: bold; @if($ajax == 0)padding: 2px !important; @endif">
                                    {{ $tline  }}
                                    @php $totalline += $tline @endphp
                                </td>
                            </tr>
                        @endforeach
                        <tr    style="color:#333;">
                            <td align="left"   class="titulo tdline  ">   </td>
                            @foreach($arraysucursal as $index => $sucursal)
                                <td align="center"class="titulo tdline text-primary " style="font-weight: bold">
                                    {{(isset($totalessucu[$index]) and $totalessucu[$index] >0)? $totalessucu[$index]   :'' }}
                                </td>
                            @endforeach
                            <td align="center" class="titulo tdline text-primary " style="font-weight: bold">
                                {{ $totalline  }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

