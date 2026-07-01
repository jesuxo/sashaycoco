@extends('layouts.master')
@section('title')
    Informaci&oacute;n para Transferencias
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')
    <x-breadcrumb title="Info transferencias" pagetitle="Transferencias" />
    <form action="/transferencia/informacion" method="post" name="form1" id="form1">
        @csrf
        @method('POST')
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-height-100">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Informaci&oacute;n para transferencias</h4>

                        <div class="input-group telefonodiv" @if(!isset($telefono)) style="display: none" @endif>
                            <input type="text" @if($error) style="color: red" @endif class="form-control" name="telefono" value="{{$telefono}}"  placeholder="Telefono: 584141230011"   >
                            @if(!isset($telefono) or  $error)
                            <button type="submit" class="input-group-text bg-primary border-primary text-white">
                                <i class="bi-arrow-right"></i>
                            </button>
                            @else
                                <a  target="_blank"  href="https://api.whatsapp.com/send?phone=+{{ $telefono}}&text={{ $urlencode }}"  class="input-group-text bg-primary border-primary text-white">
                                    <i class="bi-send"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    <div data-simplebar="init" style="max-height: 445px;" class="simplebar-scrollable-y">
                        <div class="simplebar-wrapper" style="margin: 0px;">
                            <div class="simplebar-height-auto-observer-wrapper">
                                <div class="simplebar-height-auto-observer"></div>
                            </div>
                            <div class="simplebar-mask">
                                <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                    <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: auto; overflow: hidden scroll;">
                                        <div class="simplebar-content" style="padding: 0px;">

                                            @foreach($listado as $item)
                                                <div class="p-3 border-bottom border-bottom-dashed">
                                                    <div class="d-flex align-items-center gap-2">

                                                        <div class="flex-shrink-0 avatar-xs me-2">
                                                            <input type="radio" class="btn-check" value="{{$item->id}}-{{$item->fksucursal}}"
                                                                   name="options" id="{{$item->id}}-{{$item->fksucursal}}" @if($options == $item->id.'-'.$item->fksucursal) checked @endif >
                                                            <label class="btn btn-success " for="{{$item->id}}-{{$item->fksucursal}}"><i class="ph-envelope"></i></label>
                                                        </div>

                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1">{{$item->descrip}}</h6>
                                                            <p class="fs-13 text-muted mb-0">{!! $item->texto !!} </p>
                                                        </div>

                                                    </div>
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="simplebar-placeholder" style="width: 290px; height: 585px;"></div>
                        </div>
                        <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                            <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                        </div>
                        <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
                            <div class="simplebar-scrollbar" style="height: 338px; transform: translate3d(0px, 0px, 0px); display: block;"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>

@endsection
@section('scripts')
    <script>
        $('.btn-check').click(function (){
            $('.telefonodiv').fadeIn();
        });
    </script>

    <!-- Sweet Alerts js -->
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
