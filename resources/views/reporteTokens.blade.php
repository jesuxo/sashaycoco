@extends('layouts.master')
@section('title', 'Gestión de Tokens')
@section('css')
    <style>
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 20px;
            color: white;
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-number {
            font-size: 28px;
            font-weight: bold;
        }
        .filter-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .token-code {
            font-family: monospace;
            font-size: 1rem;
            font-weight: bold;
            background: #f0f0f0;
            padding: 4px 8px;
            border-radius: 4px;
        }
        .btn-copiar {
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-copiar:hover {
            background-color: #e9ecef;
        }
        .token-row {
            transition: all 0.2s;
        }
        .token-row:hover {
            background-color: #f8f9fa;
        }
        .bstrong b{
            color: #0072c5 !important;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">
                                <i class="bi bi-key me-2"></i>Gestión de Tokens
                            </h4>
                            <p class="text-white-50 mb-0 small">Creación y seguimiento de tokens de autorización temporal</p>
                        </div>
                        <div class="d-none">
                            <button class="btn btn-light" onclick="exportarTokens()">
                                <i class="bi bi-file-excel me-1"></i> Exportar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">


                    <!-- Filtros -->
                    <div class="filter-section">
                        <form method="POST" action="{{ route('reportetokens') }}" class="row g-3" id="form1" name="form1">
                            @csrf
                            <div class="col-md-4">

                                <input type="text" name="busquedatoken" class="form-control"
                                       placeholder="Buscar Token, usuario, observación..." onchange="$('#form1').submit()"
                                       value="{{ $busquedatoken ?? '' }}">
                            </div>
                            <div class="col-md-3">

                                <select name="fksucursal" class="form-select" onchange="$('#form1').submit()">
                                    <option value="">Todas las sucursales</option>
                                    @foreach($sucursales as $suc)
                                        <option value="{{ $suc->id }}" {{ ($fksucursal ?? '') == $suc->id ? 'selected' : '' }}>
                                            {{ $suc->descrip }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">

                                <select name="estado" class="form-select">
                                    <option value="todos" {{ ($estado ?? 'todos') == 'todos' ? 'selected' : '' }}>Todos</option>
                                    <option value="pendientes" {{ ($estado ?? '') == 'pendientes' ? 'selected' : '' }}>Pendientes</option>
                                    <option value="usados" {{ ($estado ?? '') == 'usados' ? 'selected' : '' }}>Usados</option>
                                </select>
                            </div>
                            <div class="col-md-2">

                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search me-1"></i> Filtrar
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Tabla de tokens -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                            <th width="5%">ID</th>
                            <th width="20%">Token</th>
                            <th width="10%">Estado</th>
                            <th width="10%">Usuario</th>
                            <th width="15%">Sucursal</th>
                            <th width="25%">Observación</th>
                            <th width="10%">Creado</th>
                            <th width="5%">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($tokens as $token)
                                <tr class="token-row">
                                    <td class="fw-bold">#{{ $token->id }}</td>
                                    <td>
                                        @if($token->token)
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="token-code">{{ $token->token }}</span>
                                                <button class="btn btn-sm btn-outline-secondary btn-copiar"
                                                        onclick="copiarToken('{{ $token->token }}')"
                                                        title="Copiar token">
                                                    <i class="mdi mdi-content-copy"></i>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-muted">Pendiente</span>
                                            <button class="btn btn-sm btn-outline-primary ms-2"
                                                    onclick="editarToken({{ $token->id }}, '{{ addslashes($token->obs) }}')">
                                                <i class="bi bi-pencil"></i> Editar
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                            <span class="badge bg-{{ $token->status_class }}">
                                                {{ $token->status_text }}
                                            </span>
                                        @if($token->status == 0 && $token->tiempo_restante != 'Usado')
                                            <br>
                                            <small class="text-muted">{{ $token->tiempo_restante }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($token->codusua)

                                            {{ $token->codusua }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($token->sucursal)

                                            {{ str_replace("SARA","",$token->sucursal->descrip) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted bstrong">{!!   $token->obs  !!}</small>
                                    </td>
                                    <td>
                                        <small>{{ $token->fechaformat }}</small>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-soft-primary" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                @if($token->token)
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:;" onclick="copiarToken('{{ $token->token }}')">
                                                            <i class="bi bi-copy me-1"></i> Copiar
                                                        </a>
                                                    </li>
                                                @endif
                                                @if($token->status == 0)
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:;" onclick="editarToken({{ $token->id }}, '{{ addslashes($token->obs) }}')">
                                                            <i class="bi bi-pencil me-1"></i> Editar
                                                        </a>
                                                    </li>
                                                @endif
                                                @if(auth()->user()->can('menu_token_eliminar'))
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="javascript:;" onclick="eliminarToken({{ $token->id }})">
                                                            <i class="bi bi-trash me-1"></i> Eliminar
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted"></i>
                                        <p class="mt-2 mb-0">No hay tokens registrados</p>
                                        <button class="btn btn-primary btn-sm mt-3 d-none" data-bs-toggle="modal" data-bs-target="#createTokenModal">
                                            <i class="bi bi-plus-circle me-1"></i> Crear primer token
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-3">
                        {{ $tokens->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal crear token -->
    <div class="modal fade" id="createTokenModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle me-2"></i>Crear Nuevo Token
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('tokens.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Token</label>
                            <div class="input-group">
                                <input type="text" name="token" class="form-control"
                                       placeholder="Ej: AUTORIZACION-123" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="generarTokenAleatorioModal()">
                                    <i class="bi bi-shuffle"></i> Generar
                                </button>
                            </div>
                            <small class="text-muted">Solo letras mayúsculas, números, guiones y guiones bajos</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Sucursal</label>
                            <select name="fksucursal" class="form-select" required>
                                <option value="">Seleccione una sucursal...</option>
                                @foreach($sucursales as $suc)
                                    <option value="{{ $suc->id }}">{{ $suc->descrip }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Observación / Motivo</label>
                            <textarea name="obs" class="form-control" rows="3"
                                      placeholder="Ej: Autorización para traslado de mercancía..."></textarea>
                        </div>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-1"></i>
                            Los tokens tienen validez de <strong>7 días</strong> desde su creación.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Crear Token</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal editar token -->
    <div class="modal fade" id="editTokenModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square me-2"></i>Editar Token
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('token.update') }}">
                    @csrf
                    <input type="hidden" name="tokenid" id="edit_token_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Token</label>
                            <input type="text" name="token" id="edit_token_value" class="form-control" required>
                            <small class="text-muted">Solo letras mayúsculas, números, guiones y guiones bajos</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Observación</label>
                            <textarea name="obs" id="edit_token_obs" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-1"></i>
                            El token mantendrá su fecha de creación original.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Actualizar Token</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        // Copiar token al portapapeles
        function copiarToken(token) {
            navigator.clipboard.writeText(token).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: '¡Copiado!',
                    text: 'Token copiado al portapapeles',
                    timer: 1500,
                    showConfirmButton: false
                });
            }).catch(() => {
                alert('Token: ' + token);
            });
        }

        // Generar token aleatorio en el modal
        function generarTokenAleatorioModal() {
            const token = Math.random().toString(36).substring(2, 10).toUpperCase() + '-' + Math.floor(Math.random() * 900 + 100);
            document.querySelector('input[name="token"]').value = token;
        }

        // Editar token
        function editarToken(id, obs) {
            document.getElementById('edit_token_id').value = id;
            document.getElementById('edit_token_obs').value = obs;
            document.getElementById('edit_token_value').value = '';
            $('#editTokenModal').modal('show');
        }

        // Eliminar token
        function eliminarToken(id) {
            Swal.fire({
                title: '¿Eliminar token?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'DELETE',
                        url: '/tokens/' + id,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(data) {
                            if (data.deleted == 1) {
                                Swal.fire('Eliminado', 'Token eliminado correctamente', 'success');
                                location.reload();
                            } else {
                                Swal.fire('Error', 'No se pudo eliminar el token', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Error al eliminar el token', 'error');
                        }
                    });
                }
            });
        }

        // Exportar tokens
        function exportarTokens() {
            const params = new URLSearchParams(window.location.search);
            params.delete('_token');
            window.location.href = '/tokens/export?' + params.toString();
        }

        // Mostrar mensajes flash
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
        @endif

        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            timer: 3000,
            showConfirmButton: false
        });
        @endif
    </script>
@endsection
