<style>
    /* Estilos existentes */
    .badge {
        font-size: 11px;
        padding: 4px 6px;
        border-radius: 12px;
        white-space: nowrap;
    }

    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }


    /* Nuevos estilos para la navegación */
    .search-selected {
        background-color: rgba(13, 110, 253, 0.1) !important;
        border-left: 3px solid #0d6efd;
        transition: all 0.2s ease;
    }

    .search-selected td:first-child {
        border-left: none;
    }

    #ajaxbusquedaproductos {
        max-height: 500px;
        overflow-y: auto;
    }

    #ajaxbusquedaproductos tbody tr {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    #ajaxbusquedaproductos tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
    .search-mouse-hover {
        background-color: rgba(13, 110, 253, 0.05) !important;
        transition: all 0.2s ease;
    }

    /* La selección activa mantiene su estilo distintivo */
    .search-selected {
        background-color: rgba(13, 110, 253, 0.1) !important;
        border-left: 3px solid #0d6efd;
    }

    /* Si un elemento es hover y también está seleccionado, priorizar el estilo de selección */
    .search-selected.search-mouse-hover {
        background-color: rgba(13, 110, 253, 0.1) !important;
        border-left: 3px solid #0d6efd;
    }
</style>
<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="index" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="50">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ URL::asset('build/images/logo-light.png') }}" alt="" height="50">
                        </span>
                    </a>

                    <a href="index" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="50">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ URL::asset('build/images/logo-light.png') }}" alt="" height="50">
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger" id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>

                <button onclick="focusbusqueda()" type="button" class="btn btn-sm px-3 fs-15 user-name-text header-item d-none d-md-block" data-bs-toggle="modal" data-bs-target="#searchModal">
                    <span class="bi bi-search me-2"></span> Busqueda...
                </button>
                <script>
                    function selectinput(){
                        $('#search-options').select();
                    }
                    function focusbusqueda(){
                        $('#searchModal').modal('show');
                        setTimeout(selectinput, 600);
                    }
                </script>
            </div>

            <div class="d-flex align-items-center">

                <div class="d-md-none topbar-head-dropdown header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-dark rounded-circle" id="page-header-search-dropdown" data-bs-toggle="modal" data-bs-target="#searchModal">
                        <i class="bi bi-search fs-16"></i>
                    </button>
                </div>


                <div class="dropdown topbar-head-dropdown ms-1 header-item dropdown-hover-end" style="display: none">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-dark rounded-circle" id="page-header-cart-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                        <i class='bi bi-bag fs-18'></i>
                        <span class="position-absolute topbar-badge cartitem-badge fs-10 translate-middle badge rounded-pill bg-info">5</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-xl dropdown-menu-end p-0 dropdown-menu-cart" aria-labelledby="page-header-cart-dropdown">
                        <div class="p-3 border-top-0 border-start-0 border-end-0 border-dashed border">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fs-16 fw-semibold"> My Cart</h6>
                                </div>
                                <div class="col-auto">
                                    <span class="badge badge-soft-info fs-13"><span class="cartitem-badge">7</span>
                                        items</span>
                                </div>
                            </div>
                        </div>
                        <div data-simplebar style="max-height: 300px; ">
                            <div class="p-2">
                                <div class="text-center empty-cart" id="empty-cart">
                                    <div class="avatar-md mx-auto my-3">
                                        <div class="avatar-title bg-info-subtle text-info fs-36 rounded-circle">
                                            <i class='bx bx-cart'></i>
                                        </div>
                                    </div>
                                    <h5 class="mb-3">Your Cart is Empty!</h5>
                                    <a href="apps-ecommerce-products" class="btn btn-success w-md mb-3">Shop Now</a>
                                </div>

                                <div class="d-block dropdown-item dropdown-item-cart text-wrap px-3 py-2">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ URL::asset('build/images/products/img-1.png') }}" class="me-3 rounded-circle avatar-sm p-2 bg-light" alt="user-pic">
                                        <div class="flex-grow-1">
                                            <h6 class="mt-0 mb-2 fs-15">
                                                <a href="apps-ecommerce-product-details" class="text-reset">Branded
                                                    T222-Shirts</a>
                                            </h6>
                                            <p class="mb-0 fs-13 text-muted">
                                                Quantity: <span>10 x $32</span>
                                            </p>
                                        </div>
                                        <div class="px-2">
                                            <h5 class="m-0 fw-normal">$<span class="cart-item-price">320</span></h5>
                                        </div>
                                        <div class="ps-2">
                                            <button type="button" class="btn btn-icon btn-sm btn-ghost-danger remove-item-btn"><i class="ri-close-fill fs-16"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-block dropdown-item dropdown-item-cart text-wrap px-3 py-2">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ URL::asset('build/images/products/img-2.png') }}" class="me-3 rounded-circle avatar-sm p-2 bg-light" alt="user-pic">
                                        <div class="flex-grow-1">
                                            <h6 class="mt-0 mb-2 fs-15">
                                                <a href="apps-ecommerce-product-details" class="text-reset">Bentwood Chair</a>
                                            </h6>
                                            <p class="mb-0 fs-13 text-muted">
                                                Quantity: <span>5 x $18</span>
                                            </p>
                                        </div>
                                        <div class="px-2">
                                            <h5 class="m-0 fw-normal">$<span class="cart-item-price">89</span></h5>
                                        </div>
                                        <div class="ps-2">
                                            <button type="button" class="btn btn-icon btn-sm btn-ghost-danger remove-item-btn"><i class="ri-close-fill fs-16"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-block dropdown-item dropdown-item-cart text-wrap px-3 py-2">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ URL::asset('build/images/products/img-3.png') }}" class="me-3 rounded-circle avatar-sm p-2 bg-light" alt="user-pic">
                                        <div class="flex-grow-1">
                                            <h6 class="mt-0 mb-2 fs-15">
                                                <a href="apps-ecommerce-product-details" class="text-reset">
                                                    Borosil Paper Cup</a>
                                            </h6>
                                            <p class="mb-0 fs-13 text-muted">
                                                Quantity: <span>3 x $250</span>
                                            </p>
                                        </div>
                                        <div class="px-2">
                                            <h5 class="m-0 fw-normal">$<span class="cart-item-price">750</span></h5>
                                        </div>
                                        <div class="ps-2">
                                            <button type="button" class="btn btn-icon btn-sm btn-ghost-danger remove-item-btn"><i class="ri-close-fill fs-16"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-block dropdown-item dropdown-item-cart text-wrap px-3 py-2">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ URL::asset('build/images/products/img-6.png') }}" class="me-3 rounded-circle avatar-sm p-2 bg-light" alt="user-pic">
                                        <div class="flex-grow-1">
                                            <h6 class="mt-0 mb-2 fs-15">
                                                <a href="apps-ecommerce-product-details" class="text-reset">Gray
                                                    Styled T-Shirt</a>
                                            </h6>
                                            <p class="mb-0 fs-13 text-muted">
                                                Quantity: <span>1 x $1250</span>
                                            </p>
                                        </div>
                                        <div class="px-2">
                                            <h5 class="m-0 fw-normal">$ <span class="cart-item-price">1250</span></h5>
                                        </div>
                                        <div class="ps-2">
                                            <button type="button" class="btn btn-icon btn-sm btn-ghost-danger remove-item-btn"><i class="ri-close-fill fs-16"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-block dropdown-item dropdown-item-cart text-wrap px-3 py-2">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ URL::asset('build/images/products/img-5.png') }}" class="me-3 rounded-circle avatar-sm p-2 bg-light" alt="user-pic">
                                        <div class="flex-grow-1">
                                            <h6 class="mt-0 mb-2 fs-15">
                                                <a href="apps-ecommerce-product-details" class="text-reset">Stillbird Helmet</a>
                                            </h6>
                                            <p class="mb-0 fs-13 text-muted">
                                                Quantity: <span>2 x $495</span>
                                            </p>
                                        </div>
                                        <div class="px-2">
                                            <h5 class="m-0 fw-normal">$<span class="cart-item-price">990</span></h5>
                                        </div>
                                        <div class="ps-2">
                                            <button type="button" class="btn btn-icon btn-sm btn-ghost-danger remove-item-btn"><i class="ri-close-fill fs-16"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-3 border-bottom-0 border-start-0 border-end-0 border-dashed border" id="checkout-elem">
                            <div class="d-flex justify-content-between align-items-center pb-3">
                                <h5 class="m-0 text-muted">Total:</h5>
                                <div class="px-2">
                                    <h5 class="m-0" id="cart-item-total">$1258.58</h5>
                                </div>
                            </div>

                            <a href="apps-ecommerce-checkout" class="btn btn-success text-center w-100">
                                Checkout
                            </a>
                        </div>
                    </div>
                </div>

                <div class="dropdown topbar-head-dropdown ms-1 header-item dropdown-hover-end">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-dark rounded-circle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-arrow-left-right align-middle fs-20 "></i>
                    </button>
                    <div class="dropdown-menu p-2 dropdown-menu-end" style="width: 400px">
                        <div class="dropdown-head rounded-top">
                            <div class="p-3 border-bottom border-bottom-dashed">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="mb-0 fs-16 fw-semibold">Grupo de Empresas
                                            <span class="badge bg-danger-subtle text-danger fs-13 notification-badge">
                                {{ isset($comerciales_acceso) ? count($comerciales_acceso) : 0 }}
                            </span>
                                        </h6>
                                        <p class="fs-14 text-muted mt-1 mb-0">Seleccione el grupo que necesita consultar</p>
                                    </div>
                                    <div class="col-auto">
                                        <a href="javascript:void(0);" class="link-secondary fs-15" id="refreshComerciales">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="dropdown-body" style="max-height: 400px; overflow-y: auto;">
                            @php
                                $user = Auth::user();
                                $comerciales_acceso = $user ? $user->getComercialesAcceso() : collect();
                                $comercialdata = session('comercialdata');
                            @endphp

                            @if($comerciales_acceso->count() > 0)
                                @foreach($comerciales_acceso as $comercial)
                                    <a href="{{ route('comercial.cambiar', $comercial->id) }}"
                                       class="dropdown-item {{ session('comercialid') == $comercial->id ? 'active bg-primary text-white' : '' }}"
                                       data-comercial-id="{{ $comercial->id }}">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-shop me-2 fs-16"></i>
                                            <div class="flex-grow-1">
                                                <span class="fw-medium">{{ $comercial->descrip }}</span>
                                                @if(session('comercialid') == $comercial->id)
                                                    <span class="badge bg-success ms-2">Activo</span>
                                                @endif
                                            </div>
                                            @if(session('comercialid') == $comercial->id)
                                                <i class="bi bi-check-lg text-white fs-16"></i>
                                            @endif
                                        </div>
                                        <small class="text-muted d-block mt-1 ms-4 ps-1">
                                            <i class="bi bi-building"></i>
                                            {{ $comercial->sucursales->count() }} sucursal(es) asignada(s)
                                        </small>
                                    </a>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                                    <p class="mt-2 text-muted">No tienes comerciales asignados</p>
                                    <small class="text-muted">Contacta al administrador</small>
                                </div>
                            @endif
                        </div>

                        @if($comerciales_acceso->count() > 0)
                            <div class="dropdown-foot p-2 border-top border-top-dashed mt-2">
                                <div class="text-center">
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle"></i>
                                        Cambiar el grupo de empresas actualizará la información mostrada
                                    </small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>


                <div class="dropdown topbar-head-dropdown ms-1 header-item dropdown-hover-end" style="display:none;">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-dark rounded-circle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-sun align-middle fs-20"></i>
                    </button>
                    <div class="dropdown-menu p-2 dropdown-menu-end" id="light-dark-mode">
                        <a href="#!" class="dropdown-item" data-mode="light"><i class="bi bi-sun align-middle me-2"></i> Defualt (light mode)</a>
                        <a href="#!" class="dropdown-item" data-mode="dark"><i class="bi bi-moon align-middle me-2"></i> Dark</a>
                        <a href="#!" class="dropdown-item" data-mode="auto"><i class="bi bi-moon-stars align-middle me-2"></i> Auto (system defualt)</a>
                    </div>
                </div>

                <div class="dropdown topbar-head-dropdown ms-1 header-item dropdown-hover-end" id="notificationDropdown" style="display: none">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-dark rounded-circle" id="page-header-notifications-dropdown" data-bs-toggle="dropdown"  data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                        <i class='bi bi-bell fs-18'></i>
                        <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger"><span class="notification-badge">4</span><span class="visually-hidden">unread messages</span></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">

                        <div class="dropdown-head rounded-top">
                            <div class="p-3 border-bottom border-bottom-dashed">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="mb-0 fs-16 fw-semibold"> Notifications <span class="badge badge-soft-danger fs-13 notification-badge"> 4</span></h6>
                                        <p class="fs-14 text-muted mt-1 mb-0">You have <span class="fw-semibold notification-unread">3</span> unread messages</p>
                                    </div>
                                    <div class="col-auto dropdown">
                                        <a href="javascript:void(0);" data-bs-toggle="dropdown" class="link-secondar2 fs-15"><i class="bi bi-three-dots-vertical"></i></a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">All Clear</a></li>
                                            <li><a class="dropdown-item" href="#">Mark all as read</a></li>
                                            <li><a class="dropdown-item" href="#">Archive All</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="py-2 ps-2" id="notificationItemsTabContent">
                            <div data-simplebar style="max-height: 300px;" class="pe-2">
                                <h6 class="text-overflow text-muted fs-13 my-2 text-uppercase notification-title">New</h6>
                                <div class="text-reset notification-item d-block dropdown-item position-relative unread-message">
                                    <div class="d-flex">
                                        <div class="avatar-xs me-3 flex-shrink-0">
                                            <span class="avatar-title bg-info-subtle text-info rounded-circle fs-16">
                                                <i class="bx bx-badge-check"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <a href="#!" class="stretched-link">
                                                <h6 class="mt-0 fs-14 mb-2 lh-base">Your <b>Elite</b> author Graphic
                                                    Optimization <span class="text-secondary">reward</span> is ready!
                                                </h6>
                                            </a>
                                            <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                <span><i class="mdi mdi-clock-outline"></i> Just 30 sec ago</span>
                                            </p>
                                        </div>
                                        <div class="px-2 fs-15">
                                            <div class="form-check notification-check">
                                                <input class="form-check-input" type="checkbox" value="" id="all-notification-check01">
                                                <label class="form-check-label" for="all-notification-check01"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-reset notification-item d-block dropdown-item position-relative unread-message">
                                    <div class="d-flex">
                                        <div class="position-relative me-3 flex-shrink-0">
                                            <img src="{{ URL::asset('build/images/users/avatar-2.jpg') }}" class="rounded-circle avatar-xs" alt="user-pic">
                                            <span class="active-badge position-absolute start-100 translate-middle p-1 bg-success rounded-circle">
                                                <span class="visually-hidden">New alerts</span>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <a href="#!" class="stretched-link">
                                                <h6 class="mt-0 mb-1 fs-14 fw-semibold">Angela Bernier</h6>
                                            </a>
                                            <div class="fs-13 text-muted">
                                                <p class="mb-1">Answered to your comment on the cash flow forecast's graph 🔔.</p>
                                            </div>
                                            <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                <span><i class="mdi mdi-clock-outline"></i> 48 min ago</span>
                                            </p>
                                        </div>
                                        <div class="px-2 fs-15">
                                            <div class="form-check notification-check">
                                                <input class="form-check-input" type="checkbox" value="" id="all-notification-check02">
                                                <label class="form-check-label" for="all-notification-check02"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-reset notification-item d-block dropdown-item position-relative unread-message">
                                    <div class="d-flex">
                                        <div class="avatar-xs me-3 flex-shrink-0">
                                            <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-16">
                                                <i class='bx bx-message-square-dots'></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <a href="#!" class="stretched-link">
                                                <h6 class="mt-0 mb-2 fs-14 lh-base">You have received <b class="text-success">20</b> new messages in the conversation
                                                </h6>
                                            </a>
                                            <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                <span><i class="mdi mdi-clock-outline"></i> 2 hrs ago</span>
                                            </p>
                                        </div>
                                        <div class="px-2 fs-15">
                                            <div class="form-check notification-check">
                                                <input class="form-check-input" type="checkbox" value="" id="all-notification-check03">
                                                <label class="form-check-label" for="all-notification-check03"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="text-overflow text-muted fs-13 my-2 text-uppercase notification-title">Read Before</h6>

                                <div class="text-reset notification-item d-block dropdown-item position-relative">
                                    <div class="d-flex">

                                        <div class="position-relative me-3 flex-shrink-0">
                                            <img src="{{ URL::asset('build/images/users/avatar-8.jpg') }}" class="rounded-circle avatar-xs" alt="user-pic">
                                            <span class="active-badge position-absolute start-100 translate-middle p-1 bg-warning rounded-circle">
                                                <span class="visually-hidden">New alerts</span>
                                            </span>
                                        </div>

                                        <div class="flex-grow-1">
                                            <a href="#!" class="stretched-link">
                                                <h6 class="mt-0 mb-1 fs-14 fw-semibold">Maureen Gibson</h6>
                                            </a>
                                            <div class="fs-13 text-muted">
                                                <p class="mb-1">We talked about a project on linkedin.</p>
                                            </div>
                                            <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                <span><i class="mdi mdi-clock-outline"></i> 4 hrs ago</span>
                                            </p>
                                        </div>
                                        <div class="px-2 fs-15">
                                            <div class="form-check notification-check">
                                                <input class="form-check-input" type="checkbox" value="" id="all-notification-check04">
                                                <label class="form-check-label" for="all-notification-check04"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="notification-actions" id="notification-actions">
                                <div class="d-flex text-muted justify-content-center align-items-center">
                                    Select <div id="select-content" class="text-body fw-semibold px-1">0</div> Result <button type="button" class="btn btn-link link-danger p-0 ms-2" data-bs-toggle="modal" data-bs-target="#removeNotificationModal">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dropdown ms-sm-3 header-item topbar-user topbar-head-dropdown dropdown-hover-end" style="display: none">
                    <button type="button" class="btn  "
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                        @switch(Session::get('lang'))
                            @case('ru')
                                <img src="{{ URL::asset('build/images/flags/russia.svg') }}" class="rounded-circle me-2"
                                     alt="Header Language" height="16">
                                <span id="lang-name" class="user-name-text">русский</span>
                                @break

                            @case('it')
                                <img src="{{ URL::asset('build/images/flags/italy.svg') }}" class="rounded-circle me-2"
                                     alt="Header Language" height="16">
                                <span id="lang-name" class="user-name-text">Italiana</span>
                                @break

                            @case('sp')
                                <img src="{{ URL::asset('build/images/flags/spain.svg') }}" class="rounded-circle me-2"
                                     alt="Header Language" height="16">
                                <span id="lang-name" class="user-name-text">Español</span>
                                @break

                            @case('ch')
                                <img src="{{ URL::asset('build/images/flags/china.svg') }}" class="rounded-circle me-2"
                                     alt="Header Language" height="16">
                                <span id="lang-name" class="user-name-text">中国人</span>
                                @break

                            @case('fr')
                                <img src="{{ URL::asset('build/images/flags/french.svg') }}" class="rounded-circle me-2"
                                     alt="Header Language" height="16">
                                <span id="lang-name" class="user-name-text">français</span>
                                @break

                            @case('gr')
                                <img src="{{ URL::asset('build/images/flags/germany.svg') }}" class="rounded-circle me-2"
                                     alt="Header Language" height="16">
                                <span id="lang-name" class="user-name-text">Deutsche</span>
                                @break

                            @case('sa')
                                <img src="{{ URL::asset('build/images/flags/sa.svg') }}" class="rounded-circle me-2"
                                     alt="Header Language" height="16">
                                <span id="lang-name" class="user-name-text">عربى</span>
                                @break

                            @default
                                <img src="{{ URL::asset('build/images/flags/us.svg') }}" class="rounded-circle me-2"
                                     alt="Header Language" height="16">
                                <span id="lang-name" class="user-name-text">English</span>
                        @endswitch
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <a href="{{ url('index/en') }}" class="dropdown-item notify-item language py-2"
                           data-lang="en" title="English">
                            <img src="{{ URL::asset('build/images/flags/us.svg') }}" alt="user-image"
                                 class="me-2 rounded-circle" height="18">
                            <span class="align-middle">English</span>
                        </a>

                        <!-- item-->
                        <a href="{{ url('index/sp') }}" class="dropdown-item notify-item language" data-lang="sp"
                           title="Spanish">
                            <img src="{{ URL::asset('build/images/flags/spain.svg') }}" alt="user-image"
                                 class="me-2 rounded-circle" height="18">
                            <span class="align-middle">Español</span>
                        </a>

                        <!-- item-->
                        <a href="{{ url('index/gr') }}" class="dropdown-item notify-item language" data-lang="gr"
                           title="German">
                            <img src="{{ URL::asset('build/images/flags/germany.svg') }}" alt="user-image"
                                 class="me-2 rounded-circle" height="18"> <span
                                class="align-middle">Deutsche</span>
                        </a>

                        <!-- item-->
                        <a href="{{ url('index/it') }}" class="dropdown-item notify-item language" data-lang="it"
                           title="Italian">
                            <img src="{{ URL::asset('build/images/flags/italy.svg') }}" alt="user-image"
                                 class="me-2 rounded-circle" height="18">
                            <span class="align-middle">Italiana</span>
                        </a>

                        <!-- item-->
                        <a href="{{ url('index/ru') }}" class="dropdown-item notify-item language"
                           data-lang="ru" title="Russian">
                            <img src="{{ URL::asset('build/images/flags/russia.svg') }}" alt="user-image"
                                 class="me-2 rounded-circle" height="18">
                            <span class="align-middle">русский</span>
                        </a>

                        <!-- item-->
                        <a href="{{ url('index/ch') }}" class="dropdown-item notify-item language"
                           data-lang="ch" title="Chinese">
                            <img src="{{ URL::asset('build/images/flags/china.svg') }}" alt="user-image"
                                 class="me-2 rounded-circle" height="18">
                            <span class="align-middle">中国人</span>
                        </a>

                        <!-- item-->
                        <a href="{{ url('index/fr') }}" class="dropdown-item notify-item language"
                           data-lang="fr" title="French">
                            <img src="{{ URL::asset('build/images/flags/french.svg') }}" alt="user-image"
                                 class="me-2 rounded-circle" height="18">
                            <span class="align-middle">français</span>
                        </a>
                        <!-- item-->
                        <a href="{{ url('index/sa') }}" class="dropdown-item notify-item language"
                           data-lang="ae" title="Arabic">
                            <img src="{{ URL::asset('build/images/flags/sa.svg') }}" alt="user-image"
                                 class="me-2 rounded-circle" height="18">
                            <span class="align-middle">عربى</span>
                        </a>
                    </div>
                </div>
                <div class="dropdown  header-item topbar-user topbar-head-dropdown dropdown-hover-end"  >
                    <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user" src="@if(@Auth::user()->avatar) {{ URL::asset('images/users/')."/". @Auth::user()->avatar}} @else {{ URL::asset('build/images/users/avatar-1.jpg') }} @endif" alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ @Auth::user()->first_name  }}</span>
                                <span class="d-none d-xl-block ms-1 fs-13 user-name-sub-text " style="display: none !important;">Founder</span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header"> {{@Auth::user()->first_name}} {{@Auth::user()->last_name}}</h6>
                        <a class="dropdown-item" style="display: none" href="account"><i class="bi bi-person-circle text-muted fs-15 align-middle me-1"></i> <span class="align-middle">Profile</span></a>
                        <a class="dropdown-item" style="display: none"href="calendar"><i class="bi bi-cart4 text-muted fs-15 align-middle me-1"></i> <span class="align-middle">Order Track</span></a>
                        <a class="dropdown-item" href="/productos"><i class="bi bi-box-seam text-muted fs-15 align-middle me-1"></i> <span class="align-middle">Productos</span></a>
                        <a class="dropdown-item"  style="display: none" href="javascript:void(0)"><span class="badge bg-success-subtle text-success float-end ms-2">New</span><i class="bi bi-cassette text-muted fs-15 align-middle me-1"></i> <span class="align-middle">Frontend</span></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="account-setting"  style="display: none"><i class="bi bi-gear text-muted fs-15 align-middle me-1"></i> <span class="align-middle">Settings</span></a>
                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-right text-muted fs-15 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">{{ __('t-logout') }}</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form  method="POST" style="display: none;"  action="{{ route('logout') }}" id="logout-form">
        @csrf
    </form>
