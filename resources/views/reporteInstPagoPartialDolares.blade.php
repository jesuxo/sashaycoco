
    <style>
        .botoncal{
            background: transparent;
            border: none;
            color: white;
        }
        .botoncal:hover{
            font-size: 13px;
        }
        /* Estilos adicionales para totales */
        .total-card {
            background: white;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            color: #333;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }
        .total-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,114,197,0.2);
        }
        .total-value {
            font-size: 28px;
            font-weight: bold;
            color: #0072c5;
            line-height: 1.2;
        }
        .total-label {
            font-size: 13px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        /* Estilos para modales */
        .modal-header {
            background: linear-gradient(135deg, #0072c5 0%, #0072c5 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }
        .modal-content {
            border-radius: 15px;
            border: none;
        }
        .modal-table {
            max-height: 400px;
            overflow-y: auto;
        }
        .modal-table thead th {
            position: sticky;
            top: 0;
            background: #0072c5;
            color: white;
            z-index: 10;
        }
        /* Badges */
        .badge-fac {
            background: #0072c5;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
        }
        .badge-cxc {
            background: #ffc107;
            color: #000;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
        }
        /* Progress bars */
        .progress-sm {
            height: 8px;
            border-radius: 10px;
        }
        /* Estilos para la tabla */
        .tdline {
            border: 1px solid #0072c5 !important;
        }
        .tdlineff {
            border-left: 1px solid #fff !important;
            color: white !important;
            background-color: #0072c5 !important;
        }
    </style>

    <div class="card mt-1">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mt-3">
                        <div class="card shadow-lg border-0 mb-4" style="border-radius: 20px; overflow: hidden;">

                            @if($fecha1 != '')
                                @php
                                    $totales = [];$modalId = 0;
                                @endphp

                                <div class="card-body " style="background: #f8faff; padding: 10px 0 !important;" id="contentReport">
                                    <!-- Tabla principal -->
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover align-middle" style="border-color: #0072c5;" id="tablainstpagousd">
                                            <thead>
                                            <tr>
                                                <th class="tdlineff text-center p-3" style="width: 30%;">SUCURSAL</th>
                                                @foreach($clases as $index => $data)
                                                    <th class="tdlineff text-center p-3">{{$index}}</th>
                                                @endforeach
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($sucursales))
                                                @foreach($sucursales as $indexsuc => $puntos)
                                                    @foreach($puntos as $indexpunto => $punto)
                                                        <tr>
                                                            <td class="tdline p-3">
                                                                <i class="bi bi-shop me-2 text-primary"></i>
                                                                {{ $indexsuc }} - {{$indexpunto}}
                                                            </td>
                                                            @foreach($clases as $index => $data)
                                                                @php
                                                                    if(!isset($totales[$index])) $totales[$index] = 0;
                                                                    $monto = $listado[$indexsuc][$indexpunto][$index] ?? 0;
                                                                    $totales[$index] += $monto;
                                                                    $modalId++;
                                                                @endphp
                                                                <td class="tdline p-3" style="text-align: right !important;">
                                                                    @if($monto != 0)
                                                                        <a href="javascript:;" class="text-decoration-none text-primary text-end "
                                                                           style="text-align: right !important;" onclick="$('#asd{{$modalId}}').modal('show')">
                                                                            <i class="bi bi-link me-1"></i>
                                                                              {{ number_format($monto, 2, ',', '.') }}
                                                                        </a>
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            @endif
                                            </tbody>
                                            <tfoot>

                                            <tr style="background: #0072c5;">
                                                <td class="tdline p-3  text-white" style="background: #0072c5 ; border-color: white;">
                                                    <i class="bi bi-calculator me-2"></i> GRAN TOTAL
                                                </td>
                                                <td colspan="{{ count($clases) }}" class="tdline text-end p-3  text-white"
                                                    style="background: #0072c5 ; border-color: white; font-size: 18px;">
                                                      {{ number_format(array_sum($totales), 2, ',', '.') }}
                                                </td>
                                            </tr>
                                            </tfoot>
                                        </table>

                                        @php  $modalId = 0; @endphp
                                        @if(isset($sucursales))
                                            @foreach($sucursales as $indexsuc => $puntos)
                                                @foreach($puntos as $indexpunto => $punto)
                                                        @foreach($clases as $index => $data)
                                                            @php
                                                                if(!isset($totales[$index])) $totales[$index] = 0;
                                                                $monto = $listado[$indexsuc][$indexpunto][$index] ?? 0;

                                                                $modalId++;
                                                            @endphp
                                                                @if($monto != 0)
                                                                    @if(!$ajax)
                                                                        <!-- Modal generado aquí mismo -->
                                                                        <div class="modal fade modaltarjetas" id="asd{{$modalId}}" tabindex="-1">
                                                                            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title text-white">
                                                                                            <i class="bi bi-receipt me-2"></i>
                                                                                            Detalle de Transacciones en Dolares
                                                                                        </h5>
                                                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <div class="bg-light p-3 rounded mb-3">
                                                                                            <div class="row">
                                                                                                <div class="col-md-4">
                                                                                                    <small class="text-muted">Sucursal</small>
                                                                                                    <p class=" mb-0">{{$indexsuc}}</p>
                                                                                                </div>
                                                                                                <div class="col-md-4">
                                                                                                    <small class="text-muted">Punto</small>
                                                                                                    <p class=" mb-0">{{$indexpunto}}</p>
                                                                                                </div>
                                                                                                <div class="col-md-4">
                                                                                                    <small class="text-muted">Instrumento</small>
                                                                                                    <p class=" mb-0">{{$index}}</p>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="modal-table">
                                                                                            <table class="table table-sm table-hover">
                                                                                                <thead>
                                                                                                <tr>
                                                                                                    <th class="tdlineff">Sucursal</th>
                                                                                                    <th class="tdlineff">Instrumento</th>
                                                                                                    <th class="tdlineff">Cliente</th>
                                                                                                    <th class="tdlineff">Tipo</th>
                                                                                                    <th class="tdlineff">Documento</th>
                                                                                                    <th class="tdlineff">Referencia</th>
                                                                                                    <th class="tdlineff">Monto USD.</th>
                                                                                                </tr>
                                                                                                </thead>
                                                                                                <tbody>
                                                                                                @php $totalinst = 0; @endphp
                                                                                                @if(isset($lines[$indexsuc][$indexpunto][$index]))
                                                                                                    @foreach($lines[$indexsuc][$indexpunto][$index] as $line)
                                                                                                        @php $totalinst += $line['monto']; @endphp
                                                                                                        <tr>
                                                                                                            <td class="tdline">{{str_replace("SARA","",$indexsuc)}}</td>
                                                                                                            <td class="tdline">{{$index}}</td>
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
                                                                                                    <th colspan="6" class="text-end">Total:</th>
                                                                                                    <th class="text-end text-primary">
                                                                                                        {{ number_format($totalinst, 2, ',', '.') }}
                                                                                                    </th>
                                                                                                </tr>
                                                                                                </tfoot>
                                                                                            </table>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                @else

                                                                @endif
                                                        @endforeach
                                                @endforeach
                                            @endforeach
                                        @endif

                                    </div>

                                    @php
                                        $totalGeneral      = array_sum($totales);
                                        $totalInstrumentos = count($clases);
                                        $totalSucursales   = count($sucursales);
                                    @endphp

                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Distribución por Instrumento -->
                    @if(!empty($totales) && $fecha1 != '')
                        <div class="col-md-12">
                            <div class="card mt-5">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="bi bi-bar-chart-line me-2"></i>
                                        Distribución por Instrumento (USD)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $totalGeneral = array_sum($totales);
                                    @endphp
                                    @foreach($totales as $instrumento => $monto)
                                        @php $porcentaje = $totalGeneral > 0 ? round(($monto / $totalGeneral) * 100, 1) : 0; @endphp
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="">{{ $instrumento }}</span>
                                                <span class="text-primary">
                                                      {{ number_format($monto, 2, ',', '.') }} ({{$porcentaje}}%)
                                                </span>
                                            </div>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-primary" style="width: {{ $porcentaje }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

