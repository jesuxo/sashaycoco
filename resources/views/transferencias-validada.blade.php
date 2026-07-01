@extends('layouts.master-auth')
@section('title')
    Transferencia nro {{$transf->numero}}
@endsection
@section('css')
    <style>
        .comparison-container {
            display: flex;
            gap: 20px;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        .data-card, .image-card {
            flex: 1;
            min-width: 300px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .image-card {
            text-align: center;
        }
        .image-card img {
            max-width: 100%;
            max-height: 400px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: transform 0.3s;
        }
        .image-card img:hover {
            transform: scale(1.02);
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #f0f0f0;
        }
        .data-table tr:last-child td {
            border-bottom: none;
        }
        .data-table td:first-child {
            font-weight: 600;
            color: #495057;
            width: 35%;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }
        .status-aprobada {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-rechazada {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .comentario-container {
            margin-top: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid;
            text-align: left;
        }
        .comentario-aprobada {
            border-left-color: #28a745;
            background-color: #f0fff4;
        }
        .comentario-rechazada {
            border-left-color: #dc3545;
            background-color: #fff5f5;
        }
        .comentario-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            color: #495057;
            font-weight: 600;
        }
        .comentario-header i {
            font-size: 20px;
        }
        .comentario-texto {
            font-size: 14px;
            line-height: 1.6;
            color: #212529;
            padding: 10px;
            background: white;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }
        .comentario-meta {
            font-size: 12px;
            color: #6c757d;
            margin-top: 8px;
            text-align: right;
        }
        .info-adicional {
            margin-top: 20px;
            padding: 15px;
            background: #e7f5ff;
            border-radius: 8px;
            text-align: left;
            font-size: 13px;
            color: #004085;
            border: 1px solid #b8daff;
        }
        .info-adicional i {
            margin-right: 8px;
        }
        .verificacion-item {
            display: inline-block;
            padding: 4px 10px;
            background: #e9ecef;
            border-radius: 20px;
            font-size: 12px;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .verificacion-item i {
            color: #28a745;
            margin-right: 4px;
        }
        .image-actions {
            margin-top: 15px;
        }
    </style>
@endsection

@section('content')
    <div class="w-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="auth-card mx-lg-3">
                        <div class="card border-0 mb-0">
                            <div class="card-body p-4">
                                <!-- Estado de la transferencia -->
                                <div class="text-center mb-4">
                                    <span class="status-badge {{ $transf->status == 1 ? 'status-aprobada' : 'status-rechazada' }}">
                                        <i class="ri-{{ $transf->status == 1 ? 'check-line' : 'close-line' }} me-1"></i>
                                        TRANSFERENCIA {{ $transf->status == 1 ? 'APROBADA' : 'RECHAZADA' }}
                                    </span>
                                    <p class="fs-15 text-muted mt-2">
                                        <i class="ri-information-line me-1"></i>
                                        Detalles de la transferencia y validación
                                    </p>
                                </div>

                                <div class="comparison-container">
                                    <!-- Columna de datos -->
                                    <div class="data-card">
                                        <h5 class="mb-3" style="color: #0072c5;">
                                            <i class="ri-file-copy-line me-2"></i>Datos de la Transferencia
                                        </h5>

                                        <table class="data-table">
                                            <tr>
                                                <td><i class="ri-hashtag me-2 text-muted"></i>Número:</td>
                                                <td><strong>{{$transf->numero}}</strong></td>
                                            </tr>
                                            <tr>
                                                <td><i class="ri-calendar-line me-2 text-muted"></i>Fecha:</td>
                                                <td><strong>{{$transf->fechaformat}}</strong></td>
                                            </tr>
                                            <tr>
                                                <td><i class="ri-money-dollar-circle-line me-2 text-muted"></i>Monto:</td>
                                                <td>
                                                    <strong class="text-{{ $transf->status == 1 ? 'success' : 'danger' }}">
                                                        {{$transf->currency}}{{ number_format($transf->monto, 2, ',', '.') }}
                                                    </strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><i class="ri-user-line me-2 text-muted"></i>Titular:</td>
                                                <td><strong>{{$transf->titular}}</strong></td>
                                            </tr>
                                            <tr>
                                                <td><i class="ri-bank-line me-2 text-muted"></i>Banco:</td>
                                                <td><strong>{{$transf->banco->descrip ?? 'N/A'}}</strong></td>
                                            </tr>
                                            <tr>
                                                <td><i class="ri-store-line me-2 text-muted"></i>Sucursal:</td>
                                                <td><strong>{{$transf->sucursal->descrip ?? 'N/A'}}</strong></td>
                                            </tr>
                                            @if($transf->tipo)
                                                <tr>
                                                    <td><i class="ri-price-tag-line me-2 text-muted"></i>Tipo:</td>
                                                    <td>
                                                        @php
                                                            $tipoColor = match($transf->tipo) {
                                                                'venta' => 'success',
                                                                'pago' => 'info',
                                                                'ahorro' => 'warning',
                                                                'proveedor' => 'primary',
                                                                'gasto' => 'danger',
                                                                default => 'secondary'
                                                            };
                                                        @endphp
                                                        <span class="badge bg-{{ $tipoColor }}">{{ $transf->tipo_texto }}</span>
                                                    </td>
                                                </tr>
                                            @endif
                                            @if($transf->categoria)
                                                <tr>
                                                    <td><i class="ri-price-tag-3-line me-2 text-muted"></i>Categoría:</td>
                                                    <td><span class="badge bg-light text-dark">{{ $transf->categoria }}</span></td>
                                                </tr>
                                            @endif
                                            @if($transf->referencia)
                                                <tr>
                                                    <td><i class="ri-file-text-line me-2 text-muted"></i>Referencia:</td>
                                                    <td><strong>{{ $transf->referencia }}</strong></td>
                                                </tr>
                                            @endif
                                        </table>

                                        @if($transf->observacion)
                                            <div class="mt-4 p-3 bg-light rounded">
                                                <small class="text-muted d-block mb-2">
                                                    <i class="ri-chat-1-line me-1"></i>Observación:
                                                </small>
                                                <p class="mb-0">{{ $transf->observacion }}</p>
                                            </div>
                                        @endif

                                        <!-- Comentario de validación (si existe) -->
                                        @if($transf->comentario_validacion)
                                            <div class="comentario-container {{ $transf->status == 1 ? 'comentario-aprobada' : 'comentario-rechazada' }}">
                                                <div class="comentario-header">
                                                    <i class="ri-chat-check-line"></i>
                                                    <span>Comentario de validación</span>
                                                </div>
                                                <div class="comentario-texto">
                                                    "{{ $transf->comentario_validacion }}"
                                                </div>
                                                <div class="comentario-meta">
                                                    <i class="ri-user-line me-1"></i> Validado por:
                                                    @if($transf->usuarioValidador)
                                                        {{ $transf->usuarioValidador->name }}
                                                    @else
                                                        Usuario #{{ $transf->usuario_valida ?? 'N/A' }}
                                                    @endif
                                                    <br>
                                                    <i class="ri-time-line me-1"></i>
                                                    {{ $transf->fecha_validacion ? $transf->fecha_validacion->format('d/m/Y H:i') : 'Fecha no disponible' }}
                                                </div>
                                            </div>
                                        @else
                                            <div class="info-adicional">
                                                <i class="ri-information-line"></i>
                                                <strong>Sin comentarios</strong> - Esta transferencia fue {{ $transf->status == 1 ? 'aprobada' : 'rechazada' }} sin comentarios adicionales.
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Columna de imagen -->
                                    <div class="image-card">
                                        <h5 class="mb-3" style="color: #0072c5;">
                                            <i class="ri-image-line me-2"></i>Captura de la Transferencia
                                        </h5>

                                        @if($transf->imagen)
                                            <div class="text-center">
                                                <img src="{{ $transf->imagen_url }}"
                                                     alt="Captura de transferencia"
                                                     class="img-fluid"
                                                     onclick="abrirImagenCompleta()"
                                                     style="cursor: pointer; max-height: 350px;">
                                            </div>

                                            <div class="image-actions">
                                                <div class="mt-3">
                                                    <small class="text-muted">
                                                        <i class="ri-file-copy-line me-1"></i>
                                                        Nombre original: {{ $transf->imagen_original }}
                                                    </small>
                                                </div>
                                                <div class="mt-3 d-flex gap-2 justify-content-center">
                                                    <button class="btn btn-sm btn-info" onclick="abrirImagenCompleta()">
                                                        <i class="ri-zoom-in-line me-1"></i> Ver imagen completa
                                                    </button>
                                                    <a href="{{ $transf->imagen_url }}"
                                                       class="btn btn-sm btn-secondary"
                                                       download="{{ $transf->imagen_original }}">
                                                        <i class="ri-download-line me-1"></i> Descargar
                                                    </a>
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-warning">
                                                <i class="ri-image-line fs-1 mb-3 d-block"></i>
                                                <p class="mb-0">No se ha cargado una imagen para esta transferencia</p>
                                            </div>
                                        @endif

                                        <!-- Información adicional de la validación -->
                                        <div class="mt-4 text-start p-3 bg-light rounded">
                                            <h6 class="mb-2"><i class="ri-shield-check-line me-1"></i>Información de validación</h6>

                                            <small class="text-muted d-block mt-2">
                                                <i class="ri-time-line me-1"></i>
                                                Validación: {{ $transf->fecha_validacion ? $transf->fecha_validacion->diffForHumans() : 'N/A' }}
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botón de volver -->
                                <div class="mt-4 text-center">
                                    <a href="{{ route('reportetransferencias') }}" class="btn btn-primary px-4">
                                        <i class="ri-arrow-left-line me-2"></i>Volver al reporte
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para imagen completa -->
    <div class="modal fade" id="imagenModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ri-image-line me-2"></i>
                        Captura de Transferencia - {{ $transf->numero }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-3">
                    @if($transf->imagen)
                        <img src="{{ $transf->imagen_url }}" class="img-fluid" alt="Captura de transferencia" style="max-height: 80vh;">
                        <div class="mt-3">
                            <a href="{{ $transf->imagen_url }}"
                               class="btn btn-primary"
                               download="{{ $transf->imagen_original }}">
                                <i class="ri-download-line me-2"></i>Descargar imagen
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('partials.footer')
@endsection

@section('scripts')
    <script>
        function abrirImagenCompleta() {
            $('#imagenModal').modal('show');
        }

        // Efecto de carga suave
        $(document).ready(function() {
            $('.data-card, .image-card').hide().fadeIn(600);
        });
    </script>
@endsection
