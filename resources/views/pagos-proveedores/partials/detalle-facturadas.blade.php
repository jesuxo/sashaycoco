<div class="container-fluid">
    <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
        <table class="table table-bordered table-hover">
            <thead class="table-light sticky-top">
            <tr>
                <th>Pedido</th>
                <th>Proveedor</th>
                <th>Producto</th>
                <th class="text-center">Cantidad</th>
                <th class="text-center">Facturadas</th>
                <th class="text-center">Recibidas</th>
                <th>Fecha</th>
            </tr>
            </thead>
            <tbody>
            @forelse($detalles as $detalle)
                <tr>
                    <td>
                        <small class="text-muted">#{{ $detalle->pago_id }}</small><br>
                        <strong>{{ Str::limit($detalle->pago->notas ?? 'Sin notas', 30) }}</strong>
                        @if($detalle->pago->numero_aprobacion)
                            <br><span class="badge bg-info">Aprob: {{ $detalle->pago->numero_aprobacion }}</span>
                        @endif
                    </td>
                    <td>{{ $detalle->pago->proveedor->descrip ?? $detalle->pago->codprov }}</td>
                    <td>
                        <strong>{{ $detalle->producto_descrip }}</strong>
                        <br><small class="text-muted">Código: {{ $detalle->producto_codprod }}</small>
                    </td>
                    <td class="text-center">{{ number_format($detalle->cantidad, 0) }}</td>
                    <td class="text-center text-success fw-bold">{{ number_format($detalle->cantidad_facturada, 0) }}</td>
                    <td class="text-center text-warning">{{ number_format($detalle->cantidad_recibida, 0) }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($detalle->created_at)->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fa-3x d-block mb-2"></i>
                        No hay motos facturadas registradas
                    </td>
                </tr>
            @endforelse
            </tbody>
            @if($detalles->count() > 0)
                <tfoot class="table-secondary">
                <tr>
                    <th colspan="3" class="text-end">TOTAL FACTURADAS:</th>
                    <th class="text-center fw-bold">{{ number_format($totalGeneral, 0) }}</th>
                    <th colspan="3"></th>
                </tr>
                </tfoot>
            @endif
        </table>
    </div>
    <hr class="my-3">
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i>Cerrar
        </button>
    </div>
</div>

<style>
    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 10;
    }
</style>
