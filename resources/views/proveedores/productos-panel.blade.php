{{-- resources/views/proveedores/productos-panel.blade.php --}}
@extends('layouts.master')
@section('title')
    Análisis de Compras - {{ $proveedor->descrip }}
@endsection
@section('css')
    {{-- Cargar Chart.js CSS desde CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.css">
    <style>
        .panel-header {
            background: #0072c5;
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .kpi-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.3s;
            border-left: 4px solid #667eea;
        }
        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .kpi-card.primary { border-left-color: #667eea; }
        .kpi-card.success { border-left-color: #28a745; }
        .kpi-card.warning { border-left-color: #ffc107; }
        .kpi-card.info { border-left-color: #17a2b8; }
        .kpi-valor {
            font-size: 28px;
            font-weight: bold;
            margin: 10px 0;
        }
        .table-analisis {
            font-size: 13px;
        }
        .table-analisis th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .badge-rotacion-alta { background: #28a745; color: white; padding: 3px 8px; border-radius: 12px; font-size: 11px; }
        .badge-rotacion-media { background: #ffc107; color: black; padding: 3px 8px; border-radius: 12px; font-size: 11px; }
        .badge-rotacion-baja { background: #dc3545; color: white; padding: 3px 8px; border-radius: 12px; font-size: 11px; }
        .filtros-panel {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        .progress {
            height: 8px;
            margin-top: 5px;
        }
        .stock-bajo { color: #dc3545; font-weight: bold; }
        .stock-normal { color: #28a745; }
        .text-primary-custom { color: #667eea; }
        .text-success-custom { color: #28a745; }
        .text-warning-custom { color: #ffc107; }
        .filtro-rapido {
            display: inline-block;
            margin-left: 15px;
        }
        .filtro-rapido .btn {
            padding: 3px 10px;
            font-size: 12px;
        }
        .badge-filtro {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            margin-left: 10px;
        }
        .enlace-stock {
            text-decoration: none;
            color: inherit;
        }
        .enlace-stock:hover {
            text-decoration: none;
        }

        .btn-group .btn {
            padding: 2px 6px;
            font-size: 12px;
        }

        .btn-group .btn:first-child {
            border-radius: 4px 0 0 4px;
        }

        .btn-group .btn:last-child {
            border-radius: 0 4px 4px 0;
        }

        .ver-compra .ri-shopping-cart-line,
        .ver-existencias .ri-stack-line {
            font-size: 14px;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Header del Panel -->
            <div class="panel-header">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="/proveedores/{{$proveedor->codprov}}/tab4">
                        <h4 class="text-white mb-1">{{ $proveedor->descrip }}</h4>
                        <p class="text-white-50 mb-0">
                            RIF: {{ $proveedor->id3 ?? 'N/A' }} |
                            Tel: {{ $proveedor->telef ?? 'N/A' }}
                        </p>
                    </a>
                    <div class="d-flex align-items-center">

                        <a href="/proveedores/{{$proveedor->codprov}}/tab4" class="badge bg-light text-dark p-2 ms-2">
                            Código: {{ $proveedor->codprov }}
                        </a>
                    </div>
                </div>

                <!-- Filtros rápidos -->
                <div class="mt-3">
                    <span class="text-white-50 me-2">Filtros rápidos:</span>
                    <a href="{{ route('proveedores.productos-panel', ['codprov' => $proveedor->codprov, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'orden' => $orden, 'filtro' => 'todos']) }}"
                       class="btn btn-sm {{ (!isset($filtro) || $filtro == 'todos') ? 'btn-light' : 'btn-outline-light' }} me-1">
                        Todos
                    </a>
                    <a href="{{ route('proveedores.productos-panel', ['codprov' => $proveedor->codprov, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'orden' => $orden, 'filtro' => 'con_stock']) }}"
                       class="btn btn-sm {{ isset($filtro) && $filtro == 'con_stock' ? 'btn-success' : 'btn-outline-light' }} me-1">
                        <i class="ri-stack-line"></i> Con Stock
                    </a>
                    <a href="{{ route('proveedores.productos-panel', ['codprov' => $proveedor->codprov, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'orden' => $orden, 'filtro' => 'sin_stock']) }}"
                       class="btn btn-sm {{ isset($filtro) && $filtro == 'sin_stock' ? 'btn-danger' : 'btn-outline-light' }}">
                        <i class="ri-close-line"></i> Sin Stock
                    </a>
                </div>
            </div>

            <!-- Alerta de filtro activo -->
            @if(isset($filtro) && $filtro == 'con_stock')
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ri-stack-line"></i>
                    <strong>Filtro activo:</strong> Mostrando solo productos con existencia en stock.
                    <a href="{{ route('proveedores.productos-panel', ['codprov' => $proveedor->codprov, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'orden' => $orden]) }}"
                       class="alert-link">Quitar filtro</a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @elseif(isset($filtro) && $filtro == 'sin_stock')
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="ri-close-line"></i>
                    <strong>Filtro activo:</strong> Mostrando solo productos sin stock.
                    <a href="{{ route('proveedores.productos-panel', ['codprov' => $proveedor->codprov, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'orden' => $orden]) }}"
                       class="alert-link">Quitar filtro</a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Filtros -->
            <div class="filtros-panel">
                <form method="GET" action="{{ route('proveedores.productos-panel', $proveedor->codprov) }}" class="row g-3">
                    <input type="hidden" name="filtro" value="{{ $filtro ?? 'todos' }}">
                    <div class="col-md-3">
                        <label class="form-label">Fecha Desde</label>
                        <input type="date" name="fecha_desde" class="form-control" value="{{ $fecha_desde }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fecha Hasta</label>
                        <input type="date" name="fecha_hasta" class="form-control" value="{{ $fecha_hasta }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ordenar por</label>
                        <select name="orden" class="form-select">
                            <option value="vendido_desc" {{ $orden == 'vendido_desc' ? 'selected' : '' }}>Más Vendido</option>
                            <option value="vendido_asc" {{ $orden == 'vendido_asc' ? 'selected' : '' }}>Menos Vendido</option>
                            <option value="comprado_desc" {{ $orden == 'comprado_desc' ? 'selected' : '' }}>Más Comprado</option>
                            <option value="comprado_asc" {{ $orden == 'comprado_asc' ? 'selected' : '' }}>Menos Comprado</option>
                            <option value="existencia_desc" {{ $orden == 'existencia_desc' ? 'selected' : '' }}>Mayor Stock</option>
                            <option value="existencia_asc" {{ $orden == 'existencia_asc' ? 'selected' : '' }}>Menor Stock</option>
                            <option value="rotacion_desc" {{ $orden == 'rotacion_desc' ? 'selected' : '' }}>Mayor Rotación</option>
                            <option value="rentabilidad_desc" {{ $orden == 'rentabilidad_desc' ? 'selected' : '' }}>Mayor Rentabilidad</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="ri-filter-3-line"></i> Aplicar
                        </button>
                        <a href="{{ route('proveedores.productos-panel', ['codprov' => $proveedor->codprov, 'filtro' => $filtro ?? 'todos']) }}" class="btn btn-secondary">
                            <i class="ri-refresh-line"></i> Limpiar
                        </a>
                    </div>
                </form>
            </div>

            <!-- KPI Cards -->
            <div class="row">
                <div class="col-md-3">
                    <div class="kpi-card primary">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted">Total Compras</h6>
                                <h3 class="kpi-valor">${{ number_format($kpi['total_compras'] ?? 0, 2,',','.') }}</h3>
                                <small class="text-muted">Período seleccionado</small>
                            </div>
                            <div class="fs-1 text-primary-custom">
                                <i class="ri-shopping-cart-line"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="text-success">
                                <i class="ri-arrow-up-line"></i> Últimos 30 días: ${{ number_format($kpi['compras_30dias'] ?? 0, 2,',','.') }}
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card success">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted">Total Ventas</h6>
                                <h3 class="kpi-valor">${{ number_format($kpi['total_ventas'] ?? 0, 2,',','.') }}</h3>
                                <small class="text-muted">Período seleccionado</small>
                            </div>
                            <div class="fs-1 text-success-custom">
                                <i class="ri-bar-chart-line"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="text-success">
                                <i class="ri-arrow-up-line"></i> Últimos 30 días: ${{ number_format($kpi['ventas_30dias'] ?? 0, 2,',','.') }}
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('proveedores.productos-panel', ['codprov' => $proveedor->codprov, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'orden' => $orden, 'filtro' => 'con_stock']) }}" class="enlace-stock">
                        <div class="kpi-card warning">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted">Valor Inventario</h6>
                                    <h3 class="kpi-valor">${{ number_format($kpi['valor_inventario'] ?? 0, 2,',','.') }}</h3>
                                    <small class="text-muted">{{ $kpi['existencia_total'] ?? 0 }} unidades</small>
                                </div>
                                <div class="fs-1 text-warning-custom">
                                    <i class="ri-stack-line"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <small>Total productos: {{ $kpi['total_productos'] ?? 0 }}</small>
                                @if(isset($filtro) && $filtro == 'con_stock')
                                    <span class="badge bg-success float-end">Filtro activo</span>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card info">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted">Rotación</h6>
                                <h3 class="kpi-valor">{{ number_format($kpi['rotacion'] ?? 0, 1) }}x</h3>
                                <small class="text-muted">Días de cobertura</small>
                            </div>
                            <div class="fs-1 text-info">
                                <i class="ri-repeat-line"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <div class="progress">
                                @php
                                    $rotacion = $kpi['rotacion'] ?? 0;
                                    $porcentaje = min(100, ($rotacion / 30) * 100);
                                @endphp
                                <div class="progress-bar bg-info" style="width: {{ $porcentaje }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="ri-bar-chart-2-line"></i>
                                Top 10 Productos Más Vendidos (Unidades)
                                <small class="text-muted ms-2">{{ \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_hasta)->format('d/m/Y') }}</small>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="topProductosChart"></canvas>
                            </div>
                            @if(isset($top_productos) && count($top_productos) > 0)
                                <div class="mt-3">
                                    <table class="table table-sm table-borderless">
                                        <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th class="text-end">Unidades Vendidas</th>
                                            <th class="text-end">% del Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $total_unidades = $top_productos->sum('unidades_vendidas');
                                        @endphp
                                        @foreach($top_productos as $item)
                                            <tr>
                                                <td style="font-size: 10px">{{ $item->descrip  }}</td>
                                                <td class="text-center">{{ number_format($item->unidades_vendidas, 0) }}</td>
                                                <td class="text-end">
                                                    @if($total_unidades > 0)
                                                        {{ number_format(($item->unidades_vendidas / $total_unidades) * 100, 1) }}%
                                                    @else
                                                        0%
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tfoot style="border-top: 1px solid #eeeeee">
                                            <tr class="fw-bold">
                                                <td>TOTAL</td>
                                                <td class="text-center">{{ number_format($total_unidades, 0) }}</td>
                                                <td class="text-end">100%</td>
                                            </tr>
                                        </tfoot>
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="ri-pie-chart-2-line"></i>
                                Distribución de Ventas ($)
                                <small class="text-muted ms-2">{{ \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_hasta)->format('d/m/Y') }}</small>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="distribucionChart"></canvas>
                            </div>
                            <br>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-bordered">
                                    <thead class="table-light">
                                    <tr>
                                        <th width="8%"class="text-center">Fecha</th>
                                        <th width="10%">Documento</th>
                                        <th width="10%"class="text-center">Unidades</th>
                                        <th width="12%"class="text-center">Monto</th>
                                        <th width="15%">Sucursal</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($compras ?? [] as $compra)
                                        @php
                                            $signo = $compra->tipocom == 'I' ? -1 : 1;
                                            $tipoTexto = $compra->tipocom == 'H' ? 'Compra' : 'Dev ';
                                            $tipoColor = $compra->tipocom == 'H' ? 'success' : 'danger';
                                        @endphp
                                        <tr>
                                            <td class="text-center" style="font-size: 11px">{{ \Carbon\Carbon::parse($compra->fechae)->format('d/m/Y') }}</td>
                                            <td style="font-size: 11px">
                                                <button type="button"
                                                        class="btn btn-sm btn-{{$tipoColor}} ver-compra"
                                                        data-id="{{ $compra->id }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#compraModal" >
                                                    Doc: {{ $compra->numerod }}
                                                </button>
                                            </td>

                                            <td style="font-size: 11px"class="text-center {{ $compra->tipocom == 'I' ? 'text-danger' : '' }}">
                                                {{ number_format($compra->total_unidades_calculado ?? $compra->totalprd, 0) }}
                                            </td>
                                            <td style="font-size: 11px"class="text-end {{ $compra->tipocom == 'I' ? 'text-danger' : '' }}">
                                                {{ number_format($compra->total_monto_calculado ?? $compra->monto, 2) }}
                                            </td>
                                            <td style="font-size: 11px">{{ (isset($compra->sucursal->descrip))? str_replace("SARA","",$compra->sucursal->descrip) : 'N/A' }}</td>


                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <i class="ri-inbox-line fs-1 text-muted"></i>
                                                <p class="text-muted mt-2">No hay compras registradas para este proveedor</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Productos -->
            <div class="card mt-3">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="ri-list-check"></i> Análisis Detallado de Productos</h6>
                    @if(isset($filtro) && $filtro == 'con_stock')
                        <span class="badge bg-success">Mostrando solo productos con stock</span>
                    @elseif(isset($filtro) && $filtro == 'sin_stock')
                        <span class="badge bg-warning">Mostrando solo productos sin stock</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-analisis" id="productosTable">
                            <thead>
                            <tr>
                                <th class="sortable" data-sort="codprod">Código <i class="ri-arrow-up-down-line"></i></th>
                                <th class="sortable" data-sort="descrip">Producto <i class="ri-arrow-up-down-line"></i></th>
                                <th class="sortable text-end" data-sort="stock">Stock Total <i class="ri-arrow-up-down-line"></i></th>
                                <th class="sortable text-end" data-sort="dias_sin_venta">Días sin Venta <i class="ri-arrow-up-down-line"></i></th>
                                <th class="sortable text-end" data-sort="dias_stock">Días Stock <i class="ri-arrow-up-down-line"></i></th>
                                <th class="sortable text-end" data-sort="ultima_compra">Última Compra <i class="ri-arrow-up-down-line"></i></th>
                                <th class="text-center">Depositos</th>
                            </tr>
                            </thead>
                            <tbody id="productosTableBody">
                            @forelse($productos ?? [] as $producto)
                                <tr>
                                    <td><small>{{ $producto->codprod }}</small></td>
                                    <td>{{ $producto->descrip }}</td>

                                    <!-- Stock Total -->
                                    <td class="text-end {{ $producto->existencia_actual < 10 ? 'stock-bajo' : 'stock-normal' }}">
                                        {{ number_format($producto->existencia_actual ?? 0, 0) }}
                                    </td>

                                    <!-- Días sin Venta -->
                                    <td class="text-end">
                                        @if($producto->dias_sin_venta === null)
                                            <span class="badge bg-secondary">Nunca</span>
                                        @elseif($producto->dias_sin_venta > 90)
                                            <span class="badge bg-danger">{{ $producto->dias_sin_venta }} días</span>
                                        @elseif($producto->dias_sin_venta > 30)
                                            <span class="badge bg-warning">{{ $producto->dias_sin_venta }} días</span>
                                        @else
                                            <span class="badge bg-success">{{ $producto->dias_sin_venta }} días</span>
                                        @endif
                                    </td>

                                    <!-- Días Stock -->
                                    <td class="text-end">
                                        @if($producto->dias_stock > 90)
                                            <span class="badge bg-danger">{{ number_format($producto->dias_stock, 0) }}</span>
                                        @elseif($producto->dias_stock > 30)
                                            <span class="badge bg-warning">{{ number_format($producto->dias_stock, 0) }}</span>
                                        @elseif($producto->dias_stock > 0)
                                            <span class="badge bg-success">{{ number_format($producto->dias_stock, 0) }}</span>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>

                                    <!-- Última Compra -->
                                    <td class="text-end">
                                        @if(isset($producto->ultima_compra_fecha) && $producto->ultima_compra_fecha)
                                            <div class="d-flex flex-column align-items-end">
                                                <small class="text-muted"> {{ \Carbon\Carbon::parse($producto->ultima_compra_fecha)->format('d/m/Y') }} Doc: {{ $producto->ultima_compra_documento ?? 'N/A' }}</small>
                                                @if($producto->ultima_compra_dias > 30)
                                                    <button type="button"
                                                            class="btn btn-sm btn-danger ver-compra"
                                                            data-id="{{ $producto->ultima_compra_id }}"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#compraModal" >
                                                        {{ $producto->ultima_compra_dias }} días
                                                    </button>

                                                @elseif($producto->ultima_compra_dias > 15)

                                                    <button type="button"
                                                            class="btn btn-sm btn-warning ver-compra"
                                                            data-id="{{ $producto->ultima_compra_id }}"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#compraModal" >
                                                        {{ $producto->ultima_compra_dias }} días
                                                    </button>

                                                @else

                                                    <button type="button"
                                                            class="btn btn-sm btn-success ver-compra"
                                                            data-id="{{ $producto->ultima_compra_id }}"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#compraModal" >
                                                        {{ $producto->ultima_compra_dias }} días
                                                    </button>
                                                @endif

                                            </div>
                                        @else
                                            <span class="badge bg-secondary">Sin compras</span>
                                        @endif
                                    </td>

                                    <!-- Botones de Acción -->
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <!-- Botón ver existencias -->
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-info ver-existencias"
                                                    data-codprod="{{ $producto->codprod }}"
                                                    data-descrip="{{ $producto->descrip }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#existenciasModal"
                                                    title="Ver existencias por depósito">
                                                <i class="ri-stack-line"></i>
                                            </button>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="ri-inbox-line fs-1 text-muted"></i>
                                        <p class="text-muted mt-2">No hay productos para mostrar</p>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <!-- Modal para mostrar detalles de la compra -->
            <div class="modal fade" id="compraModal" tabindex="-1" aria-labelledby="compraModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title text-white" id="compraModalLabel">
                                <i class="ri-file-list-line"></i> Detalle de Compra
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="compraModalBody">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mt-2">Cargando información de la compra...</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal para mostrar existencias por sucursal/depósito -->
            <div class="modal fade" id="existenciasModal" tabindex="-1" aria-labelledby="existenciasModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title text-white" id="existenciasModalLabel">
                                <i class="ri-stack-line"></i> Existencias por Depósito
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center mb-3" id="modalProductoInfo">
                                <strong>Cargando...</strong>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="existenciasTable">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Depósito</th>
                                        <th class="text-end">Existencia</th>
                                    </tr>
                                    </thead>
                                    <tbody id="existenciasTableBody">
                                    <tr>
                                        <td colspan="2" class="text-center">Cargando existencias...</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection

@section('scripts')
    {{-- Cargar Chart.js SOLO desde CDN (el local no existe) --}}
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

    <script>
        $(document).ready(function() {
            // Verificar si Chart está definido
            if (typeof Chart === 'undefined') {
                console.error('Chart.js no se cargó correctamente');
                $('#topProductosChart').parent().html('<div class="alert alert-warning">Error al cargar Chart.js. Por favor, recargue la página.</div>');
                $('#distribucionChart').parent().html('<div class="alert alert-warning">Error al cargar Chart.js. Por favor, recargue la página.</div>');
                return;
            }

            // ========== CARGA DE DETALLES DE LA COMPRA ==========
            $('.ver-compra').click(function() {
                const compraId = $(this).data('id');
                const $modalBody = $('#compraModalBody');

                $modalBody.html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando información de la compra...</p>
        </div>
    `);

                $.ajax({
                    url: '{{ route("compras.documento-ajax") }}',
                    type: 'POST',
                    data: {
                        id: compraId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            mostrarDetalleCompra(response.compra, response.items);
                        } else {
                            $modalBody.html(`
                    <div class="alert alert-danger">
                        <i class="ri-error-warning-line"></i> Error al cargar el detalle de la compra
                    </div>
                `);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        $modalBody.html(`
                <div class="alert alert-danger">
                    <i class="ri-error-warning-line"></i> Error al cargar el detalle de la compra
                </div>
            `);
                    }
                });
            });

            function mostrarDetalleCompra(compra, items) {
                const signo = compra.tipocom == 'I' ? -1 : 1;
                const tipoTexto = compra.tipocom == 'H' ? 'Compra' : 'Devolución';
                const tipoColor = compra.tipocom == 'H' ? 'success' : 'danger';

                // Calcular el total desde los items
                let totalCalculado = 0;
                let totalUnidades = 0;

                items.forEach(function(item) {
                    const itemTotal = item.cantidad * item.preciod;
                    totalCalculado += itemTotal;
                    totalUnidades += item.cantidad;
                });

                // Aplicar signo según tipo de compra
                const totalFinal = totalCalculado * signo;

                let itemsHtml = '';
                items.forEach(function(item) {
                    const itemTotal = item.cantidad * item.preciod;
                    itemsHtml += `
            <tr>
                <td><small>${item.coditem}</small></td>
                <td>${item.descrip1 || 'Sin descripción'}</td>
                <td class="text-center">${item.cantidad}</td>
                <td class="text-end">$${Number(item.preciod).toFixed(2)}</td>
                <td class="text-end">$${Number(item.costod).toFixed(2)}</td>
                <td class="text-end">$${Number(item.costod2).toFixed(2)}</td>
                <td class="text-end">$${Number(item.costod3).toFixed(2)}</td>
                <td class="text-end">$${itemTotal.toFixed(2)}</td>
            </tr>
        `;
                });

                const html = `
        <div class="mb-3 p-3 bg-light rounded">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>Documento:</strong> ${compra.numerod}</p>
                    <p><strong>Fecha:</strong> ${new Date(compra.fechae).toLocaleDateString('es-VE')}</p>
                    <p><strong>Tipo:</strong> <span class="badge bg-${tipoColor}">${tipoTexto}</span></p>
                </div>
                <div class="col-md-4">
                    <p><strong>Proveedor:</strong> ${compra.codprov}</p>
                    <p><strong>Sucursal:</strong> ${compra.sucursal ? compra.sucursal.descrip : 'N/A'}</p>
                    <p><strong>Total Unidades:</strong> <span class="fw-bold">${totalUnidades}</span></p>
                </div>
                <div class="col-md-4">
                    <p><strong>Total monto:</strong> <span class="fw-bold">$${totalCalculado.toFixed(2)}</span></p>

                </div>
            </div>
            ${compra.descrip ? `<div class="mt-2"><strong>Observaciones:</strong> ${compra.descrip}</div>` : ''}
        </div>

        <h6 class="mb-2">Productos</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-end">Precio</th>
                        <th class="text-end">Costo</th>
                        <th class="text-end">Costo2</th>
                        <th class="text-end">Costo3</th>
                        <th class="text-end">Total Item</th>
                    </tr>
                </thead>
                <tbody>
                    ${itemsHtml}
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="3" class="text-end">TOTALES:</td>
                        <td class="text-end">-</td>
                        <td class="text-end">-</td>
                        <td class="text-end">-</td>
                        <td class="text-end">-</td>
                        <td class="text-end ${signo < 0 ? 'text-danger' : 'text-success'}">
                            $${totalCalculado.toFixed(2)}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;

                $('#compraModalBody').html(html);
            }

            // ... (código de gráficos existente) ...
            @if(isset($top_productos) && count($top_productos) > 0)
                try {
                var ctx = document.getElementById('topProductosChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: [
                            @foreach($top_productos as $item)
                                '{{ Str::limit($item->descrip, 15) }}',
                            @endforeach
                        ],
                        datasets: [{
                            label: 'Unidades Vendidas',
                            data: [
                                @foreach($top_productos as $item)
                                    {{ $item->unidades_vendidas ?? 0 }},
                                @endforeach
                            ],
                            backgroundColor: '#667eea',
                            borderColor: '#5a67d8',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString() + ' und';
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        let value = context.raw || 0;
                                        let total = {{ $top_productos->sum('unidades_vendidas') }};
                                        let porcentaje = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return label + ': ' + value.toLocaleString() + ' und (' + porcentaje + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            } catch (e) {
                console.error('Error al crear gráfico de top productos:', e);
                $('#topProductosChart').parent().html('<div class="alert alert-danger">Error al generar el gráfico</div>');
            }
            @else
            $('#topProductosChart').parent().html('<div class="alert alert-info">No hay ventas en el período seleccionado</div>');
            @endif

            // Gráfico de Distribución
            @if(isset($kpi) && ($kpi['total_ventas'] > 0 || $kpi['valor_inventario'] > 0 || $kpi['total_compras'] > 0))
                try {
                var ctx2 = document.getElementById('distribucionChart').getContext('2d');
                new Chart(ctx2, {
                    type: 'doughnut',
                    data: {
                        labels: ['Ventas', 'Stock', 'Compras'],
                        datasets: [{
                            data: [
                                {{ $kpi['total_ventas'] ?? 0 }},
                                {{ $kpi['valor_inventario'] ?? 0 }},
                                {{ $kpi['total_compras'] ?? 0 }}
                            ],
                            backgroundColor: ['#28a745', '#ffc107', '#667eea'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        let value = context.raw || 0;
                                        return label + ': $' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            } catch (e) {
                console.error('Error al crear gráfico de distribución:', e);
                $('#distribucionChart').parent().html('<div class="alert alert-danger">Error al generar el gráfico</div>');
            }
            @else
            $('#distribucionChart').parent().html('<div class="alert alert-info">No hay datos suficientes para mostrar el gráfico</div>');
            @endif

            // ========== ORDENAMIENTO DE TABLA ==========
            let currentSort = {
                column: 'stock',
                direction: 'desc'
            };

            $('.sortable').click(function() {
                const column = $(this).data('sort');
                const isAsc = currentSort.column === column && currentSort.direction === 'asc';
                const direction = isAsc ? 'desc' : 'asc';

                // Actualizar iconos
                $('.sortable i').removeClass('ri-arrow-up-line ri-arrow-down-line').addClass('ri-arrow-up-down-line');
                $(this).find('i').removeClass('ri-arrow-up-down-line').addClass(direction === 'asc' ? 'ri-arrow-up-line' : 'ri-arrow-down-line');

                sortTable(column, direction);

                currentSort.column = column;
                currentSort.direction = direction;
            });

            function sortTable(column, direction) {
                const tbody = $('#productosTableBody');
                const rows = tbody.find('tr').get();

                rows.sort(function(a, b) {
                    let aVal, bVal;

                    switch(column) {
                        case 'codprod':
                            aVal = $(a).find('td:eq(0)').text().trim();
                            bVal = $(b).find('td:eq(0)').text().trim();
                            break;
                        case 'descrip':
                            aVal = $(a).find('td:eq(1)').text().trim();
                            bVal = $(b).find('td:eq(1)').text().trim();
                            break;
                        case 'stock':
                            aVal = parseFloat($(a).find('td:eq(2)').text().replace(/\./g, '').replace(',', '.')) || 0;
                            bVal = parseFloat($(b).find('td:eq(2)').text().replace(/\./g, '').replace(',', '.')) || 0;
                            break;
                        case 'dias_sin_venta':
                            let aText = $(a).find('td:eq(3)').text().trim();
                            let bText = $(b).find('td:eq(3)').text().trim();
                            aVal = aText === 'Nunca' ? 9999 : (parseInt(aText) || 0);
                            bVal = bText === 'Nunca' ? 9999 : (parseInt(bText) || 0);
                            break;
                        case 'dias_stock':
                            aVal = parseInt($(a).find('td:eq(4)').text()) || 0;
                            bVal = parseInt($(b).find('td:eq(4)').text()) || 0;
                            break;
                        case 'ultima_compra':
                            // Buscar la fecha dentro de la celda (td:eq(5))
                            // La fecha está en un <small> con clase text-muted
                            const aFechaElement = $(a).find('td:eq(5) small.text-muted').first();
                            const bFechaElement = $(b).find('td:eq(5) small.text-muted').first();

                            // Extraer solo la fecha (formato dd/mm/yyyy)
                            let aFechaStr = aFechaElement.text().trim().split(' ')[0];
                            let bFechaStr = bFechaElement.text().trim().split(' ')[0];

                            // Si hay fecha, convertirla a timestamp, si no, usar 0
                            if (aFechaStr && aFechaStr.match(/\d{2}\/\d{2}\/\d{4}/)) {
                                const [aDay, aMonth, aYear] = aFechaStr.split('/');
                                aVal = new Date(aYear, aMonth - 1, aDay).getTime();
                            } else {
                                aVal = 0;
                            }

                            if (bFechaStr && bFechaStr.match(/\d{2}\/\d{2}\/\d{4}/)) {
                                const [bDay, bMonth, bYear] = bFechaStr.split('/');
                                bVal = new Date(bYear, bMonth - 1, bDay).getTime();
                            } else {
                                bVal = 0;
                            }
                            break;
                        default:
                            return 0;
                    }

                    if (direction === 'asc') {
                        return aVal > bVal ? 1 : aVal < bVal ? -1 : 0;
                    } else {
                        return aVal < bVal ? 1 : aVal > bVal ? -1 : 0;
                    }
                });

                $.each(rows, function(index, row) {
                    tbody.append(row);
                });
            }

            // ========== CARGA DE EXISTENCIAS POR DEPÓSITO ==========
            $('.ver-existencias').click(function() {
                const codprod = $(this).data('codprod');
                const descrip = $(this).data('descrip');

                $('#modalProductoInfo').html(`<strong>Producto: ${descrip} (${codprod})</strong>`);
                $('#existenciasTableBody').html('<tr><td colspan="2" class="text-center">Cargando existencias...</td></tr>');

                $.ajax({
                    url: '{{ route("saprod.listprodubiccompany") }}',
                    type: 'POST',
                    data: {
                        codprod: codprod,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success && response.existencias.length > 0) {
                            let html = '';
                            response.existencias.forEach(function(item) {
                                html += `
                                    <tr>
                                        <td>${item.deposito.descrip}</td>
                                        <td class="text-end">${Number(item.existen).toLocaleString('es-VE', {minimumFractionDigits: 0, maximumFractionDigits: 0})}</td>
                                    </tr>
                                `;
                            });
                            $('#existenciasTableBody').html(html);
                        } else {
                            $('#existenciasTableBody').html('<tr><td colspan="2" class="text-center text-muted">No hay existencias en ningún depósito</td></tr>');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error al cargar existencias:', xhr);
                        $('#existenciasTableBody').html('<tr><td colspan="2" class="text-center text-danger">Error al cargar existencias</td></tr>');
                    }
                });
            });

            // Inicializar tooltips
            if (typeof bootstrap !== 'undefined') {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        });
    </script>
@endsection
