@extends('layouts.master')
@section('title')
    INSTRUMENTOS DE PAGO BOLIVARES
@endsection
@section('css')
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
            background: linear-gradient(135deg, #0072c5 0%, #004b8f 100%);
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

        #tablainstpagobs a:hover, .modaltarjetas a:hover {
           color:#004b8f !important;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">REPORTE DE INSTRUMENTOS DE PAGO EN BOLÍVARES</h4>
                    <p class="text-white-50 mb-0 small">
                        @if($fecha1!='')
                            <i class="bi bi-calendar-range me-2 text-white"></i>
                            {{$fecha1}} - {{$fecha2}}
                        @endif
                    </p>
                </div>
                <div class="card-body  ">

                    <form  method="post" name="form1" id="form1">
                        @csrf
                        @method('POST')

                        <div class="row mt-3">
                            @if($fecha1!='')
                                <div class="col-md-2">
                                    <select class="form-select" data-choices onchange="$('#form1').submit()"
                                            id="idsucu" name="fksucursal">
                                        <option {{($fksucursal == '' or $fksucursal == 0)?'selected':''}} value="0">
                                            🌐 Todas
                                        </option>
                                        @foreach($allsucursales as $sucu)
                                            <option {{($sucu->id == $fksucursal)?'selected':''}} value="{{$sucu->id}}">
                                                🏢 {{$sucu->descrip}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" class="form-control" data-provider="flatpickr"
                                           data-range-date="true" data-date-format="d/m/Y"
                                           name="fechasreport" readonly="readonly" value="{{$fechasreport}}"
                                           placeholder="Seleccionar fechas">
                                    <div class="input-group-text bg-primary border-primary text-white">
                                        <button type="submit" class="botoncal">
                                            <i class="bi bi-search"></i> Consultar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="input-group">
                                    <div class="input-group-text bg-primary border-primary text-white w-100 justify-content-center">
                                        <button onclick="loadingreport('/reporte/instpagodolares')" type="button" class="botoncal w-100">
                                            <i class="bi bi-currency-dollar me-2"></i>
                                            VER REPORTE EN USD
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <button type="button" class="btn btn-sm btn-outline-primary me-2" style="height: 43px;"
                                        onclick="exportarExcel('tablainstpagobs')">
                                    <i class="bi bi-file-excel"></i> Exportar a Excel
                                </button>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    {!! $view !!}

@endsection

@section('scripts')
    <script>
        function loadingreport(action) {
            $('#form1').attr('action', action);
            $('#contentReport').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted">Cargando reporte...</p>
                </div>
            `);
            $('#form1').submit();
        }

        // Inicializar tooltips
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
