@extends('layouts.master')
@section('title')
    Ingresar Transferencia
@endsection
@section('css')
    <link rel="stylesheet" href="{{ URL::asset('build/libs/dropzone/dropzone.css') }}" type="text/css">
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        /* Estilos para autocomplete */
        #categoriaSuggestions {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: white;
            z-index: 1050;
        }

        #categoriaSuggestions .list-group-item {
            padding: 8px 12px;
            cursor: pointer;
            border-left: none;
            border-right: none;
        }

        #categoriaSuggestions .list-group-item:first-child {
            border-top: none;
        }

        #categoriaSuggestions .list-group-item:last-child {
            border-bottom: none;
        }

        #categoriaSuggestions .list-group-item:hover,
        #categoriaSuggestions .list-group-item.active {
            background-color: #e7f5ff;
            color: #0072c5;
        }

        #categoriaSuggestions .list-group-item.active {
            background-color: #0072c5;
            color: white;
        }

        .categoria-badge {
            transition: all 0.2s;
        }

        .categoria-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .preview-container {
            position: relative;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s ease;
            cursor: pointer;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .preview-container:hover {
            border-color: #0072c5;
            background: #e9ecef;
        }

        .preview-container.has-image {
            border: 2px solid #28a745;
            background: #fff;
        }

        .preview-image {
            max-width: 100%;
            max-height: 300px;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .preview-placeholder {
            color: #6c757d;
        }

        .preview-placeholder i {
            font-size: 48px;
            margin-bottom: 10px;
            color: #adb5bd;
        }

        .remove-image {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            z-index: 10;
        }

        .remove-image:hover {
            background: #c82333;
            transform: scale(1.1);
        }

        .is-invalid {
            border-color: #dc3545 !important;
            padding-right: calc(1.5em + 0.75rem) !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right calc(0.375em + 0.1875rem) center !important;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
        }

        .alertatransferencia {
            font-size: 0.9rem;
            margin-top: 5px;
            display: block;
        }

        .ri-spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-overlay.active {
            display: flex;
        }

        .loading-spinner {
            background: white;
            padding: 20px 40px;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }

        .validation-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .validation-badge.valid {
            background: #d4edda;
            color: #155724;
        }

        .validation-badge.invalid {
            background: #f8d7da;
            color: #721c24;
        }

        .form-section-title {
            font-size: 16px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #0072c5;
        }

        .file-info {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }

        .tipo-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            margin-right: 5px;
        }
        .tipo-venta { background: #d4edda; color: #155724; }
        .tipo-pago { background: #cce5ff; color: #004085; }
        .tipo-ahorro { background: #fff3cd; color: #856404; }
        .tipo-proveedor { background: #d1e7dd; color: #0f5132; }
        .tipo-gasto { background: #f8d7da; color: #721c24; }
        .tipo-otro { background: #e2e3e5; color: #383d41; }

        #numero.searching {
            border-color: #ffc107 !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23ffc107' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='10'%3E%3C/circle%3E%3Cpath d='M12 6v6l4 2'%3E%3C/path%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right calc(0.375em + 0.1875rem) center !important;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
        }
        /* Estilos para sugerencias de números */
        #numeroSuggestions {
            max-height: 250px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: white;
            z-index: 1050;
        }

        #numeroSuggestions .list-group-item {
            padding: 10px 12px;
            cursor: pointer;
            border-left: none;
            border-right: none;
            transition: all 0.2s;
        }

        #numeroSuggestions .list-group-item:first-child {
            border-top: none;
        }

        #numeroSuggestions .list-group-item:last-child {
            border-bottom: none;
        }

        #numeroSuggestions .list-group-item:hover {
            background-color: #e7f5ff;
        }

        #numeroSuggestions .list-group-item.active {
            background-color: #0072c5;
            color: white;
        }

        .suggestion-highlight {
            font-weight: bold;
            background-color: #ffc107;
            padding: 0 2px;
            border-radius: 3px;
        }

        .suggestion-badge {
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 12px;
            background: #e9ecef;
            margin-left: 8px;
        }

        .suggestion-monto {
            font-size: 12px;
            color: #6c757d;
            margin-left: 8px;
        }
    </style>
@endsection

