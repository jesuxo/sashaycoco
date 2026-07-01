@extends('layouts.master')
@section('title')
    Reporte de Compras
@endsection
@section('css')
    <style>
        .filter-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border: none;
        }

        .filter-card .form-label {
            font-weight: 600;
            color: #344767;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .filter-card .input-group-text {
            background: #0072c5 !important;
            border: none;
            padding: 0;
        }

        .filter-card .input-group-text button {
            background: transparent;
            border: none;
            color: white;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .filter-card .input-group-text button:hover {
            background: rgba(255,255,255,0.1);
            border-radius: 0 10px 10px 0;
        }

        .stats-card {
            background: linear-gradient(135deg, #2e4a99 0%, #448bc9 100%);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            color: white;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.2);
        }

        .stats-card .stat-item {
            text-align: center;
            border-right: 1px solid rgba(255,255,255,0.2);
        }

        .stats-card .stat-item:last-child {
            border-right: none;
        }

        .stats-card .stat-value {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stats-card .stat-label {
            font-size: 12px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .table-custom {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .table-custom thead th {
            background: #f8fafc;
            padding: 15px 12px;
            font-weight: 600;
            color: #344767;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
            border-bottom: 2px solid #e9ecef;
        }

        .table-custom tbody tr {
            background: white;
            border-radius: 10px;
            transition: all 0.3s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }

        .table-custom tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .table-custom tbody td {
            padding: 15px 12px;
            vertical-align: middle;
            border: none;
            border-top: 1px solid #f1f5f9;
        }

        .badge-tipo {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-compra {
            background: #28a745;
            color: white;
        }

        .badge-devolucion {
            background: #dc3545;
            color: white;
        }

        .documento-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .documento-link:hover {
            color: #0072c5;
            text-decoration: underline;
        }

        .proveedor-info {
            font-weight: 600;
            color: #344767;
            margin-bottom: 4px;
        }

        .proveedor-notas {
            font-size: 11px;
            color: #6c757d;
        }

        .monto-positivo {
            color: #28a745;
            font-weight: 600;
        }

        .monto-negativo {
            color: #dc3545;
            font-weight: 600;
        }

        .pagination-custom {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
        }

        .pagination-custom .page-link {
            border: none;
            color: #667eea;
            margin: 0 3px;
            border-radius: 8px;
            padding: 8px 14px;
            font-weight: 500;
        }

        .pagination-custom .page-item.active .page-link {
            background: linear-gradient(135deg, #2e4a99 0%, #448bc9 100%);
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: #f8fafc;
            border-radius: 15px;
        }

        .empty-state i {
            font-size: 60px;
            color: #cbd5e1;
            margin-bottom: 20px;
        }

        .empty-state h5 {
            color: #344767;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #6c757d;
            margin-bottom: 0;
        }
    </style>
@endsection

@section('content')
    <!-- Filtros mejorados -->
    <div class="filter-card">
        <form method="post" name="form1" id="form1" action="/reporte/compra">
            @csrf
            @method('POST')
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-white" style="padding: 10px;">
                            <i class="ri-search-line"></i>
                        </span>
                        <input type="text" class="form-control border-start-0 ps-0"
                               placeholder="Proveedor, N° de compra, notas..." style="padding-left: 10px !important;"
                               name="busqueda" value="{{ $busqueda ?? '' }}">
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Rango de Fechas</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-white" style="padding: 10px;">
                            <i class="ri-calendar-line "></i>
                        </span>
                        <input type="text" class="form-control border-start-0 ps-0"
                               data-provider="flatpickr" data-range-date="true"
                               data-date-format="d/m/Y" name="fechasreport" style="padding-left: 10px !important"
                               readonly="readonly" value="{{ $fechasreport ?? '' }}"
                               placeholder="Seleccionar fechas">
                    </div>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ri-filter-3-line me-1"></i> Consultar
                    </button>
                </div>

                <div class="col-md-2">
                    <a href="/reporte/compra" class="btn btn-outline-secondary w-100">
                        <i class="ri-refresh-line me-1"></i> Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>

    @if(isset($compras) && count($compras) > 0)
        <!-- Estadísticas -->
        @php
            $totalDocumentos = count($compras);
            $totalUnidades = 0;
            $totalMonto = 0;
            $totalCompras = 0;
            $totalDevoluciones = 0;

            foreach($compras as $compra) {
                $cantidad = 0;
                $monto = 0;
                foreach ($compra->items as $item) {
                    $cantidad += $item->cantidad;
                    $monto += $item->preciod * $item->cantidad;
                }

                if($compra->tipocom == 'I') {
                    $totalDevoluciones++;
                    $totalMonto -= $monto;
                } else {
                    $totalCompras++;
                    $totalMonto += $monto;
                }
                $totalUnidades += $cantidad;
            }
        @endphp

        <div class="stats-card">
            <div class="row">
                <div class="col-md-3 stat-item">
                    <div class="stat-value">{{ $totalDocumentos }}</div>
                    <div class="stat-label">Documentos</div>
                </div>
                <div class="col-md-3 stat-item">
                    <div class="stat-value">{{ number_format($totalUnidades, 0) }}</div>
                    <div class="stat-label">Unidades</div>
                </div>
                <div class="col-md-3 stat-item">
                    <div class="stat-value">${{ number_format($totalMonto, 2) }}</div>
                    <div class="stat-label">Monto Total</div>
                </div>
                <div class="col-md-3 stat-item">
                    <div class="stat-value">{{ $totalCompras }} / {{ $totalDevoluciones }}</div>
                    <div class="stat-label">Compras / Dev.</div>
                </div>
            </div>
        </div>

        <!-- Tabla de resultados -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                    <tr>
                        <th>Proveedor</th>
                        <th class="text-center">Fecha</th>
                        <th class="text-center">Documento</th>
                        <th class="text-center">Tipo</th>
                        <th class="text-center">Unidades</th>
                        <th class="text-end">Monto</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($compras as $compra)
                        @php
                            $cantidad = 0;
                            $monto = 0;
                            foreach ($compra->items as $item) {
                                $cantidad += $item->cantidad;
                                $monto += $item->preciod * $item->cantidad;
                            }
                            $tipo = $compra->tipocom == 'H' ? 'Compra' : 'Devolución';
                            $badgeClass = $compra->tipocom == 'H' ? 'badge-compra' : 'badge-devolucion';
                            $montoClass = $compra->tipocom == 'I' ? 'monto-negativo' : 'monto-positivo';
                            $montoMostrar = $compra->tipocom == 'I' ? -$monto : $monto;
                        @endphp
                        <tr>
                            <td>
                                <div class="proveedor-info">
                                    <a href="/proveedores/{{$compra->codprov}}/tab5" target="_blank">
                                        <i class="ri-store-line me-1"></i>
                                        {{$compra->descrip}}
                                    </a>

                                </div>
                                <div class="proveedor-notas">
                                    @if($compra->notas1 || $compra->notas2)
                                        <i class="ri-file-text-line me-1"></i>
                                        {{ trim($compra->notas1 . ' ' . $compra->notas2) }}
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                    <span class="badge bg-light text-dark">
                                        {{ $compra->createdformat }}
                                    </span>
                            </td>
                            <td class="text-center">
                                <a href="/compra/{{$compra->id}}" target="_blank" class="documento-link">
                                    <i class="ri-file-list-line me-1"></i>
                                    {{ $compra->numerod }}
                                </a>
                            </td>
                            <td class="text-center">
                                    <span class="badge-tipo {{ $badgeClass }}">
                                        {{ $tipo }}
                                    </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-600">{{ number_format($cantidad, 0) }}</span>
                            </td>
                            <td class="text-end {{ $montoClass }} fw-600">
                                ${{ number_format($montoMostrar, 2, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-info ver-detalle"
                                        data-id="{{ $compra->id }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#detalleModal">
                                    <i class="ri-eye-line"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación (si aplica) -->
            @if(method_exists($compras, 'links'))
                <div class="pagination-custom">
                    {{ $compras->links() }}
                </div>
            @endif
        </div>
    @else
        <!-- Estado vacío -->
        <div class="empty-state">
            <i class="ri-shopping-cart-line"></i>
            <h5>No hay compras para mostrar</h5>
            <p>Utiliza los filtros para buscar compras por proveedor, fecha o número de documento.</p>
        </div>
    @endif

    <!-- Modal para detalle de compra -->
    <div class="modal fade" id="detalleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">
                        <i class="ri-file-list-line me-2"></i>Detalle de Compra
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detalleModalBody">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-3 text-muted">Cargando información de la compra...</p>
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
        $(document).ready(function() {
            // Configurar CSRF para AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Ver detalle de compra
            $('.ver-detalle').click(function() {
                const compraId = $(this).data('id');
                const $modalBody = $('#detalleModalBody');

                $modalBody.html(`
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-3 text-muted">Cargando información de la compra...</p>
                    </div>
                `);

                $.ajax({
                    url: '{{ route("compras.documento-ajax") }}',
                    type: 'POST',
                    data: { id: compraId },
                    success: function(response) {
                        if (response.success) {
                            mostrarDetalleCompra(response.compra, response.items);
                        } else {
                            $modalBody.html(`
                                <div class="alert alert-danger m-3">
                                    <i class="ri-error-warning-line me-2"></i>
                                    Error al cargar el detalle de la compra
                                </div>
                            `);
                        }
                    },
                    error: function() {
                        $modalBody.html(`
                            <div class="alert alert-danger m-3">
                                <i class="ri-error-warning-line me-2"></i>
                                Error de conexión al cargar el detalle
                            </div>
                        `);
                    }
                });
            });

            function mostrarDetalleCompra(compra, items) {
                const signo = compra.tipocom == 'I' ? -1 : 1;
                const tipoTexto = compra.tipocom == 'H' ? 'Compra' : 'Devolución';
                const tipoColor = compra.tipocom == 'H' ? 'success' : 'danger';

                // Calcular totales
                let totalCalculado = 0;
                let totalUnidades = 0;

                items.forEach(item => {
                    const itemTotal = item.cantidad * item.preciod;
                    totalCalculado += itemTotal;
                    totalUnidades += item.cantidad;
                });

                const totalFinal = totalCalculado * signo;

                let itemsHtml = '';
                items.forEach(item => {
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
                    <div class="p-3">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-2"><strong>Documento:</strong> ${compra.numerod}</p>
                                    <p class="mb-2"><strong>Fecha:</strong> ${new Date(compra.fechae).toLocaleDateString('es-VE')}</p>
                                    <p class="mb-0"><strong>Tipo:</strong>
                                        <span class="badge bg-${tipoColor}">${tipoTexto}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-2"><strong>Proveedor:</strong> ${compra.codprov}</p>
                                    <p class="mb-2"><strong>Sucursal:</strong> ${compra.sucursal ? compra.sucursal.descrip : 'N/A'}</p>
                                    <p class="mb-0"><strong>Total Unidades:</strong> ${totalUnidades}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light p-3 rounded" style="height: 112px">
                                    <p class="mb-0 ${signo < 0 ? 'text-danger' : 'text-success'}">
                                        <strong>Total Documento:</strong> $${Number(totalCalculado).toFixed(2)}
                                    </p>
                                </div>
                            </div>
                        </div>

                        ${compra.descrip ? `
                            <div class="alert alert-info mb-4">
                                <i class="ri-information-line me-2"></i>
                                <strong>Observaciones:</strong> ${compra.descrip}
                            </div>
                        ` : ''}

                        <h6 class="mb-3">Productos</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Código</th>
                                        <th>Producto</th>
                                        <th class="text-center">Cant.</th>
                                        <th class="text-end">Precio</th>
                                        <th class="text-end">Costo1</th>
                                        <th class="text-end">Costo2</th>
                                        <th class="text-end">Costo3</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${itemsHtml}
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="3" class="text-end">TOTALES:</th>
                                        <th class="text-end">-</th>
                                        <th class="text-end">-</th>
                                        <th class="text-end">-</th>
                                        <th class="text-end">-</th>
                                        <th class="text-end ${signo < 0 ? 'text-danger' : 'text-success'}">
                                            $${totalCalculado.toFixed(2)}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                `;

                $('#detalleModalBody').html(html);
            }
        });
    </script>
@endsection
