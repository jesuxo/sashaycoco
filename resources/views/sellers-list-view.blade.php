@extends('layouts.master')
@section('title')
    Panel de vendedores
@endsection
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" rel="stylesheet" />
@endsection
@section('content')
    <x-breadcrumb title="Lista de Vendedores" pagetitle="Vendedores" />

    @if(Auth::user() and auth()->user()->can('menu_vendedores'))
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
                        <h4 class="fs-22 fw-semibold mb-3"><span class="vendedorescounter" id="totalVendedoresCount"></span> </h4>
                        <p class="mb-0 fw-medium text-uppercase fs-14">Vendedor(es)</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="sellersList">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-3">
                                <div class="search-box">
                                    <input type="text" class="form-control search" id="searchInput" placeholder="Buscar...">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <div class="col-lg-auto">
                                <select class="form-control" id="idStatus">
                                    <option value="">Status</option>
                                    <option value="All" selected>Todos</option>
                                    <option value="Active">Activos</option>
                                    <option value="Inactive">Inactivos</option>
                                </select>
                            </div>
                            <div class="col-lg-auto ms-auto">
                                <div class="hstack gap-2">
                                    <a class="btn btn-primary add-btn" href="#showModal" data-bs-toggle="modal">
                                        +1 Vendedor
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
                                            <input class="form-check-input" type="checkbox" id="checkAll" value="option">
                                        </div>
                                    </th>
                                    <th class="sort" data-sort="codvend">Cod</th>
                                    <th class="sort" data-sort="sellerName">Nombre</th>
                                    <th class="sort" data-sort="email">Email</th>
                                    <th class="sort" data-sort="phone">Teléfono</th>
                                    <th class="sort" data-sort="accountStatus">Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody class="list form-check-all" id="vendedoresTableBody">
                                <!-- Los datos se cargarán dinámicamente -->
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
                                    <p class="text-muted mb-0">Hemos buscado y no encontramos ningún vendedor para tu búsqueda.</p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <div class="pagination-wrap hstack gap-2">
                                <a class="page-item pagination-prev disabled" href="#" id="prevPage">
                                    <i class="mdi mdi-chevron-left align-middle me-1"></i> Anterior
                                </a>
                                <ul class="pagination mb-0" id="paginationList"></ul>
                                <a class="page-item pagination-next" href="#" id="nextPage">
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
                                <p class="text-muted fs-17 mx-4 mb-0">¿Estás seguro de borrar este registro?</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                            <button type="button" class="btn w-sm btn-light btn-hover" id="deleteRecord-close" data-bs-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn w-sm btn-danger btn-hover" id="delete-record">Sí, Borrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="showModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header px-4 pt-4">
                        <h5 class="modal-title" id="exampleModalLabel">Información vendedor nuevo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                    </div>
                    <form id="vendedorForm" novalidate autocomplete="off">
                        @csrf
                        <div class="modal-body p-4">
                            <div id="alert-error-msg" class="d-none alert alert-danger py-2"></div>
                            <input type="hidden" id="id-field">
                            <input type="hidden" id="editMode" value="false">

                            <div class="mb-3">
                                <label for="seller-cod-field" class="form-label">Código</label>
                                <input type="text" id="seller-cod-field" name="codvend" class="form-control"
                                       placeholder="Ej: 001" required>
                            </div>

                            <div class="mb-3">
                                <label for="seller-name-field" class="form-label">Nombre</label>
                                <input type="text" id="seller-name-field" name="descrip" class="form-control"
                                       placeholder="Ej: Pedro Perez" required>
                            </div>

                            <div class="mb-3">
                                <label for="email-field" class="form-label">Email</label>
                                <input type="email" name="email" id="email-field" class="form-control"
                                       placeholder="Ej: correo@email.com" required>
                            </div>

                            <div class="mb-3">
                                <label for="phone-field" class="form-label">Teléfono</label>
                                <input type="text" name="telef" id="phone-field" class="form-control"
                                       placeholder="Ej: 0414-12345678" required>
                            </div>

                            <div>
                                <label for="account-status-field" class="form-label">Estatus</label>
                                <select class="form-control" required id="account-status-field">
                                    <option value="">Seleccione el estatus del vendedor</option>
                                    <option value="Active">Activo</option>
                                    <option value="Inactive">Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" class="btn btn-ghost-danger" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-success" id="add-btn">+1 Vendedor</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <script>
        // Variables globales
        let allVendedores = [];
        let currentPage = 1;
        let perPage = 10;
        let currentFilter = "All";
        let currentSearch = "";
        let deleteId = null;
        let choicesStatus = null;

        // Función para cargar vendedores desde el servidor
        function loadVendedores() {
            $.ajax({
                url: '{{ route("vendedores.json") }}',
                type: 'GET',
                success: function(data) {
                    allVendedores = data;
                    updateTotalCount();
                    filterAndRender();
                },
                error: function(xhr, status, error) {
                    console.error('Error cargando vendedores:', error);
                    Swal.fire('Error', 'No se pudieron cargar los vendedores', 'error');
                }
            });
        }

        // Actualizar contador total
        function updateTotalCount() {
            $('#totalVendedoresCount').text(allVendedores.length);
            $('.vendedorescounter').text(allVendedores.length);
        }

        // Filtrar vendedores
        function filterVendedores() {
            let filtered = [...allVendedores];

            // Filtrar por status
            if (currentFilter && currentFilter !== "All") {
                filtered = filtered.filter(v => v.accountStatus === currentFilter);
            }

            // Filtrar por búsqueda
            if (currentSearch) {
                const searchLower = currentSearch.toLowerCase();
                filtered = filtered.filter(v =>
                    v.codvend.toLowerCase().includes(searchLower) ||
                    v.sellerName.toLowerCase().includes(searchLower) ||
                    (v.email && v.email.toLowerCase().includes(searchLower)) ||
                    (v.phone && v.phone.toLowerCase().includes(searchLower))
                );
            }

            return filtered;
        }

        // Renderizar tabla con paginación
        function filterAndRender() {
            const filtered = filterVendedores();
            const totalPages = Math.ceil(filtered.length / perPage);

            if (currentPage > totalPages && totalPages > 0) {
                currentPage = totalPages;
            }

            const start = (currentPage - 1) * perPage;
            const end = start + perPage;
            const pageData = filtered.slice(start, end);

            renderTable(pageData);
            renderPagination(filtered.length, totalPages);

            // Mostrar/ocultar mensaje de no resultados
            if (filtered.length === 0) {
                $('.noresult').show();
            } else {
                $('.noresult').hide();
            }
        }

        // Renderizar tabla
        function renderTable(vendedores) {
            const tbody = $('#vendedoresTableBody');
            tbody.empty();

            if (vendedores.length === 0) {
                tbody.html('<tr><td colspan="7" class="text-center">No hay datos disponibles</td></tr>');
                return;
            }

            vendedores.forEach(vendedor => {
                const statusBadge = vendedor.accountStatus === 'Active' ?
                    '<span class="badge badge-soft-success text-uppercase">Active</span>' :
                    '<span class="badge badge-soft-danger text-uppercase">Inactive</span>';

                const row = `
                    <tr>
                        <th scope="row">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="chk_child" value="${vendedor.id}">
                            </div>
                        </th>
                        <td class="codvend">${escapeHtml(vendedor.codvend)}</td>
                        <td class="sellerName">${escapeHtml(vendedor.sellerName)}</td>
                        <td class="email">${escapeHtml(vendedor.email || '')}</td>
                        <td class="phone">${escapeHtml(vendedor.phone || '')}</td>
                        <td class="accountStatus">${statusBadge}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <div class="edit">
                                    <a class="btn btn-sm btn-soft-info edit-item-btn" href="#" data-bs-toggle="modal" data-bs-target="#showModal" data-id="${vendedor.id}">Editar</a>
                                </div>
                                <div class="remove">
                                    <button class="btn btn-sm btn-soft-danger remove-item-btn" data-bs-toggle="modal" data-bs-target="#deleteRecordModal" data-id="${vendedor.id}">Eliminar</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });

            // Reasignar eventos después de renderizar
            attachTableEvents();
        }

        // Función para escapar HTML y evitar XSS
        function escapeHtml(str) {
            if (!str) return '';
            return str
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        // Renderizar paginación
        function renderPagination(totalItems, totalPages) {
            const paginationList = $('#paginationList');
            paginationList.empty();

            if (totalPages <= 1) {
                $('.pagination-wrap').hide();
                return;
            }

            $('.pagination-wrap').show();

            // Botón anterior
            if (currentPage === 1) {
                $('#prevPage').addClass('disabled');
            } else {
                $('#prevPage').removeClass('disabled');
            }

            // Botón siguiente
            if (currentPage === totalPages) {
                $('#nextPage').addClass('disabled');
            } else {
                $('#nextPage').removeClass('disabled');
            }

            // Números de página
            const maxVisible = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
            let endPage = Math.min(totalPages, startPage + maxVisible - 1);

            if (endPage - startPage + 1 < maxVisible) {
                startPage = Math.max(1, endPage - maxVisible + 1);
            }

            if (startPage > 1) {
                paginationList.append(`<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`);
                if (startPage > 2) {
                    paginationList.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                const activeClass = i === currentPage ? 'active' : '';
                paginationList.append(`<li class="page-item ${activeClass}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`);
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    paginationList.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
                }
                paginationList.append(`<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`);
            }
        }

        // Adjuntar eventos de la tabla
        function attachTableEvents() {
            // Eventos de edición
            $('.edit-item-btn').off('click').on('click', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const vendedor = allVendedores.find(v => v.id == id);
                if (vendedor) {
                    openEditModal(vendedor);
                }
            });

            // Eventos de eliminación
            $('.remove-item-btn').off('click').on('click', function(e) {
                deleteId = $(this).data('id');
            });
        }

        // Validar email
        function isValidEmail(email) {
            const regex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
            return regex.test(email);
        }

        // Abrir modal de edición
        function openEditModal(vendedor) {
            $('#editMode').val('true');
            $('#id-field').val(vendedor.id);
            $('#seller-cod-field').val(vendedor.codvend);
            $('#seller-cod-field').prop('disabled', true);
            $('#seller-name-field').val(vendedor.sellerName);
            $('#email-field').val(vendedor.email || '');
            $('#phone-field').val(vendedor.phone || '');

            if (choicesStatus) {
                choicesStatus.destroy();
            }
            choicesStatus = new Choices('#account-status-field', {
                searchEnabled: false
            });
            if (vendedor.accountStatus) {
                choicesStatus.setChoiceByValue(vendedor.accountStatus);
            }

            $('#exampleModalLabel').text('Modificar Vendedor');
            $('#add-btn').text('Modificar');
        }

        // Abrir modal de creación
        function openCreateModal() {
            $('#editMode').val('false');
            $('#id-field').val('');
            $('#seller-cod-field').val('');
            $('#seller-cod-field').prop('disabled', false);
            $('#seller-name-field').val('');
            $('#email-field').val('');
            $('#phone-field').val('');

            if (choicesStatus) {
                choicesStatus.destroy();
            }
            choicesStatus = new Choices('#account-status-field', {
                searchEnabled: false
            });

            $('#exampleModalLabel').text('Información vendedor nuevo');
            $('#add-btn').text('+1 Vendedor');
        }

        // Guardar vendedor (crear o actualizar)
        function saveVendedor(formData, isEdit) {
            const id = $('#id-field').val();
            const url = isEdit ? `/vendedores/${id}` : '/vendedores';
            const method = isEdit ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                success: function(response) {
                    $('#close-modal').click();
                    Swal.fire({
                        icon: 'success',
                        title: isEdit ? 'Vendedor actualizado' : 'Vendedor creado',
                        text: isEdit ? 'El vendedor se ha actualizado correctamente' : 'El vendedor se ha creado correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    loadVendedores(); // Recargar lista
                },
                error: function(xhr) {
                    let errorMsg = 'Error al guardar el vendedor';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $('#alert-error-msg').removeClass('d-none').html(errorMsg);
                    setTimeout(() => $('#alert-error-msg').addClass('d-none'), 3000);
                }
            });
        }

        // Eliminar vendedor
        function deleteVendedor(id) {
            $.ajax({
                url: `/vendedores/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#deleteRecord-close').click();
                    Swal.fire({
                        icon: 'success',
                        title: 'Vendedor eliminado',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    loadVendedores(); // Recargar lista
                },
                error: function(xhr) {
                    Swal.fire('Error', 'No se pudo eliminar el vendedor', 'error');
                }
            });
        }

        // Inicializar todos los componentes
        function initializeComponents() {
            // Inicializar Choices para el filtro de status
            if ($('#idStatus').length) {
                new Choices('#idStatus', {
                    searchEnabled: false,
                    shouldSort: false
                });
            }

            // Inicializar Choices para el status del formulario
            if ($('#account-status-field').length) {
                choicesStatus = new Choices('#account-status-field', {
                    searchEnabled: false
                });
            }

            // Evento de búsqueda
            $('#searchInput').on('keyup', function() {
                currentSearch = $(this).val();
                currentPage = 1;
                filterAndRender();
            });

            // Evento de filtro por status
            $('#idStatus').on('change', function() {
                currentFilter = $(this).val();
                currentPage = 1;
                filterAndRender();
            });

            // Eventos de paginación
            $('#prevPage').on('click', function(e) {
                e.preventDefault();
                if (currentPage > 1) {
                    currentPage--;
                    filterAndRender();
                }
            });

            $('#nextPage').on('click', function(e) {
                e.preventDefault();
                const filtered = filterVendedores();
                const totalPages = Math.ceil(filtered.length / perPage);
                if (currentPage < totalPages) {
                    currentPage++;
                    filterAndRender();
                }
            });

            $(document).on('click', '#paginationList .page-link', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (page) {
                    currentPage = parseInt(page);
                    filterAndRender();
                }
            });

            // Evento de apertura del modal
            $('#showModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                if (button.hasClass('add-btn')) {
                    openCreateModal();
                }
            });

            // Evento de envío del formulario
            $('#vendedorForm').on('submit', function(e) {
                e.preventDefault();

                const codvend = $('#seller-cod-field').val().trim();
                const sellerName = $('#seller-name-field').val().trim();
                const email = $('#email-field').val().trim();
                const phone = $('#phone-field').val().trim();
                const activo = $('#account-status-field').val();
                const isEdit = $('#editMode').val() === 'true';

                if (!codvend) {
                    showError('El código del vendedor es requerido');
                    return;
                }
                if (!sellerName) {
                    showError('El nombre del vendedor es requerido');
                    return;
                }
                if (email && !isValidEmail(email)) {
                    showError('Formato de email incorrecto');
                    return;
                }
                if (!phone) {
                    showError('El número de teléfono es requerido');
                    return;
                }
                if (!activo) {
                    showError('Debe seleccionar el estatus del vendedor');
                    return;
                }

                const formData = {
                    codvend: codvend,
                    sellerName: sellerName,
                    email: email,
                    phone: phone,
                    activo: activo
                };

                saveVendedor(formData, isEdit);
            });

            // Evento de eliminación
            $('#delete-record').on('click', function() {
                if (deleteId) {
                    deleteVendedor(deleteId);
                    deleteId = null;
                }
            });

            // Check all functionality
            $('#checkAll').on('change', function() {
                $('input[name="chk_child"]').prop('checked', $(this).is(':checked'));
            });
        }

        // Función para mostrar errores
        function showError(message) {
            $('#alert-error-msg').removeClass('d-none').html(message);
            setTimeout(() => $('#alert-error-msg').addClass('d-none'), 3000);
        }

        // Cargar datos al iniciar
        $(document).ready(function() {
            if ($('#sellersList').length) {
                initializeComponents();
                loadVendedores();
            }
        });
    </script>
@endsection
