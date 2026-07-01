@extends('layouts.master')
@section('title')
    Inicio
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
    </style>
@endsection
@section('content')
    <div class="row">

        <div class=" col-lg-3  ">
            <div class="row  ">
                <div class="col-12">
                    <a class="card card-animate" href="/tesoro"  >
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="vr rounded bg-secondary opacity-50" style="width: 4px;"></div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-medium text-muted fs-14 text-truncate">Confirmar Pago movil</p>
                                    <h6 class="  mb-3"> <span>  Banco del Tesoro</span> </h6>

                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-secondary-subtle text-secondary rounded fs-3">
                                        <i class="ph-wallet"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @if(Auth::user() and auth()->user()->can('menu_transferencias'))
                    <div class="col-12">
                        <a class="card card-animate" href="/transferencias/create"  >
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="vr rounded bg-secondary opacity-50" style="width: 4px;"></div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="text-uppercase fw-medium text-muted fs-14 text-truncate">Ingresar</p>
                                        <h6 class="  mb-3"> <span>Transferencia</span> </h6>

                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-secondary-subtle text-secondary rounded fs-3">
                                            <i class="ph-wallet"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
                <div class="col-12">
                    <a class="card card-animate" href="/productos"   >
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="vr rounded bg-info opacity-50" style="width: 4px;"></div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-medium text-muted fs-14 text-truncate">INVENTARIO</p>
                                    <h6 class="  mb-3"> <span>PRODUCTOS</span> </h6>

                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-info-subtle text-info rounded fs-3">
                                        <i class="ph-storefront"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

    </div>

@endsection
@section('scripts')

    <script src="{{ URL::asset('build/js/app.js') }}"></script>

@endsection
