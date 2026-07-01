

    <div class="row">

        <div class="col-md-12 ">

            @php $totales = [];@endphp
                <div class="table-responsive table-card mt-3">
                    <table width="100%" border="0"    class="table table-borderless table-centered align-middle table-nowrap mb-0 mt-3">
                        <tr bgcolor="#fff">
                            <td width="30%" height="30"align="center" class="tdline" >Cliente</td>
                            <td width="20%" height="30"align="center" class="tdline" >Documento</td>
                            @foreach($clases as $index => $data)
                                <td width="100px" align="center" class="tdlineff" > {{$index}} </td>
                            @endforeach
                        </tr>

                        @if(isset($clientes))
                            @foreach($clientes as $indexsuc => $puntos)
                                @foreach($puntos as $indexpunto => $punto)
                                    @php
                                        $n       = 0;
                                        $tmontos = 0;
                                        list($codclie, $cliente, $doc) = explode('*',$indexsuc)
                                    @endphp

                                    <tr @php if(($n%2)==0){echo 'bgcolor="#eee"'; }else{echo 'bgcolor="#fff"';} @endphp>
                                        <td  height="30"align="left" class="tdline" >
                                            <a target="_blank" href="/clientes/{{ $codclie }}">
                                                {{$cliente}}
                                            </a>
                                        </td>
                                        <td  height="30"align="left" class="tdline" >{{$doc}}</td>
                                        @foreach($clases as $index => $data)
                                            @php
                                                if(!isset($totales[$index])) $totales[$index] = 0;
                                                $totales[$index] += (isset($listado[$indexsuc][$indexpunto][$index]))? $listado[$indexsuc][$indexpunto][$index] : 0;
                                            @endphp
                                            <td width="" align="right" class=" tdline" >
                                                  {{(isset($listado[$indexsuc][$indexpunto][$index]))?number_format($listado[$indexsuc][$indexpunto][$index],2,',','.'): ''}}
                                            </td>
                                        @endforeach
                                    </tr>
                                    @php $n++; @endphp
                                @endforeach
                            @endforeach
                        @endif
                        <tr >
                            <td height="30"align="left" class=" " > </td>
                            <td height="30"align="left" class=" " > </td>
                            @foreach($clases as $index => $data)
                                <td width="" align="center" class=" " >  </td>
                            @endforeach
                        </tr>
                        <tr bgcolor="#eee">
                            <td height="30"align="left" class="tdline" >TOTALES</td>
                            <td height="30"align="left" class="tdline" > </td>
                            @foreach($clases as $index => $data)
                                <td width="" align="right" class="tdline " > {{ number_format($totales[$index],2,',','.') }} </td>
                            @endforeach
                        </tr>
                    </table>
                </div>
                <br>
                <br>
                <br>
            </div>
        </div>

