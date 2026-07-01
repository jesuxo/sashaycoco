{{-- resources/views/partials/modal-eliminar.blade.php --}}
<div class="modal fade flip" id="deleteOrder" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-5 text-center">
                <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                           colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px">
                </lord-icon>
                <div class="mt-4 text-center">
                    <h4>¿Desea eliminar esta transferencia?</h4>
                    <p class="text-muted fs-15 mb-4">
                        Borrando este registro ud eliminará la información de la base de datos </p>
                    <div class="hstack gap-2 justify-content-center remove" id="cargandodelete">
                        <button class="btn btn-link link-success fw-medium text-decoration-none"
                                id="deleteRecord-close" data-bs-dismiss="modal">
                            <i class="ri-close-line me-1 align-middle"></i> Cancelar
                        </button>
                        @if(auth()->user()->can('menu_transferencias_eliminar'))
                            <button class="btn btn-danger" id="deleterecord" data-id="">Sí, Eliminar</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
