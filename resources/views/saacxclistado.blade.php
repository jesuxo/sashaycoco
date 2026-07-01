{{-- resources/views/saacxclistado.blade.php --}}
<div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card overflow-hidden">
                <div class="accordion accordion-flush filter-accordion">
                    <div class="card-body border-bottom">
                        <div class="table-responsive table-card">
                            <table width="100%" border="0" class="table table-borderless table-centered align-middle table-nowrap mb-0">
                                <thead>
                                <tr bgcolor="#fff">
                                    <th width="33%" height="30" align="left" class="tdlineff">CLIENTE - {{$codclie}}</th>
                                    <th width="11%" align="center" class="tdlineff">TIPO</th>
                                    <th width="11%" align="center" class="tdlineff">FECHA</th>
                                    <th width="11%" align="center" class="tdlineff">NUMERO</th>
                                    <th width="11%" align="center" class="tdlineff">FACTURADO</th>
                                    <th width="11%" align="center" class="tdlineff">ABONADO</th>
                                    <th width="11%" align="center" class="tdlineff">SALDO Bs</th>
                                    <th width="11%" align="center" class="tdlineff">SALDO USD</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $nn = 1;
                                    $tmonto = $tabona = $tsaldo = $tdivis = 0;
                                @endphp
                                @foreach($saldocxc as $index => $cxc)
                                    @php
                                        $nn++;
                                        $tmonto += $cxc->credito;
                                        $tabona += $cxc->abonado;
                                        $tsaldo += $cxc->saldo;
                                        $tdivis += $cxc->saldodivisa;
                                    @endphp
                                    <tr @php if(($nn%2)==0){echo 'bgcolor="#eee"'; }else{echo 'bgcolor="#fff"';} @endphp>
                                        <td height="30" align="left" class="tdline">{{$cxc->cliente}} - {{$cxc->sucursal}}</td>
                                        <td align="center" class="tdline">{{$cxc->tipo}}</td>
                                        <td align="center" class="tdline">{{$cxc->fecha}}</td>
                                        <td align="center" class="tdline">
                                            @if($cxc->tipo == 'FACT')
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="javascript:void(0);"
                                                       class="openDocumentoEnLinea btn btn-sm btn-outline-info"
                                                       data-fksucu   = "{{ $cxc->fk_sucursal ?? '' }}"
                                                       data-numerod  = "{{ $cxc->numero }}"
                                                       data-tipofac  = "{{ $cxc->tipofac ?? 'A' }}"
                                                       data-nrounico = "{{ $cxc->nrounico ?? 0 }}"
                                                       data-cliente  = "{{ $cxc->cliente }}">
                                                        <i class="bi bi-eye"></i> {{ $cxc->numero }}
                                                    </a>
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-warning btn-descuento"
                                                            data-numerod   = "{{ $cxc->numero }}"
                                                            data-nrounico  = "{{ $cxc->nrounico }}"
                                                            data-fksucu    = "{{ $cxc->fk_sucursal ?? '' }}"
                                                            data-saldo     = "{{ $cxc->saldo }}"
                                                            data-saldo-usd = "{{ $cxc->saldodivisa }}"
                                                            data-bs-toggle = "modal"
                                                            data-bs-target = "#descuentoModal">
                                                        <i class="bi bi-list-ul"></i>
                                                    </button>
                                                </div>
                                            @else
                                                {{ $cxc->numero }}
                                            @endif
                                        </td>
                                        <td align="right" class="tdline">{{($cxc->credito != 0 )? number_format( $cxc->credito ,2,',','.').'  ' : ''}}</td>
                                        <td align="right" class="tdline">{{($cxc->abonado != 0 )? number_format( $cxc->abonado ,2,',','.').'  ' : ''}}</td>
                                        <td align="right" class="tdline">{{($cxc->saldo != 0 )? number_format($cxc->saldo,2,',','.'):''}}</td>
                                        <td align="right" class="tdline">{{($cxc->saldodivisa != 0 )? number_format($cxc->saldodivisa,2,',','.'):''}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td height="30" align="left"></td>
                                    <td align="center"></td>
                                    <td align="center"></td>
                                    <td align="center"></td>
                                    <td align="center"></td>
                                    <td align="center"></td>
                                    <td align="center"></td>
                                    <td align="center"></td>
                                </tr>
                                <tr>
                                    <td height="30" align="left" class="tdline fw-bold">TOTALES</td>
                                    <td align="right" class="tdline"></td>
                                    <td align="right" class="tdline"></td>
                                    <td align="right" class="tdline"></td>
                                    <td align="right" class="tdline fw-bold">{{($tmonto != 0)? number_format($tmonto ,2,',','.') : ''}}</td>
                                    <td align="right" class="tdline fw-bold">{{($tabona != 0)? number_format($tabona ,2,',','.') : ''}}</td>
                                    <td align="right" class="tdline fw-bold text-danger">{{($tsaldo != 0)? number_format($tsaldo ,2,',','.') : ''}}
                                    <input type="hidden" id="tsaldolistaod" value="{{($tsaldo != 0)? number_format($tsaldo+0,2,'.','') : ''}}">
                                    </td>
                                    <td align="right" class="tdline fw-bold text-primary">{{($tdivis != 0)? number_format($tdivis ,2,',','.') : ''}}</td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer con el nuevo sistema de pagos múltiples -->
<div class="modal-footer" style="padding: 0 !important; display: unset">
    <div class="row" style="justify-content: left !important;">
        <div class="col-12">
            <!-- Tabs para tipos de pago -->


            <div class="card" style="background: #e1f7e6;">

                <div class="card-body">
                    <ul class="nav nav-tabs mb-3" id="paymentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#efectivo" type="button" role="tab">
                                <i class="bi bi-cash"></i> Efectivo
                            </button>
                        </li>
                        <li class="nav-item  d-none" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#instrumentosBs" type="button" role="tab">
                                <i class="bi bi-credit-card"></i> Instrumentos Bs
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#instrumentosUsd" type="button" role="tab">
                                <i class="bi bi-currency-dollar"></i> Instrumentos USD
                            </button>
                        </li>
                        <li class="nav-item  d-none" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#instrumentosPesos" type="button" role="tab">
                                <i class="bi bi-currency-exchange"></i> Instrumentos COP
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" >
                        <!-- Tab Efectivo -->
                        <div class="tab-pane fade show active" id="efectivo" role="tabpanel">
                            <div class="row">
                                <div class="col-md-3 d-none">
                                    <div class="mb-2">
                                        <label class="form-label small">Efectivo Bs</label>
                                        <input type="number" step="0.01" id="efectivo_bs" class="form-control form-control-sm payment-amount" value="0">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <label class="form-label small">Efectivo USD</label>
                                        <input type="number" step="0.01" id="efectivo_usd" class="form-control form-control-sm payment-amount" value="0">
                                    </div>
                                </div>
                                <div class="col-md-3  d-none">
                                    <div class="mb-2">
                                        <label class="form-label small">Efectivo COP</label>
                                        <input type="number" step="0.01" id="efectivo_pesos" class="form-control form-control-sm payment-amount" value="0">
                                    </div>
                                </div>
                                <div class="col-md-3 d-none">
                                    <div class="mb-2">
                                        <label class="form-label small">Efectivo EUR</label>
                                        <input type="number" step="0.01" id="efectivo_eur" class="form-control form-control-sm payment-amount" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Instrumentos Bs -->
                        <div class="tab-pane fade" id="instrumentosBs" role="tabpanel">
                            <div id="instrumentosBsContainer">
                                <!-- Se llenará dinámicamente -->
                            </div>
                            <button type="button" class="btn btn-sm btn-warning mt-2 add-instrumento" data-currency="bs">
                                <i class="bi bi-plus-circle"></i> Agregar Instrumento Bs
                            </button>
                        </div>

                        <!-- Tab Instrumentos USD -->
                        <div class="tab-pane fade" id="instrumentosUsd" role="tabpanel">
                            <div id="instrumentosUsdContainer">
                                <!-- Se llenará dinámicamente -->
                            </div>
                            <button type="button" class="btn btn-sm btn-warning mt-2 add-instrumento" data-currency="usd">
                                <i class="bi bi-plus-circle"></i> Agregar Instrumento USD
                            </button>
                        </div>

                        <!-- Tab Instrumentos Pesos -->
                        <div class="tab-pane fade" id="instrumentosPesos" role="tabpanel">
                            <div id="instrumentosPesosContainer">
                                <!-- Se llenará dinámicamente -->
                            </div>
                            <button type="button" class="btn btn-sm btn-warning mt-2 add-instrumento" data-currency="cop">
                                <i class="bi bi-plus-circle"></i> Agregar Instrumento COP
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6  d-none">
                    <div class="mb-2">
                        <label class="form-label small">Tasa de Cambio (Bs/USD)</label>
                        <input type="number" step="0.01" id="tasa_abono" class="form-control form-control-sm" value="1">
                    </div>
                </div>
                <div class="col-md-6  d-none">
                    <div class="mb-2">
                        <label class="form-label small">Tasa Peso/USD</label>
                        <input type="number" step="0.01" id="tasa_peso" class="form-control form-control-sm" value="4000">
                    </div>
                </div>
            </div>

            <div class="mb-2">
                <label class="form-label small">Observación <span class="text-danger">*</span></label>
                <textarea id="observacion" class="form-control form-control-sm" rows="2" placeholder="Detalle del pago"></textarea>
            </div>

            <div class="alert alert-success mb-2 py-2">
                <strong>Total a pagar en USD:</strong> <span id="totalUSD">$0.00</span>
            </div>

            <div class="row">
                <div class="col-8">
                    <button type="button" class="btn btn-primary w-100" id="btnProcesarPago">
                        <i class="bi bi-check-circle"></i> PROCESAR PAGO
                    </button>
                </div>
                <div class="col-4">
                    <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> CERRAR
                    </button>
                </div>
            </div>
            <div id="alertMonto" class="col-12 text-danger small mt-2"></div>
        </div>
    </div>
</div>

<script>

    // Eventos
    $(document).ready(function() {
        cargarInstrumentos();

        // Agregar instrumento
        $('.add-instrumento').click(function() {
            const currency = $(this).data('currency');
            agregarInstrumento(currency);
        });

        // Eliminar instrumento
        $(document).on('click', '.remove-instrumento', function() {
            $(this).closest('.instrumento-row').remove();
            calcularTotalUSD();
        });

        // Recalcular al cambiar montos
        $(document).on('keyup change', '.payment-amount, #tasa_abono, #tasa_peso', function() {
            calcularTotalUSD();
        });

        // Procesar pago
        $('#btnProcesarPago').click(function() {
            procesarPago('{{$codclie}}','{{(isset($fecha1)? $fecha1: '')}}','{{(isset($fecha2)? $fecha2: '')}}');
        });

        activarparadescuentocxc();
    });
</script>
