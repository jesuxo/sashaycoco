<style>
    .card-header {
        border-bottom: 1px solid #0c192c;
    }

    .financial-summary {
        background-color: #f8f9fa;
        border-radius: 12px;
        padding: 0;
        height: 100%;
        position: sticky;
        top: 20px;
    }

    .summary-section {
        padding: 16px;
    }

    .summary-section:last-child {
        border-bottom: none;
    }

    .summary-title {
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        color: #0c192c;
        display: inline-block;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 13px;
    }

    .summary-row.total {
        font-size: 16px;
        font-weight: bold;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 2px solid #dee2e6;
    }

    .summary-row.highlight {
        background-color: #e9ecef;
        padding: 8px;
        border-radius: 6px;
        margin: 8px -8px;
    }

    .payment-item {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        margin-bottom: 6px;
        padding: 4px 0;
    }

    .payment-item:last-child {
        border-bottom: none;
    }

    .badge-total {
        background-color: #0c192c;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: normal;
    }

    .text-success-custom {
        color: #28a745;
    }

    .text-warning-custom {
        color: #ffc107;
    }

    .text-danger-custom {
        color: #dc3545;
    }

    .small-text {
        font-size: 11px;
        color: #6c757d;
    }

    .divider-dashed {
        border-top: 1px dashed #dee2e6;
        margin: 12px 0;
    }

    .product-table {
        font-size: 13px;
    }

    .product-table th {
        font-size: 12px;
        background-color: #f8f9fa;
    }

    /* Estilos para la barra de navegación */
    .nav-bar {
        background: #f8f9fa;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #dee2e6;
    }

    .nav-bar .btn-group {
        gap: 8px;
    }

    .nav-bar .btn {
        padding: 6px 16px;
        font-size: 14px;
    }

    .nav-bar .badge-info {
        background-color: #0c192c;
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
    }

    /* ========== ESTILOS PARA IMPRESIÓN ========== */
    @media print {
        /* Ocultar elementos no deseados en la impresión */
        .nav-bar,
        .btn-print,
        .btn-success,
        .hstack.gap-2.justify-content-end.d-print-none,
        .modal,
        .modal-backdrop,
        button,
        .btn,
        [data-bs-toggle="modal"],
        .btn-group .btn,
        .d-print-none {
            display: none !important;
        }

        /* Eliminar márgenes y paddings extra */
        body {
            margin: 0;
            padding: 0;
            background: white;
        }

        .container, .container-fluid, .row {
            margin: 0;
            padding: 0;
        }

        .card-body {
            padding: 0 !important;
        }

        /* Asegurar que todo el contenido se vea en blanco y negro */
        * {
            color: black !important;
            background-color: white !important;
            box-shadow: none !important;
            text-shadow: none !important;
        }

        /* Mantener bordes de la tabla */
        .product-table,
        .product-table th,
        .product-table td {
            border: 1px solid #ccc !important;
        }

        .financial-summary {
            background-color: white !important;
            box-shadow: none !important;
        }

        .summary-section {
            border-bottom: 1px solid #ccc !important;
        }

        /* Asegurar que los colores de texto se mantengan legibles */
        .text-success-custom,
        .text-warning-custom,
        .text-danger-custom,
        .badge-total {
            color: black !important;
            background-color: transparent !important;
        }

        /* Evitar saltos de página dentro de secciones importantes */
        .financial-summary,
        .product-table,
        .card-header {
            page-break-inside: avoid;
        }

        /* Forzar fondo blanco en todos los elementos */
        div, section, article, header, footer, main {
            background-color: white !important;
        }

        /* Ajustar el ancho para impresión */
        .col-lg-8, .col-lg-4 {
            width: 100% !important;
            flex: 0 0 100% !important;
            max-width: 100% !important;
        }

        /* Opcional: organizar en dos columnas en impresión si es necesario */
        @media print and (min-width: 800px) {
            .row.g-4 {
                display: flex;
                flex-direction: row;
            }
            .col-lg-8 {
                width: 60% !important;
                float: left;
            }
            .col-lg-4 {
                width: 38% !important;
                float: right;
            }
        }
    }

    @media (max-width: 768px) {
        .nav-bar .d-flex {
            flex-direction: column;
            gap: 10px;
        }
        .nav-bar .btn-group {
            justify-content: center;
        }
    }
