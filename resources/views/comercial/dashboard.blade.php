{{-- resources/views/comercial/dashboard.blade.php --}}
@extends('layouts.master')
@section('title')
    Dashboard - {{ $comercial->descrip }}
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5>Bienvenido a {{ $comercial->descrip }}</h5>
                            <p class="text-muted mb-0">Comercial asignado</p>
                        </div>

                        @if(count($sucursales) > 1)
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="mdi mdi-store"></i> Cambiar Sucursal
                                </button>
                                <ul class="dropdown-menu">
                                    @foreach($sucursales as $sucursal)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('comercial.dashboard', ['comercialId' => $sucursal->comercial->id]) }}">
                                                <i class="mdi mdi-store"></i> {{ $sucursal->descrip }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="mdi mdi-information-outline"></i>
                        Has iniciado sesión con el comercial: <strong>{{ $comercial->descrip }}</strong>
                        @if(count($sucursales) > 1)
                            <br>También tienes acceso a {{ count($sucursales) - 1 }} sucursal(es) adicional(es).
                        @endif
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Información del Comercial</h5>
                                    <p class="card-text">
                                        <strong>RIF:</strong> {{ $comercial->rif ?? 'N/A' }}<br>
                                        <strong>Dirección:</strong> {{ $comercial->direccion ?? 'N/A' }}<br>
                                        <strong>Teléfono:</strong> {{ $comercial->telefono ?? 'N/A' }}<br>
                                        <strong>Email:</strong> {{ $comercial->email ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Sucursales Asignadas</h5>
                                    <ul class="list-unstyled">
                                        @foreach($sucursales as $sucursal)
                                            <li>
                                                <i class="mdi mdi-store"></i>
                                                {{ $sucursal->descrip }}
                                                <small class="text-white-50">({{ $sucursal->direccion ?? 'Sin dirección' }})</small>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
