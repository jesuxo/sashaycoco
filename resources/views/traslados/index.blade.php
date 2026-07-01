@extends('layouts.master')
@section('title', 'Gestión de Transferencias')
@section('css')
    <style>
        .transfer-item {
            transition: all 0.2s;
        }
        .transfer-item:hover {
            background-color: #f8f9fa;
        }
        .badge-origen {
            background-color: #28a745;
        }
        .badge-destino {
            background-color: #fd7e14;
        }
        .producto-busqueda {
            cursor: pointer;
            transition: all 0.2s;
        }
        .producto-busqueda:hover {
            background-color: #e9ecef;
            transform: translateX(5px);
        }
        .cantidad-control {
            width: 100px;
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
                                <i class="bi bi-arrow-left-right me-2"></i>Gestión de Transferencias
                            </h4>
                            <p class="text-white-50 mb-0 small">Arma tu lista de transferencia entre sucursales</p>
                        </div>
                        <div>
                            <button class="btn btn-light" onclick="limpiarSesion()">
                                <i class="bi bi-trash me-1"></i> Limpiar lista
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Formulario de agregar items -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Sucursal Origen</label>
                            <select class="form-select" id="sucursalOrigen">
                                <option value="">Seleccione...</option>
                                @foreach($sucursales as $suc)
                                    <option value="{{ $suc->id }}">{{ $suc->descrip }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Sucursal Destino</label>
                            <select class="form-select" id="sucursalDestino">
                                <option value="">Seleccione...</option>
                                @foreach($sucursales as $suc)
                                    <option value="{{ $suc->id }}">{{ $suc->descrip }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Buscar Producto</label>
                            <input type="text" class="form-control" id="buscarProducto"
                                   placeholder="Código o descripción del producto">
                            <div id="resultadosBusqueda" class="list-group mt-2" style="display: none;"></div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Cantidad</label>
                            <input type="number" class="form-control" id="cantidad" value="1" min="1">
                        </div>
                    </div>

                    <!-- Lista de items en sesión -->
                    <div class="mt-4">
                        <h5 class="mb-3">
                            <i class="bi bi-list-check me-2"></i>
                            Productos a transferir
                            <span class="badge bg-primary ms-2">{{ count($itemsSesion) }}</span>
                        </h5>

                        @if(count($itemsSesion) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Desde</th>
                                        <th>Hacia</th>
                                        <th class="text-end">Cantidad</th>
                                        <th class="text-end">Stock Disponible</th>
                                        <th class="text-center">Acciones</th>
                                    </thead>
                                    <tbody>
                                    @foreach($itemsSesion as $item)
                                        @php
                                            $stockDisponible = \App\Models\Saexis::where('codprod', $item->codprod)
                                                ->where('fk_sucursal', $item->fk_sucursal_origen)
                                                ->sum('existen');
                                        @endphp
                                        <tr class="transfer-item" data-id="{{ $item->id }}">
                                            <td>
                                                <strong>{{ $item->codprod }}</strong>
                                                <br>
                                                <small class="text-muted">{{ Str::limit($item->producto->descrip ?? '', 40) }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">{{ $item->sucursalOrigen->descrip ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning text-dark">{{ $item->sucursalDestino->descrip ?? 'N/A' }}</span>
                                            </td>
                                            <td class="text-end">
                                                <input type="number" class="form-control form-control-sm cantidad-item"
                                                       value="{{ $item->cantidad }}" min="1"
                                                       style="width: 80px; display: inline-block;">
                                            </td>
                                            <td class="text-end">
                                                    <span class="badge {{ $stockDisponible >= $item->cantidad ? 'bg-success' : 'bg-danger' }}">
                                                        {{ number_format($stockDisponible, 0, ',', '.') }} uds
                                                    </span>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-danger eliminar-item" data-id="{{ $item->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="fw-bold">Total unidades a transferir:</td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($itemsSesion->sum('cantidad'), 0, ',', '.') }}
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="mt-3">
                                <label class="form-label">Observaciones generales</label>
                                <textarea class="form-control" id="observaciones" rows="2"
                                          placeholder="Observaciones para esta transferencia..."></textarea>
                            </div>

                            <div class="mt-3 text-end">
                                <button class="btn btn-success btn-lg" onclick="guardarTransferencia()">
                                    <i class="bi bi-check-circle me-2"></i> Confirmar Transferencia
                                </button>
                            </div>
                        @else
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle fs-1"></i>
                                <p class="mt-2">No hay productos en la lista de transferencia.</p>
                                <p class="mb-0 small">Selecciona origen, destino, producto y cantidad para comenzar.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de sugerencias pendientes -->
    @if(isset($sugerencias) && count($sugerencias) > 0)
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-lightbulb me-2"></i>
                            Sugerencias pendientes de transferencia
                        </h5>
                        <small>Basado en análisis de rotación</small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                32
                                <th>Producto</th>
                                <th>Desde</th>
                                <th>Hacia</th>
                                <th class="text-end">Cantidad sugerida</th>
                                <th class="text-center">Acción</th>
                                </thead>
                                <tbody>
                                @foreach($sugerencias as $sug)
                                    <tr>
                                        <td>{{ $sug['producto_cod'] }}<br><small>{{ Str::limit($sug['producto_nombre'], 30) }}</small></td>
                                        <td>{{ $sug['sucursal_origen_nombre'] }}</td>
                                        <td>{{ $sug['sucursal_destino_nombre'] }}</td>
                                        <td class="text-end fw-bold">{{ number_format($sug['cantidad_sugerida'], 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary cargar-sugerencia"
                                                    data-producto="{{ $sug['producto_cod'] }}"
                                                    data-origen="{{ $sug['sucursal_origen_id'] }}"
                                                    data-destino="{{ $sug['sucursal_destino_id'] }}"
                                                    data-cantidad="{{ $sug['cantidad_sugerida'] }}">
                                                <i class="bi bi-plus-circle me-1"></i> Agregar
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
    @endif
@endsection

@section('scripts')
    <script>
        let timeoutBusqueda;
        let productoSeleccionado = null;

        // Búsqueda de productos
        $('#buscarProducto').on('keyup', function() {
            clearTimeout(timeoutBusqueda);
            const busqueda = $(this).val();
            const origenId = $('#sucursalOrigen').val();

            if (busqueda.length < 2 || !origenId) {
                $('#resultadosBusqueda').hide().empty();
                return;
            }

            timeoutBusqueda = setTimeout(() => {
                $.ajax({
                    type: 'post',
                    url: '{{ route("transferencias.buscar") }}',
                    data: {
                        busqueda: busqueda,
                        sucursal_id: origenId
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.productos.length > 0) {
                            let html = '';
                            response.productos.forEach(prod => {
                                html += `<a href="javascript:;" class="list-group-item list-group-item-action producto-busqueda"
                                    data-codprod="${prod.codprod}"
                                    data-descrip="${prod.descrip}"
                                    data-stock="${prod.existencia_origen}">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>${prod.codprod}</strong>
                                            <br>
                                            <small>${prod.descrip.substring(0, 50)}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-info">Stock: ${prod.existencia_origen} uds</span>
                                        </div>
                                    </div>
                                </a>`;
                            });
                            $('#resultadosBusqueda').html(html).show();
                        } else {
                            $('#resultadosBusqueda').html('<div class="list-group-item text-muted">No se encontraron productos</div>').show();
                        }
                    }
                });
            }, 300);
        });

        // Seleccionar producto de búsqueda
        $(document).on('click', '.producto-busqueda', function() {
            productoSeleccionado = {
                codprod: $(this).data('codprod'),
                descrip: $(this).data('descrip'),
                stock: $(this).data('stock')
            };

            $('#buscarProducto').val(productoSeleccionado.codprod + ' - ' + productoSeleccionado.descrip);
            $('#resultadosBusqueda').hide();
        });

        // Agregar item a la sesión
        function agregarItem() {
            const origenId = $('#sucursalOrigen').val();
            const destinoId = $('#sucursalDestino').val();
            const cantidad = parseInt($('#cantidad').val());

            if (!origenId) {
                alert('Seleccione sucursal origen');
                return;
            }
            if (!destinoId) {
                alert('Seleccione sucursal destino');
                return;
            }
            if (!productoSeleccionado) {
                alert('Seleccione un producto');
                return;
            }
            if (cantidad < 1) {
                alert('La cantidad debe ser mayor a 0');
                return;
            }
            if (cantidad > productoSeleccionado.stock) {
                alert(`Stock insuficiente. Disponible: ${productoSeleccionado.stock} unidades`);
                return;
            }

            $.ajax({
                type: 'post',
                url: '{{ route("transferencias.agregar") }}',
                data: {
                    origen_id: origenId,
                    destino_id: destinoId,
                    codprod: productoSeleccionado.codprod,
                    cantidad: cantidad
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.error || 'Error al agregar item');
                }
            });
        }

        // Eliminar item
        $(document).on('click', '.eliminar-item', function() {
            const id = $(this).data('id');
            if (confirm('¿Eliminar este producto de la lista?')) {
                $.ajax({
                    type: 'delete',
                    url: `/transferencias/eliminar-item/${id}`,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        location.reload();
                    }
                });
            }
        });

        // Actualizar cantidad
        $(document).on('change', '.cantidad-item', function() {
            const id = $(this).closest('tr').data('id');
            const nuevaCantidad = $(this).val();

            $.ajax({
                type: 'put',
                url: `/transferencias/actualizar-item/${id}`,
                data: { cantidad: nuevaCantidad },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    location.reload();
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.error || 'Error al actualizar cantidad');
                    location.reload();
                }
            });
        });

        // Guardar transferencia
        function guardarTransferencia() {
            const observaciones = $('#observaciones').val();

            if (confirm('¿Confirmar transferencia? Esta acción actualizará los inventarios.')) {
                $.ajax({
                    type: 'post',
                    url: '{{ route("transferencias.guardar") }}',
                    data: { observaciones: observaciones },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        window.location.href = '{{ route("transferencias.historial") }}';
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.error || 'Error al guardar transferencia');
                    }
                });
            }
        }

        // Limpiar sesión
        function limpiarSesion() {
            if (confirm('¿Limpiar toda la lista de transferencia?')) {
                $.ajax({
                    type: 'post',
                    url: '{{ route("transferencias.limpiar") }}',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        location.reload();
                    }
                });
            }
        }

        // Cargar sugerencia de análisis
        $(document).on('click', '.cargar-sugerencia', function() {
            const sugerencia = {
                producto_cod: $(this).data('producto'),
                sucursal_origen_id: $(this).data('origen'),
                sucursal_destino_id: $(this).data('destino'),
                cantidad_sugerida: $(this).data('cantidad')
            };

            $.ajax({
                type: 'post',
                url: '{{ route("transferencias.cargar-sugerencia") }}',
                data: { sugerencia: sugerencia },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    location.reload();
                }
            });
        });
    </script>
@endsection
