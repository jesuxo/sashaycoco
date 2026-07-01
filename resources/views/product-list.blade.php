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
        .btn-soft-info:hover small{
            color: white !important;
        }

        /* Estilos para las tarjetas de resumen */
        .stat-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .stat-icon {
            font-size: 2rem;
            opacity: 0.3;
        }
        .product-list-item {
            transition: all 0.2s ease;
        }
        .product-list-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }

        /* Estilos para importación */
        .import-card {
            border: 1px dashed #dee2e6;
            background-color: #f8f9fa;
        }
        .import-card:hover {
            border-color: #0d6efd;
            background-color: #f0f8ff;
        }
        .error-list {
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
@endsection
@section('content')

    <div class="row">
        <!-- Panel izquierdo con filtros -->
        <div class="col-xl-4 col-lg-4">

            @if(isset($codprod) and $codprod !='')
                <form method="post" name="form1" id="form1" action="/operaciones">
                    <input type="hidden" name="codprod" value="{{ ($codprod)? $codprod : '' }}"/>
                    <div class="col-md-12 order-last mb-2">
                        <div class="input-group">
                            <input type="text" class="form-control" data-provider="flatpickr"
                                   data-range-date="true" data-date-format="d/m/Y"
                                   data-deafult-date="" name="fechasreport" readonly="readonly" value="{{(isset($fechasreport)?$fechasreport : '')}}">
                            <div class="input-group-text bg-primary border-primary text-white">
                                <button type="submit" class="botoncal">Consultar</button>
                            </div>
                        </div>
                    </div>
                    @csrf
                    @method('POST')
                </form>
            @endif

            <div class="mb-2">
                <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#searchModal"
                   onclick="focusbusqueda(); $('#search-dropdown').fadeIn()" style="width: 100%"
                   class="btn btn-soft-info text-start btn-sm">
                    <i class="ri-file-list-3-line align-middle"></i> Para crear un producto
                    <br><small class="text-muted">Debe buscar el producto que necesita y sino existe va a poder crearlo </small>
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
                                        <div href="#" class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <a href="/saprod/export/{{$instancia->codalte}}">
                                                    <i class="bi bi-download" style="margin-right: 10px;"></i>
                                                </a>
                                                <span class="mb-0 listname" style="font-size: 12px; padding-left: {{($instancia->nivel-1)*14}}px">  {{$instancia->label}}</span>
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
                            <p class="text-muted text-uppercase fs-13 mb-4">Actualizar Precios</p>
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

            <!-- Tarjetas de resumen estadístico -->
        </div>
        <!-- end col -->

        @if(isset($codprod) and $codprod !='')
            <div class="col-xxl-8">
                <!-- Mostrar operaciones del producto -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">Operaciones de inventario del producto {{$codprod}}</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center flex-wrap gap-3 mb-2">
                            <ul class="nav nav-pills flex-grow-1 mb-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" href="/operaciones/{{$codprod}}" role="tab">
                                        OPERACIONES
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content">
                            <div class="tab-pane active" role="tabpanel">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive table-card mb-1">
                                            <table class="table table-borderless table-striped align-middle table-sm fs-14 mb-0">
                                                <thead class="text-muted table-light">
                                                <tr>
                                                    <th width="5%" scope="col">Operación</th>
                                                    <th width="5%" scope="col" style="text-align: center" align="center">Fecha</th>
                                                    <th width="10%" scope="col" style="text-align: center" align="center">Documento</th>
                                                    <th width="5%" scope="col" style="text-align: center" align="center">Cantidad</th>
                                                    <th width="2%" scope="col" style="text-align: center" align="center">Dep1</th>
                                                    <th width="2%" scope="col" style="text-align: center" align="center">Dep2</th>
                                                    <th width="5%" scope="col" style="text-align: center" align="center">Costo</th>
                                                    <th width="5%" scope="col" style="text-align: center" align="center">Precio</th>
                                                    <th width="20%" scope="col" style="text-align: center" align="center">Sucursal</th>
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
        @else
            <div class="col-xxl-8">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card stat-card bg-primary bg-opacity-10 border-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Total Productos</h6>
                                        <h3 class="mb-0">{{ number_format($totalProductos, 0) }}</h3>
                                        <small class="text-success">Activos</small>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nueva tarjeta de importación masiva -->
                    @if(session('comercialid') == 1)
                    <div class="col-md-6">
                        <div class="card import-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="text-muted mb-1">Importación Masiva</h6>
                                        <h5 class="mb-0">Cargar productos</h5>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="bi bi-upload"></i>
                                    </div>
                                </div>

                                <form action="{{ route('productos.importarcrear') }}" method="POST" enctype="multipart/form-data" id="formImportar">
                                    @csrf
                                    <div class="mb-3">
                                        <input type="file" name="archivo_productos" id="archivo_productos"
                                               class="form-control" accept=".xlsx,.xls,.csv" required>
                                        <small class="text-muted">
                                            Formatos: Excel (.xlsx, .xls) o CSV
                                        </small>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100" id="btnImportar">
                                        <i class="bi bi-cloud-upload me-1"></i> Importar Productos
                                    </button>
                                </form>

                                <div class="mt-3">
                                    <a href="{{ route('productos.plantilla') }}" class="text-decoration-none small" target="_blank">
                                        <i class="bi bi-download me-1"></i> Descargar plantilla con instancias actualizadas
                                    </a>
                                    <br>
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle"></i> La plantilla incluye una hoja con las instancias disponibles
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Mostrar mensajes de importación -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('errores_importacion'))
                    <div class="alert alert-warning">
                        <h6><i class="bi bi-exclamation-triangle-fill me-2"></i> Errores en la importación:</h6>
                        <div class="error-list">
                            <ul class="mb-0">
                                @foreach(session('errores_importacion') as $error)
                                    <li class="small">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Últimos productos creados -->
                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="bi bi-clock-history me-2"></i> Últimos productos creados
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($ultimosProductos as $producto)
                                <a href="{{ route('productos.edit', $producto->id) }}" class="list-group-item list-group-item-action product-list-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong class="text-primary">{{ $producto->codprod }}</strong>
                                            <small class="d-block text-muted">{{ Str::limit($producto->descrip, 40) }}</small>
                                        </div>
                                        <small class="text-muted">
                                            {{ $producto->created_at ? $producto->created_at->format('d/m/Y') : 'N/A' }}
                                        </small>
                                    </div>
                                </a>
                            @empty
                                <div class="list-group-item text-muted text-center">
                                    No hay productos creados recientemente
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="card-footer text-center bg-light">
                        <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#searchModal"
                           onclick="focusbusqueda(); $('#search-dropdown').fadeIn()">
                            <i class="bi-plus-circle me-1"></i> Crear nuevo producto individual
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal de búsqueda (si existe) -->
    @if(!isset($codprod) || $codprod == '')
        <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="searchModalLabel">
                            <i class="bi-search me-2"></i> Buscar producto
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-lg" id="busquedaProducto"
                                   placeholder="Ingrese código, nombre, marca o referencia..."
                                   autocomplete="off">
                            <small class="text-muted">Presione Enter para buscar</small>
                        </div>
                        <div id="resultadosBusqueda" class="table-responsive">
                            <!-- Aquí se cargarán los resultados de búsqueda -->
                            <div class="text-center text-muted py-4">
                                <i class="bi-search fs-1 d-block mb-2"></i>
                                <p>Ingrese términos de búsqueda para encontrar productos</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('productos.create') }}" class="btn btn-success">
                            <i class="bi-plus-circle me-1"></i> Crear nuevo producto
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection
@section('scripts')
    <script>
        // Función para buscar productos en tiempo real
        let timeoutId;

        function buscarProductos() {
            const busqueda = $('#busquedaProducto').val();

            if (busqueda.length < 2) {
                $('#resultadosBusqueda').html(`
                    <div class="text-center text-muted py-4">
                        <i class="bi-search fs-1 d-block mb-2"></i>
                        <p>Ingrese al menos 2 caracteres para buscar</p>
                    </div>
                `);
                return;
            }


        }

        $(document).ready(function() {
            // Búsqueda con debounce
            $('#busquedaProducto').on('keyup', function() {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(buscarProductos, 500);
            });

            // Buscar al presionar Enter
            $('#busquedaProducto').on('keypress', function(e) {
                if (e.which === 13) {
                    clearTimeout(timeoutId);
                    buscarProductos();
                }
            });

            // Enfocar input al abrir modal
            $('#searchModal').on('shown.bs.modal', function() {
                $('#busquedaProducto').focus();
            });

            // Validar archivo antes de subir
            $('#formImportar').on('submit', function(e) {
                var fileInput = $('#archivo_productos')[0];
                if (fileInput.files.length === 0) {
                    e.preventDefault();
                    alert('Por favor seleccione un archivo para importar');
                    return false;
                }

                var fileName = fileInput.files[0].name;
                var extension = fileName.split('.').pop().toLowerCase();
                if (!['xlsx', 'xls', 'csv'].includes(extension)) {
                    e.preventDefault();
                    alert('Formato no válido. Use archivos .xlsx, .xls o .csv');
                    return false;
                }

                $('#btnImportar').html('<i class="bi bi-hourglass-split me-1"></i> Importando...').prop('disabled', true);
            });
        });
    </script>
    <script src="{{ URL::asset('build/js/backend/product-list.init.js') }}?version={{rand(0,500)}}"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
