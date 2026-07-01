@extends('layouts.master')
@section('title', 'Análisis de Rotación y Transferencias')
@section('css')
    <style>
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            color: white;
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-number {
            font-size: 32px;
            font-weight: bold;
        }
        .stats-label {
            font-size: 12px;
            opacity: 0.9;
        }
        .card-sugerencia {
            border-left: 4px solid;
            transition: all 0.2s;
            margin-bottom: 15px;
        }
        .card-sugerencia:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .badge-rotacion-alta {
            background-color: #dc3545;
        }
        .badge-rotacion-media {
            background-color: #fd7e14;
        }
        .badge-rotacion-baja {
            background-color: #ffc107;
            color: #000;
        }
        .tabla-comparativa {
            font-size: 0.85rem;
        }
        .tabla-comparativa th {
            background-color: #f8f9fa;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
        }
        .comparativa-cell {
            transition: all 0.2s;
        }
        .comparativa-cell:hover {
            background-color: rgba(13,110,253,0.1);
            cursor: pointer;
        }
        .sugerencia-icon {
            font-size: 24px;
        }
        .transfer-btn {
            white-space: nowrap;
        }
        .insight-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .rotacion-alta {
            border-left-color: #dc3545;
        }
        .rotacion-media {
            border-left-color: #fd7e14;
        }
        .rotacion-baja {
            border-left-color: #ffc107;
        }
        .sin-stock {
            background-color: #fff3cd;
        }
        .stock-bajo {
            background-color: #ffe6e6;
        }
        .stock-ok {
            background-color: #e6ffe6;
        }
    </style>
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0"> Análisis de Rotación y Transferencias</h4>
                    <p class="text-white-50 mb-0 small">Identifica qué productos se venden más en cada sucursal y sugiere transferencias para optimizar el inventario</p>
                </div>
                <div class="card-body  d-flex justify-content-between">

                    <form method="POST" action="{{ route('sugerir-transferencias') }}" class="row g-3 align-items-end" style="min-width: 80%;">
                        @csrf
                        <div class="col-md-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-calendar-range me-1"></i>Período de Análisis
                            </label>
                            <input type="date" name="fecha_inicio" class="form-control"
                                   value="{{ $fechaInicio ?? now()->subDays(30)->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-calendar-range me-1"></i>Fecha Fin
                            </label>
                            <input type="date" name="fecha_fin" class="form-control"
                                   value="{{ $fechaFin ?? now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">
                                <i class="bi bi-tags me-1"></i>Filtrar por Categoría
                            </label>
                            <select onchange="$('.error-msg').hide(); $('.card-datosprod').fadeIn(); verUltimoProd(this.value)"
                                    class="form-select"
                                    data-choices
                                    required
                                    name="codinst">
                                <option value=""> Seleccionar </option>
                                @foreach($instancias as $instancia)
                                    <option {{(isset($codinst) and $codinst>0 and $instancia->codinst == $codinst)? 'selected': '' }} style="margin-left: {{($instancia->nivel-1) * 14}}px !important;"
                                            value="{{$instancia->codinst}}">
                                        {!! $instancia->descrip !!}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-1"></i>Analizar Rotación
                            </button>
                        </div>
                    </form>

                    <div class="text-end">
                        <div class="stats-number">{{ count($sugerencias) }}</div>
                        <div class="stats-label">Sugerencias generadas</div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @if(empty($codinst))
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-info text-center p-5">
                    <i class="bi bi-folder2-open fs-1 d-block mb-3"></i>
                    <h4>Selecciona una categoría para analizar</h4>
                    <p class="mb-0">Elige una categoría del menú desplegable "Filtrar por Categoría" para ver las sugerencias de transferencia basadas en la rotación de productos.</p>
                    <hr>
                    <small class="text-muted">El análisis solo incluirá productos de la categoría seleccionada y sus subcategorías.</small>
                </div>
            </div>
        </div>
    @elseif(isset($sugerencias) && count($sugerencias) > 0)

        @if(isset($sugerencias) && count($sugerencias) > 0)
            <div class="alert alert-secondary mb-3">
                <i class="bi bi-tag me-2"></i>
                <strong>Categoría analizada:</strong>
                {{ $instancias->firstWhere('codinst', $codinst)->descrip ?? 'Seleccionada' }}
                <span class="badge bg-info ms-2 d-none">{{ count($productosAnalizados ?? []) }} productos analizados</span>
            </div>

            <!-- Sugerencias de transferencia con análisis detallado -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="bi bi-arrow-left-right me-2"></i>
                                Sugerencias de Transferencia por Análisis de Rotación
                            </h5>
                            <small class="text-muted">
                                Basado en ventas del período {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
                                | Stock de seguridad: 7 días de ventas promedio
                            </small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover tabla-comparativa mb-0">
                                    <thead>
                                    <tr class="table-light">
                                        <th width="5%">#</th>
                                        <th width="15%">Producto</th>
                                        <th width="20%">Análisis de Demanda</th>
                                        <th width="20%">Situación Actual</th>
                                        <th width="20%">Sugerencia de Transferencia</th>
                                        <th width="20%">Acción</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($sugerencias as $index => $sug)
                                        @php
                                            $rotacion = $sug['ventas_periodo_destino'] / 30; // Ventas diarias promedio
                                            $claseRotacion = $rotacion > 5 ? 'rotacion-alta' : ($rotacion > 2 ? 'rotacion-media' : 'rotacion-baja');
                                            $badgeRotacion = $rotacion > 5 ? 'badge-rotacion-alta' : ($rotacion > 2 ? 'badge-rotacion-media' : 'badge-rotacion-baja');
                                            $textoRotacion = $rotacion > 5 ? 'Alta rotación' : ($rotacion > 2 ? 'Media rotación' : 'Baja rotación');
                                        @endphp
                                        <tr class="card-sugerencia {{ $claseRotacion }}">
                                            <td class="align-middle">{{ $index + 1 }}</td>
                                            <td class="align-middle">
                                                <div class="fw-bold">{{ $sug['producto_cod'] }}</div>
                                                <small class="text-muted">{{ Str::limit($sug['producto_nombre'], 40) }}</small>
                                            </td>
                                            <td class="align-middle">
                                                <div class="insight-box p-2 mb-0">
                                                    <div class="d-flex justify-content-between">
                                                        <span class="text-muted">Ventas período:</span>
                                                        <span class="fw-bold">{{ number_format($sug['ventas_periodo_destino'], 0, ',', '.') }} uds</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mt-1">
                                                        <span class="text-muted">Promedio diario:</span>
                                                        <span class="fw-bold">{{ number_format($sug['ventas_periodo_destino'] / 30, 0, ',', '.') }} uds/día</span>
                                                    </div>
                                                    <div class="mt-2">
                                                        <span class="badge {{ $badgeRotacion }}">{{ $textoRotacion }}</span>
                                                        <small class="text-muted ms-2">(requiere stock constante)</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <div class="insight-box p-2 mb-0">
                                                    <div class="d-flex justify-content-between">
                                                        <span class="text-muted">Stock actual:</span>
                                                        <span class="fw-bold {{ $sug['stock_actual_destino'] < $sug['stock_seguridad_destino'] ? 'text-danger' : 'text-success' }}">
                                                            {{ number_format($sug['stock_actual_destino'], 0, ',', '.') }} uds
                                                        </span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mt-1">
                                                        <span class="text-muted">Stock mínimo necesario:</span>
                                                        <span class="fw-bold">{{ number_format($sug['stock_seguridad_destino'], 0, ',', '.') }} uds</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mt-1">
                                                        <span class="text-muted">Déficit:</span>
                                                        <span class="fw-bold text-danger">{{ number_format($sug['deficit'], 0, ',', '.') }} uds</span>
                                                    </div>
                                                    <div class="mt-2">
                                                        <i class="bi bi-info-circle text-info"></i>
                                                        <small class="text-muted">Stock insuficiente para cubrir 7 días de ventas</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <div class="insight-box p-2 mb-0 bg-light">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="text-center">
                                                            <i class="bi bi-building text-success fs-4"></i>
                                                            <div class="small">Desde</div>
                                                            <div class="fw-bold">{{ $sug['sucursal_origen_nombre'] }}</div>
                                                            <div class="small text-muted">
                                                                Stock disponible: {{ number_format($sug['excedente_disponible'], 0, ',', '.') }} uds
                                                            </div>
                                                        </div>
                                                        <div class="text-center">
                                                            <i class="bi bi-arrow-right-circle-fill fs-3 text-primary"></i>
                                                            <div class="fw-bold fs-5">{{ number_format($sug['cantidad_sugerida'], 0, ',', '.') }} uds</div>
                                                        </div>
                                                        <div class="text-center">
                                                            <i class="bi bi-building text-danger fs-4"></i>
                                                            <div class="small">Hacia</div>
                                                            <div class="fw-bold">{{ $sug['sucursal_destino_nombre'] }}</div>
                                                            <div class="small text-success">
                                                                Cubre {{ number_format(($sug['cantidad_sugerida'] / $sug['deficit']) * 100, 0) }}% del déficit
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <button class="btn btn-sm btn-primary w-100 transferir-btn mb-2"
                                                        data-producto="{{ $sug['producto_cod'] }}"
                                                        data-producto-nombre="{{ $sug['producto_nombre'] }}"
                                                        data-origen="{{ $sug['sucursal_origen_id'] }}"
                                                        data-origen-nombre="{{ $sug['sucursal_origen_nombre'] }}"
                                                        data-destino="{{ $sug['sucursal_destino_id'] }}"
                                                        data-destino-nombre="{{ $sug['sucursal_destino_nombre'] }}"
                                                        data-cantidad="{{ $sug['cantidad_sugerida'] }}">
                                                    <i class="bi bi-truck me-1"></i> Transferir
                                                </button>
                                                <button class="btn btn-sm btn-outline-info w-100 btn-detalle-producto"
                                                        data-codprod="{{ $sug['producto_cod'] }}"
                                                        data-producto="{{ $sug['producto_nombre'] }}">
                                                    <i class="bi bi-eye me-1"></i> Ver existencias
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen por sucursal destino (qué necesita cada sucursal) -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Sucursales que NECESITAN stock
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @php
                                    $necesidadesPorSucursal = [];
                                    foreach($sugerencias as $sug) {
                                        $key = $sug['sucursal_destino_id'];
                                        if(!isset($necesidadesPorSucursal[$key])) {
                                            $necesidadesPorSucursal[$key] = [
                                                'nombre' => $sug['sucursal_destino_nombre'],
                                                'productos' => [],
                                                'total_unidades' => 0
                                            ];
                                        }
                                        $necesidadesPorSucursal[$key]['productos'][] = $sug;
                                        $necesidadesPorSucursal[$key]['total_unidades'] += $sug['cantidad_sugerida'];
                                    }
                                @endphp
                                @foreach($necesidadesPorSucursal as $suc)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-building text-danger me-2"></i>
                                                <strong>{{ $suc['nombre'] }}</strong>
                                            </div>
                                            <div>
                                                <span class="badge bg-danger">{{ $suc['total_unidades'] }} unidades</span>
                                                <span class="badge bg-secondary ms-1">{{ count($suc['productos']) }} productos</span>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            @foreach(array_slice($suc['productos'], 0, 3) as $prod)
                                                <div class="d-flex justify-content-between small mt-1">
                                                    <span>{{ $prod['producto_cod'] }} - {{ Str::limit($prod['producto_nombre'], 25) }}</span>
                                                    <span class="text-danger">-{{ number_format($prod['deficit'], 0, ',', '.') }} uds</span>
                                                </div>
                                            @endforeach
                                            @if(count($suc['productos']) > 3)
                                                <div class="small text-muted mt-1">+{{ count($suc['productos']) - 3 }} productos más</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="bi bi-box-seam me-2"></i>
                                Sucursales con EXCEDENTE de stock
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @php
                                    $excedentesPorSucursal = [];
                                    foreach($sugerencias as $sug) {
                                        $key = $sug['sucursal_origen_id'];
                                        if(!isset($excedentesPorSucursal[$key])) {
                                            $excedentesPorSucursal[$key] = [
                                                'nombre' => $sug['sucursal_origen_nombre'],
                                                'productos' => [],
                                                'total_unidades' => 0
                                            ];
                                        }
                                        $excedentesPorSucursal[$key]['productos'][] = $sug;
                                        $excedentesPorSucursal[$key]['total_unidades'] += $sug['cantidad_sugerida'];
                                    }
                                @endphp
                                @foreach($excedentesPorSucursal as $suc)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-building text-success me-2"></i>
                                                <strong>{{ $suc['nombre'] }}</strong>
                                            </div>
                                            <div>
                                                <span class="badge bg-success">{{ $suc['total_unidades'] }} unidades</span>
                                                <span class="badge bg-secondary ms-1">{{ count($suc['productos']) }} productos</span>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            @foreach(array_slice($suc['productos'], 0, 3) as $prod)
                                                <div class="d-flex justify-content-between small mt-1">
                                                    <span>{{ $prod['producto_cod'] }} - {{ Str::limit($prod['producto_nombre'], 25) }}</span>
                                                    <span class="text-success">+{{ number_format($prod['excedente_disponible'], 0, ',', '.') }} uds</span>
                                                </div>
                                            @endforeach
                                            @if(count($suc['productos']) > 3)
                                                <div class="small text-muted mt-1">+{{ count($suc['productos']) - 3 }} productos más</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @elseif(isset($sugerencias))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-success text-center p-5">
                        <i class="bi bi-check-circle-fill fs-1"></i>
                        <h4 class="mt-3">¡Inventario Balanceado!</h4>
                        <p>Todas las sucursales tienen stock suficiente para cubrir las ventas del período analizado.</p>
                        <p class="text-muted">No se requieren transferencias en este momento.</p>
                    </div>
                </div>
            </div>
        @endif
    @elseif(isset($sugerencias))
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-success text-center p-5">
                    <i class="bi bi-check-circle-fill fs-1"></i>
                    <h4 class="mt-3">¡Inventario Balanceado!</h4>
                    <p>Para la categoría seleccionada, todas las sucursales tienen stock suficiente para cubrir las ventas del período analizado.</p>
                    <p class="text-muted">No se requieren transferencias en este momento.</p>
                </div>
            </div>
        </div>
    @endif


    <!-- Modal para detalle de producto -->
    <div class="modal fade" id="detalleProductoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Detalle de Existencias</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detalleProductoBody">
                    <div class="text-center p-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        // Función para ver detalle de producto
        $(document).on('click', '.btn-detalle-producto', function() {
            let codprod = $(this).data('codprod');
            let nombreProducto = $(this).data('producto');
            let modalBody = $('#detalleProductoBody');

            modalBody.html('<div class="text-center p-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>');
            $('#detalleProductoModal .modal-title').text(`Existencias de: ${nombreProducto}`);
            $('#detalleProductoModal').modal('show');

            $.ajax({
                type: 'post',
                data: { codprod: codprod },
                url: '/saprod/listprodubiccompany',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success && response.existencias.length > 0) {
                        let html = `
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sucursal/Depósito</th>
                                            <th class="text-end">Existencia</th>
                                            <th class="text-end">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;
                        $.each(response.existencias, function(i, item) {
                            let depositoNombre = item.deposito ? item.deposito.descrip : item.codubic;
                            let estado = '';
                            let claseEstado = '';
                            if (item.existen > 50) {
                                estado = 'Stock alto';
                                claseEstado = 'text-success';
                            } else if (item.existen > 10) {
                                estado = 'Stock medio';
                                claseEstado = 'text-warning';
                            } else {
                                estado = 'Stock bajo';
                                claseEstado = 'text-danger';
                            }
                            html += `
                                <tr>
                                    <td>${depositoNombre}</td>
                                    <td class="text-end fw-bold">${parseFloat(item.existen).toLocaleString('es-VE')}</td>
                                    <td class="text-end ${claseEstado}">${estado}</td>
                                </tr>
                            `;
                        });
                        html += '</tbody></table></div>';
                        modalBody.html(html);
                    } else {
                        modalBody.html('<div class="alert alert-warning text-center mb-0">No se encontraron existencias para este producto.</div>');
                    }
                },
                error: function() {
                    modalBody.html('<div class="alert alert-danger text-center mb-0">Error al cargar los datos.</div>');
                }
            });
        });

        // Función para transferir
        $(document).on('click', '.transferir-btn', function() {
            const sugerencia = {
                producto_cod: $(this).data('producto'),
                producto_nombre: $(this).data('producto-nombre'),
                sucursal_origen_id: $(this).data('origen'),
                sucursal_origen_nombre: $(this).data('origen-nombre'),
                sucursal_destino_id: $(this).data('destino'),
                sucursal_destino_nombre: $(this).data('destino-nombre'),
                cantidad_sugerida: $(this).data('cantidad')
            };

            if (confirm(`¿Agregar a la lista de transferencia?\n\nProducto: ${sugerencia.producto_nombre}\nDesde: ${sugerencia.sucursal_origen_nombre}\nHacia: ${sugerencia.sucursal_destino_nombre}\nCantidad: ${sugerencia.cantidad_sugerida.toLocaleString('es-VE')} unidades`)) {
                $.ajax({
                    type: 'post',
                    url: '{{ route("transferencias.cargar-sugerencia") }}',
                    data: { sugerencia: sugerencia },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        window.location.href = '{{ route("transferencias.index") }}';
                    }
                });
            }
        });
    </script>
@endsection
