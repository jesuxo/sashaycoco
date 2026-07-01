
@extends('layouts.master')
@section('title')
    REPORTE DE INVENTARIO
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css">

    <!--Swiper slider css-->
    <link href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css">
    <style>
    .botoncal{
        background: transparent;
        border: none;
        color: white;
    }
    .botoncal:hover{
         font-size: 13px;
    }
    </style>
@endsection
@section('content')
    <style>
        .tdline{
            border:1px solid #0072c5 !important;

        }
        .tdlineff{
            border-left:1px solid #fff !important;

            color: white !important;
            background-color: #0072c5 !important;
        }
    </style>
    <div class="row">
        <div class=" col-lg-3  ">
            <div class="card card-height-100">
                <div class="card-header">
                    <div class="d-flex align-items-center gap-3   mt-3 mt-xxl-0">
                        INVENTARIO POR SUCURSAL
                    </div>

                </div>
                <div class="card-body" data-simplebar  style="max-height: 285px;" >

                        <div class="table-responsive table-card ">
                            <table class="table table-borderless table-striped align-middle table-sm fs-14 mb-0">
                                <thead class="text-muted table-light">
                                    <tr>
                                            <th width="80%" scope="col">   Sucursal</th>
                                            <th  width="20%"scope="col" style="text-align: center !important" align="center">Costo</th>
                                    </tr>
                                </thead>
                                <tbody>

                                @php $tt = 0; @endphp
                                @foreach($costoinven as $index => $sucursal)
                                    @php $tt += $sucursal->suma;@endphp
                                    <tr>
                                        <td>  {{$sucursal->descrip}} </td>
                                        <td align="right"> {{number_format($sucursal->suma,2,',','.')}}  </td>
                                    </tr>

                                @endforeach
                                </tbody>
                                <tr>
                                    <td align="right">  Total: </td>
                                    <td align="right"> {{number_format($tt,2,',','.')}}  </td>
                                </tr>
                            </table>
                        </div>

                </div>
            </div>
        </div>
        <div class=" col-lg-9   ">
            <div class="card card-height-100">
                <div class="card-header">
                    <div class="d-flex align-items-center gap-3   mt-3 mt-xxl-0">
                       ASD
                    </div>

                </div>
                <div class="card-body" data-simplebar  style="max-height: 285px;" >
                    @if(isset($sucursales))
                        <div class="table-responsive table-card ">

                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>



@endsection
@section('scripts')
    <!-- apexcharts -->
    <script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- Vector map-->
    <script src="{{ URL::asset('build/libs/jsvectormap/jsvectormap.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/jsvectormap/world-merc.js') }}"></script>

    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>

    <!--Swiper slider js-->
    <script src="{{ URL::asset('build/libs/swiper/swiper-bundle.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

@endsection
