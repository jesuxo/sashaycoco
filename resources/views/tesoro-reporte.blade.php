{{-- resources/views/tesoro-reporte.blade.php --}}
@extends('layouts.master')
@section('title', 'Reporte de Auditoría - Pago Móvil')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-clipboard-data me-2"></i>
                        Reporte de Auditoría - Consultas Pago Móvil
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Estadísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Consultas</h6>
                                    <h3 class="mb-0">{{ $estadisticas['total_consultas'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Aprobadas</h6>
                                    <h3 class="mb-0">{{ $estadisticas['aprobadas'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Rechazadas</h6>
                                    <h3 class="mb-0">{{ $estadisticas['rechazadas'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de consultas -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Referencia</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th>Usuario</th>
                                <th>IP</th>
                                <th>Fecha</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($consultas as $consulta)
                                <tr>
                                    <td>{{ $consulta->id }}</td>
                                    <td>{{ $consulta->referencia }}</td>
                                    <td>Bs. {{ number_format($consulta->monto, 2) }}</td>
                                    <td>
                                    <span class="badge bg-{{ $consulta->estado == 'OK' ? 'success' : ($consulta->estado == 'Error' ? 'danger' : 'warning') }}">
                                        {{ $consulta->estado }}
                                    </span>
                                    </td>
                                    <td>{{ $consulta->user->name ?? $consulta->email_usuario }}</td>
                                    <td>{{ $consulta->ip_usuario }}</td>
                                    <td>{{ $consulta->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $consultas->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
