
@extends('layouts.master')
@section('title')
    ASIGNAR PERMISOS
@endsection
@section('css')

@endsection
@section('content')

    <div class="row">
        <div class="container">
            <h2>Permisos de {{ $user->name }}</h2>
            <ul>
                @foreach($user->getAllPermissions() as $permission)
                    <li>{{ $permission->name }}</li>
                @endforeach
            </ul>
            <a href="{{ route('permissions.assign') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>

@endsection
@section('scripts')

    <script src="{{ URL::asset('build/js/app.js') }}"></script>

@endsection
