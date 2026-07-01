@extends('layouts.master')
@section('title')
    Transferencias
@endsection
@section('css')
    <!-- extra css -->
    <!-- Sweet Alert css-->
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')
    <x-breadcrumb title="Listado de transferencias" pagetitle="Transferencias" />


    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="invoiceList">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1" style="font-weight: 100 !important;">Listado de
                            @if(!$busquedatransf and !$fechas and !$status)
                                las &uacute;ltimas 100
                            @endif

                            @if( $fechas)
                                las
                            @endif
                            Transferencias

                            @if( $busquedatransf)
                               buscando por:   <b> {{$busquedatransf}} </b>
                            @endif

                            @if( $fechas)
                             del   <b>  {{str_replace('to','al',$fechas)}}</b>
                            @endif

                            @if( $status == 0)   <b>  Pendientes por aprobar</b> @endif
                            @if( $status == 1)   <b>que fueron Aprobadas  </b> @endif
                            @if( $status == 2)   <b>que fueron Rechazadas </b> @endif
                        </h5>
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-2 flex-wrap">
                                <button class="btn btn-danger btn-icon" id="remove-actions" onClick="deleteMultiple()"><i
                                        class="ri-delete-bin-2-line"></i></button>
                                <a href="/transferencias/create" class="btn btn-primary"><i
                                        class="ri-add-line align-bottom me-1"></i>1 Transferencia</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body bg-soft-light border border-dashed border-start-0 border-end-0">

                    <form name="form1" id="form1" action="/transferencias" >
                        @method('GET')
                        @csrf

                        <div class="row g-3">
                            <div class="col-xxl-5 col-sm-12">
                                <div class="search-box">
                                    <input type="text" id="busquedatransf" name="busquedatransf"  value="{{$busquedatransf}}"
                                           class="form-control search bg-light border-light"
                                        placeholder="Puede buscar: Titular, Nro Transferencia ...">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>

                            <div class="col-xxl-3 col-sm-4">
                                <input type="text" class="form-control bg-light border-light" id="fechas"
                                       data-range-date="true" data-date-format="d/m/Y" name="fechas"
                                       data-deafult-date="" data-provider="flatpickr" value="{{$fechas}}"
                                       placeholder="Fechas">
                            </div>

                            <div class="col-xxl-3 col-sm-4">
                                <div class="input-light">
                                    <select class="form-control" data-choices data-choices-search-false
                                        name="status" id="status">
                                        <option value="2222" @if($status == '2222') selected @endif>Todas</option>
                                        <option value="1" @if($status == 1)  selected @endif>Aprobadas</option>
                                        <option value="2" @if($status == 2)  selected @endif>Rechazadas</option>
                                        <option value="0" @if($status == 0)  selected @endif>Pendientes</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-xxl-1 col-sm-4">
                                <button type="submit" class="btn btn-info w-100"  >
                                    <i class="ri-equalizer-fill me-1 align-bottom"></i>
                                </button>
                            </div>

                        </div>

                    </form>

                </div>
                <div class="card-body">
                    <div>
                        <div class="table-responsive table-card">
                            <table class="table align-middle table-nowrap" id="transferenciaTable">
                                <thead class="text-muted">
                                    <tr>
                                       <!-- <th scope="col" style="width: 50px;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="checkAll"
                                                    value="option">
                                            </div>
                                        </th>-->
                                        <th class="sort" data-sort="numero">Nro.Transf</th>
                                        <th class="sort" data-sort="titular">Titular</th>
                                        <th class="sort" data-sort="banco">Banco</th>
                                        <th class="sort" data-sort="sucursal">Sucursal</th>
                                        <th class="sort" data-sort="date">Fecha</th>
                                        <th class="sort" data-sort="monto">Monto</th>
                                        <th class="sort" data-sort="status">Status</th>
                                        <th class="sort" data-sort="action">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="list form-check-all" id="transferencias-list-data">

                                </tbody>
                            </table>

                        </div>
                        <!-- <div class="d-flex justify-content-end mt-3"  style="display: none">
                               <div class="pagination-wrap hstack gap-2 flex-wrap">
                                   <a class="page-item pagination-prev disabled" href="#">
                                       Previous
                                   </a>
                                   <ul class="pagination listjs-pagination mb-0"></ul>
                                   <a class="page-item pagination-next" href="#">
                                       Next
                                   </a>
                               </div>
                           </div>l -->
                   </div>

                   <!-- Modal -->
                    <div class="modal fade flip" id="deleteOrder" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body p-5 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                        colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px">
                                    </lord-icon>
                                    <div class="mt-4 text-center">
                                        <h4>Desea eliminar esta transferencia?</h4>
                                        <p class="text-muted fs-15 mb-4">
                                            Borrando este registro ud eliminar&aacute; la informaci&oacute;n de la base de datos </p>
                                        <div class="hstack gap-2 justify-content-center remove">
                                            <button class="btn btn-link link-success fw-medium text-decoration-none"
                                                id="deleteRecord-close" data-bs-dismiss="modal"><i
                                                    class="ri-close-line me-1 align-middle"></i> Cancelar</button>
                                            @if( auth()->user()->can('menu_transferencias_eliminar') )
                                                <button class="btn btn-danger" id="deleterecord" data-id="">Si, Eliminar</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end modal -->
                </div>
            </div>

        </div>

    </div>

@endsection
@section('scripts')
    <script>
        $('#deleterecord').unbind('click').bind('click',function () {
            var id = $(this).data('id');

            $.ajax({
                type: 'DELETE',
                url: '/transferencias/'+id,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    var deleted = data.deleted;

                    if(deleted == 1) {
                        $('#tr'+id).hide();
                        $("#deleteRecord-close").click();
                        //window.location.href='/transferencias';
                    }else{
                        console.log('no se pudo eliminar');
                    }
                }
            });
        });
    </script>
    <!-- list.js min js -->
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>

    <!--list pagination js-->
    <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>


    <script src="{{ URL::asset('build/js/backend/transferencias.init.js') }}?version={{rand(0,500)}}"></script>

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