@section('content')
    <x-breadcrumb title="Ingresar Nueva Transferencia" pagetitle="Transferencias" />

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner text-center">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <h6 class="mb-0">Procesando transferencia...</h6>
            <small class="text-muted" id="loadingMessage">Verificando datos</small>
        </div>
    </div>

    <form id="createtransf-form" autocomplete="off" class="needs-validation" method="post" novalidate
          action="{{route('transferencias.store')}}" enctype="multipart/form-data">
        @method('POST')
        @csrf

        <div class="row">
            <div class="col-xl-8 col-lg-8">
                <!-- Datos principales -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0 text-white">
                            <i class="ri-information-line me-2"></i>Información de la Transferencia
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Sucursal -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Sucursal <span class="text-danger">*</span></label>
                            <select required class="form-control" name="fksucursal" id="fksucursal">
                                <option value="">Seleccione una sucursal</option>
                                @foreach($sucursales as $sucursal)
                                    <option value="{{$sucursal->id}}">{{$sucursal->descrip}}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                Por favor, seleccione una sucursal
                            </div>
                        </div>

                        <!-- Campos ocultos que se muestran al seleccionar sucursal -->
                        <div id="datosTransferencia" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Número de Transferencia <span class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="text" class="form-control" id="numero" name="numero"
                                                   placeholder="Ej: 123456" required maxlength="50" autocomplete="off"
                                                   onpaste="return false;"
                                                   ondrop="return false;"
                                                   oncut="return false;"
                                                   oncopy="return false;">
                                            <!-- Sugerencias de números existentes -->
                                            <div class="list-group position-absolute w-100 shadow"
                                                 id="numeroSuggestions"
                                                 style="z-index: 1000; max-height: 250px; overflow-y: auto; display: none;">
                                            </div>
                                        </div>
                                        <div class="invalid-feedback">
                                            El número de transferencia es obligatorio
                                        </div>
                                        <small class="text-muted" id="numeroValidation"></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Banco <span class="text-danger">*</span></label>
                                        <div id="bancosucursaldiv">
                                            <select class="form-control" name="bancosucursal" id="bancosucursal" required>
                                                <option value="">Primero seleccione una sucursal</option>
                                            </select>
                                        </div>
                                        <div class="invalid-feedback">
                                            Debe seleccionar un banco
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Fecha <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="fecha" name="fecha"
                                               data-provider="flatpickr" data-date-format="d/m/Y"
                                               placeholder="DD/MM/AAAA" required>
                                        <div class="invalid-feedback">
                                            La fecha es obligatoria
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Monto <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text" id="monedaSymbol">Bs</span>
                                            <input type="text" class="form-control text-end" id="monto"
                                                   name="monto" placeholder="0.00" required>
                                        </div>
                                        <div class="invalid-feedback">
                                            El monto debe ser mayor a 0
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Titular/Cliente <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="titular" name="titular"
                                               placeholder="Nombre completo del titular" required>
                                        <div class="invalid-feedback">
                                            El titular es obligatorio
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Moneda</label>
                                        <input type="text" class="form-control" id="monedaDisplay" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Observación</label>
                                <textarea class="form-control" name="observacion" id="observacion"
                                          rows="2" placeholder="Información adicional (opcional)"></textarea>
                            </div>

                            <!-- NUEVOS CAMPOS: Tipo y Categoría -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="form-section-title">Clasificación de la Transferencia</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Tipo de Transferencia <span class="text-danger">*</span></label>
                                            @if(Auth::user() and auth()->user()->can('menu_transferencias_aprobar'))
                                                <select class="form-control" name="tipo" id="tipo" required>
                                                    <option value="">Seleccione un tipo</option>
                                                    <option value="venta" data-color="success" selected>💰 Venta / Cobranza</option>
                                                    <option value="pago" data-color="info">💸 Pago General</option>
                                                    <option value="ahorro" data-color="warning">🏦 Ahorro</option>
                                                    <option value="proveedor" data-color="primary">📦 Pago a Proveedor</option>
                                                    <option value="gasto" data-color="danger">🧾 Gasto</option>
                                                    <option value="otro" data-color="secondary">📌 Otro</option>
                                                </select>
                                            @else
                                                <select class="form-control" name="tipo" id="tipo" required>
                                                    <option value="venta" data-color="success" selected>💰 Venta / Cobranza</option>
                                                </select>
                                            @endif
                                        <div class="invalid-feedback">
                                            Debe seleccionar un tipo de transferencia
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 position-relative">
                                        <label class="form-label fw-bold">Categoría / Etiqueta</label>
                                        <div class="position-relative">
                                            <input type="text" class="form-control" name="categoria" id="categoria"
                                                   placeholder="Ej: Ciclismo, Ahorros, Proveedor X..."
                                                   autocomplete="off">
                                            <div class="spinner-border spinner-border-sm text-primary position-absolute"
                                                 style="right: 10px; top: 10px; display: none;" id="categoriaSpinner"></div>
                                        </div>
                                        <small class="text-muted">Para agrupar y filtrar transferencias</small>

                                        <!-- Sugerencias de categorías -->
                                        <div class="list-group position-absolute w-100 shadow"
                                             id="categoriaSuggestions"
                                             style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Campos específicos según tipo -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3" id="campoReferencia">
                                        <label class="form-label fw-bold">Número de Referencia</label>
                                        <input type="text" class="form-control" name="referencia" id="referencia"
                                               placeholder="Factura, contrato, orden de compra...">
                                        <small class="text-muted">Documento asociado</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3" id="campoProveedor" style="display: none;">
                                        <label class="form-label fw-bold">Proveedor <span class="text-danger" id="proveedorRequired" style="display: none;">*</span></label>
                                        <select class="form-control" name="proveedor_id" id="proveedor_id">
                                            <option value="">Seleccione un proveedor</option>
                                            @php
                                                // Aquí puedes cargar la lista de proveedores si tienes un modelo Proveedor
                                                // $proveedores = App\Models\Proveedor::all();
                                            @endphp
                                            <option value="1">Proveedor 1</option>
                                            <option value="2">Proveedor 2</option>
                                        </select>
                                    </div>
                                    <div class="mb-3" id="campoAhorro" style="display: none;">
                                        <label class="form-label fw-bold">Ahorro <span class="text-danger" id="ahorroRequired" style="display: none;">*</span></label>
                                        <select class="form-control" name="ahorro_id" id="ahorro_id">
                                            <option value="">Seleccione un ahorro</option>
                                            <option value="1">Ahorro Ciclismo</option>
                                            <option value="2">Ahorro Vacaciones</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview del tipo seleccionado -->
                            <div class="row mt-2">
                                <div class="col-12">
                                    <div id="tipoPreview" style="display: none;" class="p-2 rounded">
                                        <small>Tipo seleccionado: <span id="tipoPreviewText"></span></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-4">
                <!-- Preview de imagen -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0 text-white">
                            <i class="ri-image-line me-2"></i>Captura de Transferencia
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="preview-container" id="previewContainer" onclick="document.getElementById('imagen').click();">
                            <input type="file" class="d-none" id="imagen" name="imagen" accept="image/*">
                            <div id="previewPlaceholder" class="preview-placeholder">
                                <i class="ri-image-add-line"></i>
                                <p class="mb-1">Haz clic para seleccionar una imagen</p>
                                <small class="text-muted">JPG, PNG o GIF (max. 5MB)</small>
                            </div>
                            <div id="previewContent" style="display: none;"></div>
                            <button type="button" class="remove-image" id="removeImage" onclick="eliminarPreview(event)" style="display: none;">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                        <div class="file-info mt-2 text-center" id="fileInfo"></div>

                        <!-- Validaciones en tiempo real -->
                        <div class="mt-3" id="validacionesContainer" style="display: none;">
                            <div class="form-section-title">Validaciones</div>
                            <div class="d-flex flex-column gap-2">
                                <span class="validation-badge" id="validNumero">
                                    <i class="ri-checkbox-circle-line me-1"></i> Número de transferencia
                                </span>
                                <span class="validation-badge" id="validMonto">
                                    <i class="ri-checkbox-circle-line me-1"></i> Monto
                                </span>
                                <span class="validation-badge" id="validFecha">
                                    <i class="ri-checkbox-circle-line me-1"></i> Fecha
                                </span>
                                <span class="validation-badge" id="validTitular">
                                    <i class="ri-checkbox-circle-line me-1"></i> Titular
                                </span>
                                <span class="validation-badge" id="validBanco">
                                    <i class="ri-checkbox-circle-line me-1"></i> Banco
                                </span>
                                <span class="validation-badge" id="validTipo">
                                    <i class="ri-checkbox-circle-line me-1"></i> Tipo
                                </span>
                                <span class="validation-badge" id="validImagen">
                                    <i class="ri-checkbox-circle-line me-1"></i> Imagen
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumen -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0 text-white">
                            <i class="ri-file-copy-line me-2"></i>Resumen
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td width="40%">Número:</td>
                                <td class="fw-bold" id="resumenNumero">---</td>
                            </tr>
                            <tr>
                                <td>Banco:</td>
                                <td class="fw-bold" id="resumenBanco">---</td>
                            </tr>
                            <tr>
                                <td>Fecha:</td>
                                <td class="fw-bold" id="resumenFecha">---</td>
                            </tr>
                            <tr>
                                <td>Monto:</td>
                                <td class="fw-bold text-success" id="resumenMonto">---</td>
                            </tr>
                            <tr>
                                <td>Titular:</td>
                                <td class="fw-bold" id="resumenTitular">---</td>
                            </tr>
                            <tr>
                                <td>Tipo:</td>
                                <td class="fw-bold" id="resumenTipo">---</td>
                            </tr>
                            <tr>
                                <td>Categoría:</td>
                                <td class="fw-bold" id="resumenCategoria">---</td>
                            </tr>
                        </table>

                        <div class="alert alert-info mt-3 mb-0" id="alertaDuplicado" style="display: none;">
                            <i class="ri-error-warning-line me-1"></i>
                            <span id="mensajeDuplicado"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="hstack gap-2 justify-content-end">
                            <span class="alertatransferencia text-danger me-auto"></span>
                            <a href="{{ route('reportetransferencias') }}" class="btn btn-secondary">
                                <i class="ri-close-line me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success" id="btnGuardar">
                                <i class="ri-save-line me-1"></i> Guardar Transferencia
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal de confirmación -->
    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white">¿Confirmar guardado?</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que desea guardar esta transferencia?</p>
                    <div class="alert alert-warning">
                        <i class="ri-information-line me-1"></i>
                        Verifique que los datos sean correctos antes de continuar.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="confirmarGuardado">Sí, guardar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- SweetAlert2 -->
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>

        let timeoutNumeroPredictivo   = null;
        let busquedaNumerosEnProgreso = false;

        let timeoutBusqueda     = null;
        let busquedaEnProgreso  = false;
        let ultimoNumeroBuscado = '';

        // Variable para controlar el estado de validación
        let datosValidos = {
            sucursal: false,
            banco: false,
            numero: false,
            monto: false,
            fecha: false,
            titular: false,
            tipo: false,
            imagen: false
        };


        let transferenciaExistente = false;
        let bancoActual = null;

        // Autocomplete para categorías
        const categoriaInput = document.getElementById('categoria');
        const categoriaSuggestions = document.getElementById('categoriaSuggestions');
        const categoriaSpinner = document.getElementById('categoriaSpinner');
        let categoriaTimeout;

        function buscarNumerosSimilares(query) {
            if (query.length < 2) {
                ocultarSugerenciasNumeros();
                return;
            }

            // Cancelar búsqueda anterior
            if (timeoutNumeroPredictivo) {
                clearTimeout(timeoutNumeroPredictivo);
            }

            // Mostrar indicador de carga en el campo
            const numeroInput = document.getElementById('numero');
            numeroInput.classList.add('searching');

            timeoutNumeroPredictivo = setTimeout(() => {
                fetch('/transferencias/buscar-numeros-similares', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        numero: query,
                        limit: 8 // Máximo 8 sugerencias
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        numeroInput.classList.remove('searching');

                        if (data.numeros && data.numeros.length > 0) {
                            mostrarSugerenciasNumeros(data.numeros, query);
                        } else {
                            ocultarSugerenciasNumeros();
                        }
                    })
                    .catch(error => {
                        console.error('Error al buscar números similares:', error);
                        numeroInput.classList.remove('searching');
                        ocultarSugerenciasNumeros();
                    });
            }, 300); // 300ms de debounce para predictivo
        }

        function ocultarSugerenciasNumeros() {
            const suggestionsDiv = document.getElementById('numeroSuggestions');
            if (suggestionsDiv) {
                suggestionsDiv.style.display = 'none';
                suggestionsDiv.innerHTML = '';
            }
        }

        window.seleccionarNumeroSugerido = function(numero) {
            const input = document.getElementById('numero');
            input.value = numero;
            ocultarSugerenciasNumeros();

            // Disparar validación
            validarNumero();

            // Opcional: Mostrar alerta informativa
            Swal.fire({
                icon: 'info',
                title: 'Número existente',
                text: `El número ${numero} ya existe en el sistema. Verifique los datos antes de continuar.`,
                timer: 3000,
                showConfirmButton: false
            });
        };

        // Función para usar el número actual
        window.usarNumeroActual = function() {
            ocultarSugerenciasNumeros();
            // Forzar validación como número nuevo
            const badge = document.getElementById('validNumero');
            badge.className = 'validation-badge valid';
            badge.innerHTML = '<i class="ri-checkbox-circle-fill me-1"></i> Número disponible';
            datosValidos.numero = true;
            transferenciaExistente = false;
            ocultarAlertaDuplicado();
        };

        // Función helper para escapar regex
        function escapeRegex(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        // Modificar el evento input del número para incluir búsqueda predictiva
        // Reemplazar el event listener existente de 'numero'
        document.getElementById('numero').addEventListener('input', function(e) {
            const valor = this.value.trim();

            // Búsqueda predictiva (mostrar números similares)
            buscarNumerosSimilares(valor);

            // Validación normal
            validarNumero();
        });

        // Cerrar sugerencias al hacer clic fuera
        document.addEventListener('click', function(event) {
            const numeroInput = document.getElementById('numero');
            const suggestionsDiv = document.getElementById('numeroSuggestions');

            if (!numeroInput.contains(event.target) && !suggestionsDiv.contains(event.target)) {
                ocultarSugerenciasNumeros();
            }
        });

        // Navegación con teclado en sugerencias
        document.getElementById('numero').addEventListener('keydown', function(e) {
            const items = document.querySelectorAll('#numeroSuggestions .list-group-item');
            if (items.length === 0) return;

            const active = document.querySelector('#numeroSuggestions .active');

            // Tecla abajo
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (active) {
                    active.classList.remove('active');
                    const next = active.nextElementSibling;
                    if (next) {
                        next.classList.add('active');
                        next.scrollIntoView({ block: 'nearest' });
                    }
                } else {
                    items[0].classList.add('active');
                }
            }

            // Tecla arriba
            if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (active) {
                    active.classList.remove('active');
                    const prev = active.previousElementSibling;
                    if (prev) {
                        prev.classList.add('active');
                        prev.scrollIntoView({ block: 'nearest' });
                    }
                }
            }

            // Tecla Enter
            if (e.key === 'Enter' && active) {
                e.preventDefault();
                active.click();
            }

            // Tecla Escape
            if (e.key === 'Escape') {
                ocultarSugerenciasNumeros();
            }
        });

        function mostrarSugerenciasNumeros(numeros, queryOriginal) {
            const suggestionsDiv = document.getElementById('numeroSuggestions');

            if (!numeros || numeros.length === 0) {
                suggestionsDiv.style.display = 'none';
                return;
            }

            let html = '';
            numeros.forEach(numero => {
                // Resaltar la parte que coincide
                const numeroValue = numero.numero;
                const regex = new RegExp(`(${escapeRegex(queryOriginal)})`, 'gi');
                const numeroResaltado = numeroValue.replace(regex, '<mark class="suggestion-highlight">$1</mark>');

                // Determinar el estado y color
                let estadoTexto = '';
                let estadoColor = '';
                let estadoIcono = '';
                switch(numero.status) {
                    case 0:
                        estadoTexto = 'Pendiente';
                        estadoColor = 'warning';
                        estadoIcono = 'ri-time-line';
                        break;
                    case 1:
                        estadoTexto = 'Aprobada';
                        estadoColor = 'success';
                        estadoIcono = 'ri-checkbox-circle-line';
                        break;
                    case 2:
                        estadoTexto = 'Rechazada';
                        estadoColor = 'danger';
                        estadoIcono = 'ri-close-circle-line';
                        break;
                    default:
                        estadoTexto = 'Desconocido';
                        estadoColor = 'secondary';
                        estadoIcono = 'ri-question-line';
                }

                html += `
            <a href="#" class="list-group-item list-group-item-action"
               onclick="seleccionarNumeroSugerido('${numeroValue.replace(/'/g, "\\'")}'); return false;">
                <div class="d-flex justify-content-between align-items-center">
                    <div style="font-size: 12px">
                        <strong>${numeroResaltado}</strong>
                        <span class="suggestion-badge">
                            <i class="${estadoIcono} me-1"></i>${estadoTexto}
                        </span>
                    </div>
                    <div>
                        <span class="badge bg-secondary">
                            <i class="ri-bank-line me-1"></i>${numero.banco_nombre || 'N/A'}
                        </span>
                    </div>
                </div>
                <div class="small text-muted mt-1">
                    <i class="ri-money-dollar-circle-line me-1"></i> ${numero.currency || ''} ${parseFloat(numero.monto).toFixed(2)}
                    <i class="ri-calendar-line me-1"></i>            ${numero.fechaformat || numero.fecha}
                    <i class="ri-user-line ms-2 me-1"></i>           ${numero.titular || 'Sin titular'}
                    ${numero.sucursal_nombre ? `<i class="ri-store-line ms-2 me-1"></i>${numero.sucursal_nombre}` : ''}
                </div>
            </a>
        `;
            });

            // Agregar opción "Crear nuevo" al final si el número no existe exactamente
            const existeExactamente = numeros.some(n => n.numero === queryOriginal);
            if (!existeExactamente && queryOriginal.length >= 2) {
                html += `
            <a href="#" class="list-group-item list-group-item-action text-success"
               onclick="usarNumeroActual(); return false;">
                <div class="d-flex align-items-center">
                    <i class="ri-add-circle-line me-2 fs-5"></i>
                    <div>
                        <strong>Usar "${queryOriginal}" como nuevo número</strong>
                        <div class="small">No existe en el sistema, puede crearlo</div>
                    </div>
                </div>
            </a>
        `;
            }

            suggestionsDiv.innerHTML = html;
            suggestionsDiv.style.display = 'block';
        }


        // Función para buscar categorías
        function buscarCategorias(query) {
            if (query.length < 2) {
                categoriaSuggestions.style.display = 'none';
                return;
            }

            categoriaSpinner.style.display = 'block';

            fetch(`/transferencias/categorias/${encodeURIComponent(query)}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(categorias => {
                    categoriaSpinner.style.display = 'none';

                    if (categorias.length > 0) {
                        let html = '';
                        categorias.forEach(categoria => {
                            html += `
                    <a href="#" class="list-group-item list-group-item-action"
                       onclick="seleccionarCategoria('${categoria.replace(/'/g, "\\'")}'); return false;">
                        <i class="ri-price-tag-3-line me-2"></i>${categoria}
                    </a>
                `;
                        });
                        categoriaSuggestions.innerHTML = html;
                        categoriaSuggestions.style.display = 'block';
                    } else {
                        categoriaSuggestions.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    categoriaSpinner.style.display = 'none';
                });
        }

        // Función para seleccionar una categoría
        window.seleccionarCategoria = function(categoria) {
            categoriaInput.value = categoria;
            categoriaSuggestions.style.display = 'none';
            actualizarResumen();
        }

        // Evento input para búsqueda en tiempo real
        categoriaInput.addEventListener('input', function() {
            const query = this.value.trim();

            // Limpiar timeout anterior
            clearTimeout(categoriaTimeout);

            // Ocultar sugerencias si el campo está vacío
            if (query.length === 0) {
                categoriaSuggestions.style.display = 'none';
                return;
            }

            // Esperar 300ms después de que el usuario deje de escribir
            categoriaTimeout = setTimeout(() => {
                buscarCategorias(query);
            }, 300);
        });

        // Ocultar sugerencias al hacer clic fuera
        document.addEventListener('click', function(event) {
            if (!categoriaInput.contains(event.target) && !categoriaSuggestions.contains(event.target)) {
                categoriaSuggestions.style.display = 'none';
            }
        });

        // Permitir navegación con teclado
        categoriaInput.addEventListener('keydown', function(e) {
            const items = categoriaSuggestions.querySelectorAll('.list-group-item');

            if (items.length === 0) return;

            // Tecla abajo
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const active = categoriaSuggestions.querySelector('.active');
                if (active) {
                    active.classList.remove('active');
                    const next = active.nextElementSibling;
                    if (next) {
                        next.classList.add('active');
                        next.scrollIntoView({ block: 'nearest' });
                    }
                } else {
                    items[0].classList.add('active');
                }
            }

            // Tecla arriba
            if (e.key === 'ArrowUp') {
                e.preventDefault();
                const active = categoriaSuggestions.querySelector('.active');
                if (active) {
                    active.classList.remove('active');
                    const prev = active.previousElementSibling;
                    if (prev) {
                        prev.classList.add('active');
                        prev.scrollIntoView({ block: 'nearest' });
                    }
                }
            }

            // Tecla Enter
            if (e.key === 'Enter') {
                const active = categoriaSuggestions.querySelector('.active');
                if (active) {
                    e.preventDefault();
                    window.seleccionarCategoria(active.textContent.trim());
                }
            }

            // Tecla Escape
            if (e.key === 'Escape') {
                categoriaSuggestions.style.display = 'none';
            }
        });


        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar Choices
            if (document.getElementById('fksucursal')) {
                new Choices('#fksucursal', {
                    searchEnabled: true,
                    shouldSort: false,
                    placeholder: true,
                    placeholderValue: 'Seleccione una sucursal'
                });
            }

            // Inicializar Flatpickr para fecha
            if (document.getElementById('fecha')) {
              /*  flatpickr("#fecha", {
                    dateFormat: "d/m/Y",
                    allowInput: true,
                   locale: "es"
                });*/
            }

            // Evento cambio de sucursal
            document.getElementById('fksucursal').addEventListener('change', function() {
                cargarBancos(this.value);
            });

            // Evento cambio de tipo
            document.getElementById('tipo').addEventListener('change', function() {
                validarTipo();
                mostrarCamposPorTipo(this.value);
                actualizarResumen();
                actualizarPreviewTipo(this.value);
            });

            // Evento cambio de categoría
            document.getElementById('categoria').addEventListener('input', function() {
                actualizarResumen();
            });

            // Eventos de validación en tiempo real
            document.getElementById('numero').addEventListener('input', validarNumero);
            document.getElementById('monto').addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9.]/g, '');
                validarMonto();
                actualizarResumen();
            });
            document.getElementById('fecha').addEventListener('change', function() {
                validarFecha();
                actualizarResumen();
            });
            document.getElementById('titular').addEventListener('input', function() {
                validarTitular();
                actualizarResumen();
            });
            document.getElementById('imagen').addEventListener('change', previsualizarImagen);

            // Manejar submit del formulario
            document.getElementById('createtransf-form').addEventListener('submit', function(e) {
                e.preventDefault();

                // Validar todo antes de mostrar confirmación
                if (validarTodo()) {
                    $('#confirmModal').modal('show');
                } else {
                    mostrarErrores();
                }
            });

            // Confirmar guardado
            document.getElementById('confirmarGuardado').addEventListener('click', function() {
                $('#confirmModal').modal('hide');
                mostrarLoading('Guardando transferencia...');
                document.getElementById('createtransf-form').submit();
            });
        });

        // Función para cargar bancos
        function cargarBancos(fksucursal) {
            if (!fksucursal) return;

            const bancosDiv = document.getElementById('bancosucursaldiv');
            bancosDiv.innerHTML = '<div class="text-center"><span class="spinner-border spinner-border-sm"></span> Cargando bancos...</div>';

            fetch('/sascursal/bancos', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ fksucursal: fksucursal })
            })
                .then(response => response.text())
                .then(html => {
                    bancosDiv.innerHTML = html;

                    // Inicializar Choices para bancos
                    if (document.getElementById('bancosucursal')) {
                        new Choices('#bancosucursal', {
                            searchEnabled: true,
                            shouldSort: false,
                            placeholder: true,
                            placeholderValue: 'Seleccione un banco'
                        });

                        document.getElementById('bancosucursal').addEventListener('change', function() {
                            validarBanco();
                            actualizarResumen();
                        });
                    }

                    // Mostrar campos de transferencia
                    document.getElementById('datosTransferencia').style.display = 'block';
                    document.getElementById('validacionesContainer').style.display = 'block';

                    datosValidos.sucursal = true;
                    actualizarResumen();
                })
                .catch(error => {
                    console.error('Error:', error);
                    bancosDiv.innerHTML = '<div class="alert alert-danger">Error al cargar bancos</div>';
                });
        }

        // Función para mostrar campos según tipo
        function mostrarCamposPorTipo(tipo) {
            document.getElementById('campoProveedor').style.display = 'none';
            document.getElementById('campoAhorro').style.display = 'none';
            document.getElementById('proveedorRequired').style.display = 'none';
            document.getElementById('ahorroRequired').style.display = 'none';

            if (tipo === 'proveedor') {
                document.getElementById('campoProveedor').style.display = 'block';
                document.getElementById('proveedorRequired').style.display = 'inline';
            } else if (tipo === 'ahorro') {
                document.getElementById('campoAhorro').style.display = 'block';
                document.getElementById('ahorroRequired').style.display = 'inline';
            }
        }

        // Función para actualizar preview del tipo
        function actualizarPreviewTipo(tipo) {
            const preview = document.getElementById('tipoPreview');
            const previewText = document.getElementById('tipoPreviewText');

            if (tipo) {
                const select = document.getElementById('tipo');
                const option = select.options[select.selectedIndex];
                const text = option.text;
                const color = option.dataset.color || 'secondary';

                preview.style.display = 'block';
                preview.className = `p-2 rounded bg-${color} text-white`;
                previewText.innerText = text;
            } else {
                preview.style.display = 'none';
            }
        }

        // Validación para tipo
        function validarTipo() {
            const select = document.getElementById('tipo');
            const valor = select.value;
            const badge = document.getElementById('validTipo');

            if (valor) {
                badge.className = 'validation-badge valid';
                badge.innerHTML = '<i class="ri-checkbox-circle-fill me-1"></i> Tipo seleccionado';
                datosValidos.tipo = true;
            } else {
                badge.className = 'validation-badge invalid';
                badge.innerHTML = '<i class="ri-error-warning-fill me-1"></i> Seleccione un tipo';
                datosValidos.tipo = false;
            }
        }

        // Validaciones individuales (existentes)
        function validarNumero() {
            const input       = document.getElementById('numero');
            const valor       = input.value.trim();
            const badge       = document.getElementById('validNumero');
            const bancoSelect = document.getElementById('bancosucursal');

            if (valor.length >= 1) { // Cambiado de 3 a 2 para buscar más rápido
                badge.className = 'validation-badge valid';
                badge.innerHTML = '<i class="ri-checkbox-circle-fill me-1"></i> Número válido';
                datosValidos.numero = true;

                verificarExistenciaEnTiempoReal(valor);

                if (bancoSelect && bancoSelect.value) {

                } else {
                    // Mostrar mensaje de que necesita banco
                    const alertaDuplicado = document.getElementById('alertaDuplicado');
                    alertaDuplicado.style.display = 'block';
                    document.getElementById('mensajeDuplicado').innerHTML =
                        '<i class="ri-information-line me-1"></i> Seleccione un banco para verificar si el número ya existe';
                    transferenciaExistente = false;
                }
            } else if (valor.length > 0 && valor.length < 2) {
                badge.className = 'validation-badge invalid';
                badge.innerHTML = '<i class="ri-error-warning-fill me-1"></i> Número muy corto (mínimo 2 caracteres)';
                datosValidos.numero = false;
                ocultarAlertaDuplicado();
            } else {
                badge.className = 'validation-badge invalid';
                badge.innerHTML = '<i class="ri-error-warning-fill me-1"></i> Número requerido';
                datosValidos.numero = false;
                ocultarAlertaDuplicado();
            }

            actualizarResumen();
        }

        function ocultarAlertaDuplicado() {
            const alertaDuplicado = document.getElementById('alertaDuplicado');
            alertaDuplicado.style.display = 'none';
            alertaDuplicado.className = 'alert alert-info mt-3 mb-0';
            transferenciaExistente = false;
        }

        function verificarExistenciaEnTiempoReal(numero) {
            // Cancelar búsqueda anterior
            if (timeoutBusqueda) {
                clearTimeout(timeoutBusqueda);
            }

            // No buscar si el número es el mismo que el último buscado y la búsqueda está en progreso
            if (numero === ultimoNumeroBuscado && busquedaEnProgreso) {
                return;
            }

            // Esperar 500ms después de que el usuario deje de escribir
            timeoutBusqueda = setTimeout(() => {
                if (numero.length < 2 ) {
                    return;
                }

                busquedaEnProgreso = true;
                ultimoNumeroBuscado = numero;

                // Mostrar indicador de carga en el campo
                const numeroInput = document.getElementById('numero');
                const originalBorder = numeroInput.style.borderColor;
                numeroInput.style.borderColor = '#ffc107';

                fetch('/transferencias/verificar-tiempo-real', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        numero: numero
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        busquedaEnProgreso = false;
                        numeroInput.style.borderColor = originalBorder;

                        const alertaDuplicado = document.getElementById('alertaDuplicado');
                        const mensajeDuplicado = document.getElementById('mensajeDuplicado');

                        if (!data.valid) {
                            // Existe una transferencia duplicada
                            alertaDuplicado.style.display = 'block';

                            // Mostrar información detallada de la transferencia existente si está disponible
                            if (data.existente) {
                                const fechaFormateada = data.existente.fechaformat || data.existente.fecha;
                                const sucursalNombre  = data.existente.sucursal ? data.existente.sucursal.descrip : 'Desconocida';
                                const tipoTexto       = data.existente.tipo_texto || data.existente.tipo || 'N/A';

                                mensajeDuplicado.innerHTML = `
                        <div class="d-flex align-items-start">
                            <i class="ri-error-warning-fill me-2 mt-1" style="font-size: 1.2rem;"></i>
                            <div>
                                <strong>⚠️ Esta transferencia ya existe en el sistema</strong><br>
                                <small>
                                    Número: <strong>${data.existente.numero}</strong><br>
                                    Banco: <strong>${data.existente.banco_nombre || 'N/A'}</strong><br>
                                    Sucursal: <strong>${sucursalNombre}</strong><br>
                                    Fecha: <strong>${fechaFormateada}</strong><br>
                                    Monto: <strong>${data.existente.currency || ''} ${parseFloat(data.existente.monto).toFixed(2)}</strong><br>
                                    Estado: <strong>${data.existente.status === 0 ? 'Pendiente' : (data.existente.status === 1 ? 'Aprobada' : 'Rechazada')}</strong><br>
                                    ${data.existente.tipo ? `Tipo: <strong>${tipoTexto}</strong>` : ''}
                                </small>
                            </div>
                        </div>
                    `;

                                // Agregar clase de alerta peligro
                                alertaDuplicado.className = 'alert alert-danger mt-3 mb-0';
                            } else {
                                mensajeDuplicado.innerHTML =
                                    '<i class="ri-error-warning-line me-1"></i> ' +
                                    `Ya existe una transferencia con el número <strong>${numero}</strong> en el banco seleccionado`;
                                alertaDuplicado.className = 'alert alert-danger mt-3 mb-0';
                            }

                            transferenciaExistente = true;

                            // Marcar el campo como inválido
                            numeroInput.classList.add('is-invalid');

                            // Actualizar badge de validación
                            const badge = document.getElementById('validNumero');
                            badge.className = 'validation-badge invalid';
                            badge.innerHTML = '<i class="ri-error-warning-fill me-1"></i> Número duplicado';
                            datosValidos.numero = false;
                        } else {
                            // Número válido, no existe duplicado
                            ocultarAlertaDuplicado();
                            transferenciaExistente = false;
                            numeroInput.classList.remove('is-invalid');

                            // Actualizar badge
                            const badge = document.getElementById('validNumero');
                            badge.className = 'validation-badge valid';
                            badge.innerHTML = '<i class="ri-checkbox-circle-fill me-1"></i> Número disponible';
                            datosValidos.numero = true;
                        }
                    })
                    .catch(error => {
                        console.error('Error al verificar:', error);
                        busquedaEnProgreso = false;
                        numeroInput.style.borderColor = originalBorder;
                    });
            }, 500); // 500ms de debounce
        }

        function validarMonto() {
            const input = document.getElementById('monto');
            const valor = parseFloat(input.value) || 0;
            const badge = document.getElementById('validMonto');

            if (valor >= 0.01) {
                badge.className = 'validation-badge valid';
                badge.innerHTML = '<i class="ri-checkbox-circle-fill me-1"></i> Monto válido';
                datosValidos.monto = true;
            } else {
                badge.className = 'validation-badge invalid';
                badge.innerHTML = '<i class="ri-error-warning-fill me-1"></i> Monto debe ser > 0';
                datosValidos.monto = false;
            }
        }

        function validarFecha() {
            const input = document.getElementById('fecha');
            const valor = input.value;
            const badge = document.getElementById('validFecha');

            // Validar formato dd/mm/yyyy
            const regex = /^\d{2}\/\d{2}\/\d{4}$/;
            if (regex.test(valor)) {
                badge.className = 'validation-badge valid';
                badge.innerHTML = '<i class="ri-checkbox-circle-fill me-1"></i> Fecha válida';
                datosValidos.fecha = true;
            } else {
                badge.className = 'validation-badge invalid';
                badge.innerHTML = '<i class="ri-error-warning-fill me-1"></i> Formato DD/MM/AAAA';
                datosValidos.fecha = false;
            }
        }

        function validarTitular() {
            const input = document.getElementById('titular');
            const valor = input.value.trim();
            const badge = document.getElementById('validTitular');

            if (valor.length >= 3) {
                badge.className = 'validation-badge valid';
                badge.innerHTML = '<i class="ri-checkbox-circle-fill me-1"></i> Titular válido';
                datosValidos.titular = true;
            } else {
                badge.className = 'validation-badge invalid';
                badge.innerHTML = '<i class="ri-error-warning-fill me-1"></i> Titular requerido';
                datosValidos.titular = false;
            }
        }

        function validarBanco() {
            const select = document.getElementById('bancosucursal');
            const badge = document.getElementById('validBanco');

            if (select && select.value) {
                badge.className = 'validation-badge valid';
                badge.innerHTML = '<i class="ri-checkbox-circle-fill me-1"></i> Banco seleccionado';
                datosValidos.banco = true;

                // Actualizar símbolo de moneda según el banco seleccionado
                actualizarMoneda(select.value);

                // Si ya hay un número ingresado, verificarlo nuevamente con el banco seleccionado
                const numeroInput = document.getElementById('numero');
                if (numeroInput && numeroInput.value.trim().length >= 2) {
                    verificarExistenciaEnTiempoReal(numeroInput.value.trim(), select.value);
                }
            } else {
                badge.className = 'validation-badge invalid';
                badge.innerHTML = '<i class="ri-error-warning-fill me-1"></i> Seleccione un banco';
                datosValidos.banco = false;
                ocultarAlertaDuplicado();
            }
        }

        function verificarExistencia() {
            const numero = document.getElementById('numero').value;
            const banco = document.getElementById('bancosucursal')?.value;

            if (!numero || !banco) return;

            fetch('/transferencias/verificar', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    numero: numero,
                    fkbanco: banco
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.valid) {
                        document.getElementById('alertaDuplicado').style.display = 'block';
                        document.getElementById('mensajeDuplicado').innerHTML =
                            'Ya existe una transferencia con el número ' + numero;
                        transferenciaExistente = true;
                    } else {
                        document.getElementById('alertaDuplicado').style.display = 'none';
                        transferenciaExistente = false;
                    }
                });
        }

        function actualizarMoneda(bancoId) {
            fetch('/bancos/' + bancoId + '/moneda', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    const simbolo = data.moneda === 'BS' ? 'Bs' : (data.moneda === 'USD' ? '$' : 'COP');
                    document.getElementById('monedaSymbol').innerText = simbolo;
                    document.getElementById('monedaDisplay').value = data.moneda;
                })
                .catch(() => {
                    document.getElementById('monedaSymbol').innerText = 'Bs';
                });
        }

        // Preview de imagen
        function previsualizarImagen() {
            const input = document.getElementById('imagen');
            const file = input.files[0];
            const badge = document.getElementById('validImagen');

            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Archivo demasiado grande',
                        text: 'La imagen no puede superar los 5MB'
                    });
                    input.value = '';
                    return;
                }

                if (!file.type.match('image.*')) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Tipo no válido',
                        text: 'Solo se permiten imágenes JPG, PNG o GIF'
                    });
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewPlaceholder').style.display = 'none';
                    document.getElementById('previewContent').innerHTML =
                        '<img src="' + e.target.result + '" class="preview-image" alt="Preview">';
                    document.getElementById('previewContent').style.display = 'block';
                    document.getElementById('removeImage').style.display = 'flex';
                    document.getElementById('previewContainer').classList.add('has-image');

                    document.getElementById('fileInfo').innerHTML =
                        file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';

                    badge.className = 'validation-badge valid';
                    badge.innerHTML = '<i class="ri-checkbox-circle-fill me-1"></i> Imagen cargada';
                    datosValidos.imagen = true;
                };
                reader.readAsDataURL(file);
            }
        }

        function eliminarPreview(event) {
            event.stopPropagation();
            document.getElementById('imagen').value = '';
            document.getElementById('previewPlaceholder').style.display = 'block';
            document.getElementById('previewContent').innerHTML = '';
            document.getElementById('previewContent').style.display = 'none';
            document.getElementById('removeImage').style.display = 'none';
            document.getElementById('previewContainer').classList.remove('has-image');
            document.getElementById('fileInfo').innerHTML = '';

            const badge = document.getElementById('validImagen');
            badge.className = 'validation-badge invalid';
            badge.innerHTML = '<i class="ri-error-warning-fill me-1"></i> Imagen opcional';
            datosValidos.imagen = false;
        }

        // Actualizar resumen
        function actualizarResumen() {
            document.getElementById('resumenNumero').innerText =
                document.getElementById('numero').value || '---';

            const bancoSelect = document.getElementById('bancosucursal');
            if (bancoSelect && bancoSelect.selectedOptions[0]) {
                document.getElementById('resumenBanco').innerText =
                    bancoSelect.selectedOptions[0].text || '---';
            }

            document.getElementById('resumenFecha').innerText =
                document.getElementById('fecha').value || '---';

            const monto = document.getElementById('monto').value;
            const simbolo = document.getElementById('monedaSymbol').innerText;
            document.getElementById('resumenMonto').innerText =
                monto ? simbolo + ' ' + parseFloat(monto).toFixed(2) : '---';

            document.getElementById('resumenTitular').innerText =
                document.getElementById('titular').value || '---';

            const tipoSelect = document.getElementById('tipo');
            if (tipoSelect && tipoSelect.selectedOptions[0]) {
                document.getElementById('resumenTipo').innerText =
                    tipoSelect.selectedOptions[0].text || '---';
            } else {
                document.getElementById('resumenTipo').innerText = '---';
            }

            document.getElementById('resumenCategoria').innerText =
                document.getElementById('categoria').value || '---';
        }

        // Validar todo antes de enviar
        function validarTodo() {
            validarNumero();
            validarMonto();
            validarFecha();
            validarTitular();
            validarBanco();
            validarTipo();

            // No permitir guardar si hay una transferencia duplicada
            if (transferenciaExistente) {
                return false;
            }

            return datosValidos.sucursal && datosValidos.banco && datosValidos.numero &&
                datosValidos.monto && datosValidos.fecha && datosValidos.titular &&
                datosValidos.tipo && !transferenciaExistente;
        }

        // Mostrar errores
        function mostrarErrores() {
            let mensaje = 'Por favor, corrija los siguientes errores:\n';

            if (!datosValidos.sucursal) mensaje += '- Debe seleccionar una sucursal\n';
            if (!datosValidos.banco) mensaje    += '- Debe seleccionar un banco\n';
            if (!datosValidos.numero) mensaje   += '- Número de transferencia inválido\n';
            if (!datosValidos.monto) mensaje    += '- Monto inválido\n';
            if (!datosValidos.fecha) mensaje    += '- Fecha inválida\n';
            if (!datosValidos.titular) mensaje  += '- Titular inválido\n';
            if (!datosValidos.tipo) mensaje     += '- Debe seleccionar un tipo de transferencia\n';
            if (transferenciaExistente) mensaje += '- Esta transferencia ya existe\n';

            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: mensaje
            });
        }

        // Loading overlay
        function mostrarLoading(mensaje) {
            document.getElementById('loadingMessage').innerText = mensaje || 'Procesando...';
            document.getElementById('loadingOverlay').classList.add('active');
        }

        function ocultarLoading() {
            document.getElementById('loadingOverlay').classList.remove('active');
        }

        // Mostrar mensajes de sesión (si existen)
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            timer: 2000
        });
        @endif

        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}'
        });
        @endif
    </script>

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
