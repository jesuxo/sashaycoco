@php
    $moneda = $transf->bs ? 'Bs' : ($transf->dolares ? '$' : 'COP');
    $estadoClass = $transf->status == 0 ? 'bg-warning' : ($transf->status == 1 ? 'bg-success' : 'bg-danger');
    $estadoTexto = $transf->status == 0 ? 'Pendiente' : ($transf->status == 1 ? 'Aprobada' : 'Rechazada');
    $tipoColor = match($transf->tipo) {
        'venta' => 'success',
        'pago' => 'info',
        'ahorro' => 'warning',
        'proveedor' => 'primary',
        'gasto' => 'danger',
        default => 'secondary'
    };


    $texto = "Por favor revisar transferencia
Nro: " . strtoupper($transf->numero) . "
Fecha: " . $transf->fechaformat . "
Monto: " . $transf->currency . " " . number_format($transf->monto,2,',','.') . "
Titular: " . strtoupper($transf->titular) . "
" . ($transf->banco->descrip ?? '') . "
" . $transf->observacion . "

Enlace: " . route('transferencias.validar', $transf->hashid);
    $texto = urlencode($texto);
    $telefono = isset($transf->banco->telefono) ? "https://api.whatsapp.com/send?phone=" . $transf->banco->telefono . "&text=$texto" : '#';
    $enlace = route('transferencias.validar', $transf->hashid);

    $estadoClass = $transf->status == 0 ? 'bg-warning' : ($transf->status == 1 ? 'bg-success' : 'bg-danger');
    $estadoTexto = $transf->status == 0 ? 'Pendiente' : ($transf->status == 1 ? 'Aprobada' : 'Rechazada');

    $monedaClass = $transf->bs ? 'currency-bs' : ($transf->dolares ? 'currency-usd' : 'currency-cop');
    $monedaSimbolo = $transf->currency;
@endphp
<tr id="tr{{ $transf->id }}">
    <td class="p-2 text-center tdline" valign="top">
        <div class="thumbnail-container" onclick="verImagen('{{ $transf->imagen_url ?? '#' }}')">
            @if($transf->imagen_url)
                <div class="no-image" style="color: #0072c5 !important;">
                    <i class="ri-image-line"></i>
                </div>
            @else
                <div class="no-image">
                    <i class="ri-image-line"></i>
                </div>
            @endif
        </div>
    </td>
    <td class="p-2 text-start tdline" style="font-size: 11px;" valign="top">{{ $transf->fechaformat }}</td>
    <td class="p-2 text-start tdline" style="font-size: 11px;" valign="top">{{ $transf->numero }}</td>
    <td class="p-2 text-start tdline" valign="top">
        {{ $transf->titular }}
        @if($transf->observacion)
            <br>
            <span style="font-size: 10px; color: {{ $tipo == 2 ? 'red' : '#0072c5' }};">
                <i class="ri-chat-1-line"></i> {{ Str::limit($transf->observacion, 30) }}
            </span>
        @endif
    </td>
    <td class="p-2 text-start tdline" style="font-size: 11px;" valign="top">{{ $transf->banco->descrip ?? '' }}</td>
    <td class="p-2 text-start tdline" style="font-size: 11px;" valign="top">{{ $transf->sucursal->descrip ?? '' }}</td>

    <td class="p-2 text-end tdline" style="font-size: 11px;" valign="top">{{ number_format($transf->monto, 2, ',', '.') }}</td>
    <td class="p-2 text-center tdline" valign="top">
        <span class="badge {{ $estadoClass }}">{{ $estadoTexto }}</span>
    </td>

    <td class="p-2 text-start tdline" style="font-size: 10px;" valign="top">
        {{ $transf->created_at->format('d/m/Y H:i') }}
    </td>
    <td class="p-2 text-end tdline" valign="top">

        <div class="dropdown">
            <button class="btn btn-soft-primary btn-sm dropdown btn-icon" type="button" data-bs-toggle="dropdown">
                <i class="ri-more-fill align-middle"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end">
                @if(($tipo == 0 || $tipo == ''))
                    @if(isset($telefono))
                        <li>
                            <a class="dropdown-item" target="_blank" href="{{ $telefono }}">
                                <i class="ri-whatsapp-line align-bottom me-2 text-muted"></i>
                                WhatsApp
                            </a>
                        </li>
                    @endif
                    <li>
                        <a class="dropdown-item" target="_blank" href="{{ $enlace }}">
                            <i class="ri-check-line align-bottom me-2 text-muted"></i>
                            Aprobar/Rechazar
                        </a>
                    </li>
                @endif

                @if($tipo == 1 || $tipo == 2)
                    <li>
                        <a onclick="$('#pendrecord').data('id', {{ $transf->id }})"
                           class="dropdown-item" data-bs-toggle="modal" href="#pendienteOrder">
                            <i class="ri-refresh-line align-bottom me-2 text-muted"></i>
                            Pendiente
                        </a>
                    </li>
                @endif

                <li>
                    <a class="dropdown-item" href="javascript:;" onclick="verImagen('{{ $transf->imagen_url ?? '#' }}')">
                        <i class="ri-image-line align-bottom me-2 text-muted"></i>
                        Ver imagen
                    </a>
                </li>

                <li class="dropdown-divider"></li>
                <li>
                    <a onclick="$('#deleterecord').data('id', {{ $transf->id }})"
                       class="dropdown-item text-danger" data-bs-toggle="modal" href="#deleteOrder">
                        <i class="ri-delete-bin-fill align-bottom me-2"></i>
                        Eliminar
                    </a>
                </li>
            </ul>
        </div>
    </td>
</tr>
