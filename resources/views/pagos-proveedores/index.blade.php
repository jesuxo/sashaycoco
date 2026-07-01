{{-- resources/views/pagos-proveedores/index.blade.php --}}
@extends('layouts.master')
@section('title')
    Pedidos a Proveedores
@endsection
@section('css')
    <style>
        .input-group{
            flex-wrap: unset !important;
        }
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 6px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .select2-dropdown {
            z-index: 1060;
        }

        #resultadosProductosTemp .list-group-item {
            cursor: pointer;
            transition: all 0.2s;
        }

        #resultadosProductosTemp .list-group-item:hover {
            background-color: #e3f2fd;
        }

        .table-responsive {
            max-height: 300px;
            overflow-y: auto;
        }

        .pago-card {
            transition: all 0.3s ease;
            border-left: 5px solid transparent;
            margin-bottom: 1rem;
            border-radius: 8px;
            overflow: hidden;
        }
        .pago-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .estado-pendiente { border-left-color: #6c757d; }
        .estado-parcial { border-left-color: #ffc107; }
        .estado-completado { border-left-color: #28a745; }
        .estado-aprobado { border-left-color: #17a2b8; }

        .quick-stats {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .stat-item {
            text-align: center;
            padding: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .stat-label{
            font-size: 12px;
        }

        .stat-item:hover  .text-info{

            color: white !important;
        }
        .stat-value {
            font-size: 15px;
            font-weight: bold;
        }
        .statitemactive {
            background: #007bff !important;
            color: white !important;
            border-radius: 3px;
        }
        .search-box-pagos {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 40px;
            padding: 0.5rem 1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .search-box-pagos input {
            border: none;
            background: transparent;
            width: 100%;
            padding: 0.5rem;
        }
        .action-btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 2px;
            transition: all 0.2s;
        }
        .action-btn:hover {
            transform: scale(1.1);
        }
        .loading-spinner {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
        }
        .loading-spinner.active {
            display: block;
        }
        .progress {
            height: 8px;
            border-radius: 4px;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid py-4">

        {{-- Estadísticas rápidas --}}
        <div class="quick-stats shadow-sm mb-4">
            <div class="row">
                <div class="col-md-1 col-12 stat-item btn btn-outline-primary"  onclick="abrirModalCrear()">
                    <div class="stat-value">NUEVO</div>
                    <div class="stat-label ">Pedido</div>
                </div>
                <div class="col-md-1 col-6 stat-item btn btn-outline-success" onclick="verResumenGeneral('completos')">
                    <div class="stat-value  ">RESUMEN</div>
                    <div class="stat-label">Pagados</div>
                </div>
                <div class="col-md-1 col-6 stat-item btn btn-outline-warning" onclick="verResumenGeneral('pendientes')">
                    <div class="stat-value  ">RESUMEN</div>
                    <div class="stat-label">Pendientes</div>
                </div>
                <div class="col-md-1 col-6 stat-item btn btn-outline-info" onclick="abrirModalFechas()">
                    <div class="stat-value  ">PAGADAS</div>
                    <div class="stat-label">Por fechas</div>
                </div>
                <div class="col-md-1 col-6 stat-item"></div>

                <div   class="cursor-pointer col-md-1 col-6 stat-item  ">

                </div>
                <div   class="cursor-pointer col-md-1 col-6 stat-item  ">

                </div>
                <div   class="cursor-pointer col-md-1 col-6 stat-item  ">

                </div>
                <div onclick="filtrarPorEstado('todos')" class="cursor-pointer col-md-1 col-6 stat-item {{ $estado == 'todos' ? 'statitemactive' : '' }}">
                    <div class="stat-value {{ $estado == 'todos' ? 'text-white' : 'text-primary' }}">{{ $estadisticas['total'] }}</div>
                    <div class="stat-label {{ $estado == 'todos' ? 'text-white' : '' }}">Total <br> Pedidos</div>
                </div>
                <div onclick="verDetalleFacturadas()" class="cursor-pointer col-md-1 col-6 stat-item">
                    <div class="stat-value text-secondary" id="totalFacturadas">{{ (isset($totalFacturadas))? number_format($totalFacturadas, 0,',','.'): 0 }}</div>
                    <div class="stat-label">Motos facturadas</div>
                </div>
                <div class="col-md-1 col-6 stat-item">
                    <div class="stat-value text-secondary" id="totalPendiente">{{ number_format($totalPendiente, 0,',','.') }}</div>
                    <div class="stat-label">Motos por recibir</div>
                </div>
                <div class="col-md-1 col-6 stat-item btn btn-outline-secondary" style="float:right;" data-bs-toggle="collapse" data-bs-target="#filtrosAvanzados">
                    <div class="stat-value ">Filtros</div>
                    <div class="stat-label">Avanzados</div>
                </div>
            </div>
        </div>

        {{-- Barra de búsqueda --}}
        <div class="mb-4">
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="search-box-pagos">
                        <i class="bi bi-search text-muted me-2"></i>
                        <input type="text" id="searchInput" placeholder="Buscar por folio, aprobación, proveedor..." value="{{ $search ?? '' }}" autocomplete="off">
                        @if($search)
                            <button class="btn btn-sm btn-link text-danger" onclick="limpiarBusqueda()"><i class="bi bi-x"></i></button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Filtros avanzados --}}
            <div class="collapse {{ $fecha_desde || $fecha_hasta || $codprov ? 'show' : '' }}" id="filtrosAvanzados">
                <div class="card card-body bg-light mt-3">
                    <form id="filtrosForm" method="GET" action="{{ route('pagos-proveedores.index') }}">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Proveedor</label>
                                <select class="form-select" name="codprov" onchange="$('#filtrosForm').submit()">
                                    <option value="">Todos</option>
                                    @foreach($proveedores as $prov)
                                        <option value="{{ $prov->codprov }}" {{ ($codprov ?? '') == $prov->codprov ? 'selected' : '' }}>
                                            {{ $prov->descrip }} ({{ $prov->codprov }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Fecha Desde</label>
                                <input type="date" class="form-control" name="fecha_desde" value="{{ $fecha_desde ?? '' }}" onchange="$('#filtrosForm').submit()">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Fecha Hasta</label>
                                <input type="date" class="form-control" name="fecha_hasta" value="{{ $fecha_hasta ?? '' }}" onchange="$('#filtrosForm').submit()">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-secondary d-block w-100" onclick="limpiarFiltros()">
                                    <i class="bi bi-arrow-repeat me-2"></i>Limpiar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Resultados --}}
        <div id="resultadosPagos">
            @include('pagos-proveedores.partials.lista', ['pagos' => $pagos])
        </div>
    </div>


    {{-- Modales --}}
    <div class="modal fade" id="modalFechas" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Motos pagadas por fecha</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Desde</label>
                            <input type="date" id="fechaDesde" class="form-control">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Hasta</label>
                            <input type="date" id="fechaHasta" class="form-control">
                        </div>
                    </div>

                    <div id="resultadoFechas" class="row mt-3 text-center fw-bold row"  style="display: none">
                        <div class="col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div class="vr rounded bg-info opacity-50" style="width: 4px;"></div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="text-uppercase fw-medium text-start text-muted fs-14 text-truncate">Motos</p>
                                            <h4 class="fs-22 fw-semibold mb-3 text-start " id="totalmotospagadas">Consultando </h4>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-info-subtle text-info rounded fs-3">
                                        <i class="ri-motorbike-fill"></i>
                                    </span>
                                        </div>
                                    </div>
                                </div><!-- end card body -->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div class="vr rounded bg-success opacity-50" style="width: 4px;"></div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="text-uppercase fw-medium text-start text-muted fs-14 text-truncate">Pagado</p>
                                            <h4 class="fs-22 fw-semibold mb-3 text-start " id="totalmontopagado">Consultando </h4>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-success-subtle text-success rounded fs-3">
                                        <i class="bi bi-currency-dollar"></i>
                                    </span>
                                        </div>
                                    </div>
                                </div><!-- end card body -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="consultarFechas()">Consultar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPago" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white"><i class="bi bi-cash-stack me-2"></i><span id="modalTitleText">Gestión de Pedidos</span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Cargando...</p></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalConfirmacion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white"><i class="bi bi-exclamation-triangle me-2"></i>Confirmar</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body"><p id="confirmacionMensaje">¿Estás seguro?</p></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmarAccionBtn">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
    </div>

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="liveToast" class="toast" role="alert">
            <div class="toast-header"><i class="bi bi-info-circle me-2"></i><strong id="toastTitle">Notificación</strong><button type="button" class="btn-close" data-bs-dismiss="toast"></button></div>
            <div class="toast-body" id="toastMessage"></div>
        </div>
    </div>

    {{-- Modal para agregar producto temporal --}}
    <div class="modal fade" id="modalAgregarProductoTemp" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title  text-white"><i class="bi bi-plus-circle me-2"></i>Agregar Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Buscar Producto <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="buscarProductoTemp"
                               placeholder="Escriba al menos 2 caracteres..." autocomplete="off">
                        <div id="resultadosProductosTemp" class="list-group mt-2" style="display: none; max-height: 200px; overflow-y: auto;"></div>
                    </div>

                    <input type="hidden" id="producto_id_temp" value="">
                    <input type="hidden" id="producto_codprod_temp" value="">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Producto Seleccionado</label>
                        <input type="text" class="form-control bg-light" id="producto_descrip_temp" readonly>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Cantidad <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="cantidad_temp" min="1" value="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Precio Unitario <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="precio_temp" step="0.01" min="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-success" id="btnAgregarProductoTemp">
                        <i class="bi bi-plus-circle me-1"></i>Agregar Producto
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/es.js"></script>

    <script>
        // Variables globales
        let pagoActualId = null;
        let accionConfirmar = null;
        let productosTemporales = [];
        let timeoutBusquedaTemp;

        const modalPago = new bootstrap.Modal(document.getElementById('modalPago'));
        const modalConfirmacion = new bootstrap.Modal(document.getElementById('modalConfirmacion'));
        const toast = new bootstrap.Toast(document.getElementById('liveToast'));
        let timeoutId;


        // Configurar Select2 en español
        $.fn.select2.defaults.set('language', 'es');

        function verResumenGeneral(tipo = 'pendientes') {
            document.getElementById('modalTitleText').innerHTML = '<i class="bi bi-graph-up me-2"></i>Resumen General de Pedidos';
            document.getElementById('modalBody').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Cargando resumen...</p></div>';
            modalPago.show();

            fetch(`/pagos-proveedores/resumen-general`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ tipo: tipo })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta');
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById('modalBody').innerHTML = data.html;
                    setTimeout(() => {
                        inicializarTooltips();
                    }, 100);
                })
                .catch(() => mostrarToast('Error al cargar resumen', 'Error', 'danger'));
        }

        // Búsqueda de productos en tiempo real (funciona siempre)
        $(document).on('keyup', '#buscarProductoTemp', function() {
            const term = $(this).val();
            clearTimeout(timeoutBusquedaTemp);

            if (term.length < 2) {
                $('#resultadosProductosTemp').hide();
                return;
            }

            timeoutBusquedaTemp = setTimeout(() => {
                $('#resultadosProductosTemp').show().html('<div class="list-group-item text-center"><div class="spinner-border spinner-border-sm"></div> Buscando...</div>');

                fetch(`/buscar-productos?q=${encodeURIComponent(term)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            $('#resultadosProductosTemp').html('');
                            data.forEach(producto => {
                                $('#resultadosProductosTemp').append(`
                            <a href="javascript:void(0)" class="list-group-item list-group-item-action"
                               onclick="seleccionarProducto(${producto.id}, '${producto.descrip.replace(/'/g, "\\'")}', '${producto.codprod}')">
                                <strong>${producto.descrip}</strong>
                                <br><small class="text-muted">Código: ${producto.codprod}</small>
                            </a>
                        `);
                            });
                        } else {
                            $('#resultadosProductosTemp').html('<div class="list-group-item text-muted">No se encontraron productos</div>');
                        }
                    })
                    .catch(() => {
                        $('#resultadosProductosTemp').html('<div class="list-group-item text-danger">Error al buscar</div>');
                    });
            }, 300);
        });

        function inicializarTooltips() {
            // Inicializar todos los tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                // Destruir tooltip existente si lo hay
                var tooltip = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
                if (tooltip) {
                    tooltip.dispose();
                }
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        function mostrarLoading(mostrar) {
            document.getElementById('loadingSpinner').classList.toggle('active', mostrar);
        }

        function mostrarToast(mensaje, titulo = 'Notificación', tipo = 'info') {
            document.getElementById('toastTitle').innerText = titulo;
            document.getElementById('toastMessage').innerText = mensaje;
            toast.show();
            setTimeout(() => toast.hide(), 5000);
        }

        function mostrarErrores(errors) {
            let msg = 'Errores:\n';
            for (let campo in errors) msg += `- ${errors[campo].join(', ')}\n`;
            alert(msg);
        }

        // Búsqueda en tiempo real
        document.getElementById('searchInput')?.addEventListener('keyup', function(e) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                window.location.href = '{{ route("pagos-proveedores.index") }}?search=' + encodeURIComponent(e.target.value);
            }, 1000);
        });

        function filtrarPorEstado(estado) {
            const url = new URL(window.location.href);
            if (estado === 'todos') url.searchParams.delete('estado');
            else url.searchParams.set('estado', estado);
            const search = document.getElementById('searchInput')?.value;
            if (search) url.searchParams.set('search', search);
            window.location.href = url.toString();
        }

        function limpiarFiltros() {
            window.location.href = '{{ route("pagos-proveedores.index") }}';
        }

        function limpiarBusqueda() {
            document.getElementById('searchInput').value = '';
            window.location.href = '{{ route("pagos-proveedores.index") }}';
        }

        // ========== FUNCIONES DEL MODAL DE CREACIÓN ==========

        function abrirModalCrear() {
            document.getElementById('modalTitleText').innerText = 'Nuevo Pedido';
            document.getElementById('modalBody').innerHTML = `
                <div class="container-fluid">
                    <form id="formCrearPago" action="{{ route('pagos-proveedores.store') }}" method="POST">
                        @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Fecha de Pago <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="fecha_pago" id="fecha_pago" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Proveedor <span class="text-danger">*</span></label>
                                <select class="form-select" name="codprov" id="codprov" required>
                                    <option value="">Seleccione un proveedor...</option>
                                    @foreach($proveedores as $prov)
            <option value="{{ $prov->codprov }}">{{ $prov->codprov }} - {{ $prov->descrip }}</option>
                                    @endforeach
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mb-3">
            <label class="form-label fw-bold">Notas</label>
            <textarea class="form-control" name="notas" id="notas" rows="2" placeholder="Observaciones adicionales..."></textarea>
        </div>
    </div>
    <hr class="my-3">
    <div id="seccionProductos" style="display: none;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Productos del Pedido</h5>
            <button type="button" class="btn btn-sm btn-success" id="btnAgregarProducto">
                <i class="bi bi-plus-circle me-1"></i>Agregar Producto
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="tablaProductosTemp">
                <thead class="table-light">
                    <tr><th>Producto</th><th width="100">Cantidad</th><th width="120">Precio Unitario</th><th width="120">Subtotal</th><th width="50">Acción</th></tr>
                </thead>
                <tbody id="tbodyProductosTemp">
                    <tr class="text-muted"><td colspan="5" class="text-center">No hay productos agregados</td></tr>
                </tbody>
                <tfoot class="table-secondary">
                    <tr><th colspan="3" class="text-end">Total del Pago:</th><th id="totalTempPago">$0.00</th><th></th></tr>
                </tfoot>
            </table>
        </div>
    </div>
    <hr class="my-3">
    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i>Cancelar</button>
        <button type="submit" class="btn btn-primary" id="btnGuardarPago" disabled><i class="bi bi-save me-1"></i>Guardar Pedido</button>
    </div>
</form>
</div>
`;
            modalPago.show();

            // Inicializar Select2 después de cargar el HTML
            setTimeout(() => {
                inicializarSelect2();
                inicializarEventosCreacion();
                inicializarTooltips(); //
            }, 100);
        }

        function inicializarSelect2() {
            if ($('#codprov').length && $.fn.select2) {
                $('#codprov').select2({
                    dropdownParent: $('#modalPago'),
                    placeholder: 'Seleccione un proveedor...',
                    width: '100%'
                });

                // Evento de Select2
                $('#codprov').on('select2:select', function(e) {
                    if (e.params.data.id) {
                        $('#seccionProductos').slideDown();
                        $('#btnGuardarPago').prop('disabled', productosTemporales.length === 0);
                    }
                });

                // Evento change normal
                $('#codprov').on('change', function() {
                    if ($(this).val()) {
                        $('#seccionProductos').slideDown();
                        $('#btnGuardarPago').prop('disabled', productosTemporales.length === 0);
                    } else {
                        $('#seccionProductos').slideUp();
                        $('#btnGuardarPago').prop('disabled', true);
                    }
                });
            }
        }

        function inicializarEventosCreacion() {
            // Botón agregar producto (fuera del modal)
            $('#btnAgregarProducto').off('click').on('click', function() {
                mostrarModalAgregarProducto();
            });


            // Botón agregar producto temporal (dentro del modal)
            $('#btnAgregarProductoTemp').off('click').on('click', function() {
                agregarProductoTemporal();
            });

            // Cuando se cierra el modal de producto, restaurar estado
            $('#modalAgregarProductoTemp').on('hidden.bs.modal', function() {
                restaurarModalProducto();
            });

            // Submit del formulario
            $('#formCrearPago').off('submit').on('submit', function(e) {
                e.preventDefault();
                guardarPago();
            });
        }

        function agregarProductos(id) {
            pagoActualId = id;
            mostrarLoading(true);

            fetch(`/pagos-proveedores/${id}/productos`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        productosTemporales = data.productos.map(p => ({
                            producto_id: p.producto_id,
                            producto_codprod: p.producto_codprod,
                            producto_descrip: p.producto_descrip,
                            cantidad: p.cantidad,
                            cantidad_facturada: p.cantidad_facturada || 0, // AGREGADO
                            precio_unitario: p.precio_unitario,
                            subtotal: p.cantidad * p.precio_unitario,
                            id: p.id
                        }));

                        // IMPORTANTE: Activar modo edición ANTES de abrir el modal
                        window.modoEdicion = true;
                        window.pagoEditandoId = id;

                        abrirModalEditarProductos(id);
                        setTimeout(() => {
                            inicializarTooltips(); // Agregar esta línea después de abrir el modal
                        }, 200);
                    } else {
                        mostrarToast('Error al cargar productos', 'Error', 'danger');
                    }
                })
                .catch(() => mostrarToast('Error al cargar productos', 'Error', 'danger'))
                .finally(() => mostrarLoading(false));
        }

        function abrirModalEditarProductos(pagoId) {
            const modalContent = `
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Productos del Pedido</h5>
                <button type="button" class="btn btn-sm btn-success" id="btnAgregarProductoEdit">
                    <i class="bi bi-plus-circle me-1"></i>Agregar Producto
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tablaProductosEdit">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th width="100">Cantidad</th>
                            <th width="100">Facturadas</th>
                            <th width="100">Recibidas</th>
                            <th width="120">Precio Unitario</th>
                            <th width="120">Subtotal</th>
                            <th width="50">Acción</th>
                        </thead>
                    <tbody id="tbodyProductosEdit">
                        ${productosTemporales.length === 0 ?
                '<tr class="text-muted"><td colspan="7" class="text-center">No hay productos agregados</td></tr>' :
                productosTemporales.map((prod, index) => `
                                <tr data-index="${index}">
                                    <td>
                                        <strong>${prod.producto_descrip}</strong>
                                        <br><small class="text-muted">Código: ${prod.producto_codprod || 'N/A'}</small>
                                        <input type="hidden" class="producto-id-edit" value="${prod.producto_id}">
                                        <input type="hidden" class="producto-detalle-id" value="${prod.id || ''}">
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <button type="button" class="btn btn-outline-secondary btn-cantidad-menor" data-index="${index}">-</button>
                                            <input type="number" class="form-control text-center cantidad-edit" data-index="${index}" value="${prod.cantidad}" min="1" style="width: 70px;">
                                            <button type="button" class="btn btn-outline-secondary btn-cantidad-mayor" data-index="${index}">+</button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <button type="button" class="btn btn-outline-secondary btn-facturada-menor" data-index="${index}">-</button>
                                            <input type="number" class="form-control text-center cantidad-facturada-edit" data-index="${index}" value="${prod.cantidad_facturada || 0}" min="0" max="${prod.cantidad}" style="width: 70px;">
                                            <button type="button" class="btn btn-outline-secondary btn-facturada-mayor" data-index="${index}">+</button>
                                        </div>
                                     </td>
                                    <td>
                                        <span class="badge bg-secondary">${prod.cantidad_recibida || 0}</span>
                                     </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control precio-edit" data-index="${index}" value="${prod.precio_unitario}" step="0.01" min="0" style="width: 100px;">
                                        </div>
                                     </td>
                                    <td class="subtotal-edit" data-index="${index}">$${prod.subtotal.toFixed(2)}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger btn-eliminar-producto" data-index="${index}" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `).join('')
            }
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <th colspan="5" class="text-end">Total:</th>
                            <th id="totalEditPago">$${productosTemporales.reduce((sum, p) => sum + p.subtotal, 0).toFixed(2)}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <hr class="my-3">
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarProductosEdit">Guardar Cambios</button>
            </div>
        </div>
    `;

            document.getElementById('modalTitleText').innerText = 'Editar Productos del Pedido';
            document.getElementById('modalBody').innerHTML = modalContent;
            modalPago.show();

            setTimeout(() => {
                inicializarEventosEdicion();
                inicializarTooltips();
                $('#btnAgregarProductoEdit').off('click').on('click', function() {
                    limpiarFormularioProducto();
                    window.modoEdicion = true;
                    $('#modalAgregarProductoTemp').modal('show');
                });

                $('#btnGuardarProductosEdit').off('click').on('click', function() {
                    guardarProductosEdit(pagoId);
                });
            }, 100);
        }

        function inicializarEventosEdicion() {
            // Eventos para botones de cantidad
            $('.btn-cantidad-menor').off('click').on('click', function() {
                const index = $(this).data('index');
                const input = $(`.cantidad-edit[data-index="${index}"]`);
                let valor = parseInt(input.val()) || 1;
                valor = Math.max(1, valor - 1);
                input.val(valor).trigger('change');
            });

            $('.btn-cantidad-mayor').off('click').on('click', function() {
                const index = $(this).data('index');
                const input = $(`.cantidad-edit[data-index="${index}"]`);
                let valor = parseInt(input.val()) || 1;
                valor = valor + 1;
                input.val(valor).trigger('change');
            });

            // Eventos para botones de cantidad facturada
            $('.btn-facturada-menor').off('click').on('click', function() {
                const index = $(this).data('index');
                const input = $(`.cantidad-facturada-edit[data-index="${index}"]`);
                let valor = parseInt(input.val()) || 0;
                const max = parseInt(input.attr('max')) || 0;
                valor = Math.max(0, valor - 1);
                if (valor <= max) {
                    input.val(valor).trigger('change');
                }
            });

            $('.btn-facturada-mayor').off('click').on('click', function() {
                const index = $(this).data('index');
                const input = $(`.cantidad-facturada-edit[data-index="${index}"]`);
                let valor = parseInt(input.val()) || 0;
                const max = parseInt(input.attr('max')) || 0;
                valor = Math.min(max, valor + 1);
                input.val(valor).trigger('change');
            });

            // Evento para cambio de cantidad
            $('.cantidad-edit').off('change').on('change', function() {
                const index = $(this).data('index');
                const nuevaCantidad = parseInt($(this).val()) || 1;
                const precio = parseFloat($(`.precio-edit[data-index="${index}"]`).val()) || 0;
                const nuevoSubtotal = nuevaCantidad * precio;

                // Actualizar el producto en el array
                if (productosTemporales[index]) {
                    productosTemporales[index].cantidad = nuevaCantidad;
                    productosTemporales[index].subtotal = nuevoSubtotal;
                    // Actualizar el max del campo facturada
                    $(`.cantidad-facturada-edit[data-index="${index}"]`).attr('max', nuevaCantidad);
                    // Si la facturada supera la nueva cantidad, ajustarla
                    let facturadaActual = parseInt($(`.cantidad-facturada-edit[data-index="${index}"]`).val()) || 0;
                    if (facturadaActual > nuevaCantidad) {
                        $(`.cantidad-facturada-edit[data-index="${index}"]`).val(nuevaCantidad).trigger('change');
                    }
                }

                // Actualizar el subtotal en la tabla
                $(`.subtotal-edit[data-index="${index}"]`).text('$' + nuevoSubtotal.toFixed(2));

                // Actualizar total general
                actualizarTotalEdit();
            });

            // Evento para cambio de cantidad facturada
            $('.cantidad-facturada-edit').off('change').on('change', function() {
                const index = $(this).data('index');
                const nuevaFacturada = parseInt($(this).val()) || 0;
                const max = parseInt($(this).attr('max')) || 0;

                // Validar que no exceda la cantidad total
                if (nuevaFacturada > max) {
                    $(this).val(max);
                    mostrarToast('La cantidad facturada no puede exceder la cantidad total', 'Advertencia', 'warning');
                    return;
                }

                if (productosTemporales[index]) {
                    productosTemporales[index].cantidad_facturada = nuevaFacturada;
                }
            });

            // Evento para cambio de precio
            $('.precio-edit').off('change').on('change', function() {
                const index = $(this).data('index');
                const nuevoPrecio = parseFloat($(this).val()) || 0;
                const cantidad = parseInt($(`.cantidad-edit[data-index="${index}"]`).val()) || 1;
                const nuevoSubtotal = cantidad * nuevoPrecio;

                // Actualizar el producto en el array
                if (productosTemporales[index]) {
                    productosTemporales[index].precio_unitario = nuevoPrecio;
                    productosTemporales[index].subtotal = nuevoSubtotal;
                }

                // Actualizar el subtotal en la tabla
                $(`.subtotal-edit[data-index="${index}"]`).text('$' + nuevoSubtotal.toFixed(2));

                // Actualizar total general
                actualizarTotalEdit();
            });

            // Evento para eliminar producto
            $('.btn-eliminar-producto').off('click').on('click', function() {
                const index = $(this).data('index');
                if (confirm('¿Está seguro de eliminar este producto?')) {
                    productosTemporales.splice(index, 1);
                    const pagoId = pagoActualId;
                    abrirModalEditarProductos(pagoId);
                    mostrarToast('Producto eliminado', 'Éxito', 'success');
                }
            });
        }

        function actualizarTotalEdit() {
            let total = 0;
            productosTemporales.forEach(prod => {
                total += prod.subtotal;
            });
            $('#totalEditPago').text('$' + total.toFixed(2));
        }

        function guardarProductosEdit(pagoId) {
            mostrarLoading(true);

            // Preparar datos para enviar
            const productosActualizar = productosTemporales.filter(p => p.id && !p.es_nuevo).map(p => ({
                id: p.id,
                cantidad: p.cantidad,
                cantidad_facturada: p.cantidad_facturada , // Incluir cantidad facturada
                precio_unitario: p.precio_unitario
            }));

            const productosNuevos = productosTemporales.filter(p => !p.id || p.es_nuevo).map(p => ({
                producto_id: p.producto_id,
                producto_codprod: p.producto_codprod,
                producto_descrip: p.producto_descrip,
                cantidad: p.cantidad,
                cantidad_facturada: p.cantidad_facturada, // Incluir cantidad facturada
                precio_unitario: p.precio_unitario
            }));

            fetch(`/pagos-proveedores/${pagoId}/productos`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    productos_actualizar: productosActualizar,
                    productos_nuevos: productosNuevos
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast(data.message, 'Éxito', 'success');
                        $('#modalPago').modal('hide');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        mostrarToast(data.error || 'Error al guardar', 'Error', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarToast('Error de conexión', 'Error', 'danger');
                })
                .finally(() => mostrarLoading(false));
        }

        function mostrarModalAgregarProductoEdit() {
            limpiarFormularioProducto();
            window.modoEdicion = true;
            $('#modalAgregarProductoTemp').modal('show');
        }

        function agregarProductoTemporalEdit() {
            const productoId = $('#producto_id_temp').val();
            const productoCodprod = $('#producto_codprod_temp').val();
            const productoDescrip = $('#producto_descrip_temp').val();
            const cantidad = parseInt($('#cantidad_temp').val());
            const precio = parseFloat($('#precio_temp').val());

            if (!productoId || !productoDescrip) {
                mostrarToast('Debe seleccionar un producto', 'Error', 'danger');
                return;
            }
            if (!cantidad || cantidad <= 0) {
                mostrarToast('Cantidad inválida', 'Error', 'danger');
                return;
            }
            if (!precio || precio <= 0) {
                mostrarToast('Precio inválido', 'Error', 'danger');
                return;
            }

            const existe = productosTemporales.find(p => p.producto_id == productoId);
            if (existe) {
                if (confirm('El producto ya existe. ¿Desea sumar la cantidad?')) {
                    existe.cantidad += cantidad;
                    existe.subtotal = existe.cantidad * existe.precio_unitario;
                    // Recargar la tabla de edición
                    const pagoId = window.pagoEditandoId || pagoActualId;
                    abrirModalEditarProductos(pagoId);
                    mostrarToast('Cantidad actualizada', 'Éxito', 'success');
                }
                $('#modalAgregarProductoTemp').modal('hide');
                limpiarFormularioProducto();
                return;
            }

            const subtotal = cantidad * precio;
            productosTemporales.push({
                producto_id: productoId,
                producto_codprod: productoCodprod,
                producto_descrip: productoDescrip,
                cantidad: cantidad,
                precio_unitario: precio,
                subtotal: subtotal,
                es_nuevo: true
            });

            // Recargar la tabla de edición
            const pagoId = window.pagoEditandoId || pagoActualId;
            abrirModalEditarProductos(pagoId);
            $('#modalAgregarProductoTemp').modal('hide');
            limpiarFormularioProducto();
            mostrarToast('Producto agregado correctamente', 'Éxito', 'success');
        }

        function actualizarTablaProductosEdit() {
            const tbody = $('#tbodyProductosEdit');
            let total = 0;

            if (productosTemporales.length === 0) {
                tbody.html('<tr class="text-muted"><td colspan="5" class="text-center">No hay productos agregados</td></tr>');
                $('#totalEditPago').text('$0.00');
                return;
            }

            tbody.empty();
            productosTemporales.forEach((prod, index) => {
                total += prod.subtotal;
                tbody.append(`
            <tr data-index="${index}">
                <td>
                    <strong>${prod.producto_descrip}</strong>
                    <br><small class="text-muted">Código: ${prod.producto_codprod || 'N/A'}</small>
                    <input type="hidden" name="productos_edit[${index}][id]" value="${prod.id || ''}">
                    <input type="hidden" name="productos_edit[${index}][producto_id]" value="${prod.producto_id}">
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <button type="button" class="btn btn-outline-secondary" onclick="modificarCantidadEdit(${index}, -1)">-</button>
                        <input type="number" class="form-control text-center cantidad-edit" value="${prod.cantidad}" min="1" style="width: 70px;" onchange="actualizarCantidadEdit(${index}, this.value)">
                        <button type="button" class="btn btn-outline-secondary" onclick="modificarCantidadEdit(${index}, 1)">+</button>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control precio-edit" value="${prod.precio_unitario}" step="0.01" min="0" style="width: 100px;" onchange="actualizarPrecioEdit(${index}, this.value)">
                    </div>
                </td>
                <td class="subtotal-edit">$${prod.subtotal.toFixed(2)}</td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-sm btn-danger" onclick="eliminarProductoEdit(${index})" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `);
            });
            $('#totalEditPago').text('$' + total.toFixed(2));
        }

        function eliminarProductoEdit(index) {
            if (confirm('¿Está seguro de eliminar este producto?')) {
                productosTemporales.splice(index, 1);
                actualizarTablaProductosEdit();
                mostrarToast('Producto eliminado', 'Éxito', 'success');
            }
        }

        function modificarCantidadEdit(index, cambio) {
            const producto = productosTemporales[index];
            if (!producto) return;
            let nuevaCantidad = producto.cantidad + cambio;
            if (nuevaCantidad < 1) nuevaCantidad = 1;
            actualizarCantidadEdit(index, nuevaCantidad);
        }

        function actualizarPrecioEdit(index, nuevoPrecio) {
            const producto = productosTemporales[index];
            if (!producto) return;
            nuevoPrecio = parseFloat(nuevoPrecio);
            if (isNaN(nuevoPrecio) || nuevoPrecio < 0) nuevoPrecio = 0;
            producto.precio_unitario = nuevoPrecio;
            producto.subtotal = producto.cantidad * producto.precio_unitario;
            actualizarTablaProductosEdit();
        }

        function actualizarCantidadEdit(index, nuevaCantidad) {
            const producto = productosTemporales[index];
            if (!producto) return;
            nuevaCantidad = parseInt(nuevaCantidad);
            if (isNaN(nuevaCantidad) || nuevaCantidad < 1) nuevaCantidad = 1;
            producto.cantidad = nuevaCantidad;
            producto.subtotal = producto.cantidad * producto.precio_unitario;
            actualizarTablaProductosEdit();
        }

        function seleccionarProducto(id, descrip, codprod) {
            $('#producto_id_temp').val(id);
            $('#producto_codprod_temp').val(codprod);
            $('#producto_descrip_temp').val(descrip);
            $('#buscarProductoTemp').val(descrip);
            $('#resultadosProductosTemp').hide();

            /*fetch(`/pagos-proveedores/productos/${id}/precio`)
                .then(r => r.json())
                .then(data => { if (data.precio) $('#precio_temp').val(data.precio); })
                .catch(() => {});*/
        }

        function agregarProductoTemporal() {
            const productoId = $('#producto_id_temp').val();
            const productoDescrip = $('#producto_descrip_temp').val();
            const productoCodprod = $('#producto_codprod_temp').val();
            const cantidad = parseInt($('#cantidad_temp').val());
            const precio = parseFloat($('#precio_temp').val());

            if (!productoId || !productoDescrip) {
                mostrarToast('Debe seleccionar un producto', 'Error', 'danger');
                return;
            }
            if (!cantidad || cantidad <= 0) {
                mostrarToast('Cantidad inválida', 'Error', 'danger');
                return;
            }
            if (!precio || precio <= 0) {
                mostrarToast('Precio inválido', 'Error', 'danger');
                return;
            }

            const existe = productosTemporales.find(p => p.producto_id == productoId);
            if (existe) {
                mostrarToast('El producto ya está agregado', 'Advertencia', 'warning');
                return;
            }

            const subtotal = cantidad * precio;
            productosTemporales.push({
                producto_id: productoId,
                producto_codprod: productoCodprod,
                producto_descrip: productoDescrip,
                cantidad: cantidad,
                precio_unitario: precio,
                subtotal: subtotal
            });

            actualizarTablaProductos();
            $('#modalAgregarProductoTemp').modal('hide');
            limpiarFormularioProducto();
            $('#btnGuardarPago').prop('disabled', false);
        }

        function modificarCantidad(index, cambio) {
            const producto = productosTemporales[index];
            if (!producto) return;

            let nuevaCantidad = producto.cantidad + cambio;
            if (nuevaCantidad < 1) nuevaCantidad = 1;

            actualizarCantidad(index, nuevaCantidad);
        }

        function actualizarCantidad(index, nuevaCantidad) {
            const producto = productosTemporales[index];
            if (!producto) return;

            nuevaCantidad = parseInt(nuevaCantidad);
            if (isNaN(nuevaCantidad) || nuevaCantidad < 1) nuevaCantidad = 1;

            producto.cantidad = nuevaCantidad;
            producto.subtotal = producto.cantidad * producto.precio_unitario;

            actualizarTablaProductos();
        }

        function actualizarPrecio(index, nuevoPrecio) {
            const producto = productosTemporales[index];
            if (!producto) return;

            nuevoPrecio = parseFloat(nuevoPrecio);
            if (isNaN(nuevoPrecio) || nuevoPrecio < 0) nuevoPrecio = 0;

            producto.precio_unitario = nuevoPrecio;
            producto.subtotal = producto.cantidad * producto.precio_unitario;

            actualizarTablaProductos();
        }

        function actualizarTablaProductos() {
            const tbody = $('#tbodyProductosTemp');
            let total = 0;

            if (productosTemporales.length === 0) {
                tbody.html('<tr class="text-muted"><td colspan="5" class="text-center">No hay productos agregados</td></tr>');
                $('#totalTempPago').text('$0.00');
                return;
            }

            tbody.empty();
            productosTemporales.forEach((prod, index) => {
                total += prod.subtotal;
                tbody.append(`
                    <tr data-index="${index}">
                        <td>
                            <strong>${prod.producto_descrip}</strong>
                            <br><small class="text-muted">Código: ${prod.producto_codprod || 'N/A'}</small>
                            <input type="hidden" name="productos[${index}][producto_id]" value="${prod.producto_id}">
                            <input type="hidden" name="productos[${index}][producto_codprod]" value="${prod.producto_codprod}">
                            <input type="hidden" name="productos[${index}][producto_descrip]" value="${prod.producto_descrip}">
                        </td>
                        <td>${prod.cantidad}<input type="hidden" name="productos[${index}][cantidad]" value="${prod.cantidad}"></td>
                        <td>$${prod.precio_unitario.toFixed(2)}<input type="hidden" name="productos[${index}][precio_unitario]" value="${prod.precio_unitario}"></td>
                        <td class="subtotal">$${prod.subtotal.toFixed(2)}</td>
                        <td class="text-center">
                        <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-sm btn-warning" onclick="editarProducto(${index})" title="Editar producto">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="eliminarProducto(${index})" title="Eliminar producto">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `);
            });
            $('#totalTempPago').text('$' + total.toFixed(2));
        }

        function eliminarProducto(index) {
            if (confirm('¿Está seguro de eliminar este producto?')) {
                productosTemporales.splice(index, 1);
                actualizarTablaProductos();

                if (productosTemporales.length === 0) {
                    $('#btnGuardarPago').prop('disabled', true);
                }
                mostrarToast('Producto eliminado correctamente', 'Éxito', 'success');
            }
        }

        function editarProducto(index) {
            const producto = productosTemporales[index];
            if (!producto) return;

            // Llenar el modal con los datos del producto
            $('#producto_id_temp').val(producto.producto_id);
            $('#producto_codprod_temp').val(producto.producto_codprod);
            $('#producto_descrip_temp').val(producto.producto_descrip);
            $('#buscarProductoTemp').val(producto.producto_descrip);
            $('#cantidad_temp').val(producto.cantidad);
            $('#precio_temp').val(producto.precio_unitario);

            // Guardar índice para actualizar después
            window.productoEditIndex = index;

            // Cambiar el título y botón del modal
            $('#modalAgregarProductoTemp .modal-title').html('<i class="bi bi-pencil me-2"></i>Editar Producto');
            $('#btnAgregarProductoTemp').html('<i class="bi bi-save me-1"></i>Actualizar Producto');
            $('#btnAgregarProductoTemp').off('click').on('click', function() {
                actualizarProductoExistente();
            });

            $('#modalAgregarProductoTemp').modal('show');
        }

        function actualizarProductoExistente() {
            const index = window.productoEditIndex;
            if (index === undefined) return;

            const producto = productosTemporales[index];
            if (!producto) return;

            const nuevaCantidad = parseInt($('#cantidad_temp').val());
            const nuevoPrecio = parseFloat($('#precio_temp').val());

            if (isNaN(nuevaCantidad) || nuevaCantidad < 1) {
                mostrarToast('Cantidad inválida', 'Error', 'danger');
                return;
            }
            if (isNaN(nuevoPrecio) || nuevoPrecio < 0) {
                mostrarToast('Precio inválido', 'Error', 'danger');
                return;
            }

            producto.cantidad = nuevaCantidad;
            producto.precio_unitario = nuevoPrecio;
            producto.subtotal = producto.cantidad * producto.precio_unitario;

            actualizarTablaProductos();
            $('#modalAgregarProductoTemp').modal('hide');

            // Restaurar modal a su estado original
            restaurarModalProducto();

            mostrarToast('Producto actualizado correctamente', 'Éxito', 'success');
        }

        function restaurarModalProducto() {
            $('#modalAgregarProductoTemp .modal-title').html('<i class="bi bi-plus-circle me-2"></i>Agregar Producto');
            $('#btnAgregarProductoTemp').html('<i class="bi bi-plus-circle me-1"></i>Agregar Producto');
            $('#btnAgregarProductoTemp').off('click').on('click', function() {
                agregarProductoTemporal();
            });
            limpiarFormularioProducto();
            window.productoEditIndex = undefined;
        }

        function limpiarFormularioProducto() {
            $('#producto_id_temp').val('');
            $('#producto_codprod_temp').val('');
            $('#producto_descrip_temp').val('');
            $('#buscarProductoTemp').val('');
            $('#cantidad_temp').val('1');
            $('#precio_temp').val('');
            $('#resultadosProductosTemp').hide();
        }

        function calcularTotal() {
            let total = 0;
            productosTemporales.forEach(prod => { total += prod.subtotal; });
            return total;
        }

        function guardarPago() {
            const proveedor = $('#codprov').val();
            const fechaPago = $('#fecha_pago').val();

            if (!proveedor) {
                mostrarToast('Seleccione un proveedor', 'Error', 'danger');
                return;
            }
            if (!fechaPago) {
                mostrarToast('Seleccione la fecha de pago', 'Error', 'danger');
                return;
            }
            if (productosTemporales.length === 0) {
                mostrarToast('Agregue al menos un producto', 'Error', 'danger');
                return;
            }

            mostrarLoading(true);

            fetch('{{ route("pagos-proveedores.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    fecha_pago: fechaPago,
                    codprov: proveedor,
                    notas: $('#notas').val(),
                    monto_total: calcularTotal(),
                    productos: productosTemporales
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast(data.message, 'Éxito', 'success');
                        $('#modalPago').modal('hide');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        mostrarToast(data.error || 'Error al guardar', 'Error', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarToast('Error de conexión', 'Error', 'danger');
                })
                .finally(() => mostrarLoading(false));
        }

        // Limpiar al cerrar el modal
        $('#modalPago').on('hidden.bs.modal', function () {
            productosTemporales = [];
            $('#formCrearPago')[0]?.reset();
            $('#seccionProductos').hide();
            $('#btnGuardarPago').prop('disabled', true);
            if ($('#codprov').length && $.fn.select2) {
                $('#codprov').val('').trigger('change');
            }
            actualizarTablaProductos();
        });

        // ========== FUNCIONES PARA VER/EDITAR/ELIMINAR ==========

        function verPago(id) {
            pagoActualId = id;
            document.getElementById('modalTitleText').innerText = 'Detalles del Pago';
            document.getElementById('modalBody').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Cargando...</p></div>';
            modalPago.show();

            fetch(`/pagos-proveedores/${id}`)
                .then(r => r.json())
                .then(data => document.getElementById('modalBody').innerHTML = data.html)
                .catch(() => mostrarToast('Error al cargar detalles', 'Error', 'danger'));
        }

        function editarPago(id) {
            pagoActualId = id;
            document.getElementById('modalTitleText').innerText = 'Editar Pago';
            document.getElementById('modalBody').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Cargando...</p></div>';
            modalPago.show();

            fetch(`/pagos-proveedores/${id}/edit`)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('modalBody').innerHTML = data.html;
                    // Inicializar Select2 después de cargar el HTML
                    setTimeout(() => {
                        inicializarSelect2Edicion();
                        inicializarEventosEdicionPago();
                        inicializarTooltips();
                    }, 100);
                })
                .catch(() => mostrarToast('Error al cargar formulario', 'Error', 'danger'));
        }

        function inicializarEventosEdicionPago() {
            // Enviar formulario de edición
            $('#formEditarPago').off('submit').on('submit', function(e) {
                e.preventDefault();
                guardarEdicionPago();
            });
        }

        function inicializarSelect2Edicion() {
            if ($('#codprov_edit').length && $.fn.select2) {
                $('#codprov_edit').select2({
                    dropdownParent: $('#modalPago'),
                    placeholder: 'Seleccione un proveedor...',
                    width: '100%'
                });
            }
        }

        function guardarEdicionPago() {
            const pagoId = pagoActualId;
            const fechaPago = $('#fecha_pago_edit').val();
            const proveedor = $('#codprov_edit').val();
            const notas = $('#notas_edit').val();

            if (!fechaPago) {
                mostrarToast('Seleccione la fecha de pago', 'Error', 'danger');
                return;
            }
            if (!proveedor) {
                mostrarToast('Seleccione un proveedor', 'Error', 'danger');
                return;
            }

            mostrarLoading(true);

            fetch(`/pagos-proveedores/${pagoId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    fecha_pago: fechaPago,
                    codprov: proveedor,
                    notas: notas
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast(data.message, 'Éxito', 'success');
                        $('#modalPago').modal('hide');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        mostrarToast(data.error || 'Error al actualizar', 'Error', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarToast('Error de conexión', 'Error', 'danger');
                })
                .finally(() => mostrarLoading(false));
        }

        function eliminarPago(id) {
            pagoActualId = id;
            document.getElementById('confirmacionMensaje').innerText = '¿Eliminar este pago? Esta acción no se puede deshacer.';
            accionConfirmar = () => {
                fetch(`/pagos-proveedores/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            modalConfirmacion.hide();
                            mostrarToast(data.message, 'Éxito', 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else mostrarToast(data.error || 'Error', 'Error', 'danger');
                    })
                    .catch(() => mostrarToast('Error de conexión', 'Error', 'danger'));
            };
            modalConfirmacion.show();
        }

        function mostrarModalAgregarProducto() {
            if (!$('#codprov').val()) {
                mostrarToast('Primero seleccione un proveedor', 'Atención', 'warning');
                return;
            }
            // Resetear modo edición para creación normal
            window.modoEdicion = false;
            window.pagoEditandoId = null;
            limpiarFormularioProducto();
            $('#modalAgregarProductoTemp').modal('show');
        }


        function registrarDespacho(id) {
            pagoActualId = id;
            document.getElementById('modalTitleText').innerText = 'Registrar Recepción de Motos';
            document.getElementById('modalBody').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Cargando...</p></div>';
            modalPago.show();

            fetch(`/pagos-proveedores/${id}/despachos/form`)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('modalBody').innerHTML = data.html;
                    setTimeout(() => {

                        inicializarEventosDespacho();
                        inicializarTooltips();
                    }, 100);
                })
                .catch(() => mostrarToast('Error al cargar formulario', 'Error', 'danger'));
        }

        function inicializarEventosDespacho() {
            // Enviar formulario de despacho
            $('#formRegistrarDespacho').off('submit').on('submit', function(e) {
                e.preventDefault();
                guardarDespacho();
            });

            // Prevenir que el Enter envíe el formulario
            $('#formRegistrarDespacho').off('keypress').on('keypress', function(e) {
                if (e.which === 13) { // 13 es el código de Enter
                    e.preventDefault();
                    return false;
                }
            });

            // También prevenir Enter específicamente en los inputs de cantidad
            $('.cantidad-recibir').off('keypress').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    return false;
                }
            });

            // Prevenir Enter en el input de número de guía
            $('#numero_guia').off('keypress').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    return false;
                }
            });

            // Prevenir Enter en el textarea de notas
            $('#notas_despacho').off('keypress').on('keypress', function(e) {
                if (e.which === 13 && !e.shiftKey) {
                    e.preventDefault();
                    return false;
                }
            });

            // Seleccionar todos los productos (checkbox)
            $('#seleccionarTodos').off('change').on('change', function() {
                const isChecked = $(this).prop('checked');
                $('.checkbox-producto').prop('checked', isChecked);
                if (isChecked) {
                    // Si selecciona todos, poner la cantidad máxima en cada producto
                    $('.checkbox-producto').each(function() {
                        const detalleId = $(this).val();
                        const maxCantidad = $(`#cantidad_${detalleId}`).attr('max');
                        $(`#cantidad_${detalleId}`).val(maxCantidad);
                    });
                } else {
                    // Si deselecciona todos, poner cantidad 0 en cada producto
                    $('.cantidad-recibir').val(0);
                }
            });

            // Cuando cambia la cantidad de un producto
            $('.cantidad-recibir').off('change').on('change', function() {
                const max = parseInt($(this).attr('max')) || 0;
                let valor = parseInt($(this).val()) || 0;
                if (valor > max) valor = max;
                if (valor < 0) valor = 0;
                $(this).val(valor);

                // Obtener el checkbox correspondiente
                const detalleId = $(this).attr('id').replace('cantidad_', '');
                const checkbox = $(`.checkbox-producto[value="${detalleId}"]`);

                // Si la cantidad es mayor a 0, seleccionar el checkbox; si es 0, deseleccionar
                if (valor > 0) {
                    checkbox.prop('checked', true);
                } else {
                    checkbox.prop('checked', false);
                }
            });

            // Cuando se selecciona/deselecciona un checkbox individualmente
            $('.checkbox-producto').off('change').on('change', function() {
                const detalleId = $(this).val();
                const cantidadInput = $(`#cantidad_${detalleId}`);
                const maxCantidad = parseInt(cantidadInput.attr('max')) || 0;

                if ($(this).prop('checked')) {
                    // Si se selecciona el checkbox y la cantidad es 0, poner la cantidad máxima
                    if (parseInt(cantidadInput.val()) === 0) {
                        cantidadInput.val(maxCantidad);
                    }
                } else {
                    // Si se deselecciona el checkbox, poner cantidad 0
                    cantidadInput.val(0);
                }
            });
        }

        function guardarDespacho() {
            const pagoId = pagoActualId;
            const fechaDespacho = $('#fecha_despacho').val();
            const numeroGuia = $('#numero_guia').val();
            const notas = $('#notas_despacho').val();

            // Obtener productos seleccionados
            const productos = [];
            $('.checkbox-producto:checked').each(function() {
                const detalleId = $(this).val();
                const cantidad = $(`#cantidad_${detalleId}`).val();
                if (cantidad > 0) {
                    productos.push({
                        detalle_id: detalleId,
                        cantidad: parseInt(cantidad)
                    });
                }
            });

            if (!fechaDespacho) {
                mostrarToast('Seleccione la fecha de recepción', 'Error', 'danger');
                return;
            }

            if (productos.length === 0) {
                mostrarToast('Seleccione al menos un producto para recibir', 'Error', 'danger');
                return;
            }

            mostrarLoading(true);

            fetch(`/pagos-proveedores/${pagoId}/despachos`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    fecha_despacho: fechaDespacho,
                    numero_guia: numeroGuia,
                    notas: notas,
                    productos: productos
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast(data.message, 'Éxito', 'success');
                        $('#modalPago').modal('hide');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        mostrarToast(data.error || 'Error al registrar recepción', 'Error', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarToast('Error de conexión', 'Error', 'danger');
                })
                .finally(() => mostrarLoading(false));
        }

        function verHistorialDespachos(id) {
            pagoActualId = id;
            document.getElementById('modalTitleText').innerText = 'Historial de Recepciones';
            document.getElementById('modalBody').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Cargando historial...</p></div>';
            modalPago.show();

            fetch(`/pagos-proveedores/${id}/despachos`)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('modalBody').innerHTML = data.html;
                    setTimeout(() => {
                        inicializarTooltips();
                    }, 100);
                })
                .catch(() => mostrarToast('Error al cargar historial', 'Error', 'danger'));
        }

        function agregarComprobante(id) {
            pagoActualId = id;
            document.getElementById('modalTitleText').innerText = 'Agregar Comprobante de Pago';
            document.getElementById('modalBody').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Cargando...</p></div>';
            modalPago.show();

            fetch(`/pagos-proveedores/${id}/comprobantes/form`)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('modalBody').innerHTML = data.html;
                    setTimeout(() => {
                        inicializarEventosComprobante();
                        inicializarTooltips();
                    }, 100);
                })
                .catch(() => mostrarToast('Error al cargar formulario', 'Error', 'danger'));
        }

        function inicializarEventosComprobante() {
            // Enviar formulario de comprobante
            $('#formAgregarComprobante').off('submit').on('submit', function(e) {
                e.preventDefault();
                guardarComprobante();
            });

            // Previsualizar imagen seleccionada
            $('#archivo_comprobante').off('change').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Mostrar nombre y tamaño
                    const tamanoMB = (file.size / (1024 * 1024)).toFixed(2);
                    $('#nombreArchivo').html(`${file.name} <small class="text-muted">(${tamanoMB} MB)</small>`);

                    // Advertencia si es muy grande
                    if (file.size > 5 * 1024 * 1024) {
                        $('#nombreArchivo').append('<br><span class="text-warning">⚠️ El archivo será comprimido automáticamente</span>');
                    }

                    // Si es imagen, mostrar preview
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            $('#previewImagen').attr('src', event.target.result).show();
                        };
                        reader.readAsDataURL(file);
                    } else {
                        $('#previewImagen').hide();
                        $('#previewImagen').after('<div class="text-center p-3 border rounded"><i class="bi bi-file-pdf fa-3x text-danger"></i><p>PDF - ' + tamanoMB + ' MB</p></div>');
                    }
                } else {
                    $('#previewImagen').hide();
                    $('#nombreArchivo').text('');
                }
            });

            // Calcular total automático al cambiar monto
            $('#monto_comprobante').off('input').on('input', function() {
                const monto = parseFloat($(this).val()) || 0;
                $('#totalMonto').text('$' + monto.toFixed(2));
            });
        }

        function guardarComprobante() {
            const pagoId = pagoActualId;
            const formData = new FormData();

            formData.append('tipo_comprobante', $('#tipo_comprobante').val());
            formData.append('numero_comprobante', $('#numero_comprobante').val());
            formData.append('monto', $('#monto_comprobante').val());
            formData.append('fecha_comprobante', $('#fecha_comprobante').val());
            formData.append('notas', $('#notas_comprobante').val());
            formData.append('archivo', $('#archivo_comprobante')[0].files[0]);

            if (!$('#fecha_comprobante').val()) {
                mostrarToast('Seleccione la fecha del comprobante', 'Error', 'danger');
                return;
            }

            if (!$('#monto_comprobante').val() || parseFloat($('#monto_comprobante').val()) <= 0) {
                mostrarToast('Ingrese un monto válido', 'Error', 'danger');
                return;
            }

            mostrarLoading(true);

            fetch(`/pagos-proveedores/${pagoId}/comprobantes`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast(data.message, 'Éxito', 'success');
                        $('#modalPago').modal('hide');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        mostrarToast(data.error || 'Error al guardar comprobante', 'Error', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarToast('Error de conexión', 'Error', 'danger');
                })
                .finally(() => mostrarLoading(false));
        }

        function verComprobantes(id) {
            pagoActualId = id;
            document.getElementById('modalTitleText').innerText = 'Comprobantes de Pago';
            document.getElementById('modalBody').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Cargando comprobantes...</p></div>';
            modalPago.show();

            fetch(`/pagos-proveedores/${id}/comprobantes`)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('modalBody').innerHTML = data.html;
                    setTimeout(() => {
                        inicializarTooltips();
                    }, 100);
                })
                .catch(() => mostrarToast('Error al cargar comprobantes', 'Error', 'danger'));
        }

        function eliminarComprobante(comprobanteId) {
            if (!confirm('¿Está seguro de eliminar este comprobante? Esta acción no se puede deshacer.')) return;

            mostrarLoading(true);

            fetch(`/pagos-proveedores/${pagoActualId}/comprobantes/${comprobanteId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast(data.message, 'Éxito', 'success');
                        // Recargar la lista de comprobantes
                        verComprobantes(pagoActualId);
                    } else {
                        mostrarToast(data.error || 'Error al eliminar', 'Error', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarToast('Error de conexión', 'Error', 'danger');
                })
                .finally(() => mostrarLoading(false));
        }

        function verFotoComprobante(url) {
            $('#modalFotoComprobante .modal-body').html(`
        <div class="text-center">
            <img src="${url}" class="img-fluid" style="max-height: 70vh;" alt="Comprobante">
        </div>
    `);
            $('#modalFotoComprobante').modal('show');
        }

        function asignarAprobacion(id) {
            const numero = prompt('Ingrese el número de aprobación:');
            if (!numero) return;

            mostrarLoading(true);

            fetch(`/pagos-proveedores/${id}/asignar-aprobacion`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ numero_aprobacion: numero })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast(data.message, 'Éxito', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        mostrarToast(data.error || 'Error al asignar', 'Error', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarToast('Error de conexión', 'Error', 'danger');
                })
                .finally(() => mostrarLoading(false));
        }

        function verDetalleFacturadas() {
            document.getElementById('modalTitleText').innerHTML = '<i class="bi bi-receipt me-2"></i>Detalle de Motos Facturadas';
            document.getElementById('modalBody').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Cargando detalle...</p></div>';
            modalPago.show();

            fetch('/pagos-proveedores/detalle-facturadas')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modalBody').innerHTML = data.html;
                    setTimeout(() => {
                        inicializarTooltips();
                    }, 100);
                })
                .catch(() => mostrarToast('Error al cargar detalle', 'Error', 'danger'));
        }

        function editarAprobacion(id, numeroActual) {
            const nuevoNumero = prompt('Editar número de aprobación:', numeroActual);
            if (!nuevoNumero) return;
            if (nuevoNumero === numeroActual) return;

            mostrarLoading(true);

            fetch(`/pagos-proveedores/${id}/editar-aprobacion`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ numero_aprobacion: nuevoNumero })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast(data.message, 'Éxito', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        mostrarToast(data.error || 'Error al editar', 'Error', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarToast('Error de conexión', 'Error', 'danger');
                })
                .finally(() => mostrarLoading(false));
        }


        $(document).ready(function() {

            inicializarTooltips();

            // Evento para el botón de agregar producto en el modal temporal
            $('#btnAgregarProductoTemp').off('click').on('click', function() {
                console.log('Botón agregar producto clickeado. Modo edición:', window.modoEdicion);
                if (window.modoEdicion === true) {
                    agregarProductoTemporalEdit();
                } else {
                    agregarProductoTemporal();
                }
            });

            // Cuando se abre el modal temporal
            $('#modalAgregarProductoTemp').off('show.bs.modal').on('show.bs.modal', function() {
                console.log('Modal agregar producto abierto. Modo edición:', window.modoEdicion);
            });
        });


        function abrirModalFechas() {
            $('#modalFechas').modal('show');
        }

        function consultarFechas() {
            let desde = $('#fechaDesde').val();
            let hasta = $('#fechaHasta').val();

            if (!desde || !hasta) {
                alert('Selecciona ambas fechas');
                return;
            }

            $('#resultadoFechas').show();
            $('#totalmotospagadas').html('Consultando');
            $('#totalmontopagado').html('Consultando');

            $.ajax({
                url: '/motosporfecha',
                method: 'GET',
                hheaders: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: { desde, hasta },
                success: function(res) {
                    $('#totalmotospagadas').html(`${res.total_motos}`);
                    $('#totalmontopagado').html(`$${res.total_monto}`);
                }
            });
        }

            document.getElementById('confirmarAccionBtn')?.addEventListener('click', () => accionConfirmar && accionConfirmar());
    </script>
@endsection
