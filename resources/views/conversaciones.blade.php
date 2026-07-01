@extends('layouts.master')

@section('title')
    Conversaciones
@endsection

@section('css')
<style>
    .linetrprod {
        background: #faebd799;
    }
</style>

@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Conversaciones del Chat</h4>
                    <div class="page-title-right">
                        <a href="{{ route('chat.export') }}" class="btn btn-success btn-sm">
                            <i class="ri-download-line"></i> Exportar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Estadísticas rápidas --}}
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Total Conversaciones</p>
                                <h4 class="mb-2">{{ $stats['total'] }}</h4>
                            </div>
                            <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="ri-message-2-line font-size-24"></i>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Activas</p>
                                <h4 class="mb-2">{{ $stats['active'] }}</h4>
                            </div>
                            <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="ri-chat-3-line font-size-24"></i>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Resueltas</p>
                                <h4 class="mb-2">{{ $stats['resolved'] }}</h4>
                            </div>
                            <div class="avatar-sm">
                            <span class="avatar-title bg-light text-warning rounded-3">
                                <i class="ri-check-double-line font-size-24"></i>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Mensajes Hoy</p>
                                <h4 class="mb-2">{{ $stats['messages_today'] }}</h4>
                                <p class="text-muted mb-0">Total: {{ $stats['total_messages'] }}</p>
                            </div>
                            <div class="avatar-sm">
                            <span class="avatar-title bg-light text-info rounded-3">
                                <i class="ri-chat-1-line font-size-24"></i>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('chat.index') }}" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Buscar</label>
                                <input type="text" class="form-control" name="search"
                                       placeholder="Session ID, IP, mensaje..."
                                       value="{{ request('search') }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Estado</label>
                                <select class="form-select" name="status">
                                    <option value="">Todos</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activas</option>
                                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resueltas</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendientes</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Fecha desde</label>
                                <input type="date" class="form-control" name="date_from"
                                       value="{{ request('date_from') }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Fecha hasta</label>
                                <input type="date" class="form-control" name="date_to"
                                       value="{{ request('date_to') }}">
                            </div>

                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="ri-filter-2-line"></i> Filtrar
                                </button>
                                <a href="{{ route('chat.index') }}" class="btn btn-secondary">
                                    <i class="ri-refresh-line"></i> Limpiar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Listado de conversaciones --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Visitante</th>
                                    <th>Session ID</th>
                                    <th>IP</th>
                                    <th>Mensajes</th>
                                    <th>Estado</th>
                                    <th>Inicio</th>
                                    <th>Último mensaje</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($conversations as $conv)
                                    <tr id="linetrprod{{$conv->id}}" class="linetrprodtr">
                                        <td>#{{ $conv->id }}</td>
                                        <td>
                                            <strong>{{ $conv->visitor_name ?? 'Anónimo' }}</strong>
                                            @if($conv->user_id)
                                                <br><small class="text-muted">(Registrado)</small>
                                            @endif
                                        </td>
                                        <td>
                                            <code>{{ substr($conv->session_id, 0, 8) }}...</code>
                                        </td>
                                        <td>{{ $conv->ip_address ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $conv->messages_count }}</span>
                                        </td>
                                        <td>
                                            @if($conv->status == 'active')
                                                <span class="badge bg-success">Activa</span>
                                            @elseif($conv->status == 'resolved')
                                                <span class="badge bg-secondary">Resuelta</span>
                                            @else
                                                <span class="badge bg-warning">Pendiente</span>
                                            @endif
                                        </td>
                                        <td>{{ $conv->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($conv->messages->last())
                                                <small>{{ $conv->messages->last()->created_at->format('H:i') }}</small>
                                            @else
                                                <small>-</small>
                                            @endif
                                        </td>
                                        <td>
                                            <a   data-id="{{$conv->id}}" data-bs-toggle="modal" href="#openConversation"  onclick="$('.linetrprodtr').removeClass('linetrprod'); $('#linetrprod{{$conv->id}}').addClass('linetrprod')"
                                               class="btn btn-sm btn-info openConversation" title="Ver detalles">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            <a  data-id="{{$conv->id}}" onclick='$(this).html("<i class=ri-loader-2-fill></i>")'

                                                    class="btn btn-sm btn-danger deleterecord" title="Eliminar">
                                                <i class="ri-delete-bin-line"></i>

                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="ri-inbox-line fs-1 text-muted"></i>
                                            <p class="mt-2">No hay conversaciones disponibles</p>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $conversations->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade flip" id="openConversation" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-body p-5 " id="ConversationShowContent">

                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')

    <script src="{{ URL::asset('build/js/app.js?'.rand(0,5555555)) }}"></script>
    <script>


        $('.openConversation').unbind('click').bind('click',function () {
            var id = $(this).data('id');
            $('#ConversationShowContent').html('');

            $.ajax({
                url: `/chat/conversations/${id}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('#ConversationShowContent').html(data)
                },
                error: function() {

                }
            });
        });


        $('.deleterecord').unbind('click').bind('click',function () {
            var id = $(this).data('id');

            $.ajax({
                url: `/chat/conversations/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        window.location.href="/chat/conversations";
                    }
                },
                error: function() {

                }
            });
        });

        $('input[type=text]').attr("autocomplete", "off");


        function updateStatus(id, status) {
            $.ajax({
                url: `/chat/conversations/${id}/status`,
                type: 'PUT',
                data: { status: status },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {

                    }
                },
                error: function() {

                }
            });
        }

        function copyConversation() {

            const input = document.getElementById('textcopy');
            input.select();
            input.setSelectionRange(0, 99999);

            try {
                const copied = document.execCommand('copy');
                if (copied) {
                    $("#btncopy").html('Copiado <i class="bi bi-check-circle"></i>');
                } else {

                }
            } catch (err) {

            }

            window.getSelection().removeAllRanges();
        }


        function exportConversation() {
            let text =  document.getElementById('textcopy').value;

            const blob = new Blob([text], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'conversacion_{{ now()->format('Y-m-d') }}.txt';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }

    </script>

@endsection
