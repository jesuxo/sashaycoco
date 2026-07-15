<div class="table-responsive table-card" style="padding-bottom:20px; margin-top: 0px; ">
    <table class="table table-bordered table-centered align-middle table-nowrap mb-0" style="font-size: 13px;">
        <thead class="text-muted table-light">
        <tr>
            <th width="5%" class="text-center">Código</th>
            <th width="40%" class="text-center">Producto</th>
            <th width="8%" class="text-center">Costo </th>
            <th width="8%" class="text-center">Precio3</th>
            <th width="25%" class="text-center">Existencias por Sucursal</th>
            <th width="5%" class="text-center">Total</th>
            <th width="5%" class="text-center">Ver Oper</th>
        </tr>
        </thead>
        <tbody>
        @php $pros = 0; @endphp
        @foreach($productos as $producto)
            @php
                $pros++;
                $totalExistencias = 0;
                $existenciasHtml = [];
            @endphp

            @if(isset($producto->existencias_por_sucursal) and count($producto->existencias_por_sucursal) > 0)
                @foreach($producto->existencias_por_sucursal as $array)
                    @php
                        $existencia = $array->existen ?? 0;
                        $totalExistencias += $existencia;

                        if($existencia > 0) {
                            $color = 'primary'; //$existencia < 10 ? 'danger' : ($existencia < 30 ? 'warning' : 'success');
                            $existenciasHtml[] = "<span class='badge bg-{$color} bg-opacity-10 text-{$color}' title='{$array->deposito->descrip}'>" .
                                                str_replace('SARA','',$array->deposito->descrip) . ": " . number_format($existencia, 0) .
                                                "</span>";
                        }
                    @endphp
                @endforeach
            @endif

            <tr>
                <td class="align-middle">
                    <a href="{{ route('productos.edit', $producto->id) }}" class="fw-medium link-primary">
                        {{ $producto->codprod }}
                    </a>
                </td>
                <td class="align-middle">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('productos.edit', $producto->id) }}" class="fw-medium link-primary">
                            {{ $producto->descrip }}
                        </a>
                        <a href="{{ route('productos.edit', $producto->id) }}" class="ms-2">
                            <i class="bi-pencil-square text-primary"></i>
                        </a>
                    </div>
                </td>
                <td class="text-end align-middle">
                    ${{ number_format($producto->preciod, 2, ',', '.') }}
                </td>
                <td class="text-end align-middle">
                    ${{ number_format($producto->costod3, 2, ',', '.') }}
                </td>

                <!-- Existencias resumidas -->
                <td class="align-middle">
                    <div style="width: 100%; max-height: 60px; overflow: auto;">
                    @if(!empty($existenciasHtml))
                        <div style=" display: flex; flex-wrap: wrap; gap: 3px;">
                            @foreach($existenciasHtml as $html)
                                {!! $html !!}
                            @endforeach
                        </div>
                    @else
                        <span class="text-muted">Sin stock</span>
                    @endif
                    </div>
                </td>

                <!-- Total
                 <span class="{{ $totalExistencias == 0 ? 'text-secondary' : ($totalExistencias < 10 ? 'text-danger' : ($totalExistencias < 30 ? 'text-warning' : 'text-success')) }}">
                            {{ number_format($totalExistencias, 0) }}
                </span>
-->
                <td class="text-center align-middle fw-bold">
                        <span class="text-primary">
                            {{ number_format($totalExistencias, 0) }}
                        </span>
                </td>

                <!-- Ver Operaciones -->
                <td class="text-center align-middle">
                    <a href="/operaciones/{{ $producto->codprod }}" class="btn btn-sm btn-outline-primary" title="Ver operaciones">
                        <i class="bi-bar-chart"></i>
                    </a>
                </td>
            </tr>
        @endforeach

        @if($pros == 0)
            <tr>
                <td colspan="2" class="text-center py-4">
                    <div class="text-muted">
                        <i class="bi bi-box-arrow-down fs-1 d-block mb-2"></i>
                        <a href="{{ route('productos.create') }}" class="btn btn-primary btn-sm mt-2">
                            <i class="bi-plus-circle me-1"></i> Crear nuevo producto
                        </a>
                    </div>
                </td>
                <td colspan="5" class="text-center py-4">
                    <div class="text-muted">
                        <i class="bi-search fs-1 d-block mb-2"></i>
                        <h6>No se encontraron productos</h6>
                        <p class="mb-2">Intenta con otros términos de búsqueda</p>

                    </div>
                </td>

            </tr>
        @endif
        </tbody>
    </table>
</div>

<style>
    .badge {
        font-size: 11px;
        padding: 4px 6px;
        border-radius: 12px;
        white-space: nowrap;
    }

    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }


</style>
