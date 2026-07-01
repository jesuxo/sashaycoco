{{-- resources/views/proveedores/pagos-pendientes.blade.php --}}
@extends('layouts.master')

@section('title', 'Pagos Pendientes a Proveedores')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Pagos Pendientes a Proveedores</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Compras</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('proveedores.index') }}">Proveedores</a></li>
                            <li class="breadcrumb-item active">Pagos Pendientes</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('proveedores.pagos-pendientes') }}" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Proveedor</label>
                                <select class="form-select" name="proveedor">
                                    <option value="todos">Todos los proveedores</option>
                                    @foreach($proveedores as $prov)
                                        <option value="{{ $prov->codprov }}" {{ $proveedorId == $prov->codprov ? 'selected' : '' }}>
                                            {{ $prov->descrip }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Fecha Desde</label>
                                <input type="date" class="form-control" name="fecha_inicio" value="{{ $fechaInicio }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Fecha Hasta</label>
                                <input type="date" class="form-control" name="fecha_fin" value="{{ $fechaFin }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="ri-filter-3-line"></i> Filtrar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="text-white">Total Pendiente</h5>
                        <h2 class="text-white">${{ number_format($totales['monto'], 2) }}</h2>
                        <p class="mb-0">{{ $totales['registros'] }} registros pendientes</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Detalle de Pagos Pendientes</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>Fecha Viaje</th>
                                    <th>Viaje</th>
                                    <th>Proveedor</th>
                                    <th>Cliente</th>
                                    <th>Modelo</th>
                                    <th>Cant.</th>
                                    <th>Transporte</th>
                                    <th>Retención</th>
                                    <th>Monto a Pagar</th>
                                    <th>Estado</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($pagos as $pago)
                                    <tr>
                                        <td>{{ $pago->viaje->fecha_inicio->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('viajes.ver', $pago->viaje_id) }}" target="_blank">
                                                {{ $pago->viaje->folio ?? $pago->viaje_id }}
                                            </a>
                                        </td>
                                        <td>{{ $pago->proveedor->descrip ?? 'N/A' }}</td>
                                        <td>{{ $pago->cliente->descrip ?? 'N/A' }}</td>
                                        <td>{{ $pago->modelo_moto }}</td>
                                        <td class="text-center">{{ $pago->cantidad }}</td>
                                        <td class="text-end">${{ number_format($pago->monto_transporte_proveedor, 2) }}</td>
                                        <td class="text-end">${{ number_format($pago->retencion_proveedor, 2) }}</td>
                                        <td class="text-end"><strong>${{ number_format($pago->monto_esperado_cliente, 2) }}</strong></td>
                                        <td>
                                            <span class="badge bg-warning">Pendiente</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No hay pagos pendientes</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            {{ $pagos->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
