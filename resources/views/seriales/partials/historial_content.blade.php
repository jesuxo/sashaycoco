@if($producto)
    <div class="alert alert-info mb-3">
        <div class="row">
            <div class="col-md-6">
                <strong><i class="bi bi-box"></i> Producto:</strong> {{ $producto->descrip }}
            </div>
            <div class="col-md-3">
                <strong><i class="bi bi-upc-scan"></i> Código:</strong> {{ $producto->codprod }}
            </div>
            <div class="col-md-3">
                <strong><i class="bi bi-upc-scan"></i> Serial:</strong> {{ $serial }}
            </div>
        </div>
    </div>
@endif

@if($historial && $historial->count() > 0)
    <div class="timeline">
        @foreach($historial as $movimiento)
            <div class="timeline-item">
                <div class="timeline-badge">
                    @if($movimiento->tipo_movimiento == 'COMPRA')
                        <i class="bi bi-cart text-success"></i>
                    @elseif($movimiento->tipo_movimiento == 'VENTA')
                        <i class="bi bi-receipt text-primary"></i>
                    @else
                        <i class="bi bi-arrow-repeat text-warning"></i>
                    @endif
                </div>

                <div class="timeline-content">
                    <div class="row">
                        <div class="col-md-2">
                            <span class="badge bg-{{ $movimiento->badge_color }}">
                                {{ $movimiento->tipo_movimiento }}
                            </span>
                        </div>
                        <div class="col-md-2">
                            <strong>Fecha:</strong> {{ $movimiento->fecha }}
                        </div>
                        <div class="col-md-2">
                            <strong>Tipo:</strong> {{ $movimiento->tipo_descripcion }}
                        </div>
                        <div class="col-md-2">
                            <strong>Documento:</strong>
                            @if(in_array($movimiento->tipo, ['A', 'B', 'Z', 'W']))
                                <a href="/doc/{{ $movimiento->tipo }}/{{ $movimiento->numerod }}/{{ $movimiento->fk_sucursal }}"
                                   target="_blank">
                                    {{ $movimiento->numerod }}
                                </a>
                            @else
                                {{ $movimiento->numerod }}
                            @endif
                        </div>
                        <div class="col-md-4">
                            <strong>Sucursal:</strong> {{ $movimiento->sucursal_nombre }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-clock" style="font-size: 3rem; color: #ccc;"></i>
        <p class="mt-3">No hay movimientos registrados para este serial</p>
    </div>
@endif
