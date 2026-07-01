
@extends('layouts.master')
@section('title')
    ASIGNAR PERMISOS
@endsection
@section('css')

@endsection
@section('content')

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="text-white">Crear Nuevo Permiso</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('permissions.create') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre del Permiso:</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   placeholder="Ej: manage_posts" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Crear</button>
                    </form>
                </div>
            </div>
        </div>
        <hr>
        <h2>Asignar Permisos</h2>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('permissions.assign') }}">
            @csrf

            @php
                $vectorpermisos = [];
                if(isset($user) and isset($user->id)){
                    foreach($user->getAllPermissions() as $permission){
                       $vectorpermisos[$permission->name] = 1;
                    }
                }
            @endphp

            <div class="mb-3">
                <label for="user_id" class="form-label">Usuario:</label>
                <select class="form-select" id="user_id" name="user_id" required>
                    <option {{(!isset($id))? 'selected': ''}} value="">Seleccionar Usuario</option>
                    @foreach($users as $user)
                        <option {{(isset($id) and $id == $user->id)? 'selected': ''}} onclick="window.location.href='/verpermisos/{{$user->id}}'" value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }}) {{ $user->id }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Permisos:</label>
                @foreach($permissions as $permission)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               name="permissions[]" {{(isset($vectorpermisos[$permission->name]) and $vectorpermisos[$permission->name] == 1)? 'checked': ''}}
                               value="{{ $permission->name }}"
                               id="perm_{{ $permission->id }}">
                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                            {{ $permission->name }}
                        </label>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary">Asignar Permisos</button>
        </form>
        <input type="text" value="" id="input" size="1" style="width: 0px; height: 0px; opacity: 0" />
        @if(isset($id) and $id >0)
            <script> $('#input').select(); </script>
        @endif
    </div>

@endsection
@section('scripts')

    <script src="{{ URL::asset('build/js/app.js') }}"></script>

@endsection
