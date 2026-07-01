{{-- resources/views/usersucursal/index.blade.php --}}
@extends('layouts.master')
@section('title')
    Asignación de Sucursales a Usuarios
@endsection
@section('css')
    <style>
        .drag-user {
            cursor: all-scroll !important;
            padding: 10px;
            margin: 5px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .drag-user:hover {
            background-color: #e9ecef;
            transform: scale(1.02);
            cursor: grab;
        }

        .drag-user:active {
            cursor: grabbing;
        }

        .sucursal-dropzone {
            min-height: 300px;
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 15px;
            transition: all 0.3s;
        }

        .sucursal-dropzone.drag-over {
            background-color: #e3f2fd;
            border-color: #2196f3;
        }

        .sucursal-item {
            padding: 10px;
            margin: 8px 0;
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sucursal-item:hover {
            background-color: #f8f9fa;
        }

        .usuario-card {
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
        }

        .usuario-header {
            background-color: #0072c5;
            color: white;
            padding: 10px 15px;
            cursor: pointer;
        }

        .usuario-header h6 {
            margin: 0;
            color:white;
            display: inline-block;
        }

        .usuario-content {
            padding: 15px;
            display: none;
        }

        .usuario-content.active {
            display: block;
        }

        .btn-quitar-sucursal {
            color: #dc3545;
            cursor: pointer;
            font-size: 18px;
            text-decoration: none;
        }

        .btn-quitar-sucursal:hover {
            color: #c82333;
        }

        .sucursales-lista {
            max-height: 300px;
            overflow-y: auto;
        }

        .empty-message {
            text-align: center;
            color: #6c757d;
            padding: 20px;
        }

        .usuario-en-sucursal {
            background-color: white;
            padding: 8px;
            margin: 5px 0;
            border-radius: 5px;
            border: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .usuario-en-sucursal:hover {
            background-color: #f8f9fa;
        }
    </style>
@endsection
@section('content')
    <x-breadcrumb title="Asignación de Sucursales a Usuarios" pagetitle="Administración" />

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Arrastra un usuario a las sucursales para asignarlo</h5>
                    <p class="text-muted mb-0">Los usuarios pueden estar en múltiples sucursales</p>
                </div>
                <div class="card-body">
                    <div class="row" style="height: calc(100vh - 200px);">
                        <div class="col-md-4" style="overflow-y: auto; height: 100%;">
                            <h5 class="mb-3">Usuarios Disponibles</h5>
                            <div id="usuarios-container">
                                <div class="text-center">Cargando usuarios...</div>
                            </div>
                        </div>
                        <div class="col-md-8" style="overflow-y: auto; height: 100%;">
                            <h5 class="mb-3">Sucursales</h5>
                            <div id="sucursales-container">
                                <div class="text-center">Cargando sucursales...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">

    <script>
        $(document).ready(function() {
            cargarUsuarios();
            cargarSucursales();
        });

        function cargarUsuarios() {
            $.ajax({
                url: '{{ route("usersucursal.usuarios") }}',
                type: 'GET',
                success: function(usuarios) {
                    var html = '';
                    if (usuarios.length === 0) {
                        html = '<div class="empty-message">No hay usuarios registrados</div>';
                    } else {
                        usuarios.forEach(function(usuario) {
                            html += `
                                <div class="usuario-card">
                                    <div class="usuario-header" data-user-id="${usuario.id}">
                                        <h6 style="text-transform: uppercase">${usuario.first_name} ${usuario.last_name}</h6>
                                        <i class="mdi mdi-chevron-down float-right"></i>
                                    </div>
                                    <div class="usuario-content" id="usuario-${usuario.id}">
                                        <div class="drag-user" data-user-id="${usuario.id}">
                                            <strong>👤 ${usuario.first_name} ${usuario.last_name}</strong><br>
                                            <small class="text-muted">${usuario.email}</small>
                                        </div>
                                        <div class="mt-3">
                                            <strong>Sucursales Asignadas:</strong>
                                            <div id="sucursales-usuario-${usuario.id}" class="sucursales-lista mt-2">
                                                <div class="text-center"><small>Cargando...</small></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    }
                    $('#usuarios-container').html(html);

                    // Configurar acordeón para usuarios
                    $('.usuario-header').click(function() {
                        $(this).next('.usuario-content').toggleClass('active');
                        $(this).find('i').toggleClass('mdi-chevron-down mdi-chevron-up');

                        // Cargar sucursales asignadas al expandir
                        var userId = $(this).data('user-id');
                        if ($(this).next('.usuario-content').hasClass('active')) {
                            cargarSucursalesUsuario(userId);
                        }
                    });

                    // Inicializar drag and drop
                    $('.drag-user').draggable({
                        revert: 'invalid',
                        helper: 'clone',
                        cursor: 'move',
                        opacity: 0.6,
                        zIndex: 100,
                        start: function(event, ui) {
                            $(this).css('cursor', 'grabbing');
                        },
                        stop: function(event, ui) {
                            $(this).css('cursor', 'grab');
                        }
                    });
                }
            });
        }

        function cargarSucursalesUsuario(userId) {
            $.ajax({
                url: `/usersucursal/sucursales-asignadas/${userId}`,
                type: 'GET',
                success: function(data) {
                    $(`#sucursales-usuario-${userId}`).html(data);
                }
            });
        }

        function cargarSucursales() {
            $.ajax({
                url: '{{ route("usersucursal.sucursales") }}',
                type: 'GET',
                success: function(sucursales) {
                    var html = '<div class="row">';
                    if (sucursales.length === 0) {
                        html = '<div class="empty-message">No hay sucursales registradas</div>';
                    } else {
                        sucursales.forEach(function(sucursal) {

                            html += `
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-header bg-primary text-white d-flex justify-content-between">
                                            <strong style="text-transform: uppercase">🏢 ${sucursal.descrip}</strong>
                                            <small style="text-transform: uppercase">  ${sucursal.comercial.descrip}</small>
                                        </div>
                                        <div class="card-body">
                                            <p><small><strong>Dirección:</strong> ${sucursal.direccion}</small></p>
                                            <div class="sucursal-dropzone" data-sucursal-id="${sucursal.id}">
                                                <div id="usuarios-sucursal-${sucursal.id}">
                                                    <div class="text-center"><small>Cargando usuarios...</small></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    }
                    html += '</div>';
                    $('#sucursales-container').html(html);

                    // Configurar drop zones
                    $('.sucursal-dropzone').each(function() {
                        var sucursalId = $(this).data('sucursal-id');
                        cargarUsuariosSucursal(sucursalId);

                        $(this).droppable({
                            accept: '.drag-user',
                            drop: function(event, ui) {
                                var userId = $(ui.draggable).data('user-id');
                                var sucursalId = $(this).data('sucursal-id');
                                asignarSucursal(userId, sucursalId);
                            },
                            over: function() {
                                $(this).addClass('drag-over');
                            },
                            out: function() {
                                $(this).removeClass('drag-over');
                            }
                        });
                    });
                }
            });
        }

        function cargarUsuariosSucursal(sucursalId) {
            $.ajax({
                url: `/usersucursal/usuarios-por-sucursal/${sucursalId}`,
                type: 'GET',
                success: function(data) {
                    $(`#usuarios-sucursal-${sucursalId}`).html(data);

                    // Re-inicializar drag and drop para los nuevos elementos
                    $(`#usuarios-sucursal-${sucursalId} .drag-user`).draggable({
                        revert: 'invalid',
                        helper: 'clone',
                        cursor: 'move',
                        opacity: 0.6,
                        zIndex: 100
                    });
                }
            });
        }

        function asignarSucursal(userId, sucursalId) {
            $.ajax({
                url: '{{ route("usersucursal.asignar") }}',
                type: 'POST',
                data: {
                    user_id: userId,
                    sucursal_id: sucursalId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        actualizarContenido(userId, sucursalId);
                        mostrarNotificacion('success', response.message);
                    } else {
                        mostrarNotificacion('error', response.message);
                    }
                },
                error: function(xhr) {
                    mostrarNotificacion('error', 'Error al asignar la sucursal');
                }
            });
        }

        function quitarSucursal(userId, sucursalId) {
            $.ajax({
                url: '{{ route("usersucursal.quitar") }}',
                type: 'POST',
                data: {
                    user_id: userId,
                    sucursal_id: sucursalId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        actualizarContenido(userId, sucursalId);
                        mostrarNotificacion('success', response.message);
                    } else {
                        mostrarNotificacion('error', response.message);
                    }
                },
                error: function(xhr) {
                    mostrarNotificacion('error', 'Error al quitar la sucursal');
                }
            });
        }

        function actualizarContenido(userId, sucursalId) {
            // Actualizar la lista de sucursales del usuario
            if ($(`#usuario-${userId}`).parent().hasClass('active')) {
                cargarSucursalesUsuario(userId);
            }

            // Actualizar la lista de usuarios de la sucursal
            cargarUsuariosSucursal(sucursalId);
        }

        function mostrarNotificacion(tipo, mensaje) {
            // Puedes implementar toastr o sweetalert aquí

        }
    </script>
@endsection
