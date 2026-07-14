@extends('layouts.master')
@section('title')
    Inicio
@endsection
@section('css')
    <style>
        .botoncal{
            background: transparent;
            border: none;
            color: white;
        }
        .botoncal:hover{
            font-size: 13px;
        }
        .linkunderline:hover{
            text-decoration: underline;
        }

        /* Nuevos estilos para el dashboard mejorado */
        .dashboard-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .card-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .dashboard-card:hover .card-icon-wrapper {
            transform: scale(1.1);
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 600;
            line-height: 1.2;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            margin-bottom: 8px;
        }

        .trend-indicator {
            font-size: 0.8rem;
            padding: 3px 8px;
            border-radius: 20px;
            background: #e8f5e9;
            color: #2e7d32;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .trend-indicator.down {
            background: #ffebee;
            color: #c62828;
        }

        .quick-actions {
            background: #0072c5;
            color: white;
            border-radius: 4px;
            padding: 20px;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .quick-action-btn {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .quick-action-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
            color: white;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title i {
            color: #667eea;
        }

        /* Colores personalizados para cada tarjeta */
        .bg-soft-primary { background-color: rgba(102, 126, 234, 0.1); color: #667eea; }
        .bg-soft-success { background-color: rgba(72, 187, 120, 0.1); color: #48bb78; }
        .bg-soft-warning { background-color: rgba(255, 159, 67, 0.1); color: #ff9f43; }
        .bg-soft-danger { background-color: rgba(245, 101, 101, 0.1); color: #f56565; }
        .bg-soft-info { background-color: rgba(66, 153, 225, 0.1); color: #4299e1; }
        .bg-soft-purple { background-color: rgba(159, 122, 234, 0.1); color: #9f7aea; }

        /* Estilos para el modal de documento */
        .modal-xl {
            max-width: 90%;
        }

        .modal-dialog-scrollable .modal-content {
            max-height: 90vh;
        }

        .modal-dialog-scrollable .modal-body {
            overflow-y: auto;
            padding: 0;
        }

        #documentView {
            padding: 20px;
            background: #f8f9fa;
            min-height: 400px;
        }

        /* Estilos para la impresión dentro del modal */
        @media print {
            .modal {
                position: absolute;
                left: 0;
                top: 0;
                margin: 0;
                padding: 0;
                width: 100%;
                height: 100%;
            }

            .modal-dialog {
                margin: 0;
                width: 100%;
                max-width: 100%;
            }

            .modal-content {
                border: none;
                box-shadow: none;
            }

            .modal-header, .modal-footer {
                display: none;
            }

            .modal-body {
                padding: 0;
            }
        }
    </style>
@endsection
@section('content')
    <!-- Sección de Bienvenida -->
    <div class="row  ">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white border-0"  style="margin: 0px !important;">
                <div class="card-body  " style="padding: 0px !important;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">¡Bienvenido de nuevo, {{ Auth::user()->first_name ?? 'Usuario' }}!</h3>
                            <p class="mb-0 opacity-75">Aquí tienes un resumen de tu negocio</p>
                        </div>
                        <div class="text-end">
                            <div class="h4 mb-1">{{ now()->format('l, d F Y') }}</div>
                            <div class="opacity-75">{{ now()->format('h:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 1: KPIs Principales -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card  ">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Total Ventas</div>
                            <a href="/resumenVentas" class="stretched-link text-decoration-none">
                                <div class="mt-3 text-muted small">
                                    Ver resumen completo <i class="ri-arrow-right-line"></i>
                                </div>
                            </a>
                        </div>
                        <div class="card-icon-wrapper bg-soft-primary">
                            <i class="ph-wallet fs-3 text-primary"></i>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card ">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Costo Inventario</div>
                            <a href="/existencias" class="stretched-link text-decoration-none">
                                <div class="mt-3 text-muted small">
                                    Ver reporte inventario <i class="ri-arrow-right-line"></i>
                                </div>
                            </a>
                        </div>
                        <div class="card-icon-wrapper bg-soft-success">
                            <i class="ph-sketch-logo fs-3 text-success"></i>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Cuentas x Cobrar</div>
                            <a href="/cxc" class="stretched-link text-decoration-none">
                                <div class="mt-3 text-muted small">
                                    Ver reporte cxc <i class="ri-arrow-right-line"></i>
                                </div>
                            </a>
                        </div>
                        <div class="card-icon-wrapper bg-soft-warning">
                            <i class="ph-currency-dollar-bold fs-3 text-warning"></i>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card ">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Unidades Vendidas</div>
                            <a href="/ventas/productos/sucursales" class="stretched-link text-decoration-none">
                                <div class="mt-3 text-muted small">
                                    Ver reporte <i class="ri-arrow-right-line"></i>
                                </div>
                            </a>
                        </div>
                        <div class="card-icon-wrapper bg-soft-danger">
                            <i class="bi-box fs-3 text-danger"></i>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Fila 2: Reportes y Módulos Especiales -->
    <div class="row g-4  ">
        <div class="col-lg-4">
            <div class="card dashboard-card  ">
                <div class="card-header bg-transparent border-0">
                    <h5 class="section-title mb-0">
                        <i class="ph-chart-line"></i>
                        Resultado General
                    </h5>
                </div>

                <div class="card-footer bg-transparent border-0">
                    <a href="/ventas/resultado" class="btn btn-outline-primary w-100">
                        Ver detalle completo
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card dashboard-card ">
                <div class="card-header bg-transparent border-0">
                    <h5 class="section-title mb-0">
                        <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                        Reporte de Compras
                    </h5>
                </div>

                @if(Auth::user() and auth()->user()->can('menu_compras'))
                    <div class="card-footer bg-transparent border-0">
                        <a href="/reporte/compra" class="btn btn-outline-secondary w-100">
                            Ver reporte completo
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="row">
        <div class="col-12">
            <div class="quick-actions">
                <h5 class="mb-3 text-white">Acciones Rápidas</h5>
                <div class="d-flex flex-wrap gap-2">
                    @if(Auth::user() and auth()->user()->can('menu_transferencias'))
                        <a href="/transferencias/create" class="quick-action-btn">
                            <i class="ri-add-line"></i>Ingresar Transferencia
                        </a>
                    @endif

                    <a href="/reporte/instpagobs" class="quick-action-btn">
                        <i class="ri-file-chart-line"></i> Rep InstPago BS
                    </a>

                    <a href="/reporte/instpagodolares" class="quick-action-btn">
                        <i class="ri-file-chart-line"></i> Rep InstPago USD
                    </a>


                </div>
            </div>
        </div>
    </div>

    <!-- Agrega esto en la sección de Acciones Rápidas -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h5 class="section-title mb-3">
                        <i class="ri-search-line"></i>
                        Búsqueda Rápida de Facturas
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="input-group">
                            <span class="input-group-text bg-primary text-white">
                                <i class="ri-file-copy-line"></i>
                            </span>
                                <input type="text"
                                       class="form-control form-control-lg"
                                       id="numeroFactura"
                                       placeholder="Ingrese número de factura (ej: 00001234)"
                                       autocomplete="off">
                                <button class="btn btn-primary" type="button" id="btnBuscarFactura">
                                    <i class="ri-search-line"></i> Buscar
                                </button>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                <i class="ri-information-line"></i>
                                Busca facturas en todas las sucursales. Presiona Enter para buscar.
                            </small>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-secondary flex-grow-1" type="button" id="btnBuscarDevolucion">
                                    <i class="ri-arrow-go-back-line"></i> Buscar Devolución
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Resultados de búsqueda -->
                    <div id="resultadosBusqueda" class="mt-4" style="display: none;">
                        <div id="listaResultados" class="row g-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <!-- App js -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputFactura = document.getElementById('numeroFactura');
            const btnBuscar = document.getElementById('btnBuscarFactura');
            const btnBuscarDevolucion = document.getElementById('btnBuscarDevolucion');
            const resultadosDiv = document.getElementById('resultadosBusqueda');
            const listaResultados = document.getElementById('listaResultados');

            let timeoutId = null;
            let buscando = 0;

            // Función para buscar factura
            function buscarFactura(tipo = 'A') {
                if(buscando == 0){
                    buscando = 1;
                    const numero = inputFactura.value.trim();

                    if (!numero) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Campo vacío',
                            text: 'Por favor ingrese una busqueda',
                            confirmButtonColor: '#ff9f43'
                        });
                        inputFactura.focus();
                        buscando = 0;
                        return;
                    }

                    // Mostrar loading
                    Swal.fire({
                        title: 'Buscando  ...',
                        text: 'Por favor espere',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Realizar la petición AJAX
                    fetch(`/buscar-factura/${tipo}/${numero}`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close();

                            if (data.success) {
                                if (data.results && data.results.length > 0) {
                                    mostrarResultados(data.results, tipo, numero);
                                } else if (data.redirect) {
                                    // Si solo hay una coincidencia, abrir en modal
                                    abrirFacturaEnModal(data.redirect);
                                } else {
                                    mostrarSinResultados(numero, tipo);
                                }
                                buscando = 0;
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message || 'Error al buscar',
                                    confirmButtonColor: '#dc3545'
                                });
                                buscando = 0;
                            }
                        })
                        .catch(error => {
                            Swal.close();
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error de conexión',
                                text: 'No se pudo conectar con el servidor',
                                confirmButtonColor: '#dc3545'
                            });
                            buscando = 0;
                        });
                }
            }

            // Función para mostrar resultados
            function mostrarResultados(results, tipo, numero) {
                resultadosDiv.style.display = 'block';
                listaResultados.innerHTML = '';

                results.forEach(sucursal => {
                    const col = document.createElement('div');
                    col.className = 'col-md-6 col-lg-4';
                    col.innerHTML = `
                    <div class="card h-100 border-hover shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-0">
                                    <i class="ri-store-3-line text-primary"></i>
                                    ${sucursal.sucursal_nombre}
                                </h6>
                                <span class="badge ${sucursal.tipo === 'A' ? 'bg-success' : 'bg-warning'}">
                                    ${sucursal.tipo === 'A' ? 'Fac' : 'Dev'} ${sucursal.numerod}
                                </span>
                            </div>
                            <p class="card-text small text-muted mb-2">
                                <div class="small text-muted" style="width: 100%">
                                    <i class="ri-calendar-line text-muted small"></i> ${sucursal.fecha} ${sucursal.hora || ''}
                                    <span style="float:right;" class="badge ${sucursal.credito > 0 ? 'bg-warning' : 'bg-success'}">
                                        ${sucursal.credito > 0 ? 'Credito' : 'Contado'}
                                    </span>
                                </div>
                                <i class="ri-user-line small text-muted"></i> <a class="small" href="clientes/${sucursal.codclie}/tab1" target="_blank">${sucursal.cliente_nombre}</a><br>
                                <div class="small text-muted"><i class="ri-money-dollar-circle-line"></i> Total: $${formatNumber(sucursal.total)}</div>
                            </p>
                            <button class="btn btn-sm btn-primary w-100" onclick="abrirFacturaEnModal('${sucursal.tipo}', '${sucursal.numerod}', ${sucursal.sucursal_id})">
                                <i class="ri-eye-line"></i> Ver Documento
                            </button>
                        </div>
                    </div>
                `;
                    listaResultados.appendChild(col);
                });

                // Scroll suave hacia los resultados
                resultadosDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            // Función para mostrar mensaje sin resultados
            function mostrarSinResultados(numero, tipo) {
                resultadosDiv.style.display = 'block';
                listaResultados.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="ri-information-line fs-4"></i>
                        <p class="mb-0 mt-2">No se encontró resultados para <strong>${numero}</strong> en ninguna sucursal.</p>
                        <small class="text-muted">Verifique la busqueda e intente de nuevo.</small>
                    </div>
                </div>
            `;
            }

            // Función para formatear números
            function formatNumber(num) {
                return parseFloat(num).toLocaleString('es-VE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            // Función para abrir factura en modal
            window.abrirFacturaEnModal = function(tipo, numero, sucursalId) {
                // Crear o obtener el modal
                let modal = document.getElementById('documentModal');
                if (!modal) {
                    // Si no existe el modal, crearlo
                    modal = document.createElement('div');
                    modal.className = 'modal fade';
                    modal.id = 'documentModal';
                    modal.setAttribute('tabindex', '-1');
                    modal.setAttribute('aria-hidden', 'true');
                    modal.innerHTML = `
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Cargando documento...</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="documentView">
                                <div class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <p class="mt-2">Cargando documento...</p>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                `;
                    document.body.appendChild(modal);
                }

                // Actualizar título del modal
                const modalTitle = modal.querySelector('.modal-title');
                modalTitle.innerHTML = `<i class="ri-file-copy-line"></i> Documento ${tipo === 'A' ? 'Factura' : 'Devolución'} N° ${numero}`;

                // Mostrar loading en el cuerpo
                const documentView = modal.querySelector('#documentView');
                documentView.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando documento...</p>
                </div>
            `;

                // Inicializar el modal de Bootstrap
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();

                // Cargar el contenido vía AJAX
                fetch('/openDoc', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        tipofac: tipo,
                        numerod: numero,
                        fksucu: sucursalId
                    })
                })
                    .then(response => response.text())
                    .then(html => {
                        documentView.innerHTML = html;
                        // Ejecutar scripts dentro del contenido cargado
                        const scripts = documentView.querySelectorAll('script');
                        scripts.forEach(script => {
                            const newScript = document.createElement('script');
                            if (script.src) {
                                newScript.src = script.src;
                            } else {
                                newScript.textContent = script.textContent;
                            }
                            documentView.appendChild(newScript);
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        documentView.innerHTML = `
                    <div class="alert alert-danger text-center">
                        <i class="ri-error-warning-line fs-4"></i>
                        <p class="mb-0 mt-2">Error al cargar el documento</p>
                        <small>${error.message}</small>
                    </div>
                `;
                    });
            };

            // Función para abrir factura desde URL (para compatibilidad)
            window.abrirFactura = function(tipo, numero, sucursalId) {
                abrirFacturaEnModal(tipo, numero, sucursalId);
            };

            // Eventos
            btnBuscar.addEventListener('click', () => buscarFactura('A'));
            btnBuscarDevolucion.addEventListener('click', () => buscarFactura('B'));

            // Búsqueda al presionar Enter
            inputFactura.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    buscarFactura('A');
                }
            });

        });
    </script>
@endsection
