@extends('layouts.master')
@section('title')
    Clientes
@endsection
@section('css')
    <!-- extra css -->
    <!-- Sweet Alert css-->
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <x-breadcrumb title="Clientes" pagetitle="Listado Clientes" />

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
                    <h4 class="fs-22 fw-semibold mb-3"><span class="counter-value" data-target="{{count($clientes)}}"></span> </h4>
                    <p class="mb-0 fw-medium text-uppercase fs-14">Clientes</p>
                </div>
            </div>
        </div>
        <!--end col-->
        <div class="col-xxl-3 col-md-6">
            <div class="card card-height-100 bg-success-subtle border-0 overflow-hidden">
                <div class="position-absolute end-0 start-0 top-0 z-0">
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                        width="400" height="250" preserveAspectRatio="none" viewBox="0 0 400 250">
                        <g mask="url(&quot;#SvgjsMask1608&quot;)" fill="none">
                            <path d="M390 87L269 208" stroke-width="10" stroke="url(#SvgjsLinearGradient1609)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M358 175L273 260" stroke-width="8" stroke="url(#SvgjsLinearGradient1610)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M319 84L189 214" stroke-width="10" stroke="url(#SvgjsLinearGradient1609)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M327 218L216 329" stroke-width="8" stroke="url(#SvgjsLinearGradient1610)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M126 188L8 306" stroke-width="8" stroke="url(#SvgjsLinearGradient1610)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M220 241L155 306" stroke-width="10" stroke="url(#SvgjsLinearGradient1610)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M361 92L427 26" stroke-width="6" stroke="url(#SvgjsLinearGradient1609)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M391 188L275 304" stroke-width="8" stroke="url(#SvgjsLinearGradient1609)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M178 74L248 4" stroke-width="10" stroke="url(#SvgjsLinearGradient1610)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M84 52L-56 192" stroke-width="6" stroke="url(#SvgjsLinearGradient1610)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M183 111L247 47" stroke-width="10" stroke="url(#SvgjsLinearGradient1610)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M46 8L209 -155" stroke-width="6" stroke="url(#SvgjsLinearGradient1609)"
                                stroke-linecap="round" class="TopRight"></path>
                        </g>
                        <defs>
                            <mask id="SvgjsMask1608">
                                <rect width="400" height="250" fill="#ffffff"></rect>
                            </mask>
                            <linearGradient x1="0%" y1="100%" x2="100%" y2="0%"
                                id="SvgjsLinearGradient1609">
                                <stop stop-color="rgba(var(--tb-success-rgb), 0)" offset="0"></stop>
                                <stop stop-color="rgba(var(--tb-success-rgb), 0.2)" offset="1"></stop>
                            </linearGradient>
                            <linearGradient x1="100%" y1="0%" x2="0%" y2="100%"
                                id="SvgjsLinearGradient1610">
                                <stop stop-color="rgba(var(--tb-success-rgb), 0)" offset="0"></stop>
                                <stop stop-color="rgba(var(--tb-success-rgb), 0.2)" offset="1"></stop>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="card-body p-4 z-1 position-relative">
                    <h4 class="fs-22 fw-semibold mb-3"><span class="counter-value" data-target="559.25"></span>k </h4>
                    <p class="mb-0 fw-medium text-uppercase fs-14">Activsssse Users</p>
                </div>
            </div>
        </div>
        <!--end col-->
        <div class="col-xxl-3 col-md-6">
            <div class="card card-height-100 bg-info-subtle border-0 overflow-hidden">
                <div class="position-absolute end-0 start-0 top-0 z-0">
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                        width="400" height="250" preserveAspectRatio="none" viewBox="0 0 400 250">
                        <g mask="url(&quot;#SvgjsMask1551&quot;)" fill="none">
                            <path d="M306 65L446 -75" stroke-width="8" stroke="url(#SvgjsLinearGradient1552)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M399 2L315 86" stroke-width="10" stroke="url(#SvgjsLinearGradient1553)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M83 77L256 -96" stroke-width="6" stroke="url(#SvgjsLinearGradient1553)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M281 212L460 33" stroke-width="6" stroke="url(#SvgjsLinearGradient1553)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M257 62L76 243" stroke-width="6" stroke="url(#SvgjsLinearGradient1553)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M305 123L214 214" stroke-width="6" stroke="url(#SvgjsLinearGradient1552)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M327 222L440 109" stroke-width="6" stroke="url(#SvgjsLinearGradient1552)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M287 109L362 34" stroke-width="10" stroke="url(#SvgjsLinearGradient1553)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M259 194L332 121" stroke-width="8" stroke="url(#SvgjsLinearGradient1553)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M376 186L240 322" stroke-width="8" stroke="url(#SvgjsLinearGradient1553)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M308 153L123 338" stroke-width="6" stroke="url(#SvgjsLinearGradient1553)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M218 62L285 -5" stroke-width="8" stroke="url(#SvgjsLinearGradient1552)"
                                stroke-linecap="round" class="BottomLeft"></path>
                        </g>
                        <defs>
                            <mask id="SvgjsMask1551">
                                <rect width="400" height="250" fill="#ffffff"></rect>
                            </mask>
                            <linearGradient x1="100%" y1="0%" x2="0%" y2="100%"
                                id="SvgjsLinearGradient1552">
                                <stop stop-color="rgba(var(--tb-info-rgb), 0)" offset="0"></stop>
                                <stop stop-color="rgba(var(--tb-info-rgb), 0.2)" offset="1"></stop>
                            </linearGradient>
                            <linearGradient x1="0%" y1="100%" x2="100%" y2="0%"
                                id="SvgjsLinearGradient1553">
                                <stop stop-color="rgba(var(--tb-info-rgb), 0)" offset="0"></stop>
                                <stop stop-color="rgba(var(--tb-info-rgb), 0.2)" offset="1"></stop>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="card-body p-4 z-1 position-relative">
                    <h4 class="fs-22 fw-semibold mb-3"><span class="counter-value" data-target="559.25"></span>k </h4>
                    <p class="mb-0 fw-medium text-uppercase fs-14">Unactive Users</p>
                </div>
            </div>
        </div>
        <!--end col-->
        <div class="col-xxl-3 col-md-6">
            <div class="card bg-light border-0">
                <div class="card-body p-3">
                    <div class="p-3 bg-white rounded">
                        <div class="d-flex align-items-center gap-3">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <div class="avatar-title bg-danger-subtle text-danger fs-4 rounded">
                                        <i class="ph-gift"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <a href="#!" class="stretched-link">
                                    <h6 class="fs-17">Invite your friends to Toner</h6>
                                </a>
                                <p class="text-muted mb-0">Nor again is there anyone pursues</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="clientList">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-lg-2 g-4">
                        <div class="col-lg-7">
                            <div class="search-box">
                                <input type="text" class="form-control search" placeholder="Busqueda de clientes...">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
                        <div class="col-lg-auto col-sm-3 ms-auto">

                        </div>
                        <div class="col-lg-auto col-sm-9" style="display: none !important;">
                            <select class="form-control"  data-choices data-choices-search-false
                                name="choices-single-default" id="idStatus">
                                <option value="all">All</option>
                                <option value="Today">Today</option>
                                <option value="Yesterday">Yesterday</option>
                                <option value="Last 7 Days">Last 7 Days</option>
                                <option value="Last 30 Days">Last 30 Days</option>
                                <option value="This Month" selected>This Month</option>
                                <option value="Last Month">Last Month</option>
                            </select>
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
                                            <input class="form-check-input" type="checkbox" id="checkAll" value="option">
                                        </div>
                                    </th>
                                    <th class="sort" style="width: 150px;"data-sort="codclie">Cod/Rif</th>
                                    <th class="sort" data-sort="descrip">Nombre/Raz&oacute;n Social</th>
                                    <th class="sort" data-sort="telef">Tel&eacute;fono</th>
                                    <th class=" " >Registrado</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all">
                                <tr >
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="chk_child" value="option1">
                                        </div>
                                    </th>
                                    <td class="id" style="display:none;"><a href="javascript:void(0);" class="fw-medium link-primary">#VZ01</a></td>
                                    <td class="codclie">...</td>
                                    <td class="descrip">...</td>
                                    <td class="telef"  >...</td>
                                    <td class="date"   >...</td>

                                </tr>

                            </tbody>
                        </table>
                        <div class="noresult" style="display: none">
                            <div class="text-center">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                    colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                <h5 class="mt-2">Disculpe! No hay resultados</h5>
                                <p class="text-muted mb-0">Hemos buscado entre mas de {{count($clientes)}} clientes registrados, no hemos encontrado coincidencias para tu busqueda.</p>
                            </div>
                        </div>
                    </div>


                    <div class="d-flex justify-content-end">
                        <div class="pagination-wrap hstack gap-2">
                            <a class="page-item pagination-prev disabled" href="#">
                                <i class="mdi mdi-chevron-left align-middle me-1"></i> Ant</a>
                            </a>
                            <ul class="pagination listjs-pagination mb-0"></ul>
                            <a class="page-item pagination-next" href="#">
                                Sig <i class="mdi mdi-chevron-right align-middle ms-1"></i></a>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




@endsection
@section('scripts')
    <!-- list.js min js -->
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>

    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script src="{{ URL::asset('build/js/backend/clients-list.init.js') }}"></script>

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
