<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu" style="background: #eaeaea !important;">
    <!-- LOGO -->
    <div class="navbar-brand-box" style="background: {{(session('comercialid')==6)? '#ffffff': '#eaeaea'}} !important;">
        <a href="/index" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="50">
            </span>
            <span class="logo-lg">
                @php
                    $comercialdata  = session('comercialdata');
                    $logowhite      = (isset($comercialdata->logowhite))? $comercialdata->logowhite: ''
                @endphp
                <img src="{{ URL::asset('build/images/'.$logowhite) }}" alt="" height="50">
            </span>
        </a>
        <a href="/index" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="50">
            </span>
            <span class="logo-lg">asd
                <img src="{{ URL::asset('build/images/logo-light.png') }}" alt="" height="50">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar" style="background: #eaeaea !important;">
        <div class="container-fluid">
            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">{{ __('t-menu') }}</span></li>

                <!-- Panel / Dashboard -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarPanel" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarPanel">
                        <i class="bi bi-speedometer2"></i> <span data-key="t-dashboard">Dashboard</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarPanel">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="/" class="nav-link" data-key="t-home">Inicio</a>
                            </li>
                        </ul>
                        @if(Auth::user() and auth()->user()->type == 'admin')
                            <ul class="nav nav-sm flex-column">

                                <li class="nav-item">
                                    <a href="/reporte/venta" class="nav-link" data-key="t-sales-report">Reporte de Ventas</a>
                                </li>
                                <li class="nav-item">
                                    <a href="/reporte/instpagobs" class="nav-link" data-key="t-inst-payment-bs">Rep. Inst Pago Bs</a>
                                </li>
                                <li class="nav-item">
                                    <a href="/reporte/instpagodolares" class="nav-link" data-key="t-inst-payment-usd">Rep. Inst Pago Dólares</a>
                                </li>
                                <li class="nav-item">
                                    <a href="/resumenVentas" class="nav-link" data-key="t-sales-summary">Resumen Ventas</a>
                                </li>
                            </ul>
                        @endif
                    </div>
                </li>

                <!-- Inventario -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarProducts" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarProducts">
                        <i class="bi bi-box-seam"></i> <span data-key="t-inventory">Inventario</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarProducts">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="/productos" class="nav-link" data-key="t-products-list">
                                    <i class="bi bi-grid-3x3-gap-fill me-2"></i>Productos
                                </a>
                            </li>
                            @if(Auth::user() and auth()->user()->type == 'admin')
                                <li class="nav-item">
                                    <a href="/existencias" class="nav-link" data-key="t-stock">
                                        <i class="bi bi-database me-2"></i>Existencias
                                    </a>
                                </li>
                            @endif

                            @if(Auth::user() and auth()->user()->can('menu_inventario_instancias'))
                                <li class="nav-item">
                                    <a href="/instancias" class="nav-link" data-key="t-categories">
                                        <i class="bi bi-tags me-2"></i>Instancias Inv.
                                    </a>
                                </li>
                            @endif

                            @if(Auth::user() and auth()->user()->can('menu_inventario_depositos'))
                                <li class="nav-item">
                                    <a href="/depositos" class="nav-link" data-key="t-warehouses">
                                        <i class="  ri-stack-line me-2"></i>Dep&oacute;sitos
                                    </a>
                                </li>
                            @endif

                            <li class="nav-item d-none  ">
                                <a href="/sugerir-transferencias" class="nav-link" data-key="t-transfer-suggestions">
                                    <i class="bi bi-arrow-left-right me-2 text-warning"></i>
                                    <span class="fw-semibold">Sugerir Transferencias</span>
                                    <span class="badge bg-danger ms-2 rounded-pill">Nuevo</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Proveedores -->
                @if(Auth::user() and auth()->user()->can('menu_proveedores'))
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarProveedores" data-bs-toggle="collapse"
                           role="button" aria-expanded="false" aria-controls="sidebarProveedores">
                            <i class="bi bi-truck"></i> <span data-key="t-suppliers">Proveedores</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarProveedores">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="/proveedores" class="nav-link" data-key="t-suppliers-list">
                                        <i class="bi bi-search me-2"></i>Buscar Proveedor
                                    </a>
                                </li>
                                @if(session('comercialid') == 6)
                                <li class="nav-item ">
                                    <a href="{{ route('pagos-proveedores.index') }}" class="nav-link" data-key="t-list-view">
                                        <i class="ri-motorbike-fill me-2"></i>Motos
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                <!-- Clientes -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarClientes" data-bs-toggle="collapse"
                       role="button" aria-expanded="false" aria-controls="sidebarClientes">
                        <i class="bi bi-people"></i> <span data-key="t-customers">Clientes</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarClientes">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="/clientes" class="nav-link" data-key="t-customers-list">
                                    <i class="bi bi-list-ul me-2"></i>Listado Clientes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/cxc" class="nav-link" data-key="t-accounts-receivable">
                                    <i class="bi bi-receipt me-2"></i>Cuentas por Cobrar
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                @if(Auth::user() and auth()->user()->can('menu_vendedores'))
                    <!-- Vendedores -->
                    <li class="nav-item">
                        <a href="/vendedores" class="nav-link menu-link">
                            <i class="bi bi-person-badge"></i> <span data-key="t-sellers">Vendedores</span>
                        </a>
                    </li>
                @endif


                @if(Auth::user() and auth()->user()->can('menu_instpago'))
                <!-- Instancias de Pago -->
                    <li class="nav-item">
                        <a href="/instpago" class="nav-link menu-link">
                            <i class="bi bi-cash-stack"></i> <span data-key="t-payment-methods">Inst. de Pago</span>
                        </a>
                    </li>
                @endif

                <!-- Transferencias -->
                @if(Auth::user() and auth()->user()->can('menu_transferencias'))
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarTransferencias"
                           data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarTransferencias">
                            <i class="bi bi-arrow-left-right"></i> <span data-key="t-transfers">Transferencias</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarTransferencias">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{route('reportetransferencias')}}" class="nav-link" data-key="t-view-transfers">
                                        <i class="bi bi-eye me-2"></i>Ver Transferencias
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/transferencias/create" class="nav-link" data-key="t-add-transfer">
                                        <i class="bi bi-plus-circle me-2"></i>Agregar Transferencia
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                <!-- Tokens -->
                @if(Auth::user() and auth()->user()->can('menu_token'))
                    <li class="nav-item">
                        <a href="/tokens" class="nav-link menu-link">
                            <i class="bi bi-key"></i> <span data-key="t-tokens">Tokens</span>
                        </a>
                    </li>
                @endif

                <!-- Asistente IA -->
                @if(Auth::user() and auth()->user()->can('menu_ia'))
                    <li class="nav-item">
                        <a href="/iaknowledge" class="nav-link menu-link">
                            <i class="bi bi-robot"></i> <span data-key="t-ai-assistant">Asistente Virtual</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/chat/conversations" class="nav-link menu-link">
                            <i class="bi bi-chat-dots"></i> <span data-key="t-conversations">Conversaciones</span>
                        </a>
                    </li>
                @endif

            </ul>
        </div>
    </div>

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<div class="vertical-overlay"></div>
