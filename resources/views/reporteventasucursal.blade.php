<style>
    .tdline{
        border:1px solid #0072c5 !important;
    }
    .tdlineff{
        border-left:1px solid #fff !important;
        color: white !important;
        background-color: #0072c5 !important;
    }
    /* Nuevos estilos para los resúmenes */
    .resumen-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        padding: 15px;
        color: white;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .resumen-card-success {
        background: linear-gradient(135deg, #48bb78 0%, #2f855a 100%);
    }
    .resumen-card-info {
        background: linear-gradient(135deg, #4299e1 0%, #2b6cb0 100%);
    }
    .resumen-card-warning {
        background: linear-gradient(135deg, #ed8936 0%, #c05621 100%);
    }
    .total-box {
        background-color: #f8f9fa;
        border-left: 4px solid #0072c5;
        padding: 10px 15px;
        margin-bottom: 15px;
        border-radius: 0 8px 8px 0;
    }
    .total-label {
        font-size: 12px;
        text-transform: uppercase;
        color: #6c757d;
        margin-bottom: 5px;
    }
    .total-value {
        font-size: 24px;
        font-weight: bold;
        color: #0072c5;
        line-height: 1.2;
    }
    .total-value small {
        font-size: 14px;
        font-weight: normal;
        color: #6c757d;
    }
    .badge-total {
        background-color: #0072c5;
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
    }
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }
    .kpi-item {
        background: white;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
    }
    .kpi-value {
        font-size: 22px;
        font-weight: bold;
        color: #2d3748;
    }
    .kpi-label {
        font-size: 12px;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .kpi-icon {
        font-size: 24px;
        margin-bottom: 10px;
        color: #0072c5;
    }
    .custom-verti-nav-pills .nav-link.active::before{
        border-left-color: #0072c5;
    }
</style>

<!-- SECCIÓN DE TOTALES Y RESUMEN -->
<div class="row  ">
    <div class="col-12">
        <!-- KPIs Principales -->
        <div class="kpi-grid">
            <div class="kpi-item">
                <div class="kpi-icon">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="kpi-value">$ {{ number_format($ttotalventa ?? 0, 2, ',', '.') }}</div>
                <div class="kpi-label">Total Ventas USD</div>
            </div>
            <div class="kpi-item">
                <div class="kpi-icon" style="color: #48bb78;">
                    <i class="bi bi-credit-card"></i>
                </div>
                <div class="kpi-value">$ {{ number_format($tcredito ?? 0, 2, ',', '.') }}</div>
                <div class="kpi-label">Total Crédito</div>
            </div>
            <div class="kpi-item">
                <div class="kpi-icon" style="color: #ed8936;">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div class="kpi-value">$ {{ number_format($ttotalventa-$tcredito ?? 0, 2, ',', '.') }}</div>
                <div class="kpi-label">Total Contado USD</div>
            </div>
            <div class="kpi-item">
                <div class="kpi-icon" style="color: #9f7aea;">
                    <i class="bi bi-bank"></i>
                </div>
                <div class="kpi-value">$ {{ number_format($ttotalventac ?? 0, 2, ',', '.') }}</div>
                <div class="kpi-label">Total Cobranza USD</div>
            </div>
            <div class="kpi-item">
                <div class="kpi-icon" style="color: #f56565;">
                    <i class="bi bi-boxes"></i>
                </div>
                <div class="kpi-value">{{ $topprod ? $topprod->sum('salidas') : 0 }}</div>
                <div class="kpi-label">Productos Vendidos</div>
            </div>
        </div>
    </div>
</div>

<div class="card-body">

    <!-- Nav tabs -->
    <ul class="nav nav-tabs nav-justified nav-border-top nav-border-top-success mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-bs-toggle="tab" href="#nav-border-justified-home" role="tab" aria-selected="true" style="display:flex;min-width: 407px;">
                <i class="ri-home-5-line align-middle me-1"></i>
                <div>Listado de facturas + Cobranza <small>   {{$fecha1}} - {{$fecha2}}</small></div>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#nav-border-justified-messages" role="tab" aria-selected="false" tabindex="-1" style="display:flex;">
                <i class="bi bi-list-ul  align-middle me-1"></i> <div>Instrumentos de pago Bs</div>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#nav-border-justified-messages1" role="tab" aria-selected="false" tabindex="-1" style="display:flex;">
                <i class="bi bi-list-ul  align-middle me-1"></i> <div>Instrumentos de pago USD</div>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#nav-border-justified-profile" role="tab" aria-selected="false" tabindex="-1" style="display:flex;">
                <i class="bi bi-grid-3x3-gap-fill  me-1 align-middle"></i> <div>Productos Vendidos</div>
            </a>
        </li>

    </ul>
    <div class="tab-content text-muted">
        <div class="tab-pane active show" id="nav-border-justified-home" role="tabpanel">

            <div class="card card-height-100">

                            <div class="card-body" data-simplebar style="max-height: 490px;">
                                <table width="100%" border="0" class="ocultar">
                                    <tr>
                                        <td width="30%" valign="top">
                                            <table width="100%" border="0" class="table table-borderless table-centered align-middle table-nowrap mb-0">
                                                <!-- Cabecera de VENTAS -->
                                                <tr bgcolor="#fff">
                                                    <td width="14%" height="30" align="center" class="tdline">VENTAS</td>
                                                    <td width="3%" align="center" class="tdlineff">DOC</td>
                                                    <td width="6%" align="center" class="tdlineff">BS</td>
                                                    <td width="5%" align="center" class="tdlineff">BS.T</td>
                                                    <td width="6%" align="center" class="tdlineff">USD</td>
                                                    <td width="5%" align="center" class="tdlineff">USD.T</td>
                                                    <td width="6%" align="center" class="tdlineff ocultarcop">COP</td>
                                                    <td width="6%" align="center" class="tdlineff ocultarcopt">COP.T</td>
                                                    <td width="5%" align="center" class="tdlineff ocultareur">EUR</td>
                                                    <td width="5%" align="center" class="tdlineff">ANTICIPOS</td>
                                                    <td width="5%" align="center" class="tdlineff">CREDITO</td>
                                                    <td width="6%" align="center" class="tdlineff">TOTAL USD</td>
                                                </tr>

                                                @php $n=0; @endphp

                                                @foreach($listado as $nrounico => $sucursal)
                                                    <tr @php echo ($n % 2 == 0) ? 'bgcolor="#eee"' : 'bgcolor="#fff"'; @endphp>
                                                        <td height="30" align="left" class="tdline">
                                                            <div style="max-width: 99%; width: 100%; height: 20px; overflow: hidden; font-size: 12px">
                                                                <a target="_blank" href="/clientes/{{ $listado[$nrounico]['codclie'] ?? '' }}">
                                                                    {{ $listado[$nrounico]['cliente'] ?? '' }}
                                                                </a>
                                                            </div>
                                                        </td>
                                                        <td align="right" class="tdline">
                                                            <a href="/doc/{{ $listado[$nrounico]['tipofac'] ?? '' }}/{{ $listado[$nrounico]['numerod'] }}/{{ $listado[$nrounico]['fksucu'] }}" target="_blank">
                                                                {{ $listado[$nrounico]['numerod'] ?? '' }}
                                                            </a>
                                                        </td>
                                                        <td align="right" class="tdline">{{ isset($listado[$nrounico]['cancele']) && $listado[$nrounico]['cancele'] != 0 ? number_format($listado[$nrounico]['cancele'],2,',','.') : '' }}</td>
                                                        <td align="right" class="tdline">{{ isset($listado[$nrounico]['cancelt']) && $listado[$nrounico]['cancelt'] != 0 ? number_format($listado[$nrounico]['cancelt'],2,',','.') : '' }}</td>
                                                        <td align="right" class="tdline">{{ isset($listado[$nrounico]['dolares']) && $listado[$nrounico]['dolares'] != 0 ? number_format($listado[$nrounico]['dolares'],2,',','.') : '' }}</td>
                                                        <td align="right" class="tdline">{{ isset($listado[$nrounico]['transf']) && $listado[$nrounico]['transf'] != 0 ? number_format($listado[$nrounico]['transf'],2,',','.') : '' }}</td>
                                                        <td align="right" class="tdline ocultarcop">{{ isset($listado[$nrounico]['pesos']) && $listado[$nrounico]['pesos'] != 0 ? number_format($listado[$nrounico]['pesos'],2,',','.') : '' }}</td>
                                                        <td align="right" class="tdline ocultarcopt">{{ isset($listado[$nrounico]['peso_tranf']) && $listado[$nrounico]['peso_tranf'] != 0 ? number_format($listado[$nrounico]['peso_tranf'],2,',','.') : '' }}</td>
                                                        <td align="right" class="tdline ocultareur">{{ isset($listado[$nrounico]['euros']) && $listado[$nrounico]['euros'] != 0 ? number_format($listado[$nrounico]['euros'],2,',','.') : '' }}</td>
                                                        <td align="right" class="tdline">{{ isset($listado[$nrounico]['cancelaUSD']) && $listado[$nrounico]['cancelaUSD'] != 0 ? number_format($listado[$nrounico]['cancelaUSD'],2,',','.') : '' }}</td>
                                                        <td align="right" class="tdline">{{ isset($listado[$nrounico]['credito']) && $listado[$nrounico]['credito'] != 0 ? number_format($listado[$nrounico]['credito'],2,',','.') : '' }}</td>
                                                        <td align="right" class="tdline">{{ isset($listado[$nrounico]['totalventa']) && $listado[$nrounico]['totalventa'] != 0 ? number_format($listado[$nrounico]['totalventa'],2,',','.') : '' }}</td>
                                                    </tr>
                                                    @php $n++; @endphp
                                                @endforeach

                                                <!-- Fila de TOTALES VENTAS -->
                                                <tr bgcolor="#e9ecef">
                                                    <td height="30" align="left" class="tdline fw-bold">TOTALES VENTAS</td>
                                                    <td align="left" class="tdline"></td>
                                                    <td align="right" class="tdline fw-bold">{{ isset($tcancele) && $tcancele != 0 ? number_format($tcancele,2,',','.') : '' }}</td>
                                                    <!-- BS.T - Hacer clickeable -->
                                                    <td align="right" class="tdline fw-bold">

                                                            {{ isset($tcancelt) && $tcancelt != 0 ? number_format($tcancelt,2,',','.') : '' }}

                                                    </td>
                                                    <td align="right" class="tdline fw-bold">{{ isset($tdolares) && $tdolares != 0 ? number_format($tdolares,2,',','.') : '' }}</td>
                                                    <!-- USD.T - Hacer clickeable -->
                                                    <td align="right" class="tdline fw-bold">

                                                            {{ isset($ttransf) && $ttransf != 0 ? number_format($ttransf,2,',','.') : '' }}

                                                    </td>
                                                    <!-- Resto de las celdas -->
                                                    <td align="right" class="tdline ocultarcop fw-bold">{{ isset($tpesos) && $tpesos != 0 ? number_format($tpesos,2,',','.') : '' }}</td>
                                                    <td align="right" class="tdline ocultarcopt fw-bold">{{ isset($tpeso_tranf) && $tpeso_tranf != 0 ? number_format($tpeso_tranf,2,',','.') : '' }}</td>
                                                    <td align="right" class="tdline ocultareur fw-bold">{{ isset($teuros) && $teuros != 0 ? number_format($teuros,2,',','.') : '' }}</td>
                                                    <td align="right" class="tdline fw-bold">{{ isset($tcancelaUSD) && $tcancelaUSD != 0 ? number_format($tcancelaUSD,2,',','.') : '' }}</td>
                                                    <td align="right" class="tdline fw-bold">{{ isset($tcredito) && $tcredito != 0 ? number_format($tcredito,2,',','.') : '' }}</td>
                                                    <td align="right" class="tdline fw-bold text-primary">{{ isset($ttotalventa) && $ttotalventa != 0 ? number_format($ttotalventa,2,',','.') : '' }}</td>
                                                </tr>

                                                <!-- Separador -->
                                                <tr><td colspan="12" style="height: 20px;"></td></tr>

                                                <!-- Cabecera de COBRANZAS -->
                                                <tr bgcolor="#fff">
                                                    <td height="30" align="center" class="tdline fw-bold">COBRANZAS</td>
                                                    <td align="center" class="tdlineff">DOC</td>
                                                    <td align="center" class="tdlineff">BS</td>
                                                    <td align="center" class="tdlineff">BS.T</td>
                                                    <td align="center" class="tdlineff">USD</td>
                                                    <td align="center" class="tdlineff">USD.T</td>
                                                    <td align="center" class="tdlineff ocultarcop">COP</td>
                                                    <td align="center" class="tdlineff ocultarcopt">COP.T</td>
                                                    <td align="center" class="tdlineff ocultareur">EUR</td>
                                                    <td align="center" class="tdlineff">ANTICIPOS</td>
                                                    <td align="center" class="tdlineff">...</td>
                                                    <td align="center" class="tdlineff">TOTAL USD</td>
                                                </tr>

                                                @php $n=0; @endphp

                                                @foreach($listadoc as $nrounico => $sucursal)
                                                    <tr @php echo ($n % 2 == 0) ? 'bgcolor="#eee"' : 'bgcolor="#fff"'; @endphp>
                                                        <td height="30" align="left" class="tdline">
                                                            <div style="max-width: 99%; width: 100%; height: 20px; overflow: hidden; font-size: 12px">{{ $listadoc[$nrounico]['cliente'] ?? '' }}</div>
                                                        </td>
                                                        <td align="right" class="tdline">{{ $listadoc[$nrounico]['numerod'] ?? '' }}</td>
                                                        <td align="right" class="tdline">{{ isset($listadoc[$nrounico]['cancele']) && $listadoc[$nrounico]['cancele'] != 0 ? number_format($listadoc[$nrounico]['cancele'],2,',','.') : '' }}</td>
                                                        <td align="right" class="tdline">{{ isset($listadoc[$nrounico]['cancelt']) && $listadoc[$nrounico]['cancelt'] != 0 ? number_format($listadoc[$nrounico]['cancelt'],2,',','.') : '' }}</td>
                                                        <td align="right" class="tdline">{{ isset($listadoc[$nrounico]['dolares']) && $listadoc[$nrounico]['dolares'] != 0 ? number_format($listadoc[$nrounico]['dolares'],2,',','.') : '' }}</td>
                                                        <td align="right" class="tdline">{{ isset($listadoc[$nrounico]['transf']) && $listadoc[$nrounico]['transf'] != 0 ? number_format($listadoc[$nrounico]['transf'],2,',','.') : '' }}</td>
                                                        <td align="right" class="tdline ocultarcop">{{ isset($listadoc[$nrounico]['pesos']) && $listadoc[$nrounico]['pesos'] != 0 ? number_format($listadoc[$nrounico]['pesos'],2,',','.') : '' }}</td>
                                                        <td align="right" class="tdline ocultarcopt">{{ isset($listadoc[$nrounico]['peso_tranf']) && $listadoc[$nrounico]['peso_tranf'] != 0 ? number_format($listadoc[$nrounico]['peso_tranf'],2,',','.') : '' }}</td>
                                                        <td align="right" class="tdline ocultareur">{{ isset($listadoc[$nrounico]['euros']) && $listadoc[$nrounico]['euros'] != 0 ? number_format($listadoc[$nrounico]['euros'],2,',','.') : '' }}</td>
                                                        <td align="right" class="tdline">{{ isset($listadoc[$nrounico]['cancelausd']) && $listadoc[$nrounico]['cancelausd'] != 0 ? number_format($listadoc[$nrounico]['cancelausd'],2,',','.') : '' }}</td>
                                                        <td align="right" class="tdline"></td>
                                                        <td align="right" class="tdline">{{ isset($listadoc[$nrounico]['totalcobranza']) && $listadoc[$nrounico]['totalcobranza'] != 0 ? number_format($listadoc[$nrounico]['totalcobranza'],2,',','.') : '' }}</td>
                                                    </tr>
                                                    @php $n++; @endphp
                                                @endforeach

                                                <!-- Fila de TOTALES COBRANZAS -->
                                                <tr bgcolor="#e9ecef">
                                                    <td height="30" align="left" class="tdline fw-bold">TOTALES COBRANZAS</td>
                                                    <td align="left" class="tdline"></td>
                                                    <td align="right" class="tdline fw-bold">{{ isset($tcancelec) && $tcancelec != 0 ? number_format($tcancelec,2,',','.') : '' }}</td>
                                                    <!-- BS.T - Hacer clickeable -->
                                                    <td align="right" class="tdline fw-bold">

                                                            {{ isset($tcanceltc) && $tcanceltc != 0 ? number_format($tcanceltc,2,',','.') : '' }}

                                                    </td>
                                                    <td align="right" class="tdline fw-bold">{{ isset($tdolaresc) && $tdolaresc != 0 ? number_format($tdolaresc,2,',','.') : '' }}</td>
                                                    <!-- USD.T - Hacer clickeable -->
                                                    <td align="right" class="tdline fw-bold">

                                                            {{ isset($ttransfc) && $ttransfc != 0 ? number_format($ttransfc,2,',','.') : '' }}

                                                    </td>
                                                    <!-- Resto de las celdas -->
                                                    <td align="right" class="tdline ocultarcop fw-bold">{{ isset($tpesosc) && $tpesosc != 0 ? number_format($tpesosc,2,',','.') : '' }}</td>
                                                    <td align="right" class="tdline ocultarcopt fw-bold">{{ isset($tpeso_tranfc) && $tpeso_tranfc != 0 ? number_format($tpeso_tranfc,2,',','.') : '' }}</td>
                                                    <td align="right" class="tdline ocultareur fw-bold">{{ isset($teurosc) && $teurosc != 0 ? number_format($teurosc,2,',','.') : '' }}</td>
                                                    <td align="right" class="tdline fw-bold">{{ isset($tcancelausdc) && $tcancelausdc != 0 ? number_format($tcancelausdc,2,',','.') : '' }}</td>
                                                    <td align="right" class="tdline"></td>
                                                    <td align="right" class="tdline fw-bold text-success">{{ isset($ttotalventac) && $ttotalventac != 0 ? number_format($ttotalventac,2,',','.') : '' }}</td>
                                                </tr>

                                                <!-- Separador -->
                                                <tr><td colspan="12" style="height: 20px;"></td></tr>

                                                <!-- Fila de GRAN TOTAL (VENTAS + COBRANZAS) -->
                                                <tr bgcolor="#0072c5" style="color: white;">
                                                    <td height="30" align="left" class="tdline fw-bold" style="color: white;">GRAN TOTAL (VENTAS + COBRANZAS)</td>
                                                    <td align="left" class="tdline" style="color: white;"></td>
                                                    <td align="right" class="tdline fw-bold" style="color: white;">{{ (($tcancele ?? 0) + ($tcancelec ?? 0)) != 0 ? number_format(($tcancele ?? 0) + ($tcancelec ?? 0),2,',','.') : '' }}</td>
                                                    <td align="right" class="tdline fw-bold" style="color: white;">

                                                            {{ (($tcancelt ?? 0) + ($tcanceltc ?? 0)) != 0 ? number_format(($tcancelt ?? 0) + ($tcanceltc ?? 0),2,',','.') : '' }}

                                                    </td>
                                                    <td align="right" class="tdline fw-bold" style="color: white;">{{ (($tdolares ?? 0) + ($tdolaresc ?? 0)) != 0 ? number_format(($tdolares ?? 0) + ($tdolaresc ?? 0),2,',','.') : '' }}</td>
                                                    <td align="right" class="tdline fw-bold" style="color: white;">

                                                            {{ (($ttransf ?? 0) + ($ttransfc ?? 0)) != 0 ? number_format(($ttransf ?? 0) + ($ttransfc ?? 0),2,',','.') : '' }}

                                                    </td>
                                                    <!-- Resto de las celdas -->
                                                    <td align="right" class="tdline ocultarcop fw-bold" style="color: white;">{{ (($tpesos ?? 0) + ($tpesosc ?? 0)) != 0 ? number_format(($tpesos ?? 0) + ($tpesosc ?? 0),2,',','.') : '' }}</td>
                                                    <td align="right" class="tdline ocultarcopt fw-bold" style="color: white;">{{ (($tpeso_tranf ?? 0) + ($tpeso_tranfc ?? 0)) != 0 ? number_format(($tpeso_tranf ?? 0) + ($tpeso_tranfc ?? 0),2,',','.') : '' }}</td>
                                                    <td align="right" class="tdline ocultareur fw-bold" style="color: white;">{{ (($teuros ?? 0) + ($teurosc ?? 0)) != 0 ? number_format(($teuros ?? 0) + ($teurosc ?? 0),2,',','.') : '' }}</td>
                                                    <td align="right" class="tdline fw-bold" style="color: white;">{{ (($tcancelaUSD ?? 0) + ($tcancelausdc ?? 0)) != 0 ? number_format(($tcancelaUSD ?? 0) + ($tcancelausdc ?? 0),2,',','.') : '' }}</td>
                                                    <td align="right" class="tdline fw-bold" style="color: white;">{{ isset($tcredito) && $tcredito != 0 ? number_format($tcredito,2,',','.') : '' }}</td>
                                                    <td align="right" class="tdline fw-bold" style="color: white;">{{ (($ttotalventa ?? 0) + ($ttotalventac ?? 0)) != 0 ? number_format(($ttotalventa ?? 0) + ($ttotalventac ?? 0),2,',','.') : '' }}</td>
                                                </tr>
                                            </table>

                                            <script>
                                                @php
                                                    if(($tpesos ?? 0) == 0 && ($tpesosc ?? 0) == 0) echo "$('.ocultarcop').hide();";
                                                    if(($tpeso_tranf ?? 0) == 0 && ($tpeso_tranfc ?? 0) == 0) echo "$('.ocultarcopt').hide();";
                                                    if(($teuros ?? 0) == 0 && ($teurosc ?? 0) == 0) echo "$('.ocultareur').hide();";
                                                @endphp
                                            </script>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

        </div>
        <div class="tab-pane" id="nav-border-justified-profile" role="tabpanel">

            <div class="card card-height-100">
                        <div class="card-header align-items-center d-flex bg-light">
                            <h4 class="card-title mb-0 flex-grow-1">Productos Vendidos</h4>
                            <small>en el período</small>
                        </div>
                        <div class="card-body" data-simplebar style="max-height: 490px;">
                            @php
                                $totalProductos = isset($topprod) ? $topprod->sum('salidas') : 0;
                            @endphp

                                <!-- Total productos vendidos -->
                            <div class="alert alert-primary py-2 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small">Total unidades:</span>
                                    <span class="badge bg-white text-primary rounded-pill fs-6">{{ $totalProductos }}</span>
                                </div>
                            </div>

                            @if(isset($topprod) && count($topprod) > 0)
                                @foreach($topprod as $index => $top)
                                    @php
                                        $porcentaje = $totalProductos > 0 ? round(($top->salidas / $totalProductos) * 100, 1) : 0;
                                    @endphp
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between small">
                                <span class="text-truncate" style="max-width: 150px;" title="{{ $top->producto->Descrip ?? $top->CodItem }}">
                                    {{ $index + 1 }}. {{ $top->producto->Descrip ?? $top->CodItem }}
                                </span>
                                            <span class="fw-bold">{{ $top->salidas }}</span>
                                        </div>
                                        <div class="progress" style="height: 3px;">
                                            <div class="progress-bar bg-primary" style="width: {{ $porcentaje }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted text-center">No hay productos vendidos</p>
                            @endif

                            <script>$('#unidadesvendidas').html('{{ $totalProductos }}')</script>
                        </div>
                    </div>

        </div>
        <div class="tab-pane" id="nav-border-justified-messages" role="tabpanel">
            <div class="row">
                <div class="col-lg-3">
                    <div class="nav nav-pills flex-column nav-pills-tab custom-verti-nav-pills text-center" role="tablist" aria-orientation="vertical">
                        @php $flag = false; @endphp
                        @foreach($tarjetasbs as $index => $tt)
                            <a class="nav-link  {{(!$flag)?'show active':''}}" id="tarjeta-{{$index}}-tab"
                               data-bs-toggle="pill" href="#tarjeta-{{$index}}"
                               role="tab" aria-controls="tarjeta-{{$index}}" aria-selected="true" style="display: flex;   align-content: center;  align-items: center;">
                                <i class="bi bi-info-circle d-block fs-20 mb-1" style="margin-right: 10px;"></i> {{$tt}}
                            </a>
                            @php $flag = true; @endphp
                        @endforeach

                    </div>
                </div> <!-- end col-->
                <div class="col-lg-9">
                    <div class="tab-content text-muted mt-3 mt-lg-0">
                        @php $flag = false; @endphp
                        @foreach($tarjetasbs as $index => $tt)
                            <div class="tab-pane fade  {{(!$flag)?'active show ':''}}" id="tarjeta-{{$index}}"
                                 role="tabpanel" aria-labelledby="tarjeta-{{$index}}-tab">

                                <table class="table table-sm table-hover" style="margin-top: 7px">
                                    <thead>
                                        <tr>
                                            <th class="tdlineff" style="padding: 15px;">Sucursal</th>
                                            <th class="tdlineff" style="padding: 15px;">Cliente</th>
                                            <th class="tdlineff" style="padding: 15px;">Tipo</th>
                                            <th class="tdlineff" style="padding: 15px;">Documento</th>
                                            <th class="tdlineff" style="padding: 15px;">Referencia</th>
                                            <th class="tdlineff" style="padding: 15px;">Monto Bs.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @php $totalinst = 0; @endphp
                                    @if(isset($lines[$index]))
                                        @foreach($lines[$index] as $line)
                                            @php $totalinst += $line['monto']; @endphp
                                            <tr>
                                                <td class="tdline"> {{$line['sucu']}}</td>
                                                <td class="tdline">{{$line['cliente']}}</td>
                                                <td class="tdline">
                                                    @if($line['doc'] == 'Fac')
                                                        <span class="badge-fac">Factura</span>
                                                    @else
                                                        <span class="badge-cxc">CXC</span>
                                                    @endif
                                                </td>
                                                <td class="tdline">
                                                    @if(isset($line['documen']) && $line['documen'] != '')
                                                        @if(isset($line['TipoFac']) && $line['TipoFac'])
                                                            <a href="/doc/{{$line['TipoFac']}}/{{$line['documen']}}/{{$line['fk_sucu']}}" target="_blank">
                                                                {{$line['documen']}}
                                                            </a>
                                                        @else
                                                            {{$line['documen']}}
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="tdline" title="{{$line['Descrip']}}">
                                                    {{ Str::limit($line['Descrip'], 30) }}
                                                </td>
                                                <td class="tdline text-end {{ $line['monto'] > 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format($line['monto'], 2, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                    <tfoot class="table-light">
                                    <tr>
                                        <th colspan="5" class="text-end">Total:</th>
                                        <th class="text-end text-primary">
                                            {{ number_format($totalinst, 2, ',', '.') }}
                                        </th>
                                    </tr>
                                    </tfoot>
                                </table>

                            </div>

                            @php $flag = true; @endphp
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="nav-border-justified-messages1" role="tabpanel">
            <div class="row">
                <div class="col-lg-3">
                    <div class="nav nav-pills flex-column nav-pills-tab custom-verti-nav-pills text-center" role="tablist" aria-orientation="vertical">
                        @php $flag = false; @endphp
                        @foreach($tarjetasus as $index => $tt)
                            <a class="nav-link  {{(!$flag)?'show active':''}}" id="tarjeta-{{$index}}-tab"
                               data-bs-toggle="pill" href="#tarjeta-{{$index}}"
                               role="tab" aria-controls="tarjeta-{{$index}}" aria-selected="true" style="display: flex;   align-content: center;  align-items: center;">
                                <i class="bi bi-info-circle d-block fs-20 mb-1" style="margin-right: 10px;"></i> {{$tt}}
                            </a>
                            @php $flag = true; @endphp
                        @endforeach

                    </div>
                </div> <!-- end col-->
                <div class="col-lg-9">
                    <div class="tab-content text-muted mt-3 mt-lg-0">
                        @php $flag = false; @endphp
                        @foreach($tarjetasus as $index => $tt)
                            <div class="tab-pane fade  {{(!$flag)?'active show ':''}}" id="tarjeta-{{$index}}"
                                 role="tabpanel" aria-labelledby="tarjeta-{{$index}}-tab">

                                <table class="table table-sm table-hover" style="margin-top: 7px">
                                    <thead>
                                    <tr>
                                        <th class="tdlineff" style="padding: 15px;">Sucursal</th>
                                        <th class="tdlineff" style="padding: 15px;">Cliente</th>
                                        <th class="tdlineff" style="padding: 15px;">Tipo</th>
                                        <th class="tdlineff" style="padding: 15px;">Documento</th>
                                        <th class="tdlineff" style="padding: 15px;">Referencia</th>
                                        <th class="tdlineff" style="padding: 15px;">Monto Bs.</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php $totalinst = 0; @endphp
                                    @if(isset($linesdol[$index]))
                                        @foreach($linesdol[$index] as $line)
                                            @php $totalinst += $line['monto']; @endphp
                                            <tr>
                                                <td class="tdline"> {{$line['sucu']}}</td>
                                                <td class="tdline">{{$line['cliente']}}</td>
                                                <td class="tdline">
                                                    @if($line['doc'] == 'Fac')
                                                        <span class="badge-fac">Factura</span>
                                                    @else
                                                        <span class="badge-cxc">CXC</span>
                                                    @endif
                                                </td>
                                                <td class="tdline">
                                                    @if(isset($line['documen']) && $line['documen'] != '')
                                                        @if(isset($line['TipoFac']) && $line['TipoFac'])
                                                            <a href="/doc/{{$line['TipoFac']}}/{{$line['documen']}}/{{$line['fk_sucu']}}" target="_blank">
                                                                {{$line['documen']}}
                                                            </a>
                                                        @else
                                                            {{$line['documen']}}
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="tdline" title="{{$line['Descrip']}}">
                                                    {{ Str::limit($line['Descrip'], 30) }}
                                                </td>
                                                <td class="tdline text-end {{ $line['monto'] > 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format($line['monto'], 2, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                    <tfoot class="table-light">
                                    <tr>
                                        <th colspan="5" class="text-end">Total:</th>
                                        <th class="text-end text-primary">
                                            {{ number_format($totalinst, 2, ',', '.') }}
                                        </th>
                                    </tr>
                                    </tfoot>
                                </table>

                            </div>

                            @php $flag = true; @endphp
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


