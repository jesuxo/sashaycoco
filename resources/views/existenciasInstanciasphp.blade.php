@if(isset($instanciaselected) && isset($instanciaselected->descrip))
    <div class="card overflow-hidden">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">{{ $instanciaselected->descrip }}</h5>
            <p class="text-muted mb-0 small">Resumen de inventario por sucursal</p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                    <tr class="table-light">
                        <th>Sucursal</th>
                        <th class="text-end">Unidades</th>
                        <th class="text-end">Costo Total (USD)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $totalUnidades = 0;
                        $totalCosto = 0;
                    @endphp
                    @foreach($sucursales as $sucursal)
                        @php
                            $fk_sucursal = $sucursal->id;

                            $codalte = $instanciaselected->codalte;
                            $len = strlen($codalte);

                            $datacodalte = "AND b.codalte like '$codalte%'";
                            if($justcodinst == 1)
                                $datacodalte = "AND b.codalte  = '$codalte'";


                              $sqlcostoinv = "
                                SELECT
                                    SUM(c.Existen) AS existen,
                                    SUM(a.preciod * c.Existen) AS preciod
                                FROM saprod a
                                INNER JOIN sainsta b ON a.CodInst = b.CodInst AND b.tipoins = 0 AND b.comercial = $comercial $datacodalte
                                INNER JOIN saexis c ON a.codprod = c.codprod AND c.fk_sucursal = $fk_sucursal
                                WHERE a.comercial = $comercial
                                GROUP BY c.fk_sucursal
                            ";
                            $costoinven = \Illuminate\Support\Facades\DB::select($sqlcostoinv);
                        @endphp
                        @if(isset($costoinven[0]) && $costoinven[0]->existen > 0)
                            @php
                                $totalUnidades += $costoinven[0]->existen;
                                $totalCosto += $costoinven[0]->preciod;
                            @endphp
                            <tr>
                                <td class="fw-medium">{{ $sucursal->descrip }}</td>
                                <td class="text-end">{{ number_format($costoinven[0]->existen, 0, ',', '.') }}</td>
                                <td class="text-end">${{ number_format($costoinven[0]->preciod, 2, ',', '.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold">
                    <tr>
                        <td>TOTAL</td>
                        <td class="text-end">{{ number_format($totalUnidades, 0, ',', '.') }}</td>
                        <td class="text-end">${{ number_format($totalCosto, 2, ',', '.') }}</td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Productos en {{ $instanciaselected->descrip }}</h5>
            <span class="badge bg-secondary">Detalle de inventario</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="min-height: 400px;">
                <table class="table table-sm table-hover table-striped align-middle mb-0" id="tablaProductosInstancia">
                    <thead class="table-light">
                    <tr>
                        <th class="text-center align-middle">Código</th>
                        <th class="text-center align-middle">Descripción</th>
                        <th class="text-center align-middle">Existencia</th>
                        <th class="text-center align-middle">Costo <br>Pro</th>
                        <th class="text-center align-middle">Valor <br>Total</th>
                        <th class="text-center align-middle" width="120">Acción</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $productosAgrupados = collect();
                        $datasucu = '';
                            if($fksucursal>0)
                                $datasucu = " and c.fk_sucursal = $fksucursal";

                            // Cuando hay una sucursal seleccionada, consulta directa a la BD
                            $codalte = $instanciaselected->codalte;

                            $datacodalte = "AND b.codalte like '$codalte%'";
                            if($justcodinst == 1)
                                $datacodalte = "AND b.codalte  = '$codalte'";

                            $sqlcostoinv = "
                                SELECT a.id, a.codprod, a.descrip, a.preciod, SUM(c.Existen) AS existen
                                FROM saprod a
                                INNER JOIN sainsta b ON a.CodInst = b.CodInst AND b.tipoins = 0 AND b.comercial = $comercial  $datacodalte
                                INNER JOIN saexis c ON a.codprod = c.codprod AND c.existen > 0 $datasucu
                                WHERE a.comercial = $comercial
                                GROUP BY a.id, a.codprod, a.descrip, a.preciod
                                ORDER BY a.descrip
                            ";
                            $productosDB = \Illuminate\Support\Facades\DB::select($sqlcostoinv);

                            // Convertir objetos stdClass a array para unificar el formato
                            foreach($productosDB as $prod) {
                                $productosAgrupados->push([
                                    'id'      => $prod->id,
                                    'codprod' => $prod->codprod,
                                    'descrip' => $prod->descrip,
                                    'preciod' => $prod->preciod,
                                    'existen' => $prod->existen
                                ]);
                            }

                    @endphp

                    @forelse($productosAgrupados as $producto)
                        <tr>
                            <td class="fw-mono small text-center">{{ $producto['codprod'] }}</td>
                            <td style="font-size: 11px">{{ $producto['descrip'] }}</td>
                            <td class="text-center fw-bold">
                                {{ number_format($producto['existen'], 0, ',', '.') }}
                            </td>
                            <td class="text-end">
                                ${{ number_format($producto['preciod'], 2, ',', '.') }}
                            </td>
                            <td class="text-end">
                                ${{ number_format($producto['preciod'] * $producto['existen'], 2, ',', '.') }}
                            </td>
                            <td class="text-center">

                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-gear"></i> Acciones
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <button type="button"
                                                    class="dropdown-item btn-ver-detalle"
                                                    data-codprod="{{ $producto['codprod'] }}"
                                                    data-producto="{{ $producto['descrip'] }}"
                                                    data-bs-toggle="tooltip"
                                                    title="Ver detalle de existencias por sucursal">
                                                <i class="bi bi-eye me-2"></i> Ver Depositos
                                            </button>
                                        </li>
                                        <li>
                                            <a href="{{ url('/productos/' . $producto['id'] . '/edit') }}"
                                               class="dropdown-item"
                                               target="_blank"
                                               data-bs-toggle="tooltip"
                                               title="Editar producto">
                                                <i class="bi bi-pencil-square me-2"></i> Editar producto
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No hay productos con existencia en esta categoría/sucursal.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para ver detalle de existencias de un producto por sucursal -->
    <div class="modal fade" id="detalleProductoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white">Detalle de Existencias</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detalleProductoBody">
                    <div class="text-center p-3">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Función para obtener detalle de un producto por sucursal
        $(document).on('click', '.btn-ver-detalle', function() {
            let codprod = $(this).data('codprod');
            let nombreProducto = $(this).data('producto');
            let modalBody = $('#detalleProductoBody');

            modalBody.html('<div class="text-center p-3"><div class="spinner-border text-info" role="status"><span class="visually-hidden">Cargando...</span></div></div>');
            $('#detalleProductoModal .modal-title').text(`Existencias de: ${nombreProducto}`);
            $('#detalleProductoModal').modal('show');

            $.ajax({
                type: 'post',
                data: { codprod: codprod },
                url: '/saprod/listprodubiccompany',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success && response.existencias && response.existencias.length > 0) {
                        let html = '<div class="table-responsive"><table class="table table-sm table-striped"><thead><tr><th>Depósito</th><th class="text-center">Existencia</th></tr></thead><tbody>';
                        $.each(response.existencias, function(i, item) {
                            let depositoNombre = item.deposito ? item.deposito.descrip : item.codubic;
                            let sucursalNombre = item.sucursal ? item.sucursal.descrip : 'Sin sucursal';
                            html += `<tr>
                                        <td>${depositoNombre}  </td>
                                        <td class="text-center fw-bold">${parseFloat(item.existen).toLocaleString('es-VE')}</td>
                                    </tr>`;
                        });
                        html += '</tbody></table></div>';
                        modalBody.html(html);
                    } else {
                        modalBody.html('<div class="alert alert-warning text-center mb-0">No se encontraron existencias para este producto.</div>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    modalBody.html('<div class="alert alert-danger text-center mb-0">Error al cargar los datos. Por favor, intente nuevamente.</div>');
                }
            });
        });
    </script>
@endif
