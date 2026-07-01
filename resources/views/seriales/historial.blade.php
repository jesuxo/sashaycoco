{{-- resources/views/seriales/historial.blade.php --}}
@extends('layouts.master')

@section('css')
    <style>
        .timeline {
            position: relative;
            padding: 20px 0;
        }
        .timeline-item {
            position: relative;
            padding-left: 40px;
            margin-bottom: 20px;
        }
        .timeline-item:before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: -20px;
            width: 2px;
            background: #0072c5;
        }
        .timeline-item:last-child:before {
            display: none;
        }
        .timeline-badge {
            position: absolute;
            left: 0;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: white;
            border: 2px solid #0072c5;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }
        .timeline-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 3px solid #0072c5;
        }
        .resultados-lista {
            max-height: calc(100vh - 250px);
            overflow-y: auto;
        }
        .serial-item {
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 8px;
        }
        .serial-item:hover {
            background-color: #e3f2fd;
            transform: translateX(5px);
        }
        .serial-item.active {
            background-color: #0072c5;
            color: white;
        }
        .badge-origen {
            font-size: 10px;
            padding: 3px 8px;
        }
        .highlight {
            background-color: #fff3cd;
            font-weight: bold;
        }
        .loader {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #0072c5;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <!-- Columna Izquierda -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0 text-white">
                        <i class="bi bi-search me-2"></i>
                        Buscar Serial
                    </h5>
                </div>
                <div class="card-body">
                    <input type="text"
                           id="serial_input"
                           class="form-control form-control-lg"
                           placeholder="Ejemplo: 8420"
                           autocomplete="off">
                    <small class="text-muted mt-1 d-block">
                        <i class="bi bi-info-circle"></i> Busca coincidencias en compras, ventas y operaciones
                    </small>

                    <div id="resultados" class="mt-4" style="display:none;">
                        <hr>
                        <h6>Resultados encontrados: <span id="totalResultados">0</span></h6>
                        <div id="resultados_lista" class="resultados-lista"></div>
                    </div>

                    <div id="loading" class="text-center mt-4" style="display:none;">
                        <div class="loader"></div>
                        <p>Buscando...</p>
                    </div>

                    <div id="error" class="alert alert-danger mt-4" style="display:none;"></div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-white">
                        <i class="bi bi-clock-history me-2"></i>
                        Historial del Serial
                        <span id="serial_seleccionado" class="badge bg-light text-dark ms-2">Ninguno</span>
                    </h5>
                </div>
                <div class="card-body" id="historial_container">
                    <div class="text-center py-5" id="mensaje_inicial">
                        <i class="bi bi-search" style="font-size: 4rem; color: #ccc;"></i>
                        <h4 class="mt-3">Seleccione un serial</h4>
                        <p>Busque un serial a la izquierda y haga clic para ver su historial</p>
                    </div>
                    <div id="historial_content" style="display:none;"></div>
                    <div id="loading_historial" class="text-center" style="display:none;">
                        <div class="loader"></div>
                        <p>Cargando historial...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let searchTimeout;

        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('serial_input');
            const resultadosDiv = document.getElementById('resultados');
            const resultadosLista = document.getElementById('resultados_lista');
            const loadingDiv = document.getElementById('loading');
            const errorDiv = document.getElementById('error');
            const totalResultados = document.getElementById('totalResultados');
            const historialContent = document.getElementById('historial_content');
            const mensajeInicial = document.getElementById('mensaje_inicial');
            const loadingHistorial = document.getElementById('loading_historial');
            const serialSeleccionado = document.getElementById('serial_seleccionado');

            function buscarSeriales(query) {
                loadingDiv.style.display = 'block';
                resultadosDiv.style.display = 'none';
                errorDiv.style.display = 'none';

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                fetch('/seriales/buscar-ajax', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ serial: query })
                })
                    .then(response => response.json())
                    .then(data => {
                        loadingDiv.style.display = 'none';

                        let resultadosArray = [];
                        if (data.data && typeof data.data === 'object') {
                            resultadosArray = Object.values(data.data);
                        }

                        // Eliminar duplicados por serial
                        const unicos = [];
                        const vistos = new Set();
                        resultadosArray.forEach(item => {
                            if (!vistos.has(item.serial)) {
                                vistos.add(item.serial);
                                unicos.push(item);
                            }
                        });

                        if (data.success && unicos.length > 0) {
                            mostrarResultados(unicos, query);
                            totalResultados.textContent = unicos.length;
                            resultadosDiv.style.display = 'block';
                        } else {
                            errorDiv.innerHTML = '<i class="bi bi-exclamation-triangle"></i> No se encontraron resultados para: ' + query;
                            errorDiv.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        loadingDiv.style.display = 'none';
                        errorDiv.innerHTML = '<strong>ERROR:</strong> ' + error.message;
                        errorDiv.style.display = 'block';
                        console.error('Error:', error);
                    });
            }

            function mostrarResultados(seriales, busqueda) {
                let html = '';
                seriales.forEach(item => {
                    let serialText = item.serial;
                    if (busqueda && serialText.toLowerCase().includes(busqueda.toLowerCase())) {
                        const regex = new RegExp(`(${busqueda})`, 'gi');
                        serialText = serialText.replace(regex, '<span class="highlight">$1</span>');
                    }

                    let badgeClass = item.origen === 'COMPRA' ? 'bg-success' : (item.origen === 'VENTA' ? 'bg-primary' : 'bg-warning text-dark');
                    let badgeIcon = item.origen === 'COMPRA' ? 'bi-cart' : (item.origen === 'VENTA' ? 'bi-receipt' : 'bi-arrow-repeat');

                    html += `
                        <div class="list-group-item serial-item"
                             data-codprod="${item.codprod}"
                             data-serial="${item.serial}"
                             onclick="verHistorial('${item.codprod}', '${item.serial}')">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>${serialText}</strong>
                                    <br>
                                    <small class="text-muted small">${item.producto || 'N/A'}</small>
                                </div>

                            </div>
                        </div>
                    `;
                });
                resultadosLista.innerHTML = html;
            }

            window.verHistorial = function(codprod, serial) {
                document.querySelectorAll('.serial-item').forEach(el => el.classList.remove('active'));
                const selected = document.querySelector(`.serial-item[data-serial="${serial}"][data-codprod="${codprod}"]`);
                if (selected) selected.classList.add('active');

                serialSeleccionado.textContent = serial;
                mensajeInicial.style.display = 'none';
                historialContent.style.display = 'none';
                loadingHistorial.style.display = 'block';

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                fetch('/seriales/historial-ajax', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ codprod: codprod, serial: serial })
                })
                    .then(response => response.json())
                    .then(data => {
                        loadingHistorial.style.display = 'none';
                        if (data.success) {
                            historialContent.innerHTML = data.html;
                            historialContent.style.display = 'block';
                        } else {
                            historialContent.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Error') + '</div>';
                            historialContent.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        loadingHistorial.style.display = 'none';
                        historialContent.innerHTML = '<div class="alert alert-danger">Error: ' + error.message + '</div>';
                        historialContent.style.display = 'block';
                    });
            };

            input.addEventListener('keyup', function(e) {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (e.key === 'Enter') {
                    if (query.length >= 2) {
                        buscarSeriales(query);
                    } else if (query.length > 0) {
                        errorDiv.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Ingrese al menos 2 caracteres';
                        errorDiv.style.display = 'block';
                    }
                    return;
                }

                if (query.length >= 2) {
                    searchTimeout = setTimeout(() => buscarSeriales(query), 500);
                } else if (query.length === 0) {
                    resultadosDiv.style.display = 'none';
                    errorDiv.style.display = 'none';
                }
            });
        });

        function formatFecha(fecha) {
            if (!fecha) return 'N/A';
            return new Date(fecha).toLocaleDateString('es-ES');
        }
    </script>
@endsection
