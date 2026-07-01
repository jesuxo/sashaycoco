<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Pedido #{{ $pago->folio }}</strong> - {{ $pago->proveedor->descrip ?? $pago->proveedor_codprov }}
                </div>
                <button class="btn btn-sm btn-success" onclick="agregarComprobante({{ $pago->id }})">
                    <i class="bi bi-plus-circle me-1"></i>Nuevo Comprobante
                </button>
            </div>
        </div>
    </div>

    @if($comprobantes->count() > 0)
        <div class="row">
            @foreach($comprobantes as $comp)
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-receipt me-2"></i>
                                <strong>{{ $comp->tipo_comprobante }}</strong>
                                @if($comp->numero_comprobante)
                                    <br><small class="text-muted">N°: {{ $comp->numero_comprobante }}</small>
                                @endif
                            </div>
                            <div>
                                <button class="btn btn-sm btn-danger" onclick="eliminarComprobante({{ $comp->id }})"
                                        data-bs-toggle="tooltip" title="Eliminar comprobante">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <i class="bi bi-calendar text-warning me-1"></i>
                                        <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($comp->fecha_comprobante)->format('d/m/Y') }}
                                    </p>
                                    <p class="mb-1">
                                        <i class="bi bi-cash-stack text-success me-1"></i>
                                        <strong>Monto:</strong> <span class="text-success">${{ number_format($comp->monto, 2) }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    @if($comp->archivo_path)
                                        @php
                                            $extension = pathinfo($comp->archivo_path, PATHINFO_EXTENSION);
                                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                                        @endphp
                                        @if($isImage)
                                            <button class="btn btn-sm btn-info w-100" onclick="verFotoComprobante('{{ asset($comp->archivo_path) }}')">
                                                <i class="bi bi-eye me-1"></i>Ver Comprobante
                                            </button>
                                        @else
                                            <a href="{{ asset($comp->archivo_path) }}" target="_blank" class="btn btn-sm btn-info w-100">
                                                <i class="bi bi-file-pdf me-1"></i>Ver PDF
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            @if($comp->notas)
                                <hr class="my-2">
                                <p class="mb-0 small">
                                    <i class="bi bi-chat-text me-1"></i>
                                    {{ $comp->notas }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Resumen de comprobantes --}}
        <div class="card mt-3 bg-light">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <h6 class="text-muted">Total Comprobantes</h6>
                        <h4 class="text-primary">{{ $comprobantes->count() }}</h4>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted">Monto Total</h6>
                        <h4 class="text-success">${{ number_format($comprobantes->sum('monto'), 2) }}</h4>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted">Monto del Pedido</h6>
                        <h4 class="text-info">${{ number_format($pago->monto_total, 2) }}</h4>
                        @if($comprobantes->sum('monto') != $pago->monto_total)
                            <small class="text-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                Diferencia: ${{ number_format($pago->monto_total - $comprobantes->sum('monto'), 2) }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning text-center py-5">
            <i class="bi bi-inbox fa-3x mb-3 d-block"></i>
            <h5>No hay comprobantes registrados</h5>
            <p class="mb-0">Agrega los comprobantes de pago para tener un mejor control.</p>
            <button class="btn btn-primary mt-3" onclick="agregarComprobante({{ $pago->id }})">
                <i class="bi bi-camera me-1"></i>Agregar Comprobante
            </button>
        </div>
    @endif

    <hr class="my-3">

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i>Cerrar
        </button>
        <button type="button" class="btn btn-primary" onclick="agregarComprobante({{ $pago->id }})">
            <i class="bi bi-plus-circle me-1"></i>Agregar Otro
        </button>
    </div>
</div>

{{-- Modal para ver foto --}}
<div class="modal fade" id="modalFotoComprobante" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="bi bi-image me-2"></i>Comprobante de Pago</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
