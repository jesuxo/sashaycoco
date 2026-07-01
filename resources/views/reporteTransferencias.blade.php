@extends('layouts.master')
@section('title')
    REPORTE DE TRANSFERENCIAS
@endsection
@section('css')
    <style>
        /* Botones de navegación */
        .scroll-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 5px;
            margin-bottom: 10px;
        }

        .scroll-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: 1px solid #dee2e6;
            color: #0072c5;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .scroll-btn:hover {
            background: #0072c5;
            color: white;
            border-color: #0072c5;
            transform: scale(1.1);
        }

        .scroll-btn:active {
            transform: scale(0.95);
        }

        /* Hint para móviles */
        .scroll-hint {
            display: none;
            text-align: center;
            padding: 5px;
            background: #f8f9fa;
            border-radius: 4px;
            margin-top: 10px;
            color: #6c757d;
            font-size: 12px;
        }

        @media (max-width: 768px) {
            .scroll-hint {
                display: block;
            }
            .scroll-buttons {
                display: none;
            }
        }

        /* Barra de scroll personalizada */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #0072c5;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #0056b3;
        }

        /* Gradientes para scroll */
        .table-responsive.tabla-container {
            position: relative;
            overflow-x: auto !important;
        }

        .table-responsive.tabla-container::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            height: 100%;
            width: 50px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.9));
            pointer-events: none;
            transition: opacity 0.3s ease;
            opacity: 1;
            z-index: 5;
        }

        .table-responsive.tabla-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 50px;
            background: linear-gradient(270deg, transparent, rgba(255,255,255,0.9));
            pointer-events: none;
            transition: opacity 0.3s ease;
            opacity: 0;
            z-index: 5;
        }

        .table-responsive.tabla-container.scrolled:not(.at-start)::after {
            opacity: 0;
        }

        .table-responsive.tabla-container.at-start::after {
            opacity: 1;
        }

        .table-responsive.tabla-container:not(.at-start)::before {
            opacity: 1;
        }

        @media (max-width: 768px) {
            .table-responsive.tabla-container::before,
            .table-responsive.tabla-container::after {
                display: none;
            }
        }

        /* Estilos para autocomplete en filtros */
        #categoriaFiltroSuggestions {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: white;
            z-index: 1050;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        #categoriaFiltroSuggestions .list-group-item {
            padding: 8px 12px;
            cursor: pointer;
            border-left: none;
            border-right: none;
            transition: all 0.2s;
        }

        #categoriaFiltroSuggestions .list-group-item:hover {
            background-color: #e7f5ff;
            color: #0072c5;
        }

        #clearCategoriaFiltro {
            color: #6c757d;
            padding: 0 5px;
        }

        #clearCategoriaFiltro:hover {
            color: #dc3545;
        }

        /* Estilos generales */
        .table-nowrap th, .table-nowrap td {
            white-space: unset !important;
        }

        .botoncal {
            background: transparent;
            border: none;
            color: white;
        }

        .nav-pills .nav-link {
            background: #eee !important;
            border-radius: 0 !important;
        }

        .nav-pills .nav-link.active {
            background: #0072c5 !important;
            color: white !important;
        }

        .nav-pills {
            border-bottom: 1px solid #0072c5;
        }

        .tdline {
            border: 1px solid #0072c5 !important;
        }

        .tdlineff {
            border-left: 1px solid #fff !important;
            color: white !important;
            background-color: #0072c5 !important;
        }

        .filter-badge {
            cursor: pointer;
            transition: all 0.2s;
            padding: 5px 10px;
            margin: 2px;
        }

        .filter-badge:hover {
            opacity: 0.8;
            transform: scale(1.02);
        }

        /* Estilos para miniaturas de imagen */
        .thumbnail-container {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 4px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s;
        }

        .thumbnail-container:hover {
            border-color: #0072c5;
            transform: scale(1.1);
        }

        .thumbnail-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .thumbnail-container .no-image {
            width: 100%;
            height: 100%;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
            font-size: 20px;
        }

        /* Badges de moneda */
        .currency-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }

        .currency-bs { background: #e3f2fd; color: #0d6efd; }
        .currency-usd { background: #d1e7dd; color: #198754; }
        .currency-cop { background: #fff3cd; color: #ffc107; }

        .limit-selector {
            min-width: 120px;
        }
    </style>
@endsection

@section('content')
    @if( isset($estadisticas['generales']))
        <div class="row mb-3">
            <div class="col-12">
                <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#panelEstadisticas">
                    <i class="ri-bar-chart-2-line me-1"></i> Ver Estadísticas Gerenciales
                </button>
                <button class="btn btn-success" type="button" onclick="exportarExcel()">
                    <i class="ri-file-excel-line me-1"></i> Exportar a Excel
                </button>
            </div>
        </div>

        <div class="collapse mb-4" id="panelEstadisticas">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0 text-white">
                        <i class="ri-pie-chart-2-line me-2"></i>ESTADÍSTICAS GERENCIALES
                    </h5>
                </div>
                <div class="card-body">
                    <!-- KPIs Principales -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="text-white-50">Tasa de Aprobación</h6>
                                    <h3 class="text-white mb-0">{{ number_format($estadisticas['generales']['tasa_aprobacion'], 1) }}%</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="text-white-50">Tiempo Promedio</h6>
                                    <h3 class="text-white mb-0">{{ $estadisticas['generales']['tiempo_promedio_aprobacion'] }} horas</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="text-white-50">Proyección Mensual</h6>
                                    <h3 class="text-white mb-0">{{ $estadisticas['proyecciones']['proyeccion_mensual'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h6 class="text-white-50">Días para Meta</h6>
                                    <h3 class="text-white mb-0">{{ $estadisticas['proyecciones']['dias_para_meta'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráficos -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Tendencia Diaria</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="graficoTendenciaDiaria" style="height: 250px;"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Distribución por Moneda</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="graficoDistribucionMoneda" style="height: 250px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ !isset($status) || $status == '0' ? 'active' : '' }}"
                            onclick="filtrarPorStatus('0')">
                        PENDIENTES ({{count($pendientes)}})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ isset($status) && $status == '1' ? 'active' : '' }}"
                            onclick="filtrarPorStatus('1')">
                        APROBADAS ({{count($aprobadas)}})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ isset($status) && $status == '2' ? 'active' : '' }}"
                            onclick="filtrarPorStatus('2')">
                        RECHAZADAS ({{count($rechazadas)}})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ isset($status) && $status == '' ? 'active' : '' }}"
                            onclick="filtrarPorStatus('')">
                        TODAS ({{count($arraytransf)}})
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                @include('partials.tabla-transferencias-mejorada', [
                    'titulo' => 'TRANSFERENCIAS',
                    'transferencias' => $transferencias,
                    'tipo' => $status ?? 'todas',
                    'fechasreport' => $fechasreport ?? ''
                ])
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Panel de Filtros -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0 text-white"><i class="ri-filter-3-line me-2"></i>Filtros de Búsqueda</h5>
                </div>
                <div class="card-body">
                    <form method="post" id="form1" action="{{route('reportetransferencias')}}">
                        @csrf
                        @method('POST')

                        <div class="mb-3">
                            <label class="form-label fw-bold">Buscar:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-search-line"></i></span>
                                <input placeholder="Referencia, Banco, Titular..." class="form-control"
                                       type="text" value="{{ $busquedatransf ?? '' }}"
                                       name="busquedatransf" id="busquedatransf">
                            </div>
                            <small class="text-muted">Puede buscar por múltiples términos</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Estado:</label>
                            <select class="form-select" name="status" id="statusww">
                                <option value="0" {{ (isset($status) && $status == '0') ? 'selected' : '' }}>Pendientes</option>
                                <option value="1" {{ (isset($status) && $status == '1') ? 'selected' : '' }}>Aprobadas</option>
                                <option value="2" {{ (isset($status) && $status == '2') ? 'selected' : '' }}>Rechazadas</option>
                                <option value="" {{ (!isset($status) || $status == '') ? 'selected' : '' }}>Todas</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Moneda:</label>
                            <select class="form-select" name="moneda" id="moneda">
                                <option value="">Todas</option>
                                <option value="bs" {{ (isset($moneda) && $moneda == 'bs') ? 'selected' : '' }}>Bolívares</option>
                                <option value="usd" {{ (isset($moneda) && $moneda == 'usd') ? 'selected' : '' }}>Dólares</option>
                                <option value="cop" {{ (isset($moneda) && $moneda == 'cop') ? 'selected' : '' }}>Pesos COP</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Sucursal:</label>
                            <select class="form-select" name="sucursal_filter" id="sucursal_filter">
                                <option value="">Todas</option>
                                @foreach($sucursales_list ?? [] as $sucursal)
                                    <option value="{{ $sucursal->id }}" {{ (isset($sucursal_filter) && $sucursal_filter == $sucursal->id) ? 'selected' : '' }}>
                                        {{ $sucursal->descrip }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @php
                            $bancoseleccionado = '';
                        @endphp
                        <div class="mb-3">
                            <label class="form-label fw-bold">Banco:</label>
                            <select class="form-select" name="selectbanco" id="selectbanco">
                                <option value="0">Todos</option>
                                @foreach($listadobancos as $indexbanco => $banco)
                                    <option value="{{ $indexbanco }}" {{ (isset($selectbanco) && $selectbanco == $indexbanco) ? 'selected' : '' }}>
                                        {{ $banco  }}
                                        @php
                                            if(isset($selectbanco) && $selectbanco == $indexbanco)
                                                $bancoseleccionado = $banco;
                                        @endphp
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Rango de fechas:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" data-provider="flatpickr"
                                       data-range-date="true" data-date-format="d/m/Y" placeholder="dd/mm/yyyy to dd/mmm/yyyy"
                                       name="fechasreport" id="fechasreport" value="{{ $fechasreport ?? '' }}">
                                <button type="submit" class="btn btn-primary">Consultar</button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo:</label>
                            <select class="form-select" name="tipo" id="tipo">
                                <option value="">Todos</option>
                                <option value="venta" {{ (isset($tipo) && $tipo == 'venta') ? 'selected' : '' }}>Venta</option>
                                <option value="pago" {{ (isset($tipo) && $tipo == 'pago') ? 'selected' : '' }}>Pago</option>
                                <option value="ahorro" {{ (isset($tipo) && $tipo == 'ahorro') ? 'selected' : '' }}>Ahorro</option>
                                <option value="proveedor" {{ (isset($tipo) && $tipo == 'proveedor') ? 'selected' : '' }}>Proveedor</option>
                                <option value="gasto" {{ (isset($tipo) && $tipo == 'gasto') ? 'selected' : '' }}>Gasto</option>
                            </select>
                        </div>

                        <!-- Categoría con autocomplete -->
                        <div class="mb-3 position-relative">
                            <label class="form-label fw-bold">Categoría:</label>
                            <input type="text" class="form-control" name="categoria" id="categoria_filtro"
                                   value="{{ $categoria ?? '' }}" autocomplete="off">
                            <div class="spinner-border spinner-border-sm text-primary position-absolute"
                                 style="right: 10px; top: 35px; display: none;" id="categoriaFiltroSpinner"></div>
                            <div class="list-group position-absolute w-100 shadow"
                                 id="categoriaFiltroSuggestions" style="z-index: 1000; display: none;"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Ordenar por:</label>
                            <select class="form-select mb-2" name="ordenar_por" id="ordenar_por">
                                <option value="created_at" {{ (isset($ordenar_por) && $ordenar_por == 'created_at') ? 'selected' : '' }}>Fecha creación</option>
                                <option value="fecha" {{ (isset($ordenar_por) && $ordenar_por == 'fecha') ? 'selected' : '' }}>Fecha transf.</option>
                                <option value="monto" {{ (isset($ordenar_por) && $ordenar_por == 'monto') ? 'selected' : '' }}>Monto</option>
                            </select>
                            <select class="form-select" name="orden_direccion" id="orden_direccion">
                                <option value="desc" {{ (!isset($orden_direccion) || $orden_direccion == 'desc') ? 'selected' : '' }}>Descendente</option>
                                <option value="asc" {{ (isset($orden_direccion) && $orden_direccion == 'asc') ? 'selected' : '' }}>Ascendente</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mostrar:</label>
                            <select class="form-select" name="limite" id="limite">
                                <option value="50" {{ (isset($limite) && $limite == 50) ? 'selected' : '' }}>50</option>
                                <option value="100" {{ (isset($limite) && $limite == 100) ? 'selected' : '' }}>100</option>
                                <option value="150" {{ (!isset($limite) || $limite == 150) ? 'selected' : '' }}>150</option>
                                <option value="500" {{ (isset($limite) && $limite == 500) ? 'selected' : '' }}>500</option>
                                <option value="1000" {{ (isset($limite) && $limite == 1000) ? 'selected' : '' }}>1000</option>
                                <option value="0" {{ (isset($limite) && $limite == 0) ? 'selected' : '' }}>TODOS</option>
                            </select>
                            <small class="text-muted">Mostrando {{ count($arraytransf) }} registros</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-filter-3-line me-1"></i> Aplicar filtros
                            </button>
                            <button type="button" class="btn btn-secondary " onclick="limpiarTodosFiltros()">
                                <i class="ri-refresh-line me-1"></i> Limpiar filtros
                            </button>
                        </div>
                        @php
                            $filtrosActivos = $busquedatransf || $fechasreport || (isset($status) && $status !== '') ||
                                             (isset($moneda) && $moneda !== '') || (isset($sucursal_filter) && $sucursal_filter) ||
                                             (isset($selectbanco) && $selectbanco > 0);
                        @endphp

                        @if($filtrosActivos)
                            <div class="mt-3">
                                <label class="form-label">Filtros activos:</label>
                                <div class="d-flex flex-wrap gap-1">
                                    @if($busquedatransf)
                                        <span class="badge bg-info filter-badge" onclick="limpiarFiltro('busqueda')">
                                            <i class="ri-close-line"></i> Búsqueda: {{ $busquedatransf }}
                                        </span>
                                    @endif
                                    @if($fechasreport)
                                        <span class="badge bg-info filter-badge" onclick="limpiarFiltro('fechas')">
                                            <i class="ri-close-line"></i> {{ str_replace('to', ' al ', $fechasreport) }}
                                        </span>
                                    @endif
                                    @if(isset($status) && $status !== '')
                                        <span class="badge bg-info filter-badge" onclick="limpiarFiltro('status')">
                                            <i class="ri-close-line"></i>
                                            @if($status == '0') Pendientes
                                            @elseif($status == '1') Aprobadas
                                            @elseif($status == '2') Rechazadas
                                            @endif
                                        </span>
                                    @endif
                                    @if(isset($moneda) && $moneda !== '')
                                        <span class="badge bg-info filter-badge" onclick="limpiarFiltro('moneda')">
                                            <i class="ri-close-line"></i>
                                            @if($moneda == 'bs') Bolívares
                                            @elseif($moneda == 'usd') Dólares
                                            @elseif($moneda == 'cop') Pesos
                                            @endif
                                        </span>
                                    @endif
                                    @if(isset($sucursal_filter) && $sucursal_filter)
                                        <span class="badge bg-info filter-badge" onclick="limpiarFiltro('sucursal')">
                                            <i class="ri-close-line"></i>
                                            @foreach($sucursales_list as $s)
                                                @if($s->id == $sucursal_filter) {{ $s->descrip }} @endif
                                            @endforeach
                                        </span>
                                    @endif
                                    @if(isset($selectbanco) && $selectbanco > 0)
                                        <span class="badge bg-info filter-badge" onclick="limpiarFiltro('banco')">
                                            <i class="ri-close-line"></i> {{ $bancoseleccionado  }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Resúmenes -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">Resumen por Moneda</div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <span class="currency-badge currency-bs">Bs</span>
                            <h6 class="mt-2">@numero($montoBs)</h6>
                            <small>{{ count($arraybstransf) }} transf.</small>
                        </div>
                        <div class="col-4">
                            <span class="currency-badge currency-usd">$</span>
                            <h6 class="mt-2">@numero($montoUsd)</h6>
                            <small>{{ count($arrayusdtransf) }} transf.</small>
                        </div>
                        <div class="col-4">
                            <span class="currency-badge currency-cop">COP</span>
                            <h6 class="mt-2">@numero($montoCop,0)</h6>
                            <small>{{ count($arraycoptransf) }} transf.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0 text-white">
                        <i class="ri-list-check me-2"></i>Transferencias por Tipo
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach(['venta' => '💰 Ventas', 'pago' => '💸 Pagos', 'ahorro' => '🏦 Ahorros',
                                 'proveedor' => '📦 Proveedores', 'gasto' => '🧾 Gastos', 'otro' => '📌 Otros'] as $key => $label)
                            @php $cantidad = count($porTipo[$key] ?? []); @endphp
                            @if($cantidad > 0)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $label }}</strong>
                                    </div>
                                    <div>
                                        <span class="badge bg-primary rounded-pill">{{ $cantidad }}</span>
                                        <button class="btn btn-sm btn-link" onclick="filtrarPorTipo('{{ $key }}')">
                                            <i class="ri-filter-line"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Resumen por Sucursal -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-white">
                        <i class="ri-store-line me-2"></i>Transferencias por Sucursal
                    </h5>
                    <span class="badge bg-light text-dark">{{ count($sucursales) }} sucursales</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($sucursales as $indexsucu => $sucursal)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $sucursal }}</strong>
                                </div>
                                <div>
                                    <span class="badge bg-primary rounded-pill">{{ $arraysucu[$indexsucu]['cant'] ?? 0 }}</span>
                                    <button class="btn btn-sm btn-link" onclick="filtrarPorSucursal('{{ $indexsucu }}')">
                                        <i class="ri-filter-line"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item">No hay datos</div>
                        @endforelse
                    </div>
                </div>
            </div>


            <!-- Resumen por Banco -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0 text-white">
                        <i class="ri-bank-line me-2"></i>Transferencias por Banco
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($bancos as $indexbanco => $banco)
                            <div class="col-sm-12 col-lg-12 mb-2">
                                <div class="d-flex justify-content-between align-items-center p-2 {{(isset($selectbanco) && $selectbanco > 0 && $indexbanco == $selectbanco)? 'bg-primary text-white' : 'bg-light'}} rounded">
                                    <div>
                                        <strong>{{ $banco['descrip'] }}</strong>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge {{(isset($selectbanco) && $selectbanco > 0 && $indexbanco == $selectbanco)? 'bg-light text-dark' : 'bg-primary'}} rounded-pill">
                                            {{ $banco['cant'] }}
                                        </span>
                                        <button class="btn btn-sm btn-link" onclick="filtrarPorBanco('{{ $indexbanco }}')">
                                            <i class="ri-filter-line"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">No hay datos</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.modal-eliminar')
    @include('partials.modal-pendiente')

    <div class="modal fade" id="imagenModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Captura de Transferencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" id="imagenModalSrc" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        // Autocomplete de categorías
        const catInput = document.getElementById('categoria_filtro');
        const catSuggest = document.getElementById('categoriaFiltroSuggestions');
        const catSpinner = document.getElementById('categoriaFiltroSpinner');
        let catTimeout;

        if (catInput) {
            catInput.addEventListener('input', function() {
                clearTimeout(catTimeout);
                const query = this.value.trim();

                if (query.length >= 2) {
                    catTimeout = setTimeout(() => buscarCategorias(query), 300);
                } else {
                    catSuggest.style.display = 'none';
                }
            });
        }

        function limpiarFiltro(tipo) {
            switch(tipo) {
                case 'busqueda':
                    $('#busquedatransf').val('');
                    break;
                case 'fechas':
                    $('#fechasreport').val('');
                    break;
                case 'status':
                    $('#statusww').val('');
                    break;
                case 'moneda':
                    $('#moneda').val('');
                    break;
                case 'sucursal':
                    $('#sucursal_filter').val('');
                    break;
                case 'banco':
                    $('#selectbanco').val('0');
                    break;
            }
            $('#form1').submit();
        }

        function buscarCategorias(query) {
            catSpinner.style.display = 'block';

            fetch(`/transferencias/categorias?q=${encodeURIComponent(query)}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
                .then(r => r.json())
                .then(data => {
                    catSpinner.style.display = 'none';
                    if (data.length) {
                        catSuggest.innerHTML = data.map(c =>
                            `<a href="#" class="list-group-item list-group-item-action"
                            onclick="seleccionarCategoria('${c.replace(/'/g, "\\'")}'); return false;">
                            <i class="ri-price-tag-3-line me-2"></i>${c}</a>`
                        ).join('');
                        catSuggest.style.display = 'block';
                    } else {
                        catSuggest.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    catSpinner.style.display = 'none';
                });
        }

        window.seleccionarCategoria = function(categoria) {
            catInput.value = categoria;
            catSuggest.style.display = 'none';
            document.getElementById('form1').submit();
        };

        document.addEventListener('click', function(event) {
            if (catInput && !catInput.contains(event.target) && !catSuggest?.contains(event.target)) {
                catSuggest.style.display = 'none';
            }
        });

        // Scroll de tablas
        function scrollTabla(dir, el) {
            const container = el.closest('.card')?.querySelector('.tabla-container');
            if (container) {
                const amount = dir === 'right' ? 300 : -300;
                container.scrollLeft = Math.max(0, container.scrollLeft + amount);
                setTimeout(() => actualizarGradiente(container), 50);
            }
        }

        function actualizarGradiente(container) {
            if (!container) return;
            const tolerance = 5;
            if (container.scrollLeft <= tolerance) {
                container.classList.add('at-start');
                container.classList.remove('scrolled');
            } else {
                container.classList.remove('at-start');
                container.classList.add('scrolled');
            }
        }

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.tabla-container').forEach(c => {
                c.addEventListener('scroll', () => actualizarGradiente(c));
                actualizarGradiente(c);
            });
        });

        window.addEventListener('resize', function() {
            document.querySelectorAll('.tabla-container').forEach(c => actualizarGradiente(c));
        });

        // Filtros
        function filtrarPorStatus(status) {
            $('#statusww').val(status);
            $('#form1').submit();
        }

        function filtrarPorTipo(tipo) {
            $('#tipo').val(tipo);
            $('#form1').submit();
        }

        function filtrarPorSucursal(id) {
            $('#sucursal_filter').val(id);
            $('#form1').submit();
        }

        function filtrarPorBanco(id) {
            $('#selectbanco').val(id);
            $('#form1').submit();
        }

        function limpiarTodosFiltros() {
            $('#busquedatransf, #fechasreport, #statusww, #moneda, #sucursal_filter, #tipo').val('');
            $('#selectbanco, #ordenar_por, #orden_direccion').val('0');
            $('#limite').val('150');
            $('#form1').submit();
        }

        // Exportaciones
        function exportarExcel() {
            const form = document.getElementById('form1');
            const params = new URLSearchParams(new FormData(form)).toString();
            window.location.href = '{{ route("transferencias.exportar.excel") }}?' + params;
        }

        function exportarEstadisticas() {
            const form = document.getElementById('form1');
            const params = new URLSearchParams(new FormData(form)).toString();
            window.location.href = '{{ route("transferencias.exportar.estadisticas") }}?' + params;
        }

        // Imagen
        window.verImagen = function(url) {
            if (url && url !== '#') {
                $('#imagenModalSrc').attr('src', url);
                $('#imagenModal').modal('show');
            }
        };

        // Eliminar y pendiente
        $('#deleterecord').off('click').on('click', function() {
            const id = $(this).data('id');
            $('#cargandodelete').html('<button class="btn btn-outline-primary btn-load"><span class="spinner-border"></span></button>');

            $.ajax({
                type: 'DELETE',
                url: '/transferencias/' + id,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(data) {
                    if (data.deleted == 1) {
                        $("#deleteRecord-close").click();
                        $('#form1').submit();
                    }
                }
            });
        });

        $('#pendrecord').off('click').on('click', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            $('#cargandopend').html('<button class="btn btn-outline-primary btn-load"><span class="spinner-border"></span></button>');

            $.ajax({
                type: 'POST',
                url: '/transferencias/pendienteAgain',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: { id: id },
                success: function(data) {
                    if (data.error == 1) {
                        window.location.href = '/reporte/transferencias';
                    }
                }
            });
        });

        // Gráficos
        @if( isset($estadisticas['generales']))
        $('#panelEstadisticas').on('shown.bs.collapse', function() {
            const ctxDiaria = document.getElementById('graficoTendenciaDiaria')?.getContext('2d');
            if (ctxDiaria) {
                const tendencia = @json($estadisticas['tendencia_diaria']);
                new Chart(ctxDiaria, {
                    type: 'line',
                    data: {
                        labels: Object.keys(tendencia).map(f => f.split('-').reverse().join('/')),
                        datasets: [{
                            label: 'Cantidad',
                            data: Object.values(tendencia).map(d => d.cantidad),
                            borderColor: '#0072c5',
                            backgroundColor: 'rgba(0,114,197,0.1)',
                            tension: 0.4
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            }

            const ctxMoneda = document.getElementById('graficoDistribucionMoneda')?.getContext('2d');
            if (ctxMoneda) {
                new Chart(ctxMoneda, {
                    type: 'doughnut',
                    data: {
                        labels: ['Bolívares', 'Dólares', 'Pesos COP'],
                        datasets: [{
                            data: [
                                {{ $estadisticas['por_moneda']['bs']['cantidad'] }},
                                {{ $estadisticas['por_moneda']['usd']['cantidad'] }},
                                {{ $estadisticas['por_moneda']['cop']['cantidad'] }}
                            ],
                            backgroundColor: ['#0d6efd', '#198754', '#ffc107']
                        }]
                    }
                });
            }
        });
        @endif
    </script>
@endsection
