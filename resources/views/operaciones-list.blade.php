@extends('layouts.master')
@section('title')
    Productos
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

        #clearall{
            text-decoration: none !important;
        }
        .error {
            border: 2px solid red !important;
            background-color: #ffe6e6 !important;
        }

        .error:focus {
            outline: none;
            border-color: #ff0000;
            box-shadow: 0 0 5px rgba(255, 0, 0, 0.5);
        }
    </style>
@endsection
@section('content')


    <div class="row">

        <div class="col-xl-4 col-lg-4">
            @if(isset($codprod) and $codprod !='')
                <form  method="post" name="form1" id="form1" action="/operaciones">
                    <input type="hidden" name="codprod" value="{{ ($codprod)? $codprod : '' }}"/>
                <div class="col-md-12 order-last mb-2">

                    <div class="input-group">
                        <input type="text" class="form-control" data-provider="flatpickr"
                               data-range-date="true" data-date-format="d/m/Y"
                               data-deafult-date="" name="fechasreport" readonly="readonly" value="{{(isset($fechasreport)?$fechasreport : '')}}"
                        >
                        <div class="input-group-text bg-primary border-primary text-white">
                            <button type="submit" class="botoncal" >Consultar</button>
                        </div>
                    </div>

                </div>
                @csrf
                @method('POST')
            </form>
            @endif
            <div class="mb-2">
                <a href="/productos/create" class="btn btn-success" id="addproduct-btn" style="width: 100%">
                    <i class="ri-add-line align-bottom me-1"></i> Agregar producto individualmente
                </a>
            </div>
            <div class="card overflow-hidden" id="saprodpreview">
                <div class="accordion accordion-flush filter-accordion">
                    <div class="card-body border-bottom">
                        <div>
                            <p class="text-muted text-uppercase fs-13 mb-3">Filtrar por instancia</p>
                            <ul class="list-unstyled mb-0 filter-list">
                                @foreach($instancias as $instancia)
                                    <li>
                                        <div href="#" class="d-flex  align-items-center">

                                            <div class="flex-grow-1" >
                                                <a href="/saprod/export/{{$instancia->codalte}}">
                                                    <i class="bi bi-download" style="margin-right: 10px;"></i>
                                                </a>
                                                <span class="mb-0 listname" style=" font-size: 12px; padding-left: {{($instancia->nivel-1)*14}}px">  {{$instancia->label}}</span>
                                            </div>
                                            <div class="flex-shrink-0 ms-2">
                                                @php
                                                    $prods = count($instancia->productos);
                                                @endphp
                                                @if($prods)
                                                    <span class="badge bg-light text-muted">{{$prods}}</span>
                                                @endif
                                            </div>

                                        </div>
                                    </li>
                                @endforeach

                            </ul>
                        </div>
                    </div>

                    @if(Auth::user() and auth()->user()->type == 'admin')
                        <div class="card-body border-bottom">
                            <p class="text-muted text-uppercase fs-13 m b-4">Actualizar Precios</p>

                            <div id="product-price-range" data-slider-color="info"></div>
                            <div class="formCost d-flex gap-2 align-items-center mt-3">

                                <form action="{{ url('saprod/update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="input-group">
                                        <input type="file" name="import_file" class="form-control" />
                                        <button type="submit" class="btn btn-primary">Actualizar</button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    @endif


                </div>
            </div>
        </div>
        <!-- end col -->
        @if(isset($codprod) and $codprod !='')
            <div class="col-xxl-8">

                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">Opraciones de inventario del producto {{$codprod}}</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center flex-wrap gap-3 mb-2">
                            <ul class="nav nav-pills flex-grow-1 mb-0" role="tablist">

                                <li class="nav-item ">
                                    <a class="nav-link active"   href="/operaciones/{{$codprod}}" role="tab">
                                        OPRERACIONES
                                    </a>
                                </li>

                            </ul>


                        </div>


                        <div class="tab-content">
                            <div class="tab-pane active"   role="tabpanel">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive table-card mb-1">
                                            <table class="table table-borderless table-striped align-middle table-sm fs-14 mb-0">
                                                <thead class="text-muted table-light">
                                                <tr>
                                                    <th width="5%" scope="col">  Operaci&oacute;n  </th>
                                                    <th  width="5%"scope="col" style="text-align: center !important" align="center">Fecha</th>
                                                    <th  width="10%"scope="col" style="text-align: center !important" align="center">Documento</th>
                                                    <th  width="5%"scope="col" style="text-align: center !important" align="center">Cantidad</th>
                                                    <th  width="2%"scope="col" style="text-align: center !important" align="center">Dep1</th>
                                                    <th  width="2%"scope="col" style="text-align: center !important" align="center">Dep2</th>
                                                    <th  width="5%"scope="col" style="text-align: center !important" align="center">Costo</th>
                                                    <th  width="5%"scope="col" style="text-align: center !important" align="center">Precio</th>
                                                    <th  width="20%"scope="col" style="text-align: center !important" align="center">Sucursal</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @php
                                                    $arraysucu = [];
                                                    foreach ($sucursales as $suc){
                                                        $aux = str_replace('SARA','',$suc->descrip);
                                                        $arraysucu[$suc->id] = $aux;
                                                    }
                                                @endphp
                                                   @foreach($operacionesrep as $index => $row)
                                                       <tr>
                                                           <td align="left">
                                                               @php
                                                                   if($row->tipo == 'A') echo "Factura";
                                                                   if($row->tipo == 'B') echo "DevFac";
                                                                   if($row->tipo == 'H') echo "Compra";
                                                                   if($row->tipo == 'I') echo "DevComp";
                                                                   if($row->tipo == 'P') echo "Descargo";
                                                                   if($row->tipo == 'O') echo "Cargo";
                                                                   if($row->tipo == 'N') echo "Traslado";
                                                               @endphp
                                                           </td>
                                                           <td align="center">{{(isset($row->fecha)? $row->fecha: '')}}</td>
                                                           <td align="center">
                                                               @if($row->tipo == 'A' or $row->tipo == 'B')
                                                                  <a href="/doc/{{$row->tipo}}/{{$row->numerod}}/{{$row->fk_sucursal}}" target="_blank"> {{$row->numerod}} </a>
                                                               @else
                                                                   {{$row->numerod}}
                                                               @endif
                                                           </td>
                                                           <td align="center">{{$row->cantidad}}</td>
                                                           <td align="center">{{$row->dep1}}</td>
                                                           <td align="center">{{($row->tipo =='P' or $row->tipo == 'O'  or $row->tipo == 'N')?$row->dep2: ''}}</td>
                                                           <td align="right">{{number_format($row->costo,2,',','.')}}</td>
                                                           <td align="right">{{($row->tipo =='A' or $row->tipo == 'B'  )?number_format($row->precio,2,',','.') :''}}</td>
                                                           <td align="right">{{$arraysucu[$row->fk_sucursal]}}</td>
                                                       </tr>
                                                   @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @endif
    </div>


@endsection
@section('scripts')

    <script src="{{ URL::asset('build/js/backend/product-list.init.js') }}?version={{rand(0,500)}}"></script>
    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