</header>


<!-- Modal -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content rounded">
            <div class="modal-header p-3">
                <div class="position-relative w-100">
                    <input type="text" class="form-control form-control-lg border-2 busquedaproductos"
                           placeholder="Busqueda de productos..." autocomplete="off" id="search-options" value="" onchange="performSearch($(this).val())">
                    <span class="bi bi-search search-widget-icon fs-17"></span>
                    <a href="javascript:void(0);" class="search-widget-icon fs-14 link-secondary text-decoration-underline search-widget-icon-close d-none" id="search-close-options">Limpiar</a>
                </div>
            </div>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0 overflow-hidden" id="search-dropdown">

                <div class="dropdown-head rounded-top">
                    <div class="p-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0 fs-14 text-muted fw-semibold"> Coincidencias con la busqueda </h6>
                            </div>
                            <div class="col" style="text-align: right">
                                <h6 class="m-0 fs-14 text-muted fw-semibold" id="textbusqueda"> </h6>
                            </div>
                        </div>
                    </div>

                    <div class="dropdown-item bg-transparent text-wrap" id="ajaxbusquedaproductos">

                    </div>

                    <script>
                        $(document).ready(function() {
                            let currentSelectedIndex = -1;
                            let searchResults = [];
                            let isScrolling = false;
                            let mouseOverIndex = -1;
                            let isNavigatingWithKeyboard = false;



                            // Variable para saber si ya se realizó una búsqueda
                            let hasSearchResults = false;

                            // Evento para el input de búsqueda


                            // Evento para el botón "Limpiar"
                            $('#search-close-options').off('click').on('click', function() {
                                $('#search-options').val('');
                                $('#textbusqueda').html('');
                                $('#ajaxbusquedaproductos').html('');
                                searchResults = [];
                                currentSelectedIndex = -1;
                                mouseOverIndex = -1;
                                hasSearchResults = false;
                                $('#search-options').focus();
                            });

                            // Cuando se abre el modal
                            $('#searchModal').off('shown.bs.modal').on('shown.bs.modal', function() {
                                const $input = $('#search-options');
                                $input.focus();
                                currentSelectedIndex = -1;
                                searchResults = [];
                                mouseOverIndex = -1;
                                hasSearchResults = false;

                                // Limpiar búsqueda anterior
                                $input.val('');
                                $('#textbusqueda').html('');
                                $('#ajaxbusquedaproductos').html('');
                            });

                            // Cuando se cierra el modal
                            $('#searchModal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
                                currentSelectedIndex = -1;
                                searchResults = [];
                                mouseOverIndex = -1;
                                hasSearchResults = false;
                            });

                            // Navegación con mouse - SOLO efecto visual
                            $(document).off('mouseenter.searchModal', '#ajaxbusquedaproductos tbody tr').on('mouseenter.searchModal', '#ajaxbusquedaproductos tbody tr', function() {
                                if ($('#searchModal').hasClass('show') && !isNavigatingWithKeyboard) {
                                    const index = searchResults.indexOf(this);
                                    if (index !== -1 && index !== currentSelectedIndex) {
                                        mouseOverIndex = index;
                                        $('.search-mouse-hover').removeClass('search-mouse-hover');
                                        $(this).addClass('search-mouse-hover');
                                    }
                                }
                            });

                            $(document).off('mouseleave.searchModal', '#ajaxbusquedaproductos tbody tr').on('mouseleave.searchModal', '#ajaxbusquedaproductos tbody tr', function() {
                                if ($('#searchModal').hasClass('show')) {

                                    $('.search-mouse-hover').removeClass('search-mouse-hover');
                                }
                            });


                        });
                        function updateSearchResults(response) {
                            $('#ajaxbusquedaproductos').html(response);

                        }

                        function performSearch(busqueda) {
                            if (busqueda.trim() !== '') {
                                $('#textbusqueda').html("Búsqueda: " + busqueda);
                            } else {
                                $('#textbusqueda').html('');
                                $('#ajaxbusquedaproductos').html('');
                                searchResults = [];
                                currentSelectedIndex = -1;
                                mouseOverIndex = -1;
                                return;
                            }

                            $('#ajaxbusquedaproductos').html('<div class="text-center p-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><p class="mt-2">Buscando productos...</p></div>');

                            $.ajax({
                                type: 'POST',
                                url: '/saprod/home/busqueda',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data: { busqueda: busqueda },
                                success: function(response) {
                                    updateSearchResults(response);
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error en búsqueda:', error);
                                    $('#ajaxbusquedaproductos').html('<div class="text-center p-4 text-danger">Error al realizar la búsqueda</div>');
                                }
                            });
                        }
                    </script>
                </div>

                <div data-simplebar style=" display:none !important; max-height: 300px;" class="pe-2 ps-3 mt-3">
                    <div class="list-group list-group-flush border-dashed">
                        <div class="notification-group-list">
                            <h5 class="text-overflow text-muted fs-13 mb-2 mt-3 text-uppercase notification-title">Apps Pages</h5>
                            <a href="javascript:void(0);" class="list-group-item dropdown-item notify-item"><i class="bi bi-speedometer2 me-2"></i> <span>Analytics Dashboard</span></a>
                            <a href="javascript:void(0);" class="list-group-item dropdown-item notify-item"><i class="bi bi-filetype-psd me-2"></i> <span>Toner.psd</span></a>
                            <a href="javascript:void(0);" class="list-group-item dropdown-item notify-item"><i class="bi bi-ticket-detailed me-2"></i> <span>Support Tickets</span></a>
                            <a href="javascript:void(0);" class="list-group-item dropdown-item notify-item"><i class="bi bi-file-earmark-zip me-2"></i> <span>Toner.zip</span></a>
                        </div>

                        <div class="notification-group-list">
                            <h5 class="text-overflow text-muted fs-13 mb-2 mt-3 text-uppercase notification-title">Links</h5>
                            <a href="javascript:void(0);" class="list-group-item dropdown-item notify-item"><i class="bi bi-link-45deg me-2 align-middle"></i> <span>www.wwww.com</span></a>
                        </div>

                        <div class="notification-group-list">
                            <h5 class="text-overflow text-muted fs-13 mb-2 mt-3 text-uppercase notification-title">People</h5>
                            <a href="javascript:void(0);" class="list-group-item dropdown-item notify-item">
                                <div class="d-flex align-items-center">
                                    <img src="{{ URL::asset('build/images/users/avatar-1.jpg') }}" alt="" class="avatar-xs rounded-circle flex-shrink-0 me-2">
                                    <div>
                                        <h6 class="mb-0">Ayaan Bowen</h6>
                                        <span class="fs-12 text-muted">React Developer</span>
                                    </div>
                                </div>
                            </a>
                            <a href="javascript:void(0);" class="list-group-item dropdown-item notify-item">
                                <div class="d-flex align-items-center">
                                    <img src="{{ URL::asset('build/images/users/avatar-7.jpg') }}" alt="" class="avatar-xs rounded-circle flex-shrink-0 me-2">
                                    <div>
                                        <h6 class="mb-0">Alexander Kristi</h6>
                                        <span class="fs-12 text-muted">React Developer</span>
                                    </div>
                                </div>
                            </a>
                            <a href="javascript:void(0);" class="list-group-item dropdown-item notify-item">
                                <div class="d-flex align-items-center">
                                    <img src="{{ URL::asset('build/images/users/avatar-5.jpg') }}" alt="" class="avatar-xs rounded-circle flex-shrink-0 me-2">
                                    <div>
                                        <h6 class="mb-0">Alan Carla</h6>
                                        <span class="fs-12 text-muted">React Developer</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- removeNotificationModal -->
<div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="NotificationModalbtn-close"></button>
            </div>
            <div class="modal-body p-md-5">
                <div class="text-center">
                    <div class="text-danger">
                        <i class="bi bi-trash display-4"></i>
                    </div>
                    <div class="mt-4 fs-15">
                        <h4 class="mb-1">Are you sure ?</h4>
                        <p class="text-muted mx-4 mb-0">Are you sure you want to remove this Notification ?</p>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                    <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete It!</button>
                </div>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    document.addEventListener('keydown', function(e) {
        /*if ((e.ctrlKey || e.metaKey) && (e.key === 'f' || e.key === 'F')) {
            e.preventDefault(); // Prevenir el comportamiento predeterminado del navegador
            focusbusqueda(); // Ejecutar tu función
        }*/
    });
</script>
