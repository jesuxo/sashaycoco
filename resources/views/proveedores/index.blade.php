@extends('layouts.master')
@section('title')
    Proveedores
@endsection
@section('css')
    <style>
        .search-box {
            position: relative;
        }

        #predictive-results {
            border: 1px solid rgba(0,0,0,0.1);
            border-top: none;
            border-radius: 0 0 8px 8px;
            max-height: 400px;
            overflow-y: auto;
            background: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        #predictive-results .list-group-item {
            border-left: none;
            border-right: none;
            border-radius: 0;
            cursor: pointer;
            transition: all 0.2s;
        }

        #predictive-results .list-group-item:first-child {
            border-top: none;
        }

        #predictive-results .list-group-item:last-child {
            border-bottom: none;
        }

        #predictive-results .list-group-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }

        #predictive-results .list-group-item.active {
            background-color: #0072c5 !important;
            border-color: #0072c5 !important;
            color: white;
        }

        #predictive-results .list-group-item.active small {
            color: rgba(255,255,255,0.9) !important;
        }

        /* Animación de carga */
        .search-box.loading::after {
            content: '';
            position: absolute;
            right: 40px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #0072c5;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: translateY(-50%) rotate(0deg); }
            100% { transform: translateY(-50%) rotate(360deg); }
        }

        .btn-soft-light:hover, .codprovseleted{
            background-color: #e0f2ff !important;
        }

        .nav-pills .nav-link {
            background: #eee !important;
            border-bottom-right-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }

        .nav-pills .nav-link.active  {
            background: #0072c5 !important;
            border-bottom-right-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
            color: white !important;
        }

        .nav-pills{
            border-bottom: 1px solid #0072c5;
        }

        .tdline{
            border:1px solid #0072c5 !important;
            font-size: 12px;
        }

        .tdlineff{
            border-left:1px solid #fff !important;
            font-size: 12px;
            color: white !important;
            background-color: #0072c5 !important;
        }

        .monto-pendiente {
            color: #f59e0b;
            font-weight: bold;
        }

        .monto-conciliado {
            color: #10b981;
            font-weight: bold;
        }

        /* Estilos para el botón y tab de análisis */
        .btn-analisis {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-analisis:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .badge-analisis {
            background: #28a745;
            color: white;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 5px;
        }

        .nav-link i {
            margin-right: 5px;
        }

        .kpi-card-mini {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 15px;
            transition: transform 0.3s;
        }

        .kpi-card-mini:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }

        .kpi-valor-mini {
            font-size: 24px;
            font-weight: bold;
            margin: 5px 0;
        }

        .text-primary-custom { color: #667eea; }
        .text-success-custom { color: #28a745; }
        .text-warning-custom { color: #f59e0b; }

        /* Estilos para Cuentas por Pagar */
        .table-danger-row {
            background-color: #fff5f5 !important;
        }
        .progress-bar-custom {
            transition: width 0.5s ease;
        }
        .cxp-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        #resumenGeneralCxpCard {
            transition: all 0.3s ease;
        }

        #resumenGeneralCxpCard.hidden {
            display: none;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-xxl-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0" id="addCategoryLabel">Buscar Proveedores</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('proveedores.index') }}" method="GET" autocomplete="off" class="needs-validation" id="proveedorForm">
                        <input type="hidden" id="codprov" name="codprov" value="{{ $codprov ?? '' }}">
                        <div class="row">
                            <div class="col-xxl-12">
                                <div class="search-box mb-3 position-relative">
                                    <input type="text" class="form-control search" id="busqueda" name="busqueda"
                                           value="{{ $busqueda ?? '' }}"
                                           placeholder="Buscar por nombre, RIF, teléfono..."
                                           autocomplete="off">
                                    <i class="ri-search-line search-icon"></i>

                                    <!-- Contenedor para resultados predictivos -->
                                    <div id="predictive-results" class="list-group position-absolute w-100 shadow"
                                         style="z-index: 1000; max-height: 400px; overflow-y: auto; display: none; background: white; border-radius: 0 0 8px 8px;">
                                    </div>
                                </div>
                                <div class="invalid-feedback">Ingrese el nombre o código del proveedor</div>
                            </div>

                            <div class="col-xxl-12 col-lg-6">
                                @if($busqueda != '')
                                    <div class="accordion accordion-flush filter-accordion">
                                        <div class="card-body border-bottom p-0">
                                            @if(isset($proveedores) and count($proveedores)>0)
                                                <div>
                                                    <p class="text-muted fs-13 mb-3">Resultados para: {{$busqueda}}</p>
                                                    @foreach($proveedores as $prov)
                                                        <div class="d-flex align-items-center">
                                                            <a href="javascript:;" onclick="$('#codprov').val('{{$prov->codprov}}'); $('#proveedorForm').submit()"
                                                               class="card btn btn-soft-light d-flex p-2
                                                               {{(isset($codprov) and $codprov != '' and $codprov == $prov->codprov) ? 'codprovseleted' : ''}}
                                                               border-bottom border-bottom-dashed cursor-pointer"
                                                               style="text-align: left; flex-grow: 1;">
                                                                <div class="flex-grow-1">
                                                                    <h5>{{$prov->descrip}}</h5>
                                                                    <p class="text-muted mb-0">{{$prov->codprov}}</p>
                                                                </div>
                                                            </a>
                                                            <a href="{{ route('proveedores.productos-panel', $prov->codprov) }}"
                                                               class="btn btn-sm btn-outline-info ms-2"
                                                               target="_blank"
                                                               data-bs-toggle="tooltip"
                                                               title="Ver análisis de compras">
                                                                <i class="ri-bar-chart-2-line"></i>
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div>
                                                    <p class="text-muted fs-13 mb-3">No se encontraron proveedores para la búsqueda: <b>{{$busqueda}}</b></p>
                                                    <a class="card btn btn-soft-light d-flex p-2 border-bottom border-bottom-dashed cursor-pointer"
                                                       style="text-align: left; display: none">
                                                        <div class="flex-grow-1" href="#modalProveedor" data-bs-toggle="modal">
                                                            <h6>+1 PROVEEDOR NUEVO</h6>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>

                    <form name="form2" id="form2" class="tablelist-form"
                          action="{{(isset($proveedor->codprov) and $proveedor->codprov != '') ? route('proveedoresupdate') : route('proveedores.store')}}"
                          autocomplete="off" method="post">
                        @csrf
                        @method('POST')
                        <div class="modal fade" id="modalProveedor" tabindex="-1" aria-labelledby="modalProveedor" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header px-4 pt-4">
                                        <h5 class="modal-title" id="exampleModalLabel">
                                            Información del Proveedor
                                            {{(isset($proveedor->codprov) and $proveedor->codprov != '') ? $proveedor->descrip : ' nuevo' }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                                    </div>

                                    <div class="modal-body p-4">
                                        <div class="row">
                                            <div id="alert-error-msg" class="d-none alert alert-danger py-2"></div>

                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="proveedor-cod-field" class="form-label">Código</label>
                                                    <input type="text" id="proveedor-cod-field" name="codprov"
                                                           value="{{(isset($proveedor->codprov) and $proveedor->codprov !== '') ? $proveedor->codprov : ((isset($busqueda) and $busqueda != '') ? $busqueda : '')}}"
                                                           class="form-control" placeholder="Ej: PROV001" readonly required>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="id3-cod-field" class="form-label">RIF / Documento</label>
                                                    <input type="text" id="id3-cod-field" name="id3" class="form-control"
                                                           value="{{(isset($proveedor->codprov) and $proveedor->codprov !== '') ? $proveedor->id3 : ''}}"
                                                           placeholder="Ej: J-12345678-5">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="mb-2">
                                                    <label for="descrip-name-field" class="form-label">Nombre / Razón Social</label>
                                                    <input type="text" id="descrip-name-field" value="{{(isset($proveedor->descrip)) ? $proveedor->descrip : ''}}"
                                                           name="descrip" class="form-control" placeholder="Ej: Proveedores C.A." required>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="email-field" class="form-label">Email</label>
                                                    <input type="email" name="email" id="email-field" class="form-control"
                                                           value="{{(isset($proveedor->codprov)) ? $proveedor->email : ''}}"
                                                           placeholder="Ej: correo@proveedor.com">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="clase-field" class="form-label">Clase</label>
                                                    <input type="text" name="clase" id="clase-field"
                                                           value="{{(isset($proveedor->codprov)) ? $proveedor->clase : ''}}"
                                                           class="form-control" placeholder="">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="phone-field" class="form-label">Teléfono</label>
                                                    <input type="text" name="telef" id="phone-field" class="form-control"
                                                           value="{{(isset($proveedor->codprov)) ? $proveedor->telef : ''}}"
                                                           placeholder="Ej: 0414-12345678">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="movil-field" class="form-label">Celular</label>
                                                    <input type="text" name="movil" id="movil-field" class="form-control"
                                                           value="{{(isset($proveedor->codprov)) ? $proveedor->movil : ''}}"
                                                           placeholder="Ej: 5841412345678">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="represent-field" class="form-label">Representante</label>
                                                    <input type="text" name="represent" id="represent-field"
                                                           value="{{(isset($proveedor->codprov)) ? $proveedor->represent : ''}}"
                                                           class="form-control" placeholder="">
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="direc1-field" class="form-label">Dirección 1</label>
                                                    <input type="text" name="direc1" id="direc1-field" class="form-control"
                                                           value="{{(isset($proveedor->codprov)) ? $proveedor->direc1 : ''}}"
                                                           placeholder="Dirección principal">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="direc2-field" class="form-label">Dirección 2</label>
                                                    <input type="text" name="direc2" id="direc2-field" class="form-control"
                                                           value="{{(isset($proveedor->codprov)) ? $proveedor->direc2 : ''}}"
                                                           placeholder="">
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="form-check mt-3">
                                                    <input class="form-check-input" type="checkbox" name="activo" id="activo-field" value="1"
                                                        {{ !isset($proveedor->activo) || $proveedor->activo == 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="activo-field">
                                                        Proveedor Activo
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button type="button" class="btn btn-ghost-danger" data-bs-dismiss="modal">Cerrar</button>
                                            <button type="button" onclick="$('#form2').submit()" class="btn btn-success">
                                                {{(isset($proveedor->codprov) and $proveedor->codprov !== '') ? 'Modificar' : 'Crear'}}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xxl-9">
            @if(isset($proveedor) and isset($proveedor->descrip))
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">{{$proveedor->descrip}}</h5>
                            <div class="flex-shrink-0">
                                <p class="mb-0">RIF: <b>{{$proveedor->id3 ?? 'N/A'}}</b></p>
                            </div>

                            <div class="flex-shrink-0" style="margin-left: 15px;">
                                <a href="{{ route('proveedores.productos-panel', $proveedor->codprov) }}"
                                   class="btn btn-primary"
                                   target="_blank"
                                   data-bs-toggle="tooltip"
                                   title="Ver análisis detallado de compras vs ventas">
                                    <i class="ri-bar-chart-2-line"></i>
                                    Análisis de Compras
                                    <span class="badge-analisis">Nuevo</span>
                                </a>
                            </div>

                            <div class="flex-shrink-0" style="margin-left: 20px;">
                                <a class="btn btn-primary" href="#modalProveedor" data-bs-toggle="modal">Modificar</a>
                            </div>
                        </div>
                        @if($tab == 'tab1')
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <div class="table-responsive">
                                        <table class="table table-borderless table-sm mb-0">
                                            <tbody>
                                            @if(isset($proveedor->direc1) and strlen($proveedor->direc1) > 2)
                                                <tr bgcolor="#eee">
                                                    <td width="25%">Dirección 1</td>
                                                    <td width="75%" class="fw-medium">{{$proveedor->direc1}}</td>
                                                </tr>
                                            @endif
                                            @if(isset($proveedor->direc2) and strlen($proveedor->direc2) > 2)
                                                <tr>
                                                    <td>Dirección 2</td>
                                                    <td class="fw-medium">{{$proveedor->direc2}}</td>
                                                </tr>
                                            @endif
                                            @if(isset($proveedor->email) and strlen($proveedor->email) > 2)
                                                <tr bgcolor="#eee">
                                                    <td>Email</td>
                                                    <td class="fw-medium">{{$proveedor->email}}</td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <div class="table-responsive">
                                        <table class="table table-borderless table-sm mb-0">
                                            <tbody>
                                            @if(isset($proveedor->telef) and strlen($proveedor->telef) > 2)
                                                <tr bgcolor="#eee">
                                                    <td width="25%">Teléfono</td>
                                                    <td width="75%" class="fw-medium">{{$proveedor->telef}}</td>
                                                </tr>
                                            @endif
                                            @if(isset($proveedor->movil) and strlen($proveedor->movil) > 2)
                                                <tr>
                                                    <td>Celular</td>
                                                    <td class="fw-medium">{{$proveedor->movil}}</td>
                                                </tr>
                                            @endif
                                            @if(isset($proveedor->represent) and strlen($proveedor->represent) > 2)
                                                <tr bgcolor="#eee">
                                                    <td>Representante</td>
                                                    <td class="fw-medium">{{$proveedor->represent}}</td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="card-body">
                        <div class="d-flex align-items-center flex-wrap gap-3 mb-4">
                            <ul class="nav nav-pills flex-grow-1 mb-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link {{ ($tab == 'tab5') ? 'active' : '' }}"
                                       href="{{ route('proveedores.index', ['codprov' => $proveedor->codprov, 'tab' => 'tab5']) }}"
                                       role="tab">
                                        <i class="ri-shopping-cart-line"></i>
                                        Compras
                                        @if(isset($compras) && $compras instanceof \Illuminate\Pagination\LengthAwarePaginator && $compras->total() > 0)
                                            <span class="badge bg-info ms-1">{{ $compras->total() }}</span>
                                        @endif
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ ($tab == 'tab6') ? 'active' : '' }}"
                                       href="{{ route('proveedores.index', ['codprov' => $proveedor->codprov, 'tab' => 'tab6']) }}"
                                       role="tab">
                                        <i class="ri-bank-card-line"></i>
                                        Cuentas por Pagar
                                        @if(isset($totalCxpDeuda) && $totalCxpDeuda > 0)
                                            <span class="badge bg-warning ms-1">${{ number_format($totalCxpDeuda, 0) }}</span>
                                        @endif
                                    </a>
                                </li>

                                <li class="nav-item d-none">
                                    <a class="nav-link {{ ($tab == 'tab1') ? 'active' : '' }}"
                                       href="{{ route('proveedores.index', ['codprov' => $proveedor->codprov, 'tab' => 'tab1']) }}"
                                       role="tab">
                                        Pagos Pendientes
                                    </a>
                                </li>
                                <li class="nav-item d-none">
                                    <a class="nav-link {{ ($tab == 'tab2') ? 'active' : '' }}"
                                       href="{{ route('proveedores.index', ['codprov' => $proveedor->codprov, 'tab' => 'tab2']) }}"
                                       role="tab">
                                        Pagos Realizados
                                    </a>
                                </li>
                                <li class="nav-item d-none">
                                    <a class="nav-link {{ ($tab == 'tab3') ? 'active' : '' }}"
                                       href="{{ route('proveedores.index', ['codprov' => $proveedor->codprov, 'tab' => 'tab3']) }}"
                                       role="tab">
                                        Resumen
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content">
                            {{-- TAB 1: PAGOS PENDIENTES --}}
                            @if($tab == 'tab1')
                                <div class="tab-pane active" role="tabpanel">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive table-card mb-1">
                                                <table width="100%" border="0" class="table align-middle table-nowrap">
                                                    <tr bgcolor="#fff">
                                                        <td width="10%" height="30" align="center" class="tdlineff">Fecha Viaje</td>
                                                        <td width="8%" align="center" class="tdlineff">Viaje</td>
                                                        <td width="15%" align="center" class="tdlineff">Cliente</td>
                                                        <td width="12%" align="center" class="tdlineff">Modelo</td>
                                                        <td width="5%" align="center" class="tdlineff">Cant.</td>
                                                        <td width="10%" align="center" class="tdlineff">Transporte</td>
                                                        <td width="10%" align="center" class="tdlineff">Retención</td>
                                                        <td width="10%" align="center" class="tdlineff">Monto a Pagar</td>
                                                        <td width="10%" align="center" class="tdlineff">Acciones</td>
                                                    </tr>

                                                    @php $totalPendiente = 0; @endphp
                                                    @forelse($pagosPendientes as $pago)
                                                        @php $totalPendiente += $pago->monto_esperado_cliente; @endphp
                                                        <tr>
                                                            <td height="30" align="center">
                                                                {{ $pago->viaje->fecha_inicio->format('d/m/Y') }}
                                                            </td>
                                                            <td align="center">
                                                                <a href="#" onclick="verViaje({{ $pago->viaje_id }})">
                                                                    {{ $pago->viaje->folio ?? $pago->viaje_id }}
                                                                </a>
                                                            </td>
                                                            <td align="left">{{ $pago->cliente->descrip ?? 'N/A' }}</td>
                                                            <td align="left">{{ $pago->modelo_moto }}</td>
                                                            <td align="center">{{ $pago->cantidad }}</td>
                                                            <td align="right">${{ number_format($pago->monto_transporte_proveedor, 2) }}</td>
                                                            <td align="right">${{ number_format($pago->retencion_proveedor, 2) }}</td>
                                                            <td align="right" class="monto-pendiente">
                                                                ${{ number_format($pago->monto_esperado_cliente, 2) }}
                                                            </td>
                                                            <td align="center">
                                                                <button class="btn btn-sm btn-success"
                                                                        onclick="marcarPagado({{ $pago->id }}, {{ $pago->monto_esperado_cliente }})">
                                                                    <i class="ri-check-line"></i> Pagar
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="9" align="center" height="50">
                                                                No hay pagos pendientes
                                                            </td>
                                                        </tr>
                                                    @endforelse

                                                    @if($pagosPendientes->count() > 0)
                                                        <tr bgcolor="#eee">
                                                            <td colspan="7" align="right"><strong>TOTAL PENDIENTE:</strong></td>
                                                            <td align="right" class="monto-pendiente">
                                                                <strong>${{ number_format($totalPendiente, 2) }}</strong>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- TAB 2: PAGOS REALIZADOS --}}
                            @if($tab == 'tab2')
                                <div class="tab-pane active" role="tabpanel">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive table-card mb-1">
                                                <table width="100%" border="0" class="table align-middle table-nowrap">
                                                    <tr bgcolor="#fff">
                                                        <td width="10%" height="30" align="center" class="tdlineff">Fecha Pago</td>
                                                        <td width="10%" align="center" class="tdlineff">Fecha Viaje</td>
                                                        <td width="8%" align="center" class="tdlineff">Viaje</td>
                                                        <td width="15%" align="center" class="tdlineff">Cliente</td>
                                                        <td width="12%" align="center" class="tdlineff">Modelo</td>
                                                        <td width="5%" align="center" class="tdlineff">Cant.</td>
                                                        <td width="10%" align="center" class="tdlineff">Esperado</td>
                                                        <td width="10%" align="center" class="tdlineff">Pagado</td>
                                                        <td width="10%" align="center" class="tdlineff">Diferencia</td>
                                                        <td width="10%" align="center" class="tdlineff">Notas</td>
                                                    </tr>

                                                    @php
                                                        $totalEsperado = 0;
                                                        $totalPagado = 0;
                                                    @endphp
                                                    @forelse($pagosRealizados as $pago)
                                                        @php
                                                            $totalEsperado += $pago->monto_esperado_cliente;
                                                            $totalPagado += $pago->monto_real_cliente;
                                                        @endphp
                                                        <tr>
                                                            <td height="30" align="center">
                                                                {{ $pago->fecha_conciliacion ? \Carbon\Carbon::parse($pago->fecha_conciliacion)->format('d/m/Y') : 'N/A' }}
                                                            </td>
                                                            <td align="center">{{ $pago->viaje->fecha_inicio->format('d/m/Y') }}</td>
                                                            <td align="center">
                                                                <a href="#" onclick="verViaje({{ $pago->viaje_id }})">
                                                                    {{ $pago->viaje->folio ?? $pago->viaje_id }}
                                                                </a>
                                                            </td>
                                                            <td align="left">{{ $pago->cliente->descrip ?? 'N/A' }}</td>
                                                            <td align="left">{{ $pago->modelo_moto }}</td>
                                                            <td align="center">{{ $pago->cantidad }}</td>
                                                            <td align="right">${{ number_format($pago->monto_esperado_cliente, 2) }}</td>
                                                            <td align="right" class="monto-conciliado">${{ number_format($pago->monto_real_cliente, 2) }}</td>
                                                            <td align="right" class="{{ ($pago->diferencia ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                                                ${{ number_format($pago->diferencia ?? 0, 2) }}
                                                            </td>
                                                            <td align="left">
                                                                <small>{{ Str::limit($pago->notas_conciliacion, 20) }}</small>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="10" align="center" height="50">
                                                                No hay pagos realizados
                                                            </td>
                                                        </tr>
                                                    @endforelse

                                                    @if($pagosRealizados->count() > 0)
                                                        <tr bgcolor="#eee">
                                                            <td colspan="6" align="right"><strong>TOTALES:</strong></td>
                                                            <td align="right"><strong>${{ number_format($totalEsperado, 2) }}</strong></td>
                                                            <td align="right" class="monto-conciliado">
                                                                <strong>${{ number_format($totalPagado, 2) }}</strong>
                                                            </td>
                                                            <td align="right">
                                                                <strong class="{{ ($totalPagado - $totalEsperado) >= 0 ? 'text-success' : 'text-danger' }}">
                                                                    ${{ number_format($totalPagado - $totalEsperado, 2) }}
                                                                </strong>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- TAB 3: RESUMEN --}}
                            @if($tab == 'tab3')
                                <div class="tab-pane active" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header bg-info text-white">
                                                    <h6 class="mb-0">Resumen de Pagos por Mes</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead>
                                                            <tr>
                                                                <th>Período</th>
                                                                <th class="text-end">Pendiente</th>
                                                                <th class="text-end">Pagado</th>
                                                                <th class="text-end">Total</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @forelse($resumenPagos as $resumen)
                                                                <tr>
                                                                    <td>{{ \Carbon\Carbon::create()->month($resumen->mes)->format('F') }} {{ $resumen->anio }}</td>
                                                                    <td class="text-end monto-pendiente">${{ number_format($resumen->total_pendiente ?? 0, 2) }}</td>
                                                                    <td class="text-end monto-conciliado">${{ number_format($resumen->total_pagado ?? 0, 2) }}</td>
                                                                    <td class="text-end">${{ number_format(($resumen->total_pendiente ?? 0) + ($resumen->total_pagado ?? 0), 2) }}</td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="4" class="text-center">No hay datos de pagos</td>
                                                                </tr>
                                                            @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header bg-success text-white">
                                                    <h6 class="mb-0">Estadísticas</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <label>Total Pagos Pendientes</label>
                                                        <h3 class="monto-pendiente">
                                                            ${{ number_format($pagosPendientes->sum('monto_esperado_cliente'), 2) }}
                                                        </h3>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Total Pagos Realizados</label>
                                                        <h3 class="monto-conciliado">
                                                            ${{ number_format($pagosRealizados->sum('monto_real_cliente'), 2) }}
                                                        </h3>
                                                    </div>
                                                    <div>
                                                        <label>Cantidad de Viajes</label>
                                                        <h3>{{ $pagosPendientes->count() + $pagosRealizados->count() }}</h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- TAB 5: COMPRAS DEL PROVEEDOR --}}
                            @if($tab == 'tab5')
                                <div class="tab-pane active" role="tabpanel">
                                    <div class="card">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                <i class="ri-shopping-cart-line"></i>
                                                Compras realizadas a {{ $proveedor->descrip }}
                                            </h6>
                                            <div class="d-none">
                                                <span class="badge bg-primary me-2">Total Unidades: {{ number_format($totales_compras['unidades'] ?? 0, 0) }}</span>
                                                <span class="badge bg-success">Total Monto: ${{ number_format($totales_compras['monto'] ?? 0, 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="card-body">

                                            <!-- Filtros rápidos -->
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <form method="GET" action="{{ route('proveedores.index') }}" class="row g-2">
                                                        <input type="hidden" name="codprov" value="{{ $proveedor->codprov }}">
                                                        <div class="col-md-3">
                                                            <label class="form-label">Fecha Desde</label>
                                                            <input type="date" name="fecha_desde" class="form-control form-control-sm" value="{{ request('fecha_desde') }}">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label">Fecha Hasta</label>
                                                            <input type="date" name="fecha_hasta" class="form-control form-control-sm" value="{{ request('fecha_hasta') }}">
                                                        </div>
                                                        <div class="col-md-2 d-flex align-items-end">
                                                            <button type="submit" class="btn btn-primary btn-sm">
                                                                <i class="ri-filter-3-line"></i> Filtrar
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover table-bordered">
                                                    <thead class="table-light">
                                                    <tr>
                                                        <th width="8%">Fecha</th>
                                                        <th width="10%">Documento</th>
                                                        <th width="8%">Tipo</th>
                                                        <th width="10%">Unidades</th>
                                                        <th width="12%">Monto</th>
                                                        <th width="15%">Sucursal</th>
                                                        <th width="15%">Observaciones</th>
                                                        <th width="12%">Acciones</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @forelse($compras ?? [] as $compra)
                                                        @php
                                                            $signo = $compra->tipocom == 'I' ? -1 : 1;
                                                            $tipoTexto = $compra->tipocom == 'H' ? 'Compra' : 'Devolución';
                                                            $tipoColor = $compra->tipocom == 'H' ? 'success' : 'danger';
                                                        @endphp
                                                        <tr>
                                                            <td>{{ \Carbon\Carbon::parse($compra->fechae)->format('d/m/Y') }}</td>
                                                            <td>
                                                                <strong>{{ $compra->numerod }}</strong>
                                                            </td>
                                                            <td>
                                            <span class="badge bg-{{ $tipoColor }}">
                                                {{ $tipoTexto }}
                                            </span>
                                                            </td>
                                                            <td class="text-end {{ $compra->tipocom == 'I' ? 'text-danger' : '' }}">
                                                                {{ number_format($compra->total_unidades_calculado ?? $compra->totalprd, 0) }}
                                                            </td>
                                                            <td class="text-end {{ $compra->tipocom == 'I' ? 'text-danger' : '' }}">
                                                                ${{ number_format($compra->total_monto_calculado ?? $compra->monto, 2) }}
                                                            </td>
                                                            <td>{{ $compra->sucursal->descrip ?? 'N/A' }}</td>
                                                            <td>
                                                                <small>{{ Str::limit($compra->descrip ?? '', 30) }}</small>
                                                            </td>
                                                            <td>
                                                                <button type="button"
                                                                        class="btn btn-sm btn-outline-primary ver-compra"
                                                                        data-id="{{ $compra->id }}"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#compraModal">
                                                                    <i class="ri-file-list-line"></i> Ver
                                                                </button>
                                                            </td>
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

                                            <!-- Paginación -->
                                            @if(isset($compras) && $compras instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                                <div class="d-flex justify-content-end mt-3">
                                                    {{ $compras->appends(request()->query())->links() }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- TAB 6: CUENTAS POR PAGAR --}}
                            @if($tab == 'tab6')
                                <div class="tab-pane active" role="tabpanel">
                                    <div class="card">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap">
                                            <h6 class="mb-0">
                                                <i class="ri-bank-card-line"></i>
                                                Cuentas por Pagar - {{ $proveedor->descrip }}
                                            </h6>
                                            <div class="d-flex gap-2 mt-2 mt-sm-0 d-none">
                                                <div class="form-check form-switch ">
                                                    <input type="checkbox" class="form-check-input" id="soloResumenSwitch">
                                                    <label class="form-check-label" for="soloResumenSwitch">Resumen por proveedor</label>
                                                </div>
                                                <div class="form-check form-switch d-none">
                                                    <input type="checkbox" class="form-check-input" id="solovencidosSwitch">
                                                    <label class="form-check-label" for="solovencidosSwitch">Solo vencidos</label>
                                                </div>
                                                <div>
                                                    <input type="text" id="fechavenceFilter" class="form-control form-control-sm datepicker"
                                                           placeholder="Fecha vencimiento" style="width: 130px;">
                                                </div>
                                                <button type="button" id="btnFiltrarCxp" class="btn btn-primary btn-sm">
                                                    <i class="ri-filter-3-line"></i> Filtrar
                                                </button>
                                                <button type="button" id="btnActualizarCxp" class="btn btn-secondary btn-sm">
                                                    <i class="ri-refresh-line"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div id="cxp-loading" class="text-center py-4 d-none">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Cargando...</span>
                                                </div>
                                                <p class="mt-2">Cargando cuentas por pagar...</p>
                                            </div>
                                            <div id="cxp-content">
                                                <!-- Contenido cargado via AJAX -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            @else
                {{-- Resumen General de Cuentas por Pagar --}}
                <div class="card mt-3" id="resumenGeneralCxpCard">
                    <div class="card-header bg-warning bg-opacity-25">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h6 class="mb-0">
                                <i class="ri-bank-card-line"></i>
                                Resumen General - Cuentas por Pagar
                                <span class="badge bg-warning ms-2" id="totalProveedoresCxp">0</span>
                            </h6>
                            <div class="d-flex gap-2 mt-2 mt-sm-0">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="resumenSoloVencidosSwitch">
                                    <label class="form-check-label" for="resumenSoloVencidosSwitch">Solo vencidos</label>
                                </div>
                                <div>
                                    <input type="text" id="resumenFechavenceFilter" class="form-control form-control-sm datepicker"
                                           placeholder="Fecha vencimiento" style="width: 130px;">
                                </div>
                                <button type="button" id="btnFiltrarResumenGeneral" class="btn btn-primary btn-sm">
                                    <i class="ri-filter-3-line"></i> Filtrar
                                </button>
                                <button type="button" id="btnActualizarResumenGeneral" class="btn btn-secondary btn-sm">
                                    <i class="ri-refresh-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="resumen-general-loading" class="text-center py-4 d-none">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2">Cargando resumen de cuentas por pagar...</p>
                        </div>
                        <div id="resumen-general-content">
                            <!-- Contenido cargado via AJAX -->
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal para ver detalle de compra --}}
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

    {{-- Modal para registrar pago --}}
    <div class="modal fade" id="modalPago" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Pago a Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('proveedores.marcar-pagado') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="pago_id" id="pago_id">

                        <div class="mb-3">
                            <label class="form-label">Monto Esperado</label>
                            <input type="text" class="form-control" id="monto_esperado" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Monto Real <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control" name="monto_real" id="monto_real" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fecha de Pago <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="fecha_pago" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notas</label>
                            <textarea class="form-control" name="notas" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Registrar Pago</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ============================================
        // CONFIGURACIÓN INICIAL
        // ============================================
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // ============================================
        // FUNCIONES EXISTENTES
        // ============================================
        function marcarPagado(id, montoEsperado) {
            $('#pago_id').val(id);
            $('#monto_esperado').val('$' + montoEsperado.toFixed(2));
            $('#monto_real').val(montoEsperado.toFixed(2));
            $('#modalPago').modal('show');
        }

        function verViaje(id) {
            window.open(`/viajes/${id}/ver`, '_blank');
        }

        // ============================================
        // FUNCIONES DE BÚSQUEDA PREDICTIVA
        // ============================================
        $(document).ready(function() {
            const $searchInput = $('#busqueda');
            const $resultsContainer = $('#predictive-results');
            const $searchBox = $('.search-box');
            const $proveedorForm = $('#proveedorForm');
            const $codprovInput = $('#codprov');

            function cargarTodosProveedores() {
                $searchBox.addClass('loading');
                $.ajax({
                    url: '{{ route("proveedores.buscarPredictivo") }}',
                    type: 'POST',
                    data: {
                        term: $searchInput.val()
                    },
                    success: function(data) {
                        $searchBox.removeClass('loading');
                        mostrarResultados(data);
                    },
                    error: function(xhr) {
                        $searchBox.removeClass('loading');
                        console.error('Error al cargar proveedores:', xhr);
                    }
                });
            }

            function seleccionarProveedor(codprov, descrip) {
                $codprovInput.val(codprov);
                $searchInput.val(descrip);
                $resultsContainer.hide();
                $proveedorForm.submit();
            }

            function mostrarResultados(proveedores) {
                if (proveedores.length === 0) {
                    $resultsContainer.html(`
                        <div class="list-group-item text-muted py-3 text-center">
                            <i class="ri-inbox-line fs-4"></i><br>
                            No se encontraron proveedores
                        </div>
                    `).show();
                    return;
                }

                let html = '';
                proveedores.forEach(function(prov, index) {
                    const descripEscaped = prov.descrip ? prov.descrip.replace(/"/g, '&quot;') : '';
                    html += `
                        <a href="javascript:void(0);" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3 proveedor-item"
                           data-index="${index}"
                           data-codprov="${prov.codprov || ''}"
                           data-descrip="${descripEscaped}">
                            <div>
                                <strong>${prov.descrip || 'Sin nombre'}</strong><br>
                                <small class="text-muted">
                                    <i class="ri-price-tag-3-line"></i> ${prov.codprov || 'N/A'}
                                    ${prov.id3 ? '| <i class="ri-id-card-line"></i> ' + prov.id3 : ''}
                                    ${prov.telef ? '| <i class="ri-phone-line"></i> ' + prov.telef : ''}
                                </small>
                            </div>
                            <i class="ri-arrow-right-s-line fs-4 text-primary"></i>
                        </a>
                    `;
                });

                $resultsContainer.html(html).show();

                $resultsContainer.find('.proveedor-item').click(function() {
                    const codprov = $(this).data('codprov');
                    const descrip = $(this).data('descrip');
                    seleccionarProveedor(codprov, descrip);
                });
            }

            $searchInput.on('focus', function() {
                cargarTodosProveedores();
            });

            $searchInput.on('keyup', function() {
                cargarTodosProveedores();
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.search-box').length) {
                    $resultsContainer.hide();
                }
            });

            $resultsContainer.on('click', function(e) {
                e.stopPropagation();
            });

            if ($searchInput.val()) {
                cargarTodosProveedores();
            }

            $searchInput.select();

            // Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // ============================================
        // FUNCIONES DE DETALLE DE COMPRA
        // ============================================
        $(document).on('click', '.ver-compra', function() {
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

            let totalCalculado = 0;
            let totalUnidades = 0;

            items.forEach(function(item) {
                const itemTotal = item.cantidad * item.preciod;
                totalCalculado += itemTotal;
                totalUnidades += item.cantidad;
            });

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
                                                            </tfoot>
                        </table>
                    </div>
                </div>
            `;

            $('#compraModalBody').html(html);
        }


        /**
         * Cargar cuentas por pagar del proveedor
         */
        function cargarCuentasPorPagar(codprov, soloResumen = false, solovencidos = false, fechavence = '') {
            const $content = $('#cxp-content');
            const $loading = $('#cxp-loading');

            $loading.removeClass('d-none');
            $content.addClass('d-none').html('');

            $.ajax({
                url: `/proveedores/${codprov}/cuentas-por-pagar`,
                type: 'GET',
                data: {
                    soloResumen  : soloResumen  ? 1 : 0,
                    solovencidos : solovencidos ? 1 : 0,
                    fechavence   : fechavence
                },
                success: function(response) {
                    $loading.addClass('d-none');
                    $content.removeClass('d-none');

                    if (response.success) {
                        if (response.soloResumen) {
                            renderResumenCuentas(response.data);
                        } else {
                            renderDetalleCuentas(response.data, response.total_monto, response.total_abonado, response.total_deuda);
                        }
                    } else {
                        $content.html(`
                            <div class="alert alert-danger">
                                <i class="ri-error-warning-line"></i> Error al cargar los datos
                            </div>
                        `);
                    }
                },
                error: function(xhr) {
                    $loading.addClass('d-none');
                    $content.removeClass('d-none');
                    let errorMsg = 'Error al cargar las cuentas por pagar';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $content.html(`
                        <div class="alert alert-danger">
                            <i class="ri-error-warning-line"></i> ${errorMsg}
                        </div>
                    `);
                }
            });
        }

        /**
         * Renderizar resumen de cuentas por pagar
         */
        function renderResumenCuentas(data) {
            if (!data || !data.codprov) {
                $('#cxp-content').html(`
                    <div class="text-center py-4">
                        <i class="ri-inbox-line fs-1 text-muted"></i>
                        <p class="text-muted mt-2">No hay cuentas por pagar para este proveedor</p>
                    </div>
                `);
                return;
            }

            const deuda = parseFloat(data.deuda) || 0;
            const monto = parseFloat(data.monto) || 0;
            const abonado = parseFloat(data.abonado) || 0;
            const porcentajePagado = monto > 0 ? (abonado / monto * 100) : 0;
            const porcentajeDeuda = monto > 0 ? (deuda / monto * 100) : 0;

            const html = `
                <div class="row">
                    <div class="col-md-8 mx-auto">
                        <div class="card shadow-sm border-0 cxp-card">
                            <div class="card-header bg-primary text-white text-center">
                                <h5 class="mb-0">Resumen de Cuentas por Pagar</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <h4>${escapeHtml(data.empresa) || escapeHtml(data.codprov)}</h4>
                                    <small class="text-muted">Código: ${escapeHtml(data.codprov)}</small>
                                </div>

                                <div class="row text-center mb-4">
                                    <div class="col-4">
                                        <div class="border-end">
                                            <small class="text-muted d-block">Total Facturado</small>
                                            <h4 class="text-primary mb-0">$${formatNumber(monto)}</h4>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border-end">
                                            <small class="text-muted d-block">Total Abonado</small>
                                            <h4 class="text-success mb-0">$${formatNumber(abonado)}</h4>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Saldo Pendiente</small>
                                        <h4 class="${deuda > 0 ? 'text-danger' : 'text-success'} mb-0">$${formatNumber(deuda)}</h4>
                                    </div>
                                </div>

                                <!-- Barra de progreso -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span>Pagado: ${porcentajePagado.toFixed(1)}%</span>
                                        <span>Pendiente: ${porcentajeDeuda.toFixed(1)}%</span>
                                    </div>
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar bg-success progress-bar-custom" role="progressbar"
                                             style="width: ${porcentajePagado}%"
                                             aria-valuenow="${porcentajePagado}" aria-valuemin="0" aria-valuemax="100">
                                            $${formatNumber(abonado)}
                                        </div>
                                        <div class="progress-bar bg-danger progress-bar-custom" role="progressbar"
                                             style="width: ${porcentajeDeuda}%"
                                             aria-valuenow="${porcentajeDeuda}" aria-valuemin="0" aria-valuemax="100">
                                            $${formatNumber(deuda)}
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-3 mb-0">
                                    <i class="ri-information-line"></i>
                                    <small>Este resumen incluye todas las cuentas por pagar activas del proveedor.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('#cxp-content').html(html);
        }

        /**
         * Renderizar detalle de cuentas por pagar
         */
        function renderDetalleCuentas(data, totalMonto, totalAbonado, totalDeuda) {
            if (!data || data.length === 0) {
                $('#cxp-content').html(`
                    <div class="text-center py-4">
                        <i class="ri-inbox-line fs-1 text-muted"></i>
                        <p class="text-muted mt-2">No hay cuentas por pagar para este proveedor</p>
                    </div>
                `);
                return;
            }

            // Convertir fechas para comparación
            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0);

            let rows = '';
            data.forEach(function(item, index) {
                // Verificar si está vencido
                let isOverdue = false;
                let diasVencido = 0;
                if (item.fechav) {
                    const partes = item.fechav.split('/');
                    if (partes.length === 3) {
                        const fechaVence = new Date(partes[2], partes[1] - 1, partes[0]);
                        isOverdue = fechaVence < hoy;
                        if (isOverdue) {
                            const diffTime = hoy - fechaVence;
                            diasVencido = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                        }
                    }
                }

                const deuda   = parseFloat(item.deuda) || 0;
                const monto   = parseFloat(item.monto) || 0;
                const abonado = parseFloat(item.abonado) || 0;

                rows += `
                    <tr class="${isOverdue ? 'table-danger' : 'tablesss'}">
                        <td class="text-center">${item.fecha2 || '-'}</td>
                        <td class="text-center">
                            ${item.fechav2 || '-'}
                            ${isOverdue ? `<br><small class="text-danger">Vencido hace ${diasVencido} día(s)</small>` : ''}
                        </td>
                        <td class="text-wrap" style="max-width: 250px;">
                            <strong>${escapeHtml(item.concepto || '-')}</strong>
                            ${item.numerod ? `<br><small class="text-muted">Doc: ${escapeHtml(item.numerod)}</small>` : ''}
                        </td>
                        <td class="text-end">$${formatNumber(monto)}</td>
                        <td class="text-end">$${formatNumber(abonado)}</td>
                        <td class="text-end fw-bold ${deuda > 0 ? 'text-danger' : 'text-success'}">
                            $${formatNumber(deuda)}
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary ver-detalle-cxp"
                                    data-id="${item.id}"
                                    data-numerod="${escapeHtml(item.numerod || '')}"
                                    data-concepto="${escapeHtml(item.concepto || '')}"
                                    data-monto="${monto}"
                                    data-abonado="${abonado}"
                                    data-deuda="${deuda}"
                                    data-fecha2="${item.fecha2 || ''}"
                                    data-fechav="${item.fechav2 || ''}">
                                <i class="ri-eye-line"></i> Ver
                            </button>
                        </td>
                    </tr>
                `;
            });

            const html = `
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="alert alert-info d-flex justify-content-between align-items-center flex-wrap">
                            <span>
                                <i class="ri-information-line"></i>
                                <strong>Resumen:</strong>
                                Total Facturado: <strong class="text-primary">$${formatNumber(totalMonto)}</strong> |
                                Total Abonado: <strong class="text-success">$${formatNumber(totalAbonado)}</strong> |
                                Saldo Pendiente: <strong class="text-danger">$${formatNumber(totalDeuda)}</strong>
                            </span>
                            <span class="mt-2 mt-sm-0">
                                <small class="text-muted">
                                    <i class="ri-file-list-line"></i> ${data.length} documento(s)
                                </small>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="10%">Fecha Emisión</th>
                                <th width="12%">Fecha Vence</th>
                                <th width="35%">Concepto / Documento</th>
                                <th width="12%" class="text-end">Monto</th>
                                <th width="12%" class="text-end">Abonado</th>
                                <th width="12%" class="text-end">Saldo</th>
                                <th width="7%">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${rows}
                        </tbody>
                        <tfoot class="table-secondary">
                            <tr class="fw-bold">
                                <td colspan="3" class="text-end">TOTALES:</td>
                                <td class="text-end">$${formatNumber(totalMonto)}</td>
                                <td class="text-end">$${formatNumber(totalAbonado)}</td>
                                <td class="text-end text-danger">$${formatNumber(totalDeuda)}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            `;

            $('#cxp-content').html(html);

            // Eventos para ver detalle
            $(document).off('click', '.ver-detalle-cxp').on('click', '.ver-detalle-cxp', function() {
                const id       = $(this).data('id');
                const numerod  = $(this).data('numerod');
                const concepto = $(this).data('concepto');
                const monto    = $(this).data('monto');
                const abonado  = $(this).data('abonado');
                const deuda    = $(this).data('deuda');
                const fecha2   = $(this).data('fecha2');
                const fechav   = $(this).data('fechav');

                // Calcular porcentaje pagado
                const porcentajePagado = monto > 0 ? (abonado / monto * 100) : 0;
                const porcentajeDeuda  = monto > 0 ? (deuda / monto * 100) : 0;

                Swal.fire({
                    title: 'Detalle de Cuenta por Pagar',
                    html: `
                        <div class="text-start">
                            <div class="mb-3">
                                <p><strong><i class="ri-file-copy-line"></i> Documento:</strong> ${numerod || 'N/A'}</p>
                                <p><strong><i class="ri-calendar-line"></i> Fecha Emisión:</strong> ${fecha2 || 'N/A'}</p>
                                <p><strong><i class="ri-calendar-event-line"></i> Fecha Vencimiento:</strong> ${fechav || 'N/A'}</p>
                                <p><strong><i class="ri-survey-line"></i> Concepto:</strong> ${concepto}</p>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-center p-2 border rounded">
                                        <small class="text-muted d-block">Monto</small>
                                        <h5 class="text-primary mb-0">$${formatNumber(monto)}</h5>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-2 border rounded">
                                        <small class="text-muted d-block">Abonado</small>
                                        <h5 class="text-success mb-0">$${formatNumber(abonado)}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="text-center p-2 border rounded bg-light">
                                    <small class="text-muted d-block">Saldo Pendiente</small>
                                    <h4 class="text-danger mb-0">$${formatNumber(deuda)}</h4>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                         style="width: ${porcentajePagado}%"
                                         aria-valuenow="${porcentajePagado}" aria-valuemin="0" aria-valuemax="100">
                                        ${porcentajePagado.toFixed(1)}%
                                    </div>
                                    <div class="progress-bar bg-danger" role="progressbar"
                                         style="width: ${porcentajeDeuda}%"
                                         aria-valuenow="${porcentajeDeuda}" aria-valuemin="0" aria-valuemax="100">
                                        ${porcentajeDeuda.toFixed(1)}%
                                    </div>
                                </div>
                            </div>
                        </div>
                    `,
                    icon: 'info',
                    confirmButtonText: 'Cerrar',
                    width: '500px'
                });
            });
        }

        /**
         * Formatear número a moneda
         */
        function formatNumber(value) {
            if (value === undefined || value === null || isNaN(value)) return '0,00';
            return parseFloat(value).toLocaleString('es-VE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        /**
         * Escapar HTML para prevenir XSS
         */
        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        /**
         * Inicializar datepicker
         */
        function initDatepicker() {
            if (typeof $.fn.datepicker !== 'undefined') {
                $('.datepicker').datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    todayHighlight: true,
                    language: 'es'
                });
            }
        }

        /**
         * Inicializar eventos del Tab 6
         */
        function initCuentasPorPagar() {
            const codprov = '{{ $proveedor->codprov ?? "" }}';

            // Variables para filtros
            let currentSoloResumen = false;
            let currentSolovencidos = false;
            let currentFechavence = '';

            // Botón filtrar
            $('#btnFiltrarCxp').off('click').on('click', function() {
                currentSoloResumen = $('#soloResumenSwitch').is(':checked');
                currentSolovencidos = $('#solovencidosSwitch').is(':checked');
                currentFechavence = $('#fechavenceFilter').val();

                if (codprov) {
                    cargarCuentasPorPagar(codprov, currentSoloResumen, currentSolovencidos, currentFechavence);
                }
            });

            // Botón actualizar/limpiar
            $('#btnActualizarCxp').off('click').on('click', function() {
                $('#soloResumenSwitch').prop('checked', false);
                $('#solovencidosSwitch').prop('checked', false);
                $('#fechavenceFilter').val('');
                currentSoloResumen = false;
                currentSolovencidos = false;
                currentFechavence = '';

                if (codprov) {
                    cargarCuentasPorPagar(codprov, false, false, '');
                }
            });

            // Cargar datos si estamos en el tab6
            if (codprov && '{{ $tab }}' === 'tab6') {
                cargarCuentasPorPagar(codprov, false, false, '');
            }
        }

        /**
         * Observar cambios de tab para cargar cuentas por pagar
         */
        function observeTabChanges() {
            // Detectar cuando se hace clic en el tab de cuentas por pagar
            $(document).on('click', 'a[href*="tab=tab6"]', function() {
                const codprov = '{{ $proveedor->codprov ?? "" }}';
                if (codprov) {
                    setTimeout(function() {
                        const soloResumen = $('#soloResumenSwitch').is(':checked');
                        const solovencidos = $('#solovencidosSwitch').is(':checked');
                        const fechavence = $('#fechavenceFilter').val();
                        cargarCuentasPorPagar(codprov, soloResumen, solovencidos, fechavence);
                    }, 100);
                }
            });
        }

        // ============================================
        // RESUMEN GENERAL DE CUENTAS POR PAGAR
        // ============================================

        /**
         * Cargar resumen general de cuentas por pagar
         */
        function cargarResumenGeneralCuentas(solovencidos = false, fechavence = '') {
            const $content = $('#resumen-general-content');
            const $loading = $('#resumen-general-loading');

            $loading.removeClass('d-none');
            $content.addClass('d-none').html('');

            $.ajax({
                url: '{{ route("proveedores.cuentas-por-pagar.resumen-general") }}',
                type: 'GET',
                data: {
                    solovencidos : solovencidos ? 1 : 0,
                    fechavence   : fechavence
                },
                success: function(response) {
                    $loading.addClass('d-none');
                    $content.removeClass('d-none');

                    if (response.success) {
                        renderResumenGeneral(response.data, response.totales);
                    } else {
                        $content.html(`
                    <div class="alert alert-danger">
                        <i class="ri-error-warning-line"></i> Error al cargar los datos
                    </div>
                `);
                    }
                },
                error: function(xhr) {
                    $loading.addClass('d-none');
                    $content.removeClass('d-none');
                    $content.html(`
                <div class="alert alert-danger">
                    <i class="ri-error-warning-line"></i> Error al cargar el resumen de cuentas por pagar
                </div>
            `);
                }
            });
        }

        /**
         * Renderizar resumen general de cuentas por pagar
         */
        function renderResumenGeneral(data, totales) {
            if (!data || data.length === 0) {
                $('#resumen-general-content').html(`
            <div class="text-center py-4">
                <i class="ri-inbox-line fs-1 text-muted"></i>
                <p class="text-muted mt-2">No hay cuentas por pagar registradas</p>
            </div>
        `);
                $('#totalProveedoresCxp').text('0');
                return;
            }

            $('#totalProveedoresCxp').text(data.length);

            let rows = '';
            data.forEach(function(item, index) {
                const deuda = parseFloat(item.deuda) || 0;
                const monto = parseFloat(item.monto) || 0;
                const abonado = parseFloat(item.abonado) || 0;
                const porcentajePagado = monto > 0 ? (abonado / monto * 100) : 0;
//onclick="seleccionarProveedorCxp('${item.codprov}', '${escapeHtml(item.empresa)}')"
                rows += `
            <tr style="cursor: pointer;" >
                <td class="text-center">${index + 1}</td>
                <td>
                    <strong>${escapeHtml(item.empresa)}</strong>
                    <br><small class="text-muted">Código: ${escapeHtml(item.codprov)}</small>
                </td>
                <td class="text-end">$${formatNumber(monto)}</td>
                <td class="text-end">$${formatNumber(abonado)}</td>
                <td class="text-end fw-bold ${deuda > 0 ? 'text-danger' : 'text-success'}">
                    $${formatNumber(deuda)}
                </td>
                <td class="text-center" style="min-width: 100px;">
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar"
                             style="width: ${porcentajePagado}%"
                             aria-valuenow="${porcentajePagado}" aria-valuemin="0" aria-valuemax="100"></div>
                        <div class="progress-bar bg-danger" role="progressbar"
                             style="width: ${100 - porcentajePagado}%"></div>
                    </div>
                    <small>${porcentajePagado.toFixed(0)}% pagado</small>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-primary ver-proveedor"
                            data-codprov="${item.codprov}"
                            data-descrip="${escapeHtml(item.empresa)}">
                        <i class="ri-eye-line"></i>
                    </button>
                </td>
            </tr>
        `;
            });

            const totalDeuda = parseFloat(totales.deuda) || 0;
            const totalMonto = parseFloat(totales.monto) || 0;
            const totalAbonado = parseFloat(totales.abonado) || 0;
            const porcentajeGeneral = totalMonto > 0 ? (totalAbonado / totalMonto * 100) : 0;

            const html = `
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="alert alert-info d-flex justify-content-between align-items-center flex-wrap">
                    <span>
                        <i class="ri-information-line"></i>
                        <strong>Resumen General:</strong>
                        Total Facturado: <strong class="text-primary">$${formatNumber(totalMonto)}</strong> |
                        Total Abonado: <strong class="text-success">$${formatNumber(totalAbonado)}</strong> |
                        Saldo Pendiente Total: <strong class="text-danger">$${formatNumber(totalDeuda)}</strong>
                    </span>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <div class="d-flex justify-content-between small mb-1">
                <span>Porcentaje General Pagado: ${porcentajeGeneral.toFixed(1)}%</span>
                <span>Porcentaje General Pendiente: ${(100 - porcentajeGeneral).toFixed(1)}%</span>
            </div>
            <div class="progress" style="height: 20px;">
                <div class="progress-bar bg-success" role="progressbar"
                     style="width: ${porcentajeGeneral}%"
                     aria-valuenow="${porcentajeGeneral}" aria-valuemin="0" aria-valuemax="100">
                    $${formatNumber(totalAbonado)}
                </div>
                <div class="progress-bar bg-danger" role="progressbar"
                     style="width: ${100 - porcentajeGeneral}%"
                     aria-valuenow="${100 - porcentajeGeneral}" aria-valuemin="0" aria-valuemax="100">
                    $${formatNumber(totalDeuda)}
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover table-bordered">
                <thead class="table-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="35%">Proveedor</th>
                        <th width="15%" class="text-end">Total Facturado</th>
                        <th width="15%" class="text-end">Total Abonado</th>
                        <th width="15%" class="text-end">Saldo Pendiente</th>
                        <th width="10%" class="text-center">Progreso</th>
                        <th width="5%">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    ${rows}
                </tbody>
                <tfoot class="table-secondary">
                    <tr class="fw-bold">
                        <td colspan="2" class="text-end">TOTALES:</td>
                        <td class="text-end">$${formatNumber(totalMonto)}</td>
                        <td class="text-end">$${formatNumber(totalAbonado)}</td>
                        <td class="text-end text-danger">$${formatNumber(totalDeuda)}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;

            $('#resumen-general-content').html(html);

            // Evento para ver detalle del proveedor
            $(document).off('click', '.ver-proveedor').on('click', '.ver-proveedor', function(e) {
                e.stopPropagation();
                const codprov = $(this).data('codprov');
                const descrip = $(this).data('descrip');
                seleccionarProveedorCxp(codprov, descrip);
            });
        }

        /**
         * Función global para seleccionar proveedor desde el resumen
         */
        window.seleccionarProveedorCxp = function(codprov, descrip) {
            // Actualizar el formulario oculto y enviar
            $('#codprov').val(codprov);
            $('#tab').val('tab');
            $('#busqueda').val(descrip);
            $('#proveedorForm').submit();
        }

        /**
         * Inicializar eventos del resumen general
         */
        function initResumenGeneral() {
            // Variables para filtros
            let currentSolovencidos = false;
            let currentFechavence = '';

            // Cargar datos iniciales
            cargarResumenGeneralCuentas(false, '');

            // Botón filtrar
            $('#btnFiltrarResumenGeneral').off('click').on('click', function() {
                currentSolovencidos = $('#resumenSoloVencidosSwitch').is(':checked');
                currentFechavence = $('#resumenFechavenceFilter').val();
                cargarResumenGeneralCuentas(currentSolovencidos, currentFechavence);
            });

            // Botón actualizar/limpiar
            $('#btnActualizarResumenGeneral').off('click').on('click', function() {
                $('#resumenSoloVencidosSwitch').prop('checked', false);
                $('#resumenFechavenceFilter').val('');
                cargarResumenGeneralCuentas(false, '');
            });
        }


        // Mostrar/ocultar resumen general según si hay proveedor seleccionado
        function toggleResumenGeneral() {
            const hasProveedor = '{{ isset($proveedor) && $proveedor->codprov ? 1 : 0 }}';
            if (hasProveedor == 1) {
                $('#resumenGeneralCxpCard').addClass('hidden');
            } else {
                $('#resumenGeneralCxpCard').removeClass('hidden');
                // Recargar datos si está visible
                cargarResumenGeneralCuentas(false, '');
            }
        }


        $(document).ready(function() {
            initDatepicker();
            initCuentasPorPagar();
            observeTabChanges();
            initResumenGeneral();
            toggleResumenGeneral();
        });


    </script>
@endsection
