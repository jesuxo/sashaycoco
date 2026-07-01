<div class="container-fluid">
    <form id="formAgregarComprobante" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Tipo de Comprobante</label>
                <select class="form-select" id="tipo_comprobante" name="tipo_comprobante">
                    <option value="Transferencia">Transferencia Bancaria</option>
                    <option value="Depósito">Depósito</option>
                    <option value="Efectivo">Efectivo</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Número de Comprobante</label>
                <input type="text" class="form-control" id="numero_comprobante" name="numero_comprobante"
                       placeholder="Opcional">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Fecha del Comprobante <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="fecha_comprobante" name="fecha_comprobante"
                       value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Monto <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" id="monto_comprobante" name="monto"
                           step="0.01" min="0" required>
                </div>
                <small class="text-muted">Monto total de este comprobante</small>
            </div>
        </div>

        <div class="col-md-12 mb-3">
            <label class="form-label fw-bold">Archivo (Imagen o PDF) <span class="text-danger">*</span></label>
            <input type="file" class="form-control" id="archivo_comprobante" name="archivo"
                   accept="image/jpeg,image/png,image/jpg,image/gif,application/pdf" required>
            <small class="text-muted">Formatos permitidos: JPG, PNG, GIF, PDF (máx. 5MB)</small>
            <div id="previewContainer" class="mt-2">
                <div id="nombreArchivo" class="text-muted small"></div>
                <img id="previewImagen" src="#" style="max-height: 150px; display: none;" class="mt-2 border rounded">
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label fw-bold">Notas</label>
                <textarea class="form-control" id="notas_comprobante" name="notas" rows="2"
                          placeholder="Observaciones adicionales..."></textarea>
            </div>
        </div>

        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Total del comprobante:</strong> <span id="totalMonto">$0.00</span>
        </div>

        <hr class="my-3">

        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="bi bi-x-circle me-1"></i>Cancelar
            </button>
            <button type="submit" class="btn btn-primary" id="btnGuardarComprobante">
                <i class="bi bi-save me-1"></i>Guardar Comprobante
            </button>
        </div>
    </form>
</div>
