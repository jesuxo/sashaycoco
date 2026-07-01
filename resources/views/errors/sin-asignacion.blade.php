{{-- resources/views/errors/sin-asignacion.blade.php --}}
@extends('layouts.master')
@section('title', 'Sin Asignación')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">⚠️ Usuario sin asignación de sucursales</h5>
                    </div>
                    <div class="card-body text-center">
                        <i class="mdi mdi-account-alert" style="font-size: 64px; color: #ffc107;"></i>
                        <h4 class="mt-3">No tienes sucursales asignadas</h4>
                        <p class="text-muted">Por favor, contacta al administrador del sistema para que te asigne las sucursales correspondientes.</p>

                        @if(session('error_sucursales'))
                            <div class="alert alert-info mt-3">
                                {{ session('error_sucursales') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('logout') }}" class="mt-4">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-logout"></i> Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
