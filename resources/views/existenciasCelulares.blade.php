@extends('layouts.master')
@section('title')
    Existencias de productos
@endsection
@section('css')
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

        /* Estilos adicionales para mejorar la visualización */
        .table-matrix {
            font-size: 0.85rem;
        }

        .table-matrix thead th {
            vertical-align: middle;
            text-align: center;
            padding: 12px 8px;
        }

        .table-matrix tbody td {
            vertical-align: middle;
            text-align: center;
            padding: 10px 8px;
        }

        .table-matrix tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        .marca-nombre {
            font-weight: 600;
            text-align: left;
        }

        .total-row {
            font-weight: bold;
        }

        .total-sucursal {
            background-color: #d4edda;
            font-weight: bold;
        }

        .total-general {
            background-color: #28a745;
            color: white;
            font-weight: bold;
        }

        /* Botones de ordenamiento */
        .sort-btn {
            cursor: pointer;
            transition: all 0.2s;
        }

        .sort-btn:hover {
            background-color: rgba(255,255,255,0.2);
        }

        .sort-icon {
            font-size: 0.7rem;
            margin-left: 3px;
        }

        /* Badges */
        .badge-cantidad {
            font-size: 0.8rem;
            padding: 5px 10px;
        }

        /* Filtros */
        .filter-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">
                                <i class="bi bi-phone-vibrate me-2"></i>EXISTENCIAS POR MARCAS
                            </h4>
                            <p class="text-white-50 mb-0 small">Distribución de inventario por marca y sucursal</p>
                        </div>
                        <div>
                            <button onclick="exportToExcel()" class="btn btn-sm btn-light me-2">
                                <i class="bi bi-download me-1"></i> Exportar a excel
                            </button>
                            <button onclick="window.print()" class="btn btn-sm btn-light">
                                <i class="bi bi-printer me-1"></i> Imprimir
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="filter-section">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-search me-1"></i>Buscar Marca
                                </label>
                                <input type="text" class="form-control" id="buscarMarca"
                                       placeholder="Escriba el nombre de la marca...">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-sort-numeric-down me-1"></i>Ordenar por
                                </label>
                                <select class="form-select" id="ordenarPor">
                                    <option value="total_desc">Total unidades (mayor a menor)</option>
                                    <option value="total_asc">Total unidades (menor a mayor)</option>
                                    <option value="nombre_asc">Nombre (A-Z)</option>
                                    <option value="nombre_desc">Nombre (Z-A)</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-secondary w-100" onclick="limpiarFiltros()">
                                    <i class="bi bi-eraser me-1"></i>Limpiar
                                </button>
                            </div>
                        </div>
                    </div>

                    @if(isset($arraysucursal) && count($arraysucursal) > 0 && isset($arrayinstanci) && count($arrayinstanci) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm table-matrix" id="tablaMatriz">
                                <thead>

                                <th class="tdlineff sort-btn" style="min-width: 180px;" onclick="ordenarPorNombre()">
                                    <i class="bi bi-tag me-1"></i> MARCA
                                    <span class="sort-icon" id="sortNombreIcon">↓</span>
                                </th>
                                @foreach($arraysucursal as $index => $sucursal)
                                    <th class="tdlineff text-center">
                                        <i class="bi bi-building me-1"></i> {{ str_replace('SARA','',$sucursal) }}
                                    </th>
                                @endforeach
                                <th class="tdlineff text-center sort-btn" onclick="ordenarPorTotal()" style="background-color: #28a745;">
                                    <i class="bi bi-calculator me-1"></i> TOTAL
                                    <span class="sort-icon" id="sortTotalIcon">↓</span>
                                </th>

                                </thead>
                                <tbody id="tablaBody">
                                @php
                                    $tantos = 0;
                                    $totalline = 0;
                                    $totaltotal = 0;
                                    $marcasData = [];
                                    $totalessucu = [];

                                    // Recopilar datos de todas las marcas

                                    foreach($arrayinstanci as $indexinst => $instancia) {
                                        $tline = 0;
                                        $sucursalesData = [];

                                        foreach($arraysucursal as $index => $sucursal) {
                                            $cantidad = isset($arraycantidad[$indexinst][$index]) ? $arraycantidad[$indexinst][$index] : 0;
                                            $tline += $cantidad;
                                            $sucursalesData[] = $cantidad;
                                        }

                                        $marcasData[] = [
                                            'inspadre'   => $indexinst,
                                            'nombre'     => $instancia,
                                            'total'      => $tline,
                                            'sucursales' => $sucursalesData
                                        ];
                                    }

                                    // Ordenar por total descendente (mayor a menor)
                                    usort($marcasData, function($a, $b) {
                                        return $b['total'] <=> $a['total'];
                                    });


                                @endphp

                                @foreach($marcasData as $indexinst => $marca)
                                    @php
                                        $tantos++;
                                        $bgcolor = "#f2f2f2";
                                        if(($tantos%2)==0){ $bgcolor = "#ffffff"; }
                                        $tieneStock = $marca['total'] > 0;
                                    @endphp

                                    <tr class="fila-marca" data-marca="{{ strtolower($marca['nombre']) }}"
                                        data-tiene-stock="{{ $tieneStock ? 'si' : 'no' }}"
                                        data-total="{{ $marca['total'] }}">
                                        <td class="tdline text-start marca-nombre" style="background-color: {{ $bgcolor }};">

                                            <div class="d-flex justify-content-between">
                                                <div style="font-size: 11px">
                                                    <i class="bi bi-tag me-2 text-primary"></i>
                                                    <strong>{{ $marca['nombre'] }}</strong>
                                                </div>
                                                <button type="button"
                                                        class="showModalModelos btn btn-link text-primary p-0"
                                                        style="font-size: 11px; border: none; background: none; cursor: pointer;"
                                                        data-inspadre="{{ $marca['inspadre'] }}"
                                                        data-bs-target="#showModalModelos"
                                                        data-bs-toggle="modal">
                                                    Ver Modelos
                                                </button>
                                            </div>

                                        </td>

                                        @foreach($marca['sucursales'] as $index => $cantidad)
                                            @php
                                                $totaltotal += $cantidad;

                                                if(!isset($totalessucu[$index]))
                                                    $totalessucu[$index]  = 0;

                                                $totalessucu[$index] += $cantidad;
                                            @endphp
                                            <td class="tdline text-center" style="background-color: {{ $bgcolor }};">
                                                @if($cantidad > 0)
                                                    <span class="badge bg-primary badge-cantidad"
                                                          data-bs-toggle="tooltip"
                                                          title="{{ $marca['nombre'] }}    {{ number_format($cantidad, 0, ',', '.') }} unidades">
                                                            {{ number_format($cantidad, 0, ',', '.') }}
                                                        </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        @endforeach

                                        <td class="tdline text-center total-row" style="background-color: #e9ecef; font-weight: bold;">
                                                <span class="badge bg-success badge-cantidad">
                                                    {{ number_format($marca['total'], 0, ',', '.') }}
                                                </span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr style="background-color: #e9ecef; font-weight: bold;">
                                    <td class="tdline text-center fw-bold">
                                        <i class="bi bi-calculator me-1"></i> TOTAL GENERAL

                                    @foreach($marca['sucursales'] as $index => $sucursal)
                                        <td class="tdline text-center fw-bold total-sucursal">
                                                <span class="badge bg-success badge-cantidad">
                                                    {{ number_format($totalessucu[$index] ?? 0, 0, ',', '.') }}
                                                </span>

                                    @endforeach
                                    <td class="tdline text-center total-general">
                                            <span class="badge bg-light text-dark fs-6">
                                                {{ number_format($totaltotal, 0, ',', '.') }}
                                            </span>

                                </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Resumen rápido -->
                        <div class="row mt-3">
                            <div class="col-4">
                                <div class="alert alert-info text-center">
                                    <i class="bi bi-tags fs-4 d-block"></i>
                                    <strong>{{ count($marcasData) }}</strong> <br> marcas en inventario
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="alert alert-success text-center">
                                    <i class="bi bi-box-seam fs-4 d-block"></i>
                                    <strong>{{ number_format($totaltotal, 0, ',', '.') }}</strong>
                                    <br> Unidades totales
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="alert alert-warning text-center">
                                    <i class="bi bi-star fs-4 d-block"></i>
                                    <strong>{{ $marcasData[0]['nombre'] ?? 'N/A' }}</strong>
                                    <br>
                                    <small>{{ number_format($marcasData[0]['total'] ?? 0, 0, ',', '.') }} unidades</small>
                                </div>
                            </div>
                        </div>

                    @else
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle me-2"></i>
                            No hay datos de existencias para mostrar
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade flip" id="showModalModelos" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-body p-5 text-center" id="showModalModelosContent">

                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        let datosOriginales = [];

        // Guardar datos originales para ordenamiento
        @if(isset($marcasData))
            datosOriginales = @json($marcasData);
        @endif

        let ordenActual = 'total_desc';

        // Función para ordenar la tabla
        function ordenarTabla(criterio) {
            let datosOrdenados = [...datosOriginales];

            switch(criterio) {
                case 'total_desc':
                    datosOrdenados.sort((a, b) => b.total - a.total);
                    break;
                case 'total_asc':
                    datosOrdenados.sort((a, b) => a.total - b.total);
                    break;
                case 'nombre_asc':
                    datosOrdenados.sort((a, b) => a.nombre.localeCompare(b.nombre));
                    break;
                case 'nombre_desc':
                    datosOrdenados.sort((a, b) => b.nombre.localeCompare(a.nombre));
                    break;
            }

            renderizarTabla(datosOrdenados);
            actualizarIconosOrdenamiento(criterio);
        }

        // Función para renderizar la tabla
        function renderizarTabla(datos) {
            let tbody = $('#tablaBody');
            tbody.empty();

            let sucursales = @json($arraysucursal);
            let tantos = 0;

            datos.forEach((marca, idx) => {
                tantos++;
                let bgcolor = (tantos % 2 === 0) ? '#ffffff' : '#f2f2f2';
                let tieneStock = marca.total > 0;

                let row = `<tr class="fila-marca" data-marca="${marca.nombre.toLowerCase()}"
                              data-tiene-stock="${tieneStock ? 'si' : 'no'}"
                              data-total="${marca.total}">
                            <td class="tdline text-start marca-nombre" style="background-color: ${bgcolor};">
                                <i class="bi bi-tag me-2 text-primary"></i>
                                <strong>${marca.nombre}</strong>
                                <br>
                                <small class="text-muted">
                                    <i class="bi bi-box-seam me-1"></i>${marca.total.toLocaleString('es-VE')}
                                </small>
                            </td>`;

                marca.sucursales.forEach((cantidad, idx) => {
                    if (cantidad > 0) {
                        row += `<td class="tdline text-center" style="background-color: ${bgcolor};">
                                    <span class="badge bg-primary badge-cantidad"
                                          data-bs-toggle="tooltip"
                                          title="${marca.nombre} en ${sucursales[idx]}: ${cantidad.toLocaleString('es-VE')} unidades">
                                        ${cantidad.toLocaleString('es-VE')}
                                    </span>
                                </td>`;
                    } else {
                        row += `<td class="tdline text-center" style="background-color: ${bgcolor};">
                                    <span class="text-muted">-</span>
                                </td>`;
                    }
                });

                row += `<td class="tdline text-center total-row" style="background-color: #e9ecef; font-weight: bold;">
                            <span class="badge bg-success badge-cantidad">
                                ${marca.total.toLocaleString('es-VE')}
                            </span>
                        </td>
                    </tr>`;

                tbody.append(row);
            });

            // Reinicializar tooltips
            if (typeof bootstrap !== 'undefined') {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        }

        // Actualizar iconos de ordenamiento
        function actualizarIconosOrdenamiento(criterio) {
            $('#sortNombreIcon').html(criterio.includes('nombre') ? (criterio.includes('asc') ? '↑' : '↓') : ' ');
            $('#sortTotalIcon').html(criterio.includes('total') ? (criterio.includes('asc') ? '↑' : '↓') : ' ');
        }

        // Ordenar por nombre
        function ordenarPorNombre() {
            let nuevoOrden = (ordenActual === 'nombre_asc') ? 'nombre_desc' : 'nombre_asc';
            ordenActual = nuevoOrden;
            ordenarTabla(ordenActual);
            $('#ordenarPor').val(ordenActual);
        }

        // Ordenar por total
        function ordenarPorTotal() {
            let nuevoOrden = (ordenActual === 'total_desc') ? 'total_asc' : 'total_desc';
            ordenActual = nuevoOrden;
            ordenarTabla(ordenActual);
            $('#ordenarPor').val(ordenActual);
        }

        // Búsqueda de marcas
        $('#buscarMarca').on('keyup', function() {
            var searchTerm = $(this).val().toLowerCase();
            $('.fila-marca').each(function() {
                var marca = $(this).data('marca');
                if (marca.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });



        // Ordenar por select
        $('#ordenarPor').on('change', function() {
            ordenActual = $(this).val();
            ordenarTabla(ordenActual);
        });

        // Limpiar filtros
        function limpiarFiltros() {
            $('#buscarMarca').val('');
            $('#filtroStock').val('todos');
            $('#ordenarPor').val('total_desc');
            ordenActual = 'total_desc';
            ordenarTabla('total_desc');
            $('.fila-marca').show();
        }

        // Inicializar tooltips
        if (typeof bootstrap !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // Función para exportar a Excel
        function exportToExcel() {
            var table = document.getElementById('tablaMatriz');
            var html = table.outerHTML;
            var url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
            var link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', 'existencias_por_marcas.xls');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        $('.showModalModelos').unbind('click').bind('click',function (e) {
            e.preventDefault();
            e.stopPropagation();

            var modalContent = $('#showModalModelosContent');
            var inspadre     = $(this).data('inspadre');

            modalContent.html(`
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Espere por favor...</p>
                    </div>
                `);

            $.ajax({
                type: 'POST',
                url: '/existencia/celulares/modelos',
                data:{inspadre:inspadre},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    modalContent.html(data);
                },
                error: function(xhr, status, error) {
                    modalContent.html(`
                            <div class="alert alert-danger">
                                Error al cargar los modelos. Por favor intente nuevamente.
                            </div>
                        `);
                    console.error(error);
                }
            });
        });
    </script>
@endsection

