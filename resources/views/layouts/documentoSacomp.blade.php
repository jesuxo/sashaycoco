<style>
    .documento-header {
        background: linear-gradient(135deg, #2e4a99 0%, #458cca 100%);
        color: white;
        padding: 20px;
        border-radius: 10px 10px 0 0;
        margin-bottom: 0;
    }

    .documento-header a {
        color: white;
        text-decoration: none;
        transition: opacity 0.3s;
    }

    .documento-header a:hover {
        opacity: 0.8;
        text-decoration: underline;
    }

    .documento-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .info-section {
        background: #f8fafc;
        padding: 20px;
        border-bottom: 1px solid #e9ecef;
    }

    .info-label {
        color: #6c757d;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
        font-weight: 600;
    }

    .info-value {
        color: #2e4a99;
        font-weight: 600;
        font-size: 16px;
    }

    .info-value a {
        color: #2e4a99;
        text-decoration: none;
    }

    .info-value a:hover {
        text-decoration: underline;
    }

    .notas-section {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 12px 15px;
        margin: 15px 20px;
        border-radius: 8px;
        font-size: 13px;
    }

    .notas-section p {
        margin-bottom: 5px;
        color: #856404;
    }

    .notas-section p:last-child {
        margin-bottom: 0;
    }

    .table-documento {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 5px;
        margin: 10px 0;
    }

    .table-documento thead th {
        background: linear-gradient(135deg, #2e4a99 0%, #458cca 100%);
        color: white;
        font-weight: 600;
        font-size: 13px;
        padding: 12px 8px;
        border: none;
        white-space: nowrap;
    }

    .table-documento thead th:first-child {
        border-radius: 8px 0 0 8px;
    }

    .table-documento thead th:last-child {
        border-radius: 0 8px 8px 0;
    }

    .table-documento tbody tr {
        background: white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        transition: all 0.3s;
    }

    .table-documento tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .table-documento tbody td {
        padding: 12px 8px;
        border: none;
        border-top: 1px solid #f1f5f9;
        font-size: 13px;
    }

    .table-documento tbody td:first-child {
        border-radius: 8px 0 0 8px;
    }

    .table-documento tbody td:last-child {
        border-radius: 0 8px 8px 0;
    }

    .codigo-producto {
        background: #e9ecef;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        color: #495057;
        white-space: nowrap;
    }

    .nombre-producto {
        font-weight: 500;
        color: #212529;
    }

    .cantidad-badge {
        background: #e7f5ff;
        color: #2e4a99;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        display: inline-block;
    }

    .precio-cell {
        font-weight: 600;
        color: #2e4a99;
    }

    .total-row {
        background: #f8f9fa !important;
        font-weight: 600;
    }

    .total-row td {
        border-top: 2px solid #dee2e6 !important;
    }

    .btn-print {
        background: linear-gradient(135deg, #2e4a99 0%, #458cca 100%);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
        box-shadow: 0 5px 15px rgba(46, 74, 153, 0.3);
    }

    .btn-print:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(46, 74, 153, 0.4);
        color: white;
    }

    .btn-print i {
        margin-right: 8px;
    }

    @media print {
        .documento-header {
            background: #2e4a99 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .table-documento thead th {
            background: #2e4a99 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .btn-print {
            display: none;
        }
    }

    .row-separator {
        background: #f8fafc;
    }

    .text-muted-custom {
        color: #6c757d;
        font-size: 12px;
    }
</style>

<div class="documento-card">
    <!-- Header con gradiente -->
    <div class="documento-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="text-white mb-2">Documento de Compra</h4>
                <p class="text-white-50 mb-0">
                    <i class="ri-file-list-line me-1"></i>
                    {{ $documento->tipocom == 'H' ? 'Compra' : 'Devolución' }} #{{ $numerod }}
                </p>
            </div>
            <div class="text-end">
                <span class="badge bg-light text-dark p-2">
                    {{ $numerod }}
                </span>
            </div>
        </div>
    </div>

    <!-- Información principal -->
    <div class="info-section">
        <div class="row">
            <div class="col-4">
                <div class="info-label">Proveedor</div>
                <div class="info-value">
                    <a href="/proveedores/{{$documento->codprov}}/tab5" target="_blank">
                        <i class="ri-store-line me-1"></i>
                        {{$documento->descrip}}
                    </a>
                </div>
                <div class="text-muted-custom mt-1">Código: {{$documento->codprov}}</div>
            </div>

            <div class="col-3">
                <div class="info-label">Fecha</div>
                <div class="info-value">
                    <i class="ri-calendar-line me-1"></i>
                    {{$documento->fechaformat}}
                </div>
            </div>

            <div class="col-5">
                <div class="info-label">Sucursal</div>
                <div class="info-value">
                    <i class="ri-store-3-line me-1"></i>
                    {{ $documento->sucursal->descrip ?? 'N/A' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Notas -->
    @if($documento->notas1 || $documento->notas2)
        <div class="notas-section">
            <i class="ri-information-line me-2"></i>
            @if($documento->notas1)
                <p><strong>NOTA 1:</strong> {{$documento->notas1}}</p>
            @endif
            @if($documento->notas2)
                <p><strong>NOTA 2:</strong> {{$documento->notas2}}</p>
            @endif
        </div>
    @endif

    <!-- Tabla de productos -->
    <div class="p-3">
        <div class="table-responsive">
            <table class="table-documento">
                <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="10%">Código</th>
                    <th width="35%">Producto</th>
                    <th width="8%">Cantidad</th>
                    <th width="10%">Costo</th>
                    <th width="10%">Precio 1</th>
                    <th width="10%">Precio 2</th>
                    <th width="12%">Precio 3</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $totalItems = 0;
                    $totalCosto = 0;
                    $totalPrecio1 = 0;
                @endphp

                @foreach($documento->items as $index => $item)
                    @if($item->fk_sucursal == $documento->fk_sucursal)
                        @php
                            $totalItems += $item->cantidad;
                            $totalCosto += $item->preciod * $item->cantidad;
                            $totalPrecio1 += $item->costod * $item->cantidad;
                        @endphp
                        <tr>
                            <td class="text-center">
                                <span class="codigo-producto">{{ $index+1 }}</span>
                            </td>
                            <td>
                                    <span class="codigo-producto">
                                        {{ (isset($item->producto->codprod))? $item->producto->codprod : $item->coditem }}
                                    </span>
                            </td>
                            <td>
                                    <span class="nombre-producto">
                                        {{ (isset($item->producto->descrip))? $item->producto->descrip : '' }}
                                    </span>
                            </td>
                            <td class="text-center">
                                    <span class="cantidad-badge">
                                        {{ number_format($item->cantidad, 0) }}
                                    </span>
                            </td>
                            <td class="text-end precio-cell">
                                ${{ number_format($item->preciod, 2, ',', '.') }}
                            </td>
                            <td class="text-end">
                                ${{ number_format($item->costod, 2, ',', '.') }}
                            </td>
                            <td class="text-end">
                                ${{ number_format($item->costod2, 2, ',', '.') }}
                            </td>
                            <td class="text-end">
                                ${{ number_format($item->costod3, 2, ',', '.') }}
                            </td>
                        </tr>
                    @endif
                @endforeach

                <!-- Fila de totales -->
                <tr class="total-row">
                    <td colspan="3" class="text-end fw-bold">TOTALES:</td>
                    <td class="text-center">
                        <span class="fw-bold">{{ number_format($totalItems, 0) }}</span>
                    </td>
                    <td class="text-end fw-bold">${{ number_format($totalCosto, 2, ',', '.') }}</td>
                    <td class="text-end fw-bold">${{ number_format($totalPrecio1, 2, ',', '.') }}</td>
                    <td class="text-end">-</td>
                    <td class="text-end">-</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Resumen final -->
        <div class="row mt-4">
            <div class="col-6">
                <div class="bg-light p-3 rounded">
                    <p class="mb-1 text-muted">Resumen del documento</p>
                    <h5 class="mb-0">
                        Total items: <span class="fw-bold">{{ $documento->items->count() }}</span> |
                        Total unidades: <span class="fw-bold">{{ $totalItems }}</span>
                    </h5>
                </div>
            </div>
            <div class="col -6 text-end">
                <div class="bg-light p-3 rounded">
                    <p class="mb-1 text-muted">Monto total</p>
                    <h3 class="mb-0" style="color: #2e4a99;">
                        ${{ number_format($totalCosto - $documento->monto, 2, ',', '.') }}
                    </h3>

                </div>
            </div>
        </div>

        <!-- Botón de impresión -->
        <div class="text-center mt-4 d-print-none">
            <a href="javascript:window.print()" class="btn-print">
                <i class="ri-printer-line"></i> Imprimir Documento
            </a>
        </div>
    </div>
</div>
