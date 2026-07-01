<div class="container-fluid">
    <form id="formEditarPago">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Folio</label>
                <input type="text" class="form-control bg-light" value="{{ $pago->folio }}" readonly disabled>
                <small class="text-muted">El folio no se puede modificar</small>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Estado</label>
                @switch($pago->estado)
                    @case('pendiente')
                        <span class="badge bg-secondary d-inline-block mt-2">Pendiente</span>
                        @break
                    @case('parcial')
                        <span class="badge bg-warning text-dark d-inline-block mt-2">Parcial</span>
                        @break
                    @case('completado')
                        <span class="badge bg-success d-inline-block mt-2">Completado</span>
                        @break
                    @case('aprobado')
                        <span class="badge bg-info d-inline-block mt-2">Aprobado</span>
                        @break
                @endswitch
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Fecha de Pedido <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="fecha_pago" id="fecha_pago_edit"
                       value="{{ $pago->fecha_pago->format('Y-m-d') }}" required>
                <div class="invalid-feedback">La fecha del pedido es requerida</div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Proveedor <span class="text-danger">*</span></label>
                <select class="form-select select2-proveedor" name="codprov" id="codprov_edit" required>
                    <option value="">Seleccione un proveedor...</option>
                    @foreach($proveedores as $prov)
                        <option value="{{ $prov->codprov }}" {{ $pago->codprov == $prov->codprov ? 'selected' : '' }}>
                            {{ $prov->codprov }} - {{ $prov->descrip }}
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback">Seleccione un proveedor</div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label fw-bold">Notas</label>
                <textarea class="form-control" name="notas" id="notas_edit" rows="3"
                          placeholder="Observaciones adicionales...">{{ $pago->notas }}</textarea>
            </div>
        </div>

        <hr class="my-3">

        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="bi bi-x-circle me-1"></i>Cancelar
            </button>
            <button type="submit" class="btn btn-primary" id="btnGuardarEdicion">
                <i class="bi bi-save me-1"></i>Guardar Cambios
            </button>
        </div>
    </form>
</div>
