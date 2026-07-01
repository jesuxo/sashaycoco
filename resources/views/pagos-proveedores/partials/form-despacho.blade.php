{{-- resources/views/pagos-proveedores/partials/form-despacho.blade.php --}}
<div class="container-fluid">
    <form id="formRegistrarDespacho">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Fecha de Recepción <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="fecha_despacho" id="fecha_despacho"
                       value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Número de Guía / Remisión</label>
                <input type="text" class="form-control" name="numero_guia" id="numero_guia"
                       placeholder="Opcional">
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label fw-bold">Notas</label>
                <textarea class="form-control" name="notas" id="notas_despacho" rows="2"
                          placeholder="Observaciones adicionales..."></textarea>
            </div>
        </div>

        <hr class="my-3">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Productos Pendientes por Recibir</h5>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="seleccionarTodos">
                <label class="form-check-label" for="seleccionarTodos">
                    Seleccionar Todos
                </label>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                <tr>
                    <th width="40">Seleccionar</th>
                    <th>Producto</th>
                    <th width="100">Solicitado</th>
                    <th width="100">Recibido</th>
                    <th width="100">Pendiente</th>
                    <th width="120">Cantidad a Recibir</th>
                </thead>
                <tbody>
                @php $hayPendientes = false; @endphp
                @foreach($pago->detalles as $detalle)
                    @php
                        $pendiente = $detalle->cantidad - $detalle->cantidad_recibida;
                    @endphp
                    @if($pendiente > 0)
                        @php $hayPendientes = true; @endphp
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox-producto" value="{{ $detalle->id }}">
                            </td>
                            <td>
                                <strong>{{ $detalle->producto_descrip }}</strong>
                                <br><small class="text-muted">Código: {{ $detalle->producto_codprod }}</small>
                            </td>
                            <td class="text-center">{{ $detalle->cantidad }}</td>
                            <td class="text-center">{{ $detalle->cantidad_recibida }}</td>
                            <td class="text-center text-warning fw-bold">{{ $pendiente }}</td>
                            <td>
                                <input type="number" class="form-control cantidad-recibir"
                                       id="cantidad_{{ $detalle->id }}"
                                       min="0" max="{{ $pendiente }}" value="0"
                                       style="width: 100px;"
                                       onkeypress="return event.keyCode !== 13">
                            </td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>

        @if(!$hayPendientes)
            <div class="alert alert-success text-center py-4">
                <i class="bi bi-check-circle fa-2x mb-2"></i>
                <h5>No hay productos pendientes por recibir</h5>
                <p class="mb-0">Todos los productos de este pedido ya han sido recibidos.</p>
            </div>
        @endif

        <hr class="my-3">

        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="bi bi-x-circle me-1"></i>Cancelar
            </button>
            @if($hayPendientes)
                <button type="submit" class="btn btn-primary" id="btnGuardarDespacho">
                    <i class="bi bi-truck me-1"></i>Registrar Recepción
                </button>
            @endif
        </div>
    </form>
</div>
