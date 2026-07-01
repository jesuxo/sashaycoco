{{-- resources/views/partials/modal-pendiente.blade.php --}}
<div class="modal fade flip" id="pendienteOrder" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-5 text-center">
                <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                           colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px">
                </lord-icon>
                <div class="mt-4 text-center">
                    <h4>¿Desea colocar como pendiente esta transferencia?</h4>
                    <p class="text-muted fs-15 mb-4">
                        La transferencia volverá a estar en estado pendiente para su revisión
                    </p>
                    <div class="hstack gap-2 justify-content-center remove" id="cargandopend">
                        <button class="btn btn-link link-success fw-medium text-decoration-none"
                                id="deleteRecord-close" data-bs-dismiss="modal">
                            <i class="ri-close-line me-1 align-middle"></i> Cancelar
                        </button>
                        @if(auth()->user()->can('menu_transferencias_pendiente'))
                            <button class="btn btn-warning" id="pendrecord" data-id="" type="button">
                                Sí, colocar pendiente
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
