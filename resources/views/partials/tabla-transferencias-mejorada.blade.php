<div class="card">
    <div class="card-header align-items-center d-flex">
        <h4 class="card-title mb-0 flex-grow-1">{{ $titulo }}</h4>
        <small>{{ str_replace('to', ' al ', $fechasreport ?? '') }}</small>
    </div>
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="text-muted small">
                <i class="ri-information-line me-1"></i> Usa las flechas o desplázate para ver todas las columnas
            </div>
            <div class="scroll-buttons">
                <button class="scroll-btn" onclick="scrollTabla('left', this)" title="Desplazar a la izquierda">
                    <i class="ri-arrow-left-s-line"></i>
                </button>
                <button class="scroll-btn" onclick="scrollTabla('right', this)" title="Desplazar a la derecha">
                    <i class="ri-arrow-right-s-line"></i>
                </button>
            </div>
        </div>

        <div class="table-responsive table-card tabla-container" style="overflow-x: auto; max-width: 100%;">
            <div style="min-width: 1400px; min-height: 400px;">
                <table width="100%" class="table table-nowrap align-middle">
                    <thead>
                    <tr>
                        <th width="5%" class="p-2 text-center tdlineff">Img</th>
                        <th width="8%" class="p-2 text-center tdlineff">Fecha</th>
                        <th width="8%" class="p-2 text-center tdlineff">Nro.Transf</th>
                        <th width="20%" class="p-2 text-start tdlineff">Titular</th>
                        <th width="12%" class="p-2 text-center tdlineff">Banco</th>
                        <th width="8%" class="p-2 text-center tdlineff">Sucursal</th>
                        <th width="8%" class="p-2 text-center tdlineff">Monto</th>
                        <th width="8%" class="p-2 text-center tdlineff">Estado</th>
                        <th width="8%" class="p-2 text-center tdlineff">Creado</th>
                        <th width="5%" class="p-2 text-center tdlineff">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($transferencias as $transf)
                        @include('partials.fila-transferencia', ['transf' => $transf, 'tipo' => $tipo])
                    @empty
                        <tr>
                            <td colspan="13" class="text-center p-4">
                                <i class="ri-inbox-line fs-1 text-muted"></i>
                                <p class="text-muted mt-2">No hay transferencias para mostrar</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="scroll-hint">
            <i class="ri-arrow-left-right-line me-1"></i> Desliza hacia los lados para ver más columnas
        </div>
    </div>
</div>
