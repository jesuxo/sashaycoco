@extends('layouts.master')
@section('title')
    BANCO DEL TESORO - Verificación Pago Móvil
@endsection
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* ... tus estilos existentes ... */

        /* Estilos para el preview de imagen */
        .preview-container {
            position: relative;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s ease;
            cursor: pointer;
            min-height: 150px;
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
            max-height: 200px;
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

        .file-info {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }

        .history-item-image {
            max-width: 40px;
            max-height: 40px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }

        .image-modal-img {
            max-width: 100%;
            max-height: 80vh;
        }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            display: none;  /* Importante: debe estar hidden por defecto */
            justify-content: center;
            align-items: center;
        }

        .loading-overlay.active {
            display: flex;  /* Solo se muestra cuando tiene la clase active */
        }
        .preview-container.required-missing {
            border-color: #dc3545;
            background-color: #fff5f5;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .invalid-feedback.show {
            display: block !important;
        }
    </style>
@endsection

@section('content')
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 mb-0">Consultando con el banco...</p>
            <small class="text-muted">Esto puede tomar unos segundos</small>
        </div>
    </div>

    <div class="row">
        <!-- COLUMNA IZQUIERDA - FORMULARIO -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-white">
                        <i class="bi bi-bank2 me-2"></i>
                        Verificación de Pago Móvil
                    </h5>
                </div>
                <div class="card-body">
                    <form method="post" name="form1" id="form1" action="/tesoro" class="needs-validation" novalidate enctype="multipart/form-data">
                        @csrf
                        @method('POST')

                        @auth
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <strong>Usuario:</strong> {{ auth()->user()->first_name }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endauth

                        <div class="mb-3">
                            <label for="referencia" class="form-label fw-bold">
                                <i class="bi bi-hash"></i> Número de Referencia *
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-house"></i>
                                </span>
                                <select required class="form-control" name="fksucursal" id="fksucursal">
                                    @if(isset($sucursales) and count($sucursales) > 1)
                                        <option value="">Seleccione una sucursal</option>
                                    @endif
                                    @foreach($sucursales as $sucursal)
                                        <option value="{{$sucursal->id}}" {{(isset($sucursales) and count($sucursales) == 1)? 'selected': ''}} >{{$sucursal->descrip}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="invalid-feedback">
                                Por favor ingrese el número de referencia
                            </div>
                            <small class="text-muted">Código de la transferencia o pago móvil</small>
                        </div>

                        <div class="mb-3">
                            <label for="referencia" class="form-label fw-bold">
                                <i class="bi bi-hash"></i> Número de Referencia *
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-upc-scan"></i>
                                </span>
                                <input type="number"
                                       step="1"
                                       min="0"
                                       maxlength="9999999999"
                                       class="form-control"
                                       id="referencia"
                                       name="referencia"
                                       required
                                       placeholder="Ej: 565645454"
                                       autocomplete="off">
                            </div>
                            <div class="invalid-feedback">
                                Por favor ingrese el número de referencia
                            </div>
                            <small class="text-muted">Código de la transferencia o pago móvil</small>
                        </div>

                        <div class="mb-3">
                            <label for="monto" class="form-label fw-bold">
                                <i class="bi bi-cash-stack"></i> Monto en Bs. *
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Bs.</span>
                                <input type="number"
                                       min="0.01"
                                       max="99999999.99"
                                       step="0.01"
                                       class="form-control text-end"
                                       id="monto"
                                       name="monto"
                                       required
                                       style="text-align: left !important;"
                                       autocomplete="off">
                            </div>
                            <div class="invalid-feedback">
                                Por favor ingrese el monto de la transacción
                            </div>
                        </div>

                        <!-- Campo de imagen/captura -->
                        <!-- Campo de imagen/captura -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-camera"></i> Captura del Pago *
                            </label>
                            <div class="preview-container" id="previewContainer" onclick="document.getElementById('imagen').click();">
                                <input type="file" class="d-none" id="imagen" name="imagen" accept="image/*" required>
                                <div id="previewPlaceholder" class="preview-placeholder">
                                    <i class="bi bi-image"></i>
                                    <p class="mb-1">Haz clic para seleccionar una imagen</p>
                                    <small class="text-muted">JPG, PNG o GIF (max. 5MB)</small>
                                </div>
                                <div id="previewContent" style="display: none;"></div>
                                <button type="button" class="remove-image" id="removeImage" onclick="eliminarPreview(event)" style="display: none;">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            <div class="file-info mt-2 text-center" id="fileInfo"></div>
                            <div class="invalid-feedback" id="imagenFeedback">
                                <i class="bi bi-exclamation-triangle"></i> Por favor adjunte una captura del comprobante de pago
                            </div>
                            <small class="text-muted text-danger">* Obligatorio - Adjunte una captura del comprobante de pago</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="btnConsultar">
                                <i class="bi bi-search me-2"></i>Consultar Pago
                            </button>
                        </div>

                        <div id="resultado" class="mt-4" style="display: none;"></div>
                    </form>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA - HISTORIAL DE ÚLTIMOS PAGOS -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Últimos Pagos Revisados
                    </h5>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary auto-refresh-btn" id="autoRefreshBtn" title="Auto-refrescar cada 30 segundos">
                            <i class="bi bi-arrow-repeat"></i> Auto
                        </button>
                        <button class="btn btn-sm btn-outline-primary ms-2" id="refreshHistoryBtn">
                            <i class="bi bi-arrow-clockwise"></i> Refrescar
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="history-stats text-white" id="historyStats" style="display: none">
                        <div class="row text-center">
                            <div class="col-4">
                                <p class="stat-number" id="totalCount">0</p>
                                <p class="stat-label">Total</p>
                            </div>
                            <div class="col-4">
                                <p class="stat-number text-success" id="approvedCount">0</p>
                                <p class="stat-label">Aprobados</p>
                            </div>
                            <div class="col-4">
                                <p class="stat-number text-warning" id="rejectedCount">0</p>
                                <p class="stat-label">Rechazados</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 border-bottom">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <select class="form-control form-control-sm" id="filterEstado">
                                    <option value="todos">Todos los estados</option>
                                    <option value="OK">✅ Aprobados</option>
                                    <option value="Error">❌ Rechazados</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control form-control-sm" id="filterReferencia" placeholder="Buscar referencia...">
                            </div>
                        </div>
                    </div>

                    <div class="history-list" id="historyList">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2 text-muted">Cargando historial...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver imagen -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Captura de Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" alt="Captura de pago" class="image-modal-img" id="modalImage">
                    <p class="mt-2 text-muted" id="modalImageInfo"></p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Variables
            let intentosRestantes    = 3;
            let referenciaBloqueada  = false;
            let referenciasAprobadas = new Set();
            let autoRefreshInterval  = null;
            let fksucursalval        = 0;
            let autoRefreshActive    = false;

            // Recuperar referencias aprobadas del localStorage
            const storedAprobadas = localStorage.getItem('referenciasAprobadas');
            if (storedAprobadas) {
                referenciasAprobadas = new Set(JSON.parse(storedAprobadas));
            }

            // Función para guardar referencia aprobada
            function guardarReferenciaAprobada(referencia) {
                referenciasAprobadas.add(referencia);
                localStorage.setItem('referenciasAprobadas', JSON.stringify([...referenciasAprobadas]));
            }

            function limpiarErrorImagen() {
                const previewContainer = document.getElementById('previewContainer');
                const feedback = document.getElementById('imagenFeedback');

                previewContainer.classList.remove('required-missing');
                feedback.classList.remove('show');
            }

            const previsualizarImagenOriginal = previsualizarImagen;
            window.previsualizarImagen = function() {
                limpiarErrorImagen();
                previsualizarImagenOriginal();
            }

            function mostrarErrorImagenRequerida() {
                const previewContainer = document.getElementById('previewContainer');
                const feedback = document.getElementById('imagenFeedback');

                previewContainer.classList.add('required-missing');
                feedback.classList.add('show');

                // Scroll al campo
                previewContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });

                // Remover la clase después de 3 segundos
                setTimeout(() => {
                    previewContainer.classList.remove('required-missing');
                }, 3000);
            }


            function tieneImagen() {
                const inputImagen = document.getElementById('imagen');
                return inputImagen.files && inputImagen.files.length > 0;
            }

            // Preview de imagen
            function previsualizarImagen() {
                const input = document.getElementById('imagen');
                const file = input.files[0];

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
                    };
                    reader.readAsDataURL(file);
                }
            }

            window.eliminarPreview = function(event) {
                event.stopPropagation();
                document.getElementById('imagen').value = '';
                document.getElementById('previewPlaceholder').style.display = 'block';
                document.getElementById('previewContent').innerHTML = '';
                document.getElementById('previewContent').style.display = 'none';
                document.getElementById('removeImage').style.display = 'none';
                document.getElementById('previewContainer').classList.remove('has-image');
                document.getElementById('fileInfo').innerHTML = '';
            }

            // Cargar historial de pagos
            function cargarHistorial() {
                const estado        = $('#filterEstado').val();
                const referencia    = $('#filterReferencia').val();
                const fksucursalval = $('#fksucursal').val();

                $.ajax({
                    url: '/tesoro/historial',
                    type: 'GET',
                    data: {
                        estado: estado !== 'todos' ? estado : '',
                        referencia: referencia,
                        fksucursal: fksucursalval ? fksucursalval :0
                    },
                    success: function(data) {
                        mostrarHistorial(data);
                        actualizarEstadisticas(data);
                    },
                    error: function() {
                        $('#historyList').html(`
                            <div class="text-center py-5 text-danger">
                                <i class="bi bi-exclamation-triangle-fill fs-1"></i>
                                <p class="mt-2">Error al cargar el historial</p>
                                <button class="btn btn-sm btn-outline-danger" onclick="location.reload()">Reintentar</button>
                            </div>
                        `);
                    }
                });
            }

            function mostrarHistorial(data) {
                const historyList = $('#historyList');

                if (!data || data.length === 0) {
                    historyList.html(`
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mt-2">No hay consultas registradas</p>
                            <small>Realiza tu primera consulta</small>
                        </div>
                    `);
                    return;
                }

                let html = '<div class="list-group list-group-flush">';

                data.forEach(item => {
                    let estadoClass = '';
                    let estadoIcon = '';
                    let bgClass = '';

                    switch(item.estado) {
                        case 'OK':
                            estadoClass = 'approved';
                            estadoIcon = '✅';
                            bgClass = 'bg-success-subtle';
                            break;
                        case 'Error':
                            estadoClass = 'rejected';
                            estadoIcon = '❌';
                            bgClass = 'bg-danger-subtle';
                            break;
                        default:
                            estadoClass = 'pending';
                            estadoIcon = '⚠️';
                            bgClass = 'bg-warning-subtle';
                    }

                    const fecha = new Date(item.consultado_en);
                    const fechaFormateada = fecha.toLocaleString('es-VE', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });

                    const imagenHtml = item.imagen ?
                        `<img src="${item.imagen}" class="history-item-image" alt="captura" onclick="event.stopPropagation(); verImagen('${item.imagen}', 'Referencia: ${item.referencia} - Monto: Bs. ${parseFloat(item.monto).toLocaleString('es-VE', {minimumFractionDigits: 2})}')">` :
                        `<div class="history-item-image bg-light d-inline-flex align-items-center justify-content-center rounded" style="width:40px;height:40px;">
                            <i class="bi bi-image text-muted"></i>
                        </div>`;

                    html += `
                        <div class="history-item ${estadoClass} p-3 border-bottom ${bgClass}" data-referencia="${item.referencia}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        ${imagenHtml}
                                        <div>
                                            <span class="fs-5">${estadoIcon}</span>
                                            <strong class="text-primary">Ref: ${item.referencia}</strong>
                                            <span class="badge bg-${item.estado === 'OK' ? 'success' : (item.estado === 'Error' ? 'danger' : 'warning')} ms-2">
                                                ${item.estado}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row small text-muted mt-2">
                                        <div class="col-md-6">
                                            <i class="bi bi-upc-scan"></i>  ${item.referenciaFull}
                                        </div>
                                        <div class="col-md-6">
                                            <i class="bi bi-cash-stack"></i> Bs. ${parseFloat(item.monto).toLocaleString('es-VE', {minimumFractionDigits: 2})}
                                        </div>
                                        <div class="col-md-6">
                                            <i class="bi bi-calendar"></i> ${fechaFormateada}
                                        </div>
                                        <div class="col-md-6">
                                            <i class="bi bi-globe"></i> ${item.ip_usuario}
                                        </div>
                                        <div class="col-md-6">
                                            <i class="bi bi-person"></i>  ${item.email_usuario}
                                        </div>
                                    </div>
                                    ${item.mensaje ? `
                                        <div class="mt-2 small">
                                            <i class="bi bi-chat-dots"></i> ${item.mensaje.substring(0, 100)}${item.mensaje.length > 100 ? '...' : ''}
                                        </div>
                                    ` : ''}
                                </div>
                                <button class="btn btn-sm btn-outline-secondary copy-ref-btn" data-ref="${item.referencia}" title="Copiar referencia">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });

                html += '</div>';
                historyList.html(html);

                $('.copy-ref-btn').off('click').on('click', function(e) {
                    e.stopPropagation();
                    const ref = $(this).data('ref');
                    navigator.clipboard.writeText(ref);
                    Swal.fire({ icon: 'success', title: 'Copiado', text: `Referencia ${ref} copiada`, timer: 1500, showConfirmButton: false });
                });

                $('.history-item').off('click').on('click', function(e) {
                    if (!$(e.target).closest('.copy-ref-btn').length && !$(e.target).closest('.history-item-image').length) {
                        const referencia = $(this).data('referencia');
                        $('#referencia').val(referencia);
                        $('#referencia').focus();
                    }
                });
            }

            window.verImagen = function(url, info) {
                $('#modalImage').attr('src', url);
                $('#modalImageInfo').text(info);
                $('#imageModal').modal('show');
            }

            function actualizarEstadisticas(data) {
                const total = data.length;
                const aprobados = data.filter(item => item.estado === 'OK').length;
                const rechazados = data.filter(item => item.estado === 'Error').length;
                $('#totalCount').text(total);
                $('#approvedCount').text(aprobados);
                $('#rejectedCount').text(rechazados);
            }

            function toggleAutoRefresh() {
                if (autoRefreshActive) {
                    clearInterval(autoRefreshInterval);
                    autoRefreshActive = false;
                    $('#autoRefreshBtn').removeClass('active btn-success').addClass('btn-outline-secondary');
                    $('#autoRefreshBtn').html('<i class="bi bi-arrow-repeat"></i> Auto');
                } else {
                    autoRefreshInterval = setInterval(() => cargarHistorial(), 30000);
                    autoRefreshActive = true;
                    $('#autoRefreshBtn').removeClass('btn-outline-secondary').addClass('active btn-success');
                    $('#autoRefreshBtn').html('<i class="bi bi-pause-circle"></i> Auto ON');
                }
            }

            $('#referencia').on('input', function() {
                let referencia = $(this).val();
                if (referenciasAprobadas.has(referencia)) {
                    $(this).addClass('is-invalid');
                    $('#referencia').after('<div class="invalid-feedback d-block">⚠️ Esta referencia ya fue utilizada anteriormente</div>');
                } else {
                    $(this).removeClass('is-invalid');
                    $('.invalid-feedback.d-block').remove();
                }
            });

            $('#filterEstado, #filterReferencia').on('change keyup', () => cargarHistorial());
            $('#refreshHistoryBtn').on('click', function() {
                $(this).html('<i class="bi bi-arrow-clockwise spinner-border spinner-border-sm"></i>');
                cargarHistorial();
                setTimeout(() => {
                    $(this).html('<i class="bi bi-arrow-clockwise"></i> Refrescar');
                }, 1000);
            });
            $('#autoRefreshBtn').on('click', toggleAutoRefresh);

            // Envío del formulario - SOLO cuando se hace clic en el botón
            $('#form1').on('submit', function(event) {
                event.preventDefault();

                // Validar que tenga imagen
                if (!tieneImagen()) {
                    mostrarErrorImagenRequerida();
                    return;
                }

                // Validar el formulario
                if (!this.checkValidity()) {
                    event.stopPropagation();
                    $(this).addClass('was-validated');
                    return;
                }

                consultarPago();
                $(this).addClass('was-validated');
            });

            document.getElementById('imagen').addEventListener('change', actualizarEstadoImagen);
            window.eliminarPreview = function(event) {
                event.stopPropagation();
                document.getElementById('imagen').value = '';
                document.getElementById('previewPlaceholder').style.display = 'block';
                document.getElementById('previewContent').innerHTML = '';
                document.getElementById('previewContent').style.display = 'none';
                document.getElementById('removeImage').style.display = 'none';
                document.getElementById('previewContainer').classList.remove('has-image');
                document.getElementById('fileInfo').innerHTML = '';
                actualizarEstadoImagen(); // Actualizar badge
                limpiarErrorImagen(); // Limpiar error si existe
            }



            function actualizarEstadoImagen() {
                const tieneImg = tieneImagen();
                const badge = $('#imagenStatusBadge');

                if (!badge.length) {
                    $('.preview-container').append('<div id="imagenStatusBadge" class="position-absolute top-0 end-0 m-2"></div>');
                }

                if (tieneImg) {
                    $('#imagenStatusBadge').html('<i class="bi bi-check-circle-fill text-success" style="font-size: 20px;"></i>');
                } else {
                    $('#imagenStatusBadge').html('<i class="bi bi-exclamation-circle-fill text-warning" style="font-size: 20px;"></i>');
                }
            }

            function consultarPago() {
                const referencia    = $('#referencia').val();
                const monto         = $('#monto').val();
                const fksucursalval = $('#fksucursal').val();

                const tieneImg      = tieneImagen();

                // Validar imagen requerida
                if (!tieneImg) {
                    mostrarErrorImagenRequerida();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Captura requerida',
                        text: 'Debe adjuntar una captura del comprobante de pago',
                        confirmButtonColor: '#ffc107',
                        confirmButtonText: 'Entendido'
                    });
                    return;
                }

                // Validaciones básicas
                if (!referencia || !monto) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Campos requeridos',
                        text: 'Debe ingresar la referencia y el monto'
                    });
                    return;
                }

                if (referenciasAprobadas.has(referencia)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Referencia No Válida',
                        html: '<strong>⚠️ Esta referencia ya fue utilizada y aprobada anteriormente.</strong>',
                        confirmButtonColor: '#dc3545'
                    });
                    return;
                }

                // Mostrar loading SOLO cuando se envía el formulario
                $('#loadingOverlay').fadeIn();
                $('#resultado').hide().empty();
                $('#btnConsultar').prop('disabled', true);

                var formData = new FormData();
                formData.append('referencia', referencia);
                formData.append('monto', monto);
                formData.append('fksucursal', fksucursalval);
                formData.append('idReceptor', 'J308244562');
                var imagenFile = $('#imagen')[0].files[0];
                if (imagenFile) {
                    formData.append('imagen', imagenFile);
                }

                $.ajax({
                    type: 'POST',
                    url: '/tesoro',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: formData,
                    processData: false,
                    contentType: false,
                    timeout: 20000,
                    success: function(data) {
                        $('#loadingOverlay').fadeOut();
                        $('#btnConsultar').prop('disabled', false);

                        let resultadoHtml = '';

                        switch(data.codigo) {
                            case 'PAGO_APROBADO':
                                guardarReferenciaAprobada(referencia);
                                cargarHistorial();
                                resultadoHtml = `
                                    <div class="alert alert-success mt-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-check-circle-fill text-success fs-2 me-3"></i>
                                            <div>
                                                <h5 class="mb-1 text-success">${data.status}</h5>
                                                <p class="mb-0"><strong>Mensaje:</strong> ${data.mensaje}</p>
                                                <p class="mb-0"><strong>Referencia:</strong> ${data.referencia}</p>
                                                <p class="mb-0"><strong>Monto:</strong> Bs. ${parseFloat(monto).toLocaleString('es-VE', {minimumFractionDigits: 2})}</p>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Pago Verificado!',
                                    html: `<strong>Referencia:</strong> ${data.referencia}<br><strong>Monto:</strong> Bs. ${parseFloat(monto).toLocaleString('es-VE', {minimumFractionDigits: 2})}`,
                                    confirmButtonText: 'Continuar'
                                });
                                // Limpiar campos
                                setTimeout(() => {
                                    $('#referencia').val('');
                                    $('#monto').val('');
                                    $('#imagen').val('');
                                    if (typeof window.eliminarPreview === 'function') {
                                        // Crear un evento simulado para eliminar preview
                                        window.eliminarPreview(new Event('click'));
                                    }
                                    limpiarErrorImagen();
                                    $('#referencia').focus();
                                }, 3000);
                                break;
                            case 'PAGO_RECHAZADO':
                                cargarHistorial();
                                resultadoHtml = `
                                    <div class="alert alert-danger mt-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-x-circle-fill text-danger fs-2 me-3"></i>
                                            <div>
                                                <h5 class="mb-1 text-danger">${data.status}</h5>
                                                <p class="mb-0">${data.mensaje}</p>
                                                ${data.intentos_restantes ? `<small class="text-muted">Intentos restantes: ${data.intentos_restantes} de 3</small>` : ''}
                                            </div>
                                        </div>
                                    </div>
                                `;
                                break;
                            default:
                                resultadoHtml = `
                                    <div class="alert alert-danger mt-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-question-circle-fill text-danger fs-2 me-3"></i>
                                            <div>
                                                <h5 class="mb-1 text-danger">Error</h5>
                                                <p class="mb-0">${data.mensaje || 'Error desconocido'}</p>
                                            </div>
                                        </div>
                                    </div>
                                `;
                        }
                        $('#resultado').html(resultadoHtml).fadeIn();
                    },
                    error: function(xhr, status, error) {
                        $('#loadingOverlay').fadeOut();
                        $('#btnConsultar').prop('disabled', false);
                        let errorMsg = status === 'timeout'
                            ? 'La consulta está tomando más tiempo de lo normal. Por favor intente nuevamente.'
                            : 'Error de conexión. Verifique su conexión a internet.';
                        $('#resultado').html(`
                            <div class="alert alert-danger mt-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-wifi-off text-danger fs-2 me-3"></i>
                                    <div>
                                        <h5 class="mb-1 text-danger">Error de Conexión</h5>
                                        <p class="mb-0">${errorMsg}</p>
                                    </div>
                                </div>
                            </div>
                        `).fadeIn();
                    }
                });
            }

            // Cargar historial inicial (esto NO debe mostrar el loading overlay)
            cargarHistorial();

            // Limpiar intervalo al salir
            $(window).on('beforeunload', function() {
                if (autoRefreshInterval) clearInterval(autoRefreshInterval);
            });
        });
    </script>
@endsection
