@extends('layouts.master')
@section('title')
    Existencias de productos
@endsection
@section('css')
    <style>
        #clearall {
            text-decoration: none !important;
        }

        /* Estilos para mejorar la visualización */
        .instancia-item {
            transition: all 0.2s ease;
        }

        .instancia-item:hover {
            background-color: rgba(13, 110, 253, 0.05) !important;
        }

        .instancia-item.active {
            background-color: rgba(13, 110, 253, 0.1) !important;
            border-left: 3px solid #0d6efd;
        }

        .summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .summary-number {
            font-size: 24px;
            font-weight: bold;
        }

        .summary-label {
            font-size: 12px;
            opacity: 0.9;
        }

        .filter-header {
            background: linear-gradient(135deg, #2f4b9a 0%, #448bc9 100%);
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 12px 15px;
        }

        .table-inventory {
            margin-bottom: 0;
        }

        .table-inventory th {
            background-color: #f8f9fa;
            border-top: none;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-inventory td {
            vertical-align: middle;
            padding: 0.75rem;
        }

        .table-inventory tbody tr:hover {
            background-color: #f8f9fa;
        }

        .badge-custom {
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 20px;
        }

        .instancia-search {
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }

        .categoria-link {
            display: inline-block;
            width: 100%;
            cursor: pointer;
        }

        .toggle-icon {
            cursor: pointer;
            display: inline-block;
            width: 20px;
            margin-right: 5px;
            transition: transform 0.2s ease;
        }

        .toggle-icon.collapsed {
            transform: rotate(-90deg);
        }

        .categoria-nombre {
            cursor: pointer;
            font-size: 11px;
        }

        .child-row {
            background-color: #fafafa;
        }

        .child-row td:first-child {
            padding-left: 30px;
        }

        .level-2 td:first-child {
            padding-left: 50px;
        }

        .level-3 td:first-child {
            padding-left: 70px;
        }

        .level-4 td:first-child {
            padding-left: 90px;
        }
    </style>
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0"> <i class="bi bi-diagram-3 me-2"></i>Resumen de Inventario</h4>
                    <p class="text-white-50 mb-0 small">Selecciona una sucursal para filtrar el inventario </p>
                </div>
                <div class="card-body   ">

                    <form method="post" name="form1" id="form1" action="/existencias">
                        @csrf @method('POST')
                        <div class="row mb-4">
                            <div class="col-md-4  ">
                                <label class="form-label fw-bold">Seleccionar Sucursal</label>
                                <select class="form-select" data-choices onchange="$('#form1').submit()" id="idsucu" name="fksucursal">
                                    <option {{($fksucursal=='' or $fksucursal==0)?'selected':''}} value="0">
                                        🌐 Todas las Sucursales
                                    </option>
                                    @foreach($allsucursales as $sucu)
                                        <option {{($sucu->id == $fksucursal)?'selected':''}} value="{{$sucu->id}}">
                                            🏪 {{ $sucu->descrip }}
                                        </option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Panel izquierdo - Resumen -->
        <div class="col-xl-5">
            <div class="card shadow-sm">

                <div class="card-body p-0">
                    <div class="accordion accordion-flush" id="accordionInventario">
                        <!-- Sección de Sucursales -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseSucursales" aria-expanded="false">
                                    <i class="bi bi-building me-2"></i> Inventario por Sucursal
                                </button>
                            </h2>
                            <div id="collapseSucursales" class="accordion-collapse collapse  ">
                                <div class="accordion-body p-0">
                                    @php
                                        $datasucu = "";
                                        if(isset($fksucursal) && $fksucursal > 0)
                                            $datasucu = " and c.fk_sucursal = $fksucursal";

                                        $tunidades = 0;
                                        $tcosto = 0;
                                        $sucursalesConStock = 0;
                                        $sucursalesData = [];
                                    @endphp

                                    @foreach($sucursales as $sucursal)
                                        @php
                                            $sucursalid = $sucursal->id;

                                            $sqlcostoinv = "
                                                SELECT d.descrip, d.id,
                                                    SUM(c.Existen) AS existen,
                                                    SUM(a.preciodpro * c.Existen) AS preciodpro
                                                FROM saprod a
                                                INNER JOIN sainsta b ON a.CodInst = b.CodInst
                                                    AND b.tipoins = 0
                                                    AND b.comercial = $comercialid
                                                INNER JOIN saexis c ON a.codprod = c.codprod
                                                    and c.fk_sucursal = $sucursalid $datasucu
                                                INNER JOIN sasucursal d ON c.fk_sucursal = d.id
                                                    AND d.fk_comercial = $comercialid
                                                WHERE a.comercial = $comercialid
                                                GROUP BY d.descrip, d.id
                                            ";
                                            $costoinven = \Illuminate\Support\Facades\DB::select($sqlcostoinv);
                                        @endphp

                                        @if(isset($costoinven[0]) && $costoinven[0]->existen != 0)
                                            @php
                                                $sucursalesConStock++;
                                                $tunidades += $costoinven[0]->existen;
                                                $tcosto += $costoinven[0]->preciodpro;
                                                $sucursalesData[] = [
                                                    'nombre' => $sucursal->descrip,
                                                    'existen' => $costoinven[0]->existen,
                                                    'preciodpro' => $costoinven[0]->preciodpro
                                                ];
                                            @endphp
                                        @endif
                                    @endforeach

                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover table-inventory mb-0">
                                            <thead>
                                            <tr>
                                                <th><i class="bi bi-building me-1"></i> Sucursal</th>
                                                <th class="text-end"><i class="bi bi-box-seam me-1"></i> Unds</th>
                                                <th class="text-end"><i class="bi bi-calculator me-1"></i> Costo  </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($sucursalesData as $sucursal)
                                                <tr>
                                                    <td class="fw-medium">{{ $sucursal['nombre'] }}</td>
                                                    <td class="text-end">
                                                            <span class="badge bg-info">
                                                                {{ number_format($sucursal['existen'], 0, ',', '.') }}
                                                            </span>
                                                    </td>
                                                    <td class="text-end">
                                                            <span class="badge bg-success">
                                                                ${{ number_format($sucursal['preciodpro'], 2, ',', '.') }}
                                                            </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-4">
                                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                        No hay inventario en las sucursales seleccionadas
                                                    </td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                            @if($sucursalesConStock > 0 and !$fksucursal)
                                                <tfoot class="table-light fw-bold">
                                                <tr>
                                                    <td>TOTAL GENERAL</td>
                                                    <td class="text-end">
                                                            <span class="badge bg-info">
                                                                {{ number_format($tunidades, 0, ',', '.') }}
                                                            </span>
                                                    </td>
                                                    <td class="text-end">
                                                            <span class="badge bg-success">
                                                                ${{ number_format($tcosto, 2, ',', '.') }}
                                                            </span>
                                                    </td>
                                                </tr>
                                                </tfoot>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de Instancias/Categorías - Versión jerárquica -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseInstancias" aria-expanded="true">
                                    <i class="bi bi-tags me-2"></i> Inventario por Categoría
                                </button>
                            </h2>
                            <div id="collapseInstancias" class="accordion-collapse collapse  ">
                                <div class="accordion-body p-0">
                                    <div class="instancia-search">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                                            <input type="text" class="form-control" id="buscarInstancia"
                                                   placeholder="Buscar categoría...">
                                        </div>
                                    </div>

                                    @php
                                        $tunidadesInst = 0;
                                        $tcostoInst = 0;
                                        $instanciasConStock = 0;

                                        // Organizar instancias en estructura jerárquica
                                        $instanciasPorPadre = [];
                                        $instanciasInfo = [];

                                        foreach($instancias as $inst) {
                                            $codalte = $inst->codalte;
                                            $len = strlen($codalte);
                                            $sqlcostoinv = "
                                                SELECT
                                                    LEFT(b.codalte, $len) AS codigo_prefijo,
                                                    SUM(c.Existen) AS existen,
                                                    SUM(a.preciodpro * c.Existen) AS preciodpro
                                                FROM saprod a
                                                INNER JOIN sainsta b ON a.CodInst = b.CodInst
                                                    AND b.tipoins = 0
                                                    AND b.comercial = $comercialid
                                                    AND LEFT(b.codalte, $len) = '$codalte'
                                                INNER JOIN saexis c ON a.codprod = c.codprod $datasucu
                                                INNER JOIN sasucursal d ON c.fk_sucursal = d.id
                                                    AND d.fk_comercial = $comercialid
                                                WHERE a.comercial = $comercialid
                                                GROUP BY LEFT(b.codalte, $len)
                                            ";
                                            $costoinven = \Illuminate\Support\Facades\DB::select($sqlcostoinv);

                                            if(isset($costoinven[0]) && $costoinven[0]->existen != 0) {
                                                $instanciasConStock++;
                                                $instanciasInfo[$inst->codinst] = [
                                                    'codinst' => $inst->codinst,
                                                    'codalte' => $codalte,
                                                    'label' => $inst->label,
                                                    'descrip' => $inst->descrip,
                                                    'nivel' => $inst->nivel,
                                                    'insPadre' => $inst->insPadre,
                                                    'existen' => $costoinven[0]->existen,
                                                    'preciodpro' => $costoinven[0]->preciodpro
                                                ];

                                                if($inst->insPadre == 0) {
                                                    $tunidadesInst += $costoinven[0]->existen;
                                                    $tcostoInst += $costoinven[0]->preciodpro;
                                                }

                                                if(!isset($instanciasPorPadre[$inst->insPadre])) {
                                                    $instanciasPorPadre[$inst->insPadre] = [];
                                                }
                                                $instanciasPorPadre[$inst->insPadre][] = $inst->codinst;
                                            }
                                        }
                                    @endphp

                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover table-inventory mb-0" id="tablaInstancias">
                                            <thead>
                                            <tr>
                                                <th><i class="bi bi-tag me-1"></i> Categoría</th>
                                                <th class="text-end"><i class="bi bi-box-seam me-1"></i> Unds</th>
                                                <th class="text-end"><i class="bi bi-calculator me-1"></i> Costo  </th>
                                                <th class="text-center" width="50"> </th>
                                            </tr>
                                            </thead>
                                            <tbody id="tablaInstanciasBody">
                                            @if($instanciasConStock > 0)
                                                @php
                                                    // Función recursiva para renderizar filas
                                                    function renderizarCategoria($codinst, $instanciasInfo, $instanciasPorPadre, $nivel = 0) {
                                                        if(!isset($instanciasInfo[$codinst])) return '';

                                                        $inst = $instanciasInfo[$codinst];
                                                        $tieneHijos = isset($instanciasPorPadre[$codinst]) && count($instanciasPorPadre[$codinst]) > 0;
                                                        $rowId = 'cat_' . $codinst;
                                                        $childContainerId = 'children_' . $codinst;
                                                        $hasStock = $inst['existen'] > 0;

                                                        $html = '<tr id="' . $rowId . '" class="instancia-item categoria-row" data-codinst="' . $inst['codinst'] . '" data-instancia-nombre="' . strtolower($inst['label']) . '" data-nivel="' . $nivel . '">';
                                                        $html .= '<td style="padding-left: ' . ($nivel * 20) . 'px">';

                                                        if($tieneHijos) {
                                                            $html .= '<span class="toggle-icon collapsed" onclick="toggleCategoria(' . $codinst . ')">';
                                                            $html .= '<i class="bi bi-caret-right-fill"></i>';
                                                            $html .= '</span>';
                                                        } else {
                                                            $html .= '<span class="toggle-icon" style="visibility: hidden;">';
                                                            $html .= '<i class="bi bi-caret-right-fill"></i>';
                                                            $html .= '</span>';
                                                        }

                                                        $html .= '<a href="javascript:;" data-codinst="' . $inst['codinst'] . '" class="text-decoration-none text-dark getexistencias categoria-nombre">';
                                                        $html .= '<i class="bi bi-folder' . ($nivel == 0 ? '-fill' : '') . ' text-warning me-2"></i>';
                                                        $html .= $inst['label'];
                                                        $html .= '</a>';
                                                        $html .= '</td>';
                                                        $html .= '<td class="text-end">';
                                                        if($hasStock) {
                                                            $html .= '<span class="badge bg-info">' . number_format($inst['existen'], 0, ',', '.') . '</span>';
                                                        } else {
                                                            $html .= '<span class="badge bg-secondary">0</span>';
                                                        }
                                                        $html .= '</td>';
                                                        $html .= '<td class="text-end">';
                                                        if($inst['preciodpro'] > 0) {
                                                            $html .= '<span class="badge bg-success">$' . number_format($inst['preciodpro'], 2, ',', '.') . '</span>';
                                                        } else {
                                                            $html .= '<span class="badge bg-secondary">$0,00</span>';
                                                        }
                                                        $html .= '</td>';
                                                        $html .= '<td class="text-center">';
                                                        $html .= '<a href="javascript:;" data-codalte="' . $inst['codalte'] . '" data-bs-toggle="modal" data-bs-target="#viewprodcodaltemodal" class="viewprodcodalte text-primary" title="Ver detalle completo">';
                                                        $html .= '<i class="bi bi-search fs-5"></i>';
                                                        $html .= '</a>';
                                                        $html .= '</td>';
                                                        $html .= '</tr>';

                                                        if($tieneHijos) {
                                                            $html .= '<tbody id="' . $childContainerId . '" style="display: none;">';
                                                            foreach($instanciasPorPadre[$codinst] as $hijoCodinst) {
                                                                $html .= renderizarCategoria($hijoCodinst, $instanciasInfo, $instanciasPorPadre, $nivel + 1);
                                                            }
                                                            $html .= '</tbody>';
                                                        }

                                                        return $html;
                                                    }
                                                @endphp

                                                @if(isset($instanciasPorPadre[0]))
                                                    @foreach($instanciasPorPadre[0] as $rootCodinst)
                                                        {!! renderizarCategoria($rootCodinst, $instanciasInfo, $instanciasPorPadre, 0) !!}
                                                    @endforeach
                                                @endif
                                            @endif
                                            </tbody>
                                            @if($instanciasConStock > 0)
                                                <tfoot class="table-light fw-bold">
                                                <tr>
                                                    <td>TOTAL CATEGORÍAS PRINCIPALES</td>
                                                    <td class="text-end">
                                                            <span class="badge bg-info">
                                                                {{ number_format($tunidadesInst, 0, ',', '.') }}
                                                            </span>
                                                    </td>
                                                    <td class="text-end">
                                                            <span class="badge bg-success">
                                                                ${{ number_format($tcostoInst, 2, ',', '.') }}
                                                            </span>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                </tfoot>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel derecho - Detalle dinámico -->
        <div class="col-xl-7">
            <div id="existencontent" class="min-vh-50">
                <div class="card shadow-sm">
                    <div class="card-body text-center text-muted py-5">
                        <i class="bi bi-folder2-open fs-1 d-block mb-3"></i>
                        <h5>Selecciona una categoría</h5>
                        <p class="mb-0">Haz clic en cualquier categoría del panel izquierdo para ver el detalle de productos</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver existencias por depósito -->
    <div class="modal fade" id="viewprodcodaltemodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
        <div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <div style="width: 80%" class="d-flex gap-3 align-items-center">

                        <div class="position-relative flex-grow-1">
                            <input type="text" style="padding-left: 60px;"
                                   class="form-control form-control-lg border-2 busquedaentredepositos"
                                   placeholder="Buscar producto por código, nombre, marca..."
                                   autocomplete="off" id="busquedaentredepositos" value="">
                            <span class="bi bi-search fs-5" style="position: absolute; left: 18px; top: 12px;"></span>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light" id="contentviewprodcodalte">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-3 text-muted">Cargando información...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>CERRAR
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        var codalte = '';
        var busqued = '';

        // Función para expandir/colapsar categorías
        window.toggleCategoria = function(codinst) {
            var childContainer = $('#children_' + codinst);
            var toggleIcon = $('#cat_' + codinst + ' .toggle-icon i');

            if (childContainer.is(':visible')) {
                childContainer.slideUp(200);
                toggleIcon.removeClass('bi-caret-down-fill').addClass('bi-caret-right-fill');
                $('#cat_' + codinst + ' .toggle-icon').removeClass('expanded').addClass('collapsed');
            } else {
                childContainer.slideDown(200);
                toggleIcon.removeClass('bi-caret-right-fill').addClass('bi-caret-down-fill');
                $('#cat_' + codinst + ' .toggle-icon').removeClass('collapsed').addClass('expanded');
            }
        };

        // Función para cargar existencias de una instancia
        $(document).on('click', '.getexistencias', function(e) {
            e.preventDefault();
            var codinst = $(this).data('codinst');
            var $row = $(this).closest('tr');

            // Remover clase active de todas las filas de instancias
            $('.instancia-item').removeClass('active');
            // Agregar clase active al elemento padre
            $row.addClass('active');

            // Mostrar loading en el panel derecho
            $('#existencontent').html(`
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-3 text-muted">Cargando productos de la categoría...</p>
                    </div>
                </div>
            `);

            $.ajax({
                type: 'post',
                data: {
                    codinst: codinst || '',
                    fksucursal: @php echo (isset($fksucursal) && $fksucursal > 0) ? $fksucursal : 0; @endphp
                },
                url: '/reporte/existen/php',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#existencontent').html(response);
                    // Inicializar tooltips después de cargar contenido
                    if (typeof bootstrap !== 'undefined') {
                        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        tooltipTriggerList.map(function(tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl);
                        });
                    }
                },
                error: function() {
                    $('#existencontent').html(`
                        <div class="card shadow-sm">
                            <div class="card-body text-center py-5">
                                <i class="bi bi-exclamation-triangle fs-1 text-danger"></i>
                                <p class="mt-3 text-danger">Error al cargar los datos. Por favor, intente nuevamente.</p>
                            </div>
                        </div>
                    `);
                }
            });
        });

        // Función para abrir modal de existencias por código alte
        $(document).on('click', '.viewprodcodalte', function() {
            codalte = $(this).data('codalte');
            busqued = $('#busquedaentredepositos').val();

            $('#contentviewprodcodalte').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Cargando productos...</p>
                </div>
            `);

            abrirmodalexistencias(codalte, busqued);
        });

        // Búsqueda en tiempo real dentro del modal
        $('#busquedaentredepositos').off('keyup').on('keyup', function() {
            busqued = $(this).val();
            $('#contentviewprodcodalte').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Buscando: ${busqued}</p>
                </div>
            `);
            abrirmodalexistencias(codalte, busqued);
        });

        function abrirmodalexistencias(codalte, busqueda) {
            $.ajax({
                type: 'post',
                data: {
                    codalte: codalte || '',
                    busqueda: busqueda || ''
                },
                url: '/saprod/viewprodinstsanciascodalte',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#contentviewprodcodalte').html(response);
                },
                error: function() {
                    $('#contentviewprodcodalte').html(`
                        <div class="alert alert-danger text-center">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Error al cargar los datos. Por favor, intente nuevamente.
                        </div>
                    `);
                }
            });
        }

        // Búsqueda de instancias en tiempo real (oculta las que no coinciden)
        $('#buscarInstancia').on('keyup', function() {
            var searchTerm = $(this).val().toLowerCase();

            if (searchTerm === '') {
                // Mostrar todas las filas
                $('#tablaInstancias tbody tr').show();
                // Expandir todas las categorías que tengan hijos para mostrar el contenido
                $('.categoria-row').each(function() {
                    var codinst = $(this).data('codinst');
                    var childContainer = $('#children_' + codinst);
                    if (childContainer.length && childContainer.is(':hidden')) {
                        window.toggleCategoria(codinst);
                    }
                });
            } else {
                // Ocultar todas las filas primero
                $('#tablaInstancias tbody tr').hide();

                // Mostrar solo las que coinciden con la búsqueda
                $('#tablaInstancias tbody tr').each(function() {
                    var instanciaNombre = $(this).data('instancia-nombre') || '';
                    if (instanciaNombre.includes(searchTerm)) {
                        $(this).show();
                        // Expandir padres para mostrar la categoría encontrada
                        var currentRow = $(this);
                        while (currentRow.length) {
                            var parentContainer = currentRow.closest('tbody[id^="children_"]');
                            if (parentContainer.length) {
                                var parentId = parentContainer.attr('id').replace('children_', '');
                                var parentRow = $('#cat_' + parentId);
                                if (parentRow.length && parentRow.is(':hidden')) {
                                    parentRow.show();
                                    var parentChildContainer = $('#children_' + parentId);
                                    if (parentChildContainer.is(':hidden')) {
                                        window.toggleCategoria(parentId);
                                    }
                                }
                                currentRow = parentRow;
                            } else {
                                break;
                            }
                        }
                    }
                });
            }
        });

        // Actualizar badges de totales
        $(document).ready(function() {
            var totalSucursales = $('.table-inventory tbody tr:visible').length;
            var totalInstancias = $('#tablaInstancias tbody tr.categoria-row:visible').length;
            $('#totalSucursalesBadge').text(totalSucursales);
            $('#totalInstanciasBadge').text(totalInstancias);

            // Expandir automáticamente la primera categoría principal si existe
            var firstRoot = $('.categoria-row[data-nivel="0"]').first();
            if (firstRoot.length && firstRoot.data('codinst')) {
                var childContainer = $('#children_' + firstRoot.data('codinst'));
                if (childContainer.length && childContainer.is(':hidden')) {
                    // Opcional: expandir automáticamente la primera categoría
                    // window.toggleCategoria(firstRoot.data('codinst'));
                }
            }
        });
    </script>
@endsection
