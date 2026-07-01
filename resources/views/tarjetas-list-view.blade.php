@extends('layouts.master')
@section('title')
    Panel de Instrumentos de Pago
@endsection
@section('css')
    <!-- extra css -->
@endsection
@section('content')
    <x-breadcrumb title="Lista de Instrumentos de Pago" pagetitle="Listado" />
    <div class="row">
        <div class="col-xxl-3 col-md-6">
            <div class="card card-height-100 bg-warning-subtle border-0 overflow-hidden">
                <div class="position-absolute end-0 start-0 top-0 z-0">
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                        width="400" height="250" preserveAspectRatio="none" viewBox="0 0 400 250">
                        <g mask="url(&quot;#SvgjsMask1530&quot;)" fill="none">
                            <path d="M209 112L130 191" stroke-width="10" stroke="url(#SvgjsLinearGradient1531)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M324 10L149 185" stroke-width="8" stroke="url(#SvgjsLinearGradient1532)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M333 35L508 -140" stroke-width="10" stroke="url(#SvgjsLinearGradient1532)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M282 58L131 209" stroke-width="10" stroke="url(#SvgjsLinearGradient1531)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M290 16L410 -104" stroke-width="6" stroke="url(#SvgjsLinearGradient1532)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M216 186L328 74" stroke-width="6" stroke="url(#SvgjsLinearGradient1531)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M255 53L176 132" stroke-width="10" stroke="url(#SvgjsLinearGradient1531)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M339 191L519 11" stroke-width="8" stroke="url(#SvgjsLinearGradient1531)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M95 151L185 61" stroke-width="6" stroke="url(#SvgjsLinearGradient1532)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M249 16L342 -77" stroke-width="6" stroke="url(#SvgjsLinearGradient1532)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M129 230L286 73" stroke-width="10" stroke="url(#SvgjsLinearGradient1531)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M80 216L3 293" stroke-width="6" stroke="url(#SvgjsLinearGradient1531)"
                                stroke-linecap="round" class="BottomLeft"></path>
                        </g>
                        <defs>
                            <mask id="SvgjsMask1530">
                                <rect width="400" height="250" fill="#ffffff"></rect>
                            </mask>
                            <linearGradient x1="100%" y1="0%" x2="0%" y2="100%"
                                id="SvgjsLinearGradient1531">
                                <stop stop-color="rgba(var(--tb-warning-rgb), 0)" offset="0"></stop>
                                <stop stop-color="rgba(var(--tb-warning-rgb), 0.2)" offset="1"></stop>
                            </linearGradient>
                            <linearGradient x1="0%" y1="100%" x2="100%" y2="0%"
                                id="SvgjsLinearGradient1532">
                                <stop stop-color="rgba(var(--tb-warning-rgb), 0)" offset="0"></stop>
                                <stop stop-color="rgba(var(--tb-warning-rgb), 0.2)" offset="1"></stop>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="card-body p-4 z-1 position-relative">
                    <h4 class="fs-22 fw-semibold mb-3"><span class="tarjetascounter counter-value" data-target="{{count($tarjetas)}}"></span> </h4>
                    <p class="mb-0 fw-medium text-uppercase fs-14">  Instrumento<?php if(count($tarjetas)!=1)echo 's'?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="tarjetasList">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-3">
                            <div class="search-box">
                                <input type="text" class="form-control search" placeholder="Buscar...">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
                        <div class="col-lg-auto">
                            <select class="form-control" id="idStatus" name="choices-single-default">
                                <option value="">Status</option>
                                <option value="All" selected>Todos</option>
                                <option value="Activo">Activos</option>
                                <option value="Inactivo">Inactivos</option>
                            </select>
                        </div>

                        <div class="col-lg-auto ms-auto">
                            <div class="hstack gap-2">
                                <a class="btn btn-primary add-btn" href="#showModal" data-bs-toggle="modal">
                                    +1 Instrumento de pago
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive table-card mb-1">
                        <table class="table align-middle table-nowrap" id="customerTable">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 50px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAll"
                                                value="option">
                                        </div>
                                    </th>
                                    <th class="sort" data-sort="codtarj">Codigo</th>
                                    <th class="sort" data-sort="descrip">Descripci&oacute;n</th>
                                    <th class="sort" data-sort="bs">Bolivares</th>
                                    <th class="sort" data-sort="dolares">Dolares</th>
                                    <th class="sort" data-sort="pesos">Pesos</th>
                                    <th class="sort" data-sort="activo"> Estatus</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all">
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="chk_child" value="option1">
                                        </div>
                                    </th>
                                    <td class="id" style="display:none;"><a href="javascript:void(0);"  class="fw-medium link-primary">#TB01</a></td>
                                    <td class="codtarj"> </td>
                                    <td class="descrip"> </td>
                                    <td class="bs"> </td>
                                    <td class="dolares"> </td>
                                    <td class="pesos"> </td>
                                    <td class="activo">
                                        <span  class="badge badge-soft-danger text-uppercase">Inactivo</span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <div class="edit">
                                                <a class="btn btn-sm btn-soft-info edit-item-btn" href="#showModal"
                                                    data-bs-toggle="modal">Editar</a>
                                            </div>
                                            <div class="remove">
                                                <button class="btn btn-sm btn-soft-danger remove-item-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteRecordModal">Eliminar</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="noresult" style="display: none">
                            <div class="text-center py-4">
                                <div class="avatar-md mx-auto mb-4">
                                    <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-24">
                                        <i class="bi bi-search"></i>
                                    </div>
                                </div>
                                <h5 class="mt-2">Disculpe!! no hay resultados</h5>
                                <p class="text-muted mb-0">Hemos buscado en mas de {{count($tarjetas)}}+ Instrumentos de pago registrados y
                                    no encontramos ninguno para tu busqueda.</p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <div class="pagination-wrap hstack gap-2">
                            <a class="page-item pagination-prev disabled" href="#">
                                <i class="mdi mdi-chevron-left align-middle me-1"></i> Anterior
                            </a>
                            <ul class="pagination listjs-pagination mb-0"></ul>
                            <a class="page-item pagination-next" href="#">
                                Siguiente <i class="mdi mdi-chevron-right align-middle ms-1"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- deleteRecordModal -->
    <div id="deleteRecordModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-md-5">
                    <div class="text-center">
                        <div class="text-danger">
                            <i class="bi bi-trash display-4"></i>
                        </div>
                        <div class="mt-4">
                            <h4 class="mb-2">Alerta</h4>
                            <p class="text-muted fs-17 mx-4 mb-0">Estas seguro de borrar este registro?</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                        <button type="button" class="btn w-sm btn-light btn-hover" id="deleteRecord-close"
                            data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn w-sm btn-danger btn-hover" id="delete-record">Si, Borrar</button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" id="showModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header px-4 pt-4">
                    <h5 class="modal-title" id="exampleModalLabel">Instrumento de pago nuevo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>
                <form class="tablelist-form" novalidate action="{{route('instpago.create')}}" autocomplete="off" method="post">
                    @csrf
                    <div class="modal-body p-4">
                        <div id="alert-error-msg" class="d-none alert alert-danger py-2"></div>
                        <input type="hidden" id="id-field">


                        <div class="mb-3">
                            <label for="codtarj-field" class="form-label">C&oacute;digo</label>
                            <input type="text" id="codtarj-field" name="codtarj-field" class="form-control"
                                   placeholder="Ej: 0001" required>
                        </div>

                        <div class="mb-3">
                            <label for="descrip-field" class="form-label">Descripci&oacute;n</label>
                            <input type="text"  name="descrip-field" id="descrip-field" class="form-control"
                                   placeholder="Ej: Punto Mercantil" required>
                        </div>

                        <div class="mb-3 input-group">
                            <div class="input-group-text">
                                <input class="form-check-input mt-0" type="checkbox" name="bs" id="bs-field"  value="1" aria-label="">
                            </div>
                            <div class="form-control" > Bolivares? </div>
                        </div>

                        <div class="mb-3 input-group">
                            <div class="input-group-text">
                                <input class="form-check-input mt-0" type="checkbox" name="dolares" id="dolares-field" value="1" aria-label="">
                            </div>
                            <div class="form-control" >Dolares? </div>
                        </div>

                        <div class="mb-3 input-group">
                            <div class="input-group-text">
                                <input class="form-check-input mt-0" type="checkbox" id="pesos-field" name="pesos" value="1" aria-label="">
                            </div>
                            <div class="form-control" >Pesos? </div>
                        </div>

                        <div  >
                            <label for="account-status-field" class="form-label"> Estatus</label>
                            <select class="form-control" required id="account-status-field">
                                <option value="">Seleccione el estatus del Instrumento de pago</option>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-ghost-danger" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-success" id="add-btn">+1 Instrumento de pago</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <!-- list.js min js -->
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>

    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <!-- Sellers list js -->
    <script src="{{ URL::asset('build/js/backend/tarjeta-list.init.js') }}?version={{rand(0,500)}}"></script>
    <script src="{{ URL::asset('build/js/pages/sweetalerts.init.js') }}"></script>

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>


@endsection
