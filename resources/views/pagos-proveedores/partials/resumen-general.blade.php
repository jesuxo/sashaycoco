<div class="container-fluid">


    {{-- Tabla de resumen por pago --}}
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center d-none">
            <h6 class="mb-0">Detalle de Pedido</h6>
            <div>
                <input type="text" id="buscarResumen" class="form-control form-control-sm" placeholder="Buscar por folio o proveedor..." style="width: 250px;">
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                <table class="table table-bordered table-hover mb-0" id="tablaResumen">
                    <thead class="table-light sticky-top">
                    <tr>
                        <th class="text-center">Notas</th>
                        <th class="text-center">Fecha</th>
                        <th class="text-center">Motos</th>
                        <th class="text-center">Motos x Recibir</th> {{-- Nueva columna --}}
                        <th class="text-center">Monto  </th>
                        <th class="text-center">Comprobantes</th>
                        <th class="text-center">Diferencia</th>
                        <th class="text-center">Acciones</th>
                    </thead>
                    <tbody>
                    @foreach($resumenPagos as $pago)
                        <tr  >
                            <td class="text-start">{{ $pago['notas'] }} <br><small class="text-muted small">{{ $pago['numero_aprobacion'] }}</small> </td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($pago['fecha_pago'])->format('d/m/Y') }}</td>
                            <td class="text-center">
                                @if(!empty($pago['motos_por_instancia']))
                                    @foreach($pago['motos_por_instancia'] as $instancia => $cantidad)
                                        <div class="mb-1">
                                            <span class="badge bg-secondary">{{ $instancia }}</span>
                                            <span class="badge bg-info">{{ $cantidad }} motos</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span class="text-muted">Sin categoría</span>
                                @endif
                            </td>
                            <td class="text-center text-warning fw-bold">
                                {{ number_format($pago['motos_pendientes_recibir'], 0) }}
                            </td>
                            <td class="text-end">${{ number_format($pago['monto_total'], 2) }}</td>
                            <td class="text-end">
                                ${{ number_format($pago['total_comprobantes'], 2) }}
                                <br><small class="text-muted">{{ $pago['cantidad_comprobantes'] }} comprobantes</small>
                            </td>
                            <td class="text-end {{ $pago['diferencia'] > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                ${{ number_format($pago['diferencia'], 2) }}
                            </td>

                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-sm btn-warning" onclick="editarPago({{ $pago['id'] }})" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-secondary" onclick="agregarComprobante({{ $pago['id'] }})" title="Agregar comprobante">
                                        <i class="bi bi-camera"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info" onclick="verComprobantes({{ $pago['id'] }})" title="Ver comprobantes">
                                        <i class="bi bi-receipt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot class="table-secondary">
                    <tr>
                        <th colspan="2" class="text-end">TOTALES:</th>
                        <th class="text-center">{{ number_format($estadisticas['total_motos'], 0) }}</th>
                        <th class="text-center"> </th>
                        <th class="text-end">${{ number_format($estadisticas['total_monto_pagos'], 2) }}</th>
                        <th class="text-end">${{ number_format($estadisticas['total_comprobantes'], 2) }}</th>
                        <th class="text-end fw-bold">${{ number_format($estadisticas['total_diferencia'], 2) }}</th>
                        <th class="text-end fw-bold"> </th>

                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <hr class="my-3 d-none">

    <div class="d-flex justify-content-end gap-2 d-none">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i>Cerrar
        </button>
        <a   class="btn btn-primary" href="/pagos-proveedores/exportar-resumen">
            <i class="ri-download-line me-1"></i>Exportar a Excel
        </a>
    </div>
</div>

<script>
    // Búsqueda en la tabla de resumen
    $('#buscarResumen').on('keyup', function() {
        const term = $(this).val().toLowerCase();
        $('#tablaResumen tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(term) > -1);
        });
    });


</script>

<style>
    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 10;
    }
</style>