</style>

<div class="row">
    @if(!isset($documento->codesta))
        {{dd('Documento no encontrado')}}
    @endif


    <div class="col-lg-12">
        <div class="card-body ">
            <div class="row g-4">

                <!-- COLUMNA IZQUIERDA: PRODUCTOS -->
                <div class="col-lg-8">
                    <div class="row mb-4">
                        @if(!$ajax)
                            <div class="col-lg-12">
                                <div class="nav-bar">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <!-- Botones de navegación -->
                                        <div class="btn-group" role="group">
                                            @if(isset($anterior) && $anterior)
                                                <a href="/doc/{{$tipofac}}/{{$anterior}}/{{$documento->fk_sucursal}}"
                                                   class="btn btn-outline-primary">
                                                    <i class="ri-arrow-left-line"></i> Anterior
                                                </a>
                                            @endif

                                            @if(isset($siguiente) && $siguiente)
                                                <a href="/doc/{{$tipofac}}/{{$siguiente}}/{{$documento->fk_sucursal}}"
                                                   class="btn btn-outline-primary">
                                                    Siguiente <i class="ri-arrow-right-line"></i>
                                                </a>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-lg-12">
                            <div class="card-header border-bottom-dashed pt-0 pb-4">
                                <div class="d-sm-flex">
                                    <div class="flex-grow-1">
                                        <img src="{{ URL::asset('build/images/logo-dark.png') }}" class="card-logo card-logo-dark"
                                             alt="logo dark" width="200px">
                                        <img src="{{ URL::asset('build/images/logo-light.png') }}" class="card-logo card-logo-light"
                                             alt="logo light" width="200px">
                                    </div>
                                    <div class="flex-shrink-0 mt-sm-0 mt-3" style="text-align: right">
                                        <h6><span class="text-muted fw-normal">Sucursal: &nbsp;</span>
                                            <span>{{(isset($documento->sucursal) and isset($documento->sucursal->descrip))? $documento->sucursal->descrip : ''}}</span>
                                        </h6>
                                        <h6><span class="text-muted fw-normal">Estación: &nbsp;</span>
                                            <span>{{$documento->codesta}}</span>
                                        </h6>
                                        <h6><span class="text-muted fw-normal">Cliente: &nbsp;</span>
                                            <span>{{$documento->descrip}}</span>
                                        </h6>
                                        <h6><span class="text-muted fw-normal">RIF/Cédula: &nbsp;</span>
                                            <span>{{$documento->id3}}</span>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1 text-uppercase fw-semibold fs-14">DOCUMENTO NRO</p>
                            <h5 class="fs-15 mb-0">
                                <a id="invoice-no" href="/doc/{{$documento->TipoFac}}/{{$documento->NumeroD}}/{{$documento->fk_sucursal}}">
                                    {{($documento->TipoFac=='A' or $documento->TipoFac=='Z')?'FACTURA': (($documento->TipoFac=='B' or $documento->TipoFac=='W')?'DEVOLUCIÓN':'DOCUMENTO')}}
                                    <strong>{{$numerod}}</strong>
                                </a>
                            </h5>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="text-muted mb-1 text-uppercase fw-semibold fs-14">FECHA / HORA</p>
                            <h5 class="fs-15 mb-0">
                                <span>{{$documento->fecha}}</span>
                                <small class="text-muted"> {{$documento->hora}}</small>
                            </h5>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered product-table">
                            <thead>
                            <tr class="table-active">
                                <th width="5%">#</th>
                                <th width="50%">Producto/Servicio</th>
                                <th width="15%" class="text-end">Precio Unit$</th>
                                <th width="10%" class="text-center">Cant</th>
                                <th width="20%" class="text-end">Total$</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $subtotalUSD = 0;
                                $ii          = 0;
                            @endphp
                            @foreach($documento->items as $index => $item)
                                @if($item->fk_sucursal == $documento->fk_sucursal)
                                    @php
                                        $ii++;
                                        $totalItem = $item->costod * $item->Cantidad;
                                        $subtotalUSD += $totalItem;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{$ii}}</td>
                                        <td>
                                            <strong>[{{ $item->CodItem }}]</strong> {{ $item->Descrip1 }}
                                            @if(isset($item->producto) && $item->producto->refere)
                                                <br><small class="text-muted">Ref: {{ $item->producto->refere }}</small>
                                            @endif
                                        </td>
                                        <td class="text-end">${{number_format($item->costod,2,',','.')}}</td>
                                        <td class="text-center">{{ number_format($item->Cantidad, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold">${{number_format($totalItem,2,',','.')}}</td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr class="table-light">
                                <td colspan="4" class="text-end fw-bold">
                                    {{($documento->porcdesconado > 0)? 'SUBTOTAL USD:' :'TOTAL VENTA USD:'}}
                                </td>
                                <td class="text-end fw-bold">${{number_format($subtotalUSD,2,',','.')}}</td>
                            </tr>
                            @if($documento->porcdesconado > 0)
                                <tr class="table-light">
                                    <td colspan="4" class="text-end fw-bold text-warning">DESCUENTO ({{number_format($documento->porcdesconado,2,',','.')}}%):</td>
                                    <td class="text-end fw-bold text-warning">- ${{number_format($subtotalUSD * $documento->porcdesconado / 100,2,',','.')}}</td>
                                </tr>
                                <tr class="table-active">
                                    <td colspan="4" class="text-end fw-bold">TOTAL VENTA USD:</td>
                                    <td class="text-end fw-bold">${{number_format(($documento->contado + $documento->credito),2,',','.')}}</td>
                                </tr>
                            @endif
                            </tfoot>
                        </table>
                    </div>

                    @if($documento->notas1 || $documento->notas2 || $documento->notas3)
                        <div class="mt-3 p-3 bg-light rounded">
                            <small class="text-muted text-uppercase fw-semibold">NOTAS:</small>
                            @if($documento->notas1)<p class="mb-1 small">{{$documento->notas1}}</p>@endif
                            @if($documento->notas2)<p class="mb-1 small">{{$documento->notas2}}</p>@endif
                            @if($documento->notas3)<p class="mb-0 small">{{$documento->notas3}}</p>@endif
                        </div>
                    @endif
                </div>

                <!-- COLUMNA DERECHA: RESUMEN DE PAGOS EN USD -->
                <div class="col-lg-4">
                    <div class="financial-summary">

                        <div class="summary-section">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="summary-title">RESUMEN DE PAGOS</span>
                                @if($documento->credito > 0)
                                    <span class="badge-total">A CRÉDITO</span>
                                @else
                                    <span class="badge-total">CONTADO</span>
                                @endif
                            </div>

                            <!-- Total de la venta -->
                            <div class="summary-row total">
                                <span>TOTAL VENTA:</span>
                                <span class="fw-bold">${{number_format(($documento->contado + $documento->credito),2,',','.')}}</span>
                            </div>

                            <div class="divider-dashed"></div>

                            <!-- DESGLOSE DE LA VENTA -->
                            <div class="summary-row">
                                <span class="text-muted">De contado:</span>
                                <span>${{number_format($documento->contado,2,',','.')}}</span>
                            </div>
                            @if($documento->credito > 0)
                                <div class="summary-row">
                                    <span class="text-muted">A crédito:</span>
                                    <span>${{number_format($documento->credito,2,',','.')}}</span>
                                </div>
                            @endif

                            <div class="divider-dashed"></div>

                            <!-- LISTA DE PAGOS (UNO POR UNO) -->
                            @php
                                $saldoPendiente = $documento->contado + $documento->credito;
                                $hayPagos = false;
                            @endphp

                                <!-- Pagos en efectivo (momento de la venta) -->
                            @php
                                if($documento->cancele != 0) {
                                    $montoUSD = $documento->cancele / $documento->tasa_dolar;
                                    $saldoPendiente -= $montoUSD;
                                    $hayPagos = true;
                            @endphp
                            <div class="payment-item">
                                <span>💰 Efectivo Bs: <small class="small-text">({{number_format($documento->cancele,2,',','.')}})</small></span>
                                <span>${{number_format($montoUSD,2,',','.')}}</span>
                            </div>
                            @php } @endphp

                            @if($documento->dolares != 0)
                                @php
                                    $saldoPendiente -= $documento->dolares;
                                    $hayPagos = true;
                                @endphp
                                <div class="payment-item">
                                    <span>💵 Efectivo USD:</span>
                                    <span>${{number_format($documento->dolares,2,',','.')}}</span>
                                </div>
                            @endif

                            @if($documento->pesos != 0)
                                @php
                                    $montoUSD = $documento->pesos / $documento->tasa_peso;
                                    $saldoPendiente -= $montoUSD;
                                    $hayPagos = true;
                                @endphp
                                <div class="payment-item">
                                    <span>🇨🇴 Efectivo COP:</span>
                                    <span>${{number_format($montoUSD,2,',','.')}} <small class="small-text">({{number_format($documento->pesos,2,',','.')}} COP)</small></span>
                                </div>
                            @endif

                            @if($documento->euros != 0)
                                @php
                                    $saldoPendiente -= $documento->euros;
                                    $hayPagos = true;
                                @endphp
                                <div class="payment-item">
                                    <span>💶 Efectivo EUR:</span>
                                    <span>${{number_format($documento->euros,2,',','.')}}</span>
                                </div>
                            @endif

                            <!-- Instrumentos de pago de la factura original (saipavta) -->
                            @if(isset($instpago) && count($instpago) > 0)
                                @foreach($instpago as $pago)
                                    @php
                                        $montoUSD = 0;
                                        $montomoneda = 0;
                                        $descripcion = '';
                                        if(isset($pago->dolares) && $pago->dolares > 0) {
                                            $montoUSD = $pago->dolares;
                                            $descripcion = '💳 ' . ($pago->satarj->descrip ?? $pago->CodPago ?? 'Tarjeta') . ' USD';
                                        } elseif(isset($pago->pesos) && $pago->pesos > 0) {
                                            $montomoneda = $pago->pesos;
                                            $montoUSD = $pago->pesos / $documento->tasa_peso;
                                            $descripcion = '💳 ' . ($pago->satarj->descrip ?? $pago->CodPago ?? 'Tarjeta') . ' COP';
                                        } elseif(isset($pago->Monto) && $pago->Monto > 0) {
                                            $montomoneda = $pago->Monto;
                                            $montoUSD = $pago->Monto / $documento->tasa_dolar;
                                            $descripcion = '💳 ' . ($pago->satarj->descrip ?? $pago->CodPago ?? 'Tarjeta') . ' Bs';
                                        }
                                    @endphp
                                    @if($montoUSD > 0)
                                        @php
                                            $saldoPendiente -= $montoUSD;
                                            $hayPagos = true;
                                        @endphp
                                        <div class="payment-item">
                                            <span>{{$descripcion}}: {{($montomoneda > 0)? number_format($montomoneda,2,',','.'): ''}}</span>
                                            <span>${{number_format($montoUSD,2,',','.')}}</span>
                                        </div>
                                    @endif
                                @endforeach
                            @endif

                            <!-- DESCUENTOS (tipocxc = 31) -->
                            @if(isset($descuentosCredito) && count($descuentosCredito) > 0)
                                @foreach($descuentosCredito as $descuento)
                                    @php
                                        $saldoPendiente -= $descuento->monto_usd;
                                        $hayPagos = true;
                                    @endphp
                                    <div class="payment-item">
                                        <span>🏷️ Descuento aplicado: {{$descuento->Document}}</span>
                                        <span>-${{number_format($descuento->monto_usd,2,',','.')}}</span>
                                    </div>
                                @endforeach
                            @endif

                            <!-- PAGOS POSTERIORES (tipocxc = 41) desde sapagcxc -->
                            @if(isset($pagosAfectados) && count($pagosAfectados) > 0)
                                @foreach($pagosAfectados as $pago)
                                    @php
                                        $saldoPendiente -= $pago->monto_usd;
                                        $hayPagos = true;
                                    @endphp
                                    <div class="payment-item">
                                        <span>💰 Pago recibido: {{$pago->Document}} ({{$pago->fecha}})</span>
                                        <span>${{number_format($pago->monto_usd,2,',','.')}}</span>
                                    </div>
                                    @if($pago->Descrip)
                                        <div class="payment-item small-text" style="padding-left: 20px;">
                                            <span class="text-muted">↳ {{$pago->Descrip}}</span>
                                            <span></span>
                                        </div>
                                    @endif
                                @endforeach
                            @endif

                            <!-- Anticipos aplicados -->
                            @if($documento->cancelausd != 0)
                                @php
                                    $saldoPendiente -= $documento->cancelausd;
                                    $hayPagos = true;
                                @endphp
                                <div class="payment-item">
                                    <span>🎯 Anticipo aplicado:</span>
                                    <span>${{number_format($documento->cancelausd,2,',','.')}}</span>
                                </div>
                            @endif

                            <!-- Si no hay pagos registrados -->
                            @if(!$hayPagos)
                                <div class="payment-item text-muted">
                                    <span>No se registraron pagos</span>
                                    <span>$0.00</span>
                                </div>
                            @endif

                            <div class="divider-dashed"></div>

                            <!-- SALDO RESTANTE -->
                            <div class="summary-row total">
                                <span>SALDO RESTANTE:</span>
                                @if($saldoPendiente > 0.01)
                                    <span class="fw-bold text-danger-custom">${{number_format($saldoPendiente,2,',','.')}}</span>
                                @elseif($saldoPendiente < -0.01)
                                    <span class="fw-bold text-warning-custom">- ${{number_format(abs($saldoPendiente),2,',','.')}} (Sobrante)</span>
                                @else
                                    <span class="fw-bold text-success-custom">$0.00 (Pagado)</span>
                                @endif
                            </div>

                            @if($documento->credito > 0)
                                <div class="summary-row small-text mt-2">
                                    <span class="text-muted">* Crédito pendiente de cobro: ${{number_format($documento->credito,2,',','.')}}</span>
                                </div>
                            @endif

                            <div class="summary-row small-text">
                                <span class="text-muted">Tasa BCV: $1 = {{number_format($documento->tasa_dolar,2,',','.')}} Bs</span>
                            </div>
                        </div>

                        <!-- SECCIÓN: DEVOLUCIONES -->
                        @if(isset($devoluciones) && count($devoluciones) > 0)
                            <div class="summary-section">
                                <div class="summary-title">↩️ DEVOLUCIONES</div>
                                @foreach($devoluciones as $devolucion)
                                    <div class="payment-item">
                                        <span>
                                            <a href="/doc/B/{{$devolucion->NumeroD}}/{{$documento->fk_sucursal}}" target="_blank">
                                                N° {{$devolucion->NumeroD}}
                                            </a>
                                            <small class="small-text">({{$devolucion->fecha}})</small>
                                        </span>
                                        <span class="text-warning-custom">${{number_format($devolucion->monto_usd,2,',','.')}}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- SECCIÓN: IGTF (si aplica) -->
                        @if($documento->igtf_monto > 0)
                            <div class="summary-section">
                                <div class="summary-title">🏛️ IGTF</div>
                                @php $igtfUSD = $documento->igtf_monto / $documento->tasa_dolar; @endphp
                                <div class="payment-item">
                                    <span>Monto IGTF:</span>
                                    <span>${{number_format($igtfUSD,2,',','.')}}</span>
                                </div>
                                @if($documento->igtf_cancele > 0)
                                    @php $igtfPagadoUSD = $documento->igtf_cancele / $documento->tasa_dolar; @endphp
                                    <div class="payment-item small-text">
                                        <span class="text-muted">Pagado en efectivo:</span>
                                        <span>${{number_format($igtfPagadoUSD,2,',','.')}}</span>
                                    </div>
                                @endif
                                @if($documento->igtf_cancelt > 0)
                                    @php $igtfPagadoTransferUSD = $documento->igtf_cancelt / $documento->tasa_dolar; @endphp
                                    <div class="payment-item small-text">
                                        <span class="text-muted">Pagado transferencia:</span>
                                        <span>${{number_format($igtfPagadoTransferUSD,2,',','.')}}</span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- SECCIÓN: FACTURA ORIGINAL (si es devolución) -->
                        @if(isset($facturaOriginal) && $facturaOriginal)
                            <div class="summary-section">
                                <div class="summary-title">📄 FACTURA ORIGINAL</div>
                                <div class="payment-item">
                                    <span>
                                        <a href="/doc/A/{{$facturaOriginal->NumeroD}}/{{$documento->fk_sucursal}}" target="_blank">
                                            N° {{$facturaOriginal->NumeroD}}
                                        </a>
                                        <small class="small-text">({{$facturaOriginal->fecha}})</small>
                                    </span>
                                    <span>${{number_format($facturaOriginal->monto_usd,2,',','.')}}</span>
                                </div>
                                <div class="payment-item small-text">
                                    <span class="text-muted">Cliente: {{$facturaOriginal->descrip}}</span>
                                    <span class="text-muted">{{$facturaOriginal->id3}}</span>
                                </div>
                            </div>
                        @endif

                        @php
                            if(isset($documento->seriales)){
                        @endphp
                        <div class="summary-section">
                            <div class="summary-row total">
                                <span>Seriales de productos:</span>
                            </div>
                            @php
                                $tantosseriales = 0;
                                echo '<div class="divider-dashed"></div>';
                                foreach($documento->seriales as $serial ){
                                    $tantosseriales ++;
                            @endphp

                            <div class="summary-row">
                                <span class="text-muted">{{$serial->coditem}}</span>
                                <span class="text-muted text-end">{!! $serial->nroserial.' <br>'.$serial->compraprov !!}</span>
                            </div>

                            @php
                                }
                            @endphp
                        </div>
                        @php
                            }
                        @endphp

                    </div>
                </div>
            </div>

            @if(!$ajax)
                <div class="hstack gap-2 justify-content-end d-print-none mt-4">
                    <a href="javascript:window.print()" class="btn btn-success">
                        <i class="ri-printer-line align-bottom me-1"></i> Imprimir
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>


<!-- Script para navegación por teclado (opcional) -->
@if(!$ajax)
    <script>
        document.addEventListener('keydown', function(e) {
            // Flecha izquierda (←) - Factura anterior
            if (e.key === 'ArrowLeft') {
                @if(isset($anterior) && $anterior)
                    window.location.href = '/doc/{{$tipofac}}/{{$anterior}}/{{$documento->fk_sucursal}}';
                @endif
            }
            // Flecha derecha (→) - Factura siguiente
            else if (e.key === 'ArrowRight') {
                @if(isset($siguiente) && $siguiente)
                    window.location.href = '/doc/{{$tipofac}}/{{$siguiente}}/{{$documento->fk_sucursal}}';
                @endif
            }
            // Tecla 'F' o 'f' - Abrir buscador
            /* else if (e.key === 'f' || e.key === 'F') {
                 e.preventDefault();
                 $('#buscarFacturaModal').modal('show');
                 setTimeout(function() {
                     $('#numerod').focus();
                 }, 500);
             }*/
        });

        // Mostrar mensaje de error si existe en la sesión
        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            confirmButtonColor: '#dc3545'
        });
        @endif
    </script>
@endif
