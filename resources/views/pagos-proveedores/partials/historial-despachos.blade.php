<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Pago #{{ $pago->folio }}</strong> - {{ $pago->proveedor->descrip ?? $pago->proveedor_codprov }}
            </div>
        </div>
    </div>

    @if($despachos->count() > 0)
        <div class="timeline">
            @foreach($despachos as $despacho)
                <div class="card mb-3 border-left-{{ $loop->first ? 'success' : 'info' }}">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-truck me-2"></i>
                                <strong>Recepción #{{ $despacho->id }}</strong>
                                @if($despacho->numero_guia)
                                    <span class="badge bg-secondary ms-2">Guía: {{ $despacho->numero_guia }}</span>
                                @endif
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>
                                {{ \Carbon\Carbon::parse($despacho->fecha_despacho)->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($despacho->notas)
                            <div class="alert alert-secondary mb-3 py-2">
                                <i class="bi bi-chat-text me-1"></i>
                                <small>{{ $despacho->notas }}</small>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th width="100">Cantidad Recibida</th>
                                    <th width="120">Precio Unitario</th>
                                    <th width="120">Subtotal</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php $totalDespacho = 0; @endphp
                                @foreach($despacho->detalles as $detalle)
                                    @php
                                        $subtotal = $detalle->cantidad_recibida * $detalle->pagoDetalle->precio_unitario;
                                        $totalDespacho += $subtotal;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $detalle->pagoDetalle->producto_descrip }}</strong>
                                            <br><small class="text-muted">Código: {{ $detalle->pagoDetalle->producto_codprod }}</small>
                                        </td>
                                        <td class="text-center">{{ number_format($detalle->cantidad_recibida, 0) }}</td>
                                        <td class="text-end">${{ number_format($detalle->pagoDetalle->precio_unitario, 2) }}</td>
                                        <td class="text-end">${{ number_format($subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot class="table-secondary">
                                <tr>
                                    <th colspan="3" class="text-end">Total Recepción:</th>
                                    <th class="text-end">${{ number_format($totalDespacho, 2) }}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Resumen general --}}
        <div class="card mt-3 bg-light">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <h6 class="text-muted">Total Recepciones</h6>
                        <h4 class="text-primary">{{ $despachos->count() }}</h4>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted">Unidades Recibidas</h6>
                        <h4 class="text-success">{{ number_format($pago->total_recibido, 0) }}</h4>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted">Monto Total Recibido</h6>
                        <h4 class="text-info">
                            ${{ number_format($despachos->sum(function($d) {
                                return $d->detalles->sum(function($det) {
                                    return $det->cantidad_recibida * $det->pagoDetalle->precio_unitario;
                                });
                            }), 2) }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning text-center py-5">
            <i class="bi bi-inbox fa-3x mb-3 d-block"></i>
            <h5>No hay recepciones registradas</h5>
            <p class="mb-0">Este pedido aún no ha recibido ningún producto.</p>
            @if($pago->total_pendiente > 0)
                <button class="btn btn-primary mt-3" onclick="registrarDespacho({{ $pago->id }})">
                    <i class="bi bi-truck me-1"></i>Registrar Primera Recepción
                </button>
            @endif
        </div>
    @endif

    <hr class="my-3">

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i>Cerrar
        </button>

    </div>
</div>

<style>
    .border-left-success {
        border-left: 4px solid #28a745;
    }
    .border-left-info {
        border-left: 4px solid #17a2b8;
    }
    .timeline {
        max-height: 60vh;
        overflow-y: auto;
        padding-right: 5px;
    }
</style>
