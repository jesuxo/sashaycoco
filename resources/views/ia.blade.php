@extends('layouts.master')

@section('title')
    IA
@endsection

@section('css')


@endsection
@section('content')

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Base de Conocimiento para IA</h3>
                        <button type="button" class="btn btn-primary float-right" data-bs-toggle="modal" data-bs-target="#knowledgeModal">
                            <i class="fas fa-plus"></i> Agregar Información
                        </button>

                    </div>

                    <div class="card-body">
                        {{-- Buscador --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <input type="text" id="searchInput" class="form-control" placeholder="Buscar información...">
                            </div>
                        </div>

                        {{-- Tabla de información --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Categoría</th>
                                    <th>Contenido</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody id="knowledgeTable">
                                @foreach($knowledgeItems as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->title }}</td>
                                        <td>{{ $item->category ?? 'Sin categoría' }}</td>
                                        <td>{{ Str::limit($item->text, 50) }}</td>
                                        <td>
                                        <span class="badge {{ $item->active ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                            {{ $item->active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="editKnowledge({{ $item }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteKnowledge({{ $item->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{ $knowledgeItems->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Modal para crear/editar --}}
    <div class="modal fade" id="knowledgeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="knowledgeForm" method="POST">
                    @csrf
                    <input type="hidden" id="method" name="_method" value="POST">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Agregar Información</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="title">Título *</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>

                        <div class="form-group">
                            <label for="category">Categoría</label>
                            <input type="text" class="form-control" id="category" name="category">
                        </div>

                        <div class="form-group">
                            <label for="text">Contenido *</label>
                            <textarea class="form-control" id="text" name="text" rows="5" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="tags">Tags (separados por coma)</label>
                            <input type="text" class="form-control" id="tags" name="tags" placeholder="ej: producto, precio, soporte">
                        </div>

                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="active" name="active" checked>
                            <label class="form-check-label" for="active">Activo</label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')

    <script src="{{ URL::asset('build/js/app.js?'.rand(0,5555555)) }}"></script>
    <script>
        $(document).ready(function() {
            // Búsqueda en tiempo real
            $('#searchInput').on('keyup', function() {
                let query = $(this).val();

                if(query.length > 2) {
                    $.get('{{ route("iaknowledge.search") }}', {query: query}, function(data) {
                        let html = '';
                        data.forEach(item => {
                            html += `<tr>
                        <td>${item.id}</td>
                        <td>${item.title}</td>
                        <td>${item.category || 'Sin categoría'}</td>
                        <td>${item.text.substring(0, 50)}...</td>
                        <td><span class="badge ${item.active ? ' badge-soft-success ' : 'badge-soft-danger'}">${item.active ? 'Activo' : 'Inactivo'}</span></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick='editKnowledge(${JSON.stringify(item)})'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteKnowledge(${item.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                        });
                        $('#knowledgeTable').html(html);
                    });
                }
            });

            // Guardar información
            $('#knowledgeForm').on('submit', function(e) {
                e.preventDefault();

                let url = '{{ route("iaknowledge.store") }}';
                let method = $('#method').val();

                if(method === 'PUT') {
                    let id = $('#knowledgeForm').data('id');
                    url = `{{ url("iaknowledge") }}/${id}`;
                }

                let formData = {
                    title: $('#title').val(),
                    category: $('#category').val(),
                    text: $('#text').val(),
                    tags: $('#tags').val() ? $('#tags').val().split(',').map(tag => tag.trim()) : [],
                    active: $('#active').is(':checked')
                };

                $.ajax({
                    url: url,
                    method: method === 'PUT' ? 'PUT' : 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#knowledgeModal').modal('hide');
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Error al guardar la información');
                    }
                });
            });
        });

        // Función para editar
        function editKnowledge(item) {
            $('#modalTitle').text('Editar Información');
            $('#method').val('PUT');
            $('#knowledgeForm').data('id', item.id);

            $('#title').val(item.title);
            $('#category').val(item.category);
            $('#text').val(item.text);
            $('#tags').val(item.tags ? item.tags.join(', ') : '');
            $('#active').prop('checked', item.active);

            $('#knowledgeModal').modal('show');
        }

        // Función para eliminar
        function deleteKnowledge(id) {
            if(confirm('¿Estás seguro de eliminar esta información?')) {
                $.ajax({
                    url: `{{ url("iaknowledge") }}/${id}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        location.reload();
                    }
                });
            }
        }

        $('input[type=text]').attr("autocomplete", "off");
    </script>

@endsection
