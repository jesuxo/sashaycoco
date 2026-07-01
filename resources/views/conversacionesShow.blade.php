<style>
    .chat-messages-container {
        scrollbar-width: thin;
        scrollbar-color: #888 #f1f1f1;
    }

    .chat-messages-container::-webkit-scrollbar {
        width: 8px;
    }

    .chat-messages-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .chat-messages-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .chat-messages-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .bg-soft {
        background-color: rgba(0,0,0,0.03) !important;
    }
</style>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Conversación #{{ $conversation->id }}</h4>
                    <div class="page-title-right">
                        <a href="{{ route('chat.index') }}" class="btn btn-secondary btn-sm">
                            <i class="ri-arrow-left-line"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Información de la conversación --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Información de la conversación</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless" style="text-align: left">
                            <tr>
                                <th width="40%">ID Sesión:</th>
                                <td style="font-size: 9px"><code>{{ $conversation->session_id }}</code></td>
                            </tr>
                            <tr>
                                <th>Visitante:</th>
                                <td>{{ $conversation->visitor_name ?? 'Anónimo' }}</td>
                            </tr>
                            <tr>
                                <th>IP:</th>
                                <td>{{ $conversation->ip_address ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>User Agent:</th>
                                <td><small class="text-muted">{{ $conversation->user_agent }}</small></td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td>
                                    <select class="form-select form-select-sm" id="statusSelect" style="max-width: 200px !important;"
                                            onchange="updateStatus({{ $conversation->id }}, this.value)">
                                        <option value="active" {{ $conversation->status == 'active' ? 'selected' : '' }}>
                                            Activa
                                        </option>
                                        <option value="resolved" {{ $conversation->status == 'resolved' ? 'selected' : '' }}>
                                            Resuelta
                                        </option>
                                        <option value="pending" {{ $conversation->status == 'pending' ? 'selected' : '' }}>
                                            Pendiente
                                        </option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>Inicio:</th>
                                <td>{{ $conversation->created_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            @if($conversation->ended_at)
                                <tr>
                                    <th>Fin:</th>
                                    <td>{{ $conversation->ended_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Duración:</th>
                                    <td>{{ $conversation->created_at->diffForHumans($conversation->ended_at, true) }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>Total mensajes:</th>
                                <td>{{ $conversation->messages->count() }}</td>
                            </tr>
                        </table>

                        <hr>

                        <h6 class="mt-3">Acciones rápidas</h6>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm" id="btncopy" onclick="copyConversation()">
                                <i class="ri-file-copy-line"></i> Copiar conversación
                            </button>
                            <button class="btn btn-outline-success btn-sm" onclick="exportConversation()">
                                <i class="ri-download-line"></i> Exportar como TXT
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mensajes de la conversación --}}
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Mensajes</h5>
                    </div>
                    <div class="card-body">
                        <div class="chat-messages-container" style="max-height: 600px; overflow-y: auto;">
                            @foreach($conversation->messages as $message)
                                @php
                                    $isUser = $message->sender == 'user';
                                    $isAssistant = $message->sender == 'assistant';
                                    $isSystem = $message->sender == 'system';
                                @endphp

                                <div class="chat-message mb-3 {{ $isUser ? 'text-end' : '' }}">
                                    <div class="d-flex {{ $isUser ? 'flex-row-reverse' : 'flex-row' }} align-items-start">
                                        <div class="avatar me-2 {{ $isUser ? 'ms-2 me-0' : '' }}">
                                            @if($isAssistant)
                                                <div class="rounded-circle bg-primary text-white p-2"
                                                     style="text-align: center; width: 40px !important;">
                                                    <i class="ri-robot-line"></i>
                                                </div>
                                            @elseif($isUser)
                                                <div class="rounded-circle bg-info text-white p-2"
                                                     style="text-align: center; width: 40px !important;">
                                                    <i class="ri-user-line"></i>
                                                </div>
                                            @else
                                                <div class="rounded-circle bg-warning text-white p-2">
                                                    <i class="ri-information-line"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="message-content {{ $isUser ? 'order-1' : '' }}"
                                             style="max-width: 80%; border-radius: 10px;
                                             {{  ($isAssistant) ? ' border: 1px solid #d9d9d9; ' : ' background-color:#e3f2fd !important' }}
                                             ">

                                            <div class="p-3 rounded-3 ">
                                                <p class="mb-1">{!! nl2br(e($message->message)) !!}</p>
                                                <small class="text-muted d-block text-end">
                                                    {{ $message->created_at->format('H:i:s') }}
                                                </small>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<textarea id="textcopy" style="width: 0; height: 0px; opacity: 0" name="textcopy">Conversación #{{ $conversation->id }}
Fecha: {{ $conversation->created_at->format('d/m/Y H:i:s') }}
Visitante: {{ $conversation->visitor_name ?? 'Anónimo' }}
IP: {{ $conversation->ip_address ?? 'N/A' }}
Estado: {{ $conversation->status }}
========================================
@foreach($conversation->messages as $message)
[{{ $message->created_at->format('H:i:s') }}] {{ strtoupper($message->sender) }}:
{{ preg_replace("/\r\n|\r|\n/", " ", trim($message->message)) }}
----------------------------------------
@endforeach</textarea>



