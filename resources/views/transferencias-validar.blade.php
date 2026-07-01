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
        }
        .image-card {
            text-align: center;
        }
        .image-card img {
            max-width: 100%;
            max-height: 400px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .data-table td {
            padding: 8px;
        }
        .match {
            background-color: #d4edda;
            color: #155724;
            border-radius: 4px;
        }
        .mismatch {
            background-color: #f8d7da;
            color: #721c24;
            border-radius: 4px;
        }
        .verification-badge {
            font-size: 12px;
            margin-left: 10px;
        }
        /* Estilo para el campo de comentario */
        .comentario-container {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #0072c5;
        }
        .comentario-container textarea {
            resize: vertical;
            min-height: 80px;
        }
        .contador-caracteres {
            font-size: 11px;
            color: #6c757d;
            text-align: right;
            margin-top: 5px;
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
                                <div class="text-center mb-4">
                                    <h4 class="fs-18 error-subtitle text-uppercase mb-0">Validación de Transferencia</h4>
                                    <p class="fs-15 text-muted mt-2">Compare los datos con la imagen de la transferencia</p>
                                </div>

                                <div class="comparison-container">
                                    <!-- Columna de datos -->
                                    <div class="data-card">
                                        <h5 class="mb-3">Datos Ingresados</h5>
                                        <table class="data-table" width="100%">
                                            <tr>
                                                <td><strong>Número:</strong></td>
                                                <td id="val-numero">{{$transf->numero}}</td>
                                                <td id="check-numero" width="30"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Fecha:</strong></td>
                                                <td id="val-fecha">{{$transf->fechaformat}}</td>
                                                <td id="check-fecha"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Monto:</strong></td>
                                                <td id="val-monto">{{$transf->currency.number_format($transf->monto,2,',','.')}}</td>
                                                <td id="check-monto"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Titular:</strong></td>
                                                <td id="val-titular">{{$transf->titular}}</td>
                                                <td id="check-titular"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Banco:</strong></td>
                                                <td id="val-banco">{{$transf->banco->descrip ?? 'N/A'}}</td>
                                                <td id="check-banco"></td>
                                            </tr>
                                        </table>

                                        <!-- Campos de verificación manual -->
                                        <div class="mt-4 p-3 bg-light rounded">
                                            <h6>Verificación Manual</h6>
                                            <div class="mb-2">
                                                <label class="d-flex align-items-center">
                                                    <input type="checkbox" class="form-check-input me-2" id="check-numero-manual">
                                                    <span>El número de transferencia coincide</span>
                                                </label>
                                            </div>
                                            <div class="mb-2">
                                                <label class="d-flex align-items-center">
                                                    <input type="checkbox" class="form-check-input me-2" id="check-fecha-manual">
                                                    <span>La fecha coincide</span>
                                                </label>
                                            </div>
                                            <div class="mb-2">
                                                <label class="d-flex align-items-center">
                                                    <input type="checkbox" class="form-check-input me-2" id="check-monto-manual">
                                                    <span>El monto coincide</span>
                                                </label>
                                            </div>
                                            <div class="mb-2">
                                                <label class="d-flex align-items-center">
                                                    <input type="checkbox" class="form-check-input me-2" id="check-titular-manual">
                                                    <span>El titular coincide</span>
                                                </label>
                                            </div>
                                            <div class="mb-2">
                                                <label class="d-flex align-items-center">
                                                    <input type="checkbox" class="form-check-input me-2" id="check-banco-manual">
                                                    <span>El banco coincide</span>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Campo de comentario de validación (AGREGADO AQUÍ) -->
                                        <div class="comentario-container">
                                            <label for="comentario_validacion" class="form-label fw-bold">
                                                <i class="ri-chat-1-line me-1"></i>Comentario de validación:
                                            </label>
                                            <textarea class="form-control" id="comentario_validacion"
                                                      rows="3"
                                                      placeholder="Agregue un comentario sobre esta validación (opcional)"></textarea>
                                            <div class="contador-caracteres">
                                                <span id="caracteres-actuales">0</span>/500 caracteres
                                            </div>
                                            <small class="text-muted">
                                                Este comentario será visible en el reporte de transferencias
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Columna de imagen -->
                                    <div class="image-card">
                                        <h5 class="mb-3">Captura de la Transferencia</h5>
                                        @if($transf->imagen)
                                            <img src="{{ $transf->imagen_url }}" alt="Captura de transferencia" class="img-fluid" id="transferencia-imagen">
                                            <div class="mt-3">
                                                <small class="text-muted">Nombre original: {{ $transf->imagen_original }}</small>
                                            </div>
                                            <div class="mt-3">
                                                <button class="btn btn-sm btn-info" onclick="abrirImagenCompleta()">
                                                    <i class="ri-zoom-in-line"></i> Ver imagen completa
                                                </button>
                                            </div>
                                        @else
                                            <div class="alert alert-warning">
                                                <i class="ri-image-line fs-1"></i>
                                                <p>No se ha cargado una imagen para esta transferencia</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Acciones -->
                                <div class="mt-4 text-center">
                                    @if(\Illuminate\Support\Facades\Auth::user() and auth()->user()->can('menu_transferencias_aprobar'))
                                        <button class="btn btn-danger" onclick="validarYRechazar()" id="btn-rechazar">
                                            <i class="mdi mdi-close me-1"></i>Rechazar
                                        </button>
                                        <button class="btn btn-success m-2" onclick="validarYAprobar()" id="btn-aprobar">
                                            <i class="mdi mdi-check me-1"></i>Aprobar
                                        </button>
                                    @else
                                        <a href="#" class="btn btn-danger">
                                            <i class="mdi mdi-close me-1"></i>SOLICITE PERMISO REQUERIDO
                                        </a>
                                    @endif
                                </div>

                                <!-- Nota de verificación -->
                                <div class="mt-3 text-center text-muted">
                                    <small>
                                        <i class="ri-information-line"></i>
                                        Asegúrese de que los datos coincidan con la imagen antes de aprobar.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver imagen completa -->
    <div class="modal fade" id="imagenModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Captura de Transferencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    @if($transf->imagen)
                        <img src="{{ $transf->imagen_url }}" class="img-fluid" alt="Captura de transferencia">
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('partials.footer')
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Contador de caracteres para el comentario
        document.addEventListener('DOMContentLoaded', function() {
            const comentario = document.getElementById('comentario_validacion');
            const contador = document.getElementById('caracteres-actuales');

            if (comentario) {
                comentario.addEventListener('input', function() {
                    const longitud = this.value.length;
                    contador.textContent = longitud;

                    // Cambiar color si se acerca al límite
                    if (longitud > 450) {
                        contador.style.color = '#dc3545';
                    } else if (longitud > 400) {
                        contador.style.color = '#ffc107';
                    } else {
                        contador.style.color = '#6c757d';
                    }
                });
            }
        });

        function abrirImagenCompleta() {
            $('#imagenModal').modal('show');
        }

        function validarYAprobar() {
            const checks = [
                $('#check-numero-manual').is(':checked'),
                $('#check-fecha-manual').is(':checked'),
                $('#check-monto-manual').is(':checked')
            ];

            if (checks.filter(v => v).length < 3) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Verificación requerida',
                    text: 'Por favor verifique que el número, fecha y monto coincidan con la imagen'
                });
                return;
            }

            const comentario = $('#comentario_validacion').val();

            Swal.fire({
                icon: 'question',
                title: 'Confirmar aprobación',
                text: '¿Está seguro que los datos coinciden con la imagen?',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, aprobar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    cambiarStatus(1, comentario);
                }
            });
        }

        function validarYRechazar() {
            const comentario = $('#comentario_validacion').val();

            Swal.fire({
                icon: 'question',
                title: 'Confirmar rechazo',
                text: '¿Está seguro de rechazar esta transferencia?',
                input: 'textarea',
                inputLabel: 'Motivo del rechazo',
                inputPlaceholder: 'Explique por qué se rechaza...',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, rechazar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Si el usuario escribió en el input de Swal, usar ese; si no, usar el comentario general
                    const motivo = result.value || comentario;
                    cambiarStatus(2, motivo);
                }
            });
        }

        function cambiarStatus(va, comentario) {
            var id = '{{$transf->hashid}}';

            $.ajax({
                type: 'GET',
                url: '/transferencias/cambiarstatus/' + id,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {id: id, va: va, comentario: comentario},
                success: function (data) {
                    if (data.cambiado) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'El estado de la transferencia ha sido actualizado',
                            timer: 1500
                        }).then(() => {
                            window.location.href = '/reporte/transferencias';
                        });
                    }
                }
            });
        }
    </script>
@endsection
