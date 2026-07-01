{{-- resources/views/pagos-proveedores/partials/lista.blade.php --}}
@foreach($pagos as $pago)
    <div class="card pago-card estado-{{ $pago->estado }} shadow-sm mb-3" data-pago-id="{{ $pago->id }}">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-2 flex-wrap">
                        <h5 class="mb-0 me-3"><strong>{{ $pago->notas }}</strong></h5>
                        @switch($pago->estado)
                            @case('pendiente')<span class="badge bg-secondary me-2">Pendiente</span>@break
                            @case('parcial')<span class="badge bg-warning text-dark me-2">Parcial</span>@break
                            @case('completado')<span class="badge bg-success me-2">Completado</span>@break
                            @case('aprobado')<span class="badge bg-info me-2">Aprobado</span>@break
                        @endswitch
                        @if($pago->numero_aprobacion)
                            <small class="text-muted">
                                <i class="bi bi-check-circle me-1"></i>Aprobación: {{ $pago->numero_aprobacion }}
                            </small>
                            <button class="btn btn-sm btn-link text-info p-0" onclick="editarAprobacion({{ $pago->id }}, '{{ $pago->numero_aprobacion }}')"
                                    data-bs-toggle="tooltip" title="Editar número de aprobación">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><i class="bi bi-building text-primary me-2"></i><strong>Proveedor:</strong> {{ $pago->proveedor->descrip ?? $pago->proveedor_codprov }}</p>
                            <p class="mb-1"><i class="bi bi-calendar text-warning me-2"></i><strong>Fecha Pago:</strong> {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</p>

                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><i class="bi bi-cash-stack text-success me-2"></i><strong>Monto:</strong> ${{ number_format($pago->monto_total, 2) }}</p>
                            <p class="mb-1">
                                <i class="bi bi-receipt text-secondary me-2"></i>
                                <strong>Comprobantes:</strong>
                                <span class="badge bg-info">{{ $pago->comprobantes_count ?? 0 }}</span>
                            </p>
                            <p class="mb-1"><i class="bi bi-box-seam text-info me-2"></i><strong>Productos:</strong> {{ $pago->detalles->count() }} tipos</p>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="d-flex justify-content-between mb-1">
                                <small>Progreso de recepción</small>
                                <small>{{ $pago->porcentaje_recibido }}%</small>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: {{ $pago->porcentaje_recibido }}%"></div>
                            </div>
                            <small class="text-muted">{{ $pago->total_recibido }}/{{ $pago->total_productos }} unidades recibidas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="mb-2">
                        <small class="text-muted d-block">Pendiente por recibir</small>
                        <h5 class="text-warning mb-0">{{ number_format($pago->total_pendiente, 0) }} unidades</h5>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Monto pendiente</small>
                        <h5 class="text-danger mb-0">${{ number_format($pago->monto_pendiente, 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="btn-group">
                        <button class="btn btn-sm btn-warning action-btn" style="padding: 0"
                                data-bs-toggle="tooltip" title="Editar"
                                onclick="editarPago({{ $pago->id }})" title="Editar"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-success action-btn" style="padding: 0"
                                data-bs-toggle="tooltip" title="Agregar Motos"
                                onclick="agregarProductos({{ $pago->id }})" title="Agregar Motos"><i class="ri-motorbike-fill" style="font-size: 20px"></i></button>
                        @if($pago->total_pendiente > 0)
                            <button class="btn btn-sm btn-primary action-btn" style="padding: 0"
                                    data-bs-toggle="tooltip" title="Recibir Motos"
                                    onclick="registrarDespacho({{ $pago->id }})" title="Registrar recepción"><i class="bi bi-truck"style="font-size: 20px"></i></button>
                        @endif

                        <button class="btn btn-sm btn-info action-btn" style="padding: 0"
                                data-bs-toggle="tooltip" title="Historial de Recepciones"
                                onclick="verHistorialDespachos({{ $pago->id }})"><i class="bi bi-clock-history" style="font-size: 20px"></i></button>

                        <button class="btn btn-sm btn-secondary action-btn"style="padding: 0"
                                data-bs-toggle="tooltip" title="Agregar comprobante"
                                onclick="agregarComprobante({{ $pago->id }})" title="Agregar comprobante"><i class="bi bi-camera"style="font-size: 20px"></i></button>

                        <button class="btn btn-sm btn-secondary action-btn" style="padding: 0"
                                data-bs-toggle="tooltip" title="Ver Capture/Recibo"
                                onclick="verComprobantes({{ $pago->id }})"><i class="bi bi-receipt" style="font-size: 20px"></i></button>

                        <button class="btn btn-sm btn-info action-btn" style="padding: 0"
                                data-bs-toggle="tooltip" title="Asignar Aprobacion"
                                onclick="asignarAprobacion({{ $pago->id }})" title="Asignar aprobación"><i class="bi bi-check2-circle"style="font-size: 20px"></i></button>

                        <button class="btn btn-sm btn-danger action-btn" style="padding: 0"onclick="eliminarPago({{ $pago->id }})" title="Eliminar"><i class="bi bi-trash"style="font-size: 20px"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

@if($pagos->isEmpty())
    <div class="alert alert-info text-center py-5">
        <i class="bi bi-info-circle fa-3x mb-3"></i>
        <h5>No se encontraron pedidos</h5>
        <button class="btn btn-primary mt-3" onclick="abrirModalCrear()"><i class="bi bi-plus-circle me-2"></i>Crear nuevo pedido</button>
    </div>
@endif

@if(method_exists($pagos, 'links'))
    <div class="d-flex justify-content-end mt-4">{{ $pagos->withQueryString()->links() }}</div>
@endif
