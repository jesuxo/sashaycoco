{{-- resources/views/usersucursal/partials/usuarios-por-sucursal.blade.php --}}
@if(count($usuarios) > 0)
    @foreach($usuarios as $usuario)
        <div class="drag-user mb-2" data-user-id="{{ $usuario->id }}">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong  >{{ strtoupper($usuario->first_name) }} {{ strtoupper($usuario->last_name) }}</strong><br>
                </div>
                <a href="javascript:void(0)"
                   onclick="quitarSucursal({{ $usuario->id }}, {{ $sucursalId }})"
                   class="btn-quitar-sucursal">
                    <i class="mdi mdi-close"></i>
                </a>
            </div>
        </div>
    @endforeach
@else
    <div class="empty-message text-center text-muted">
        <small>Arrastra usuarios aquí</small>
    </div>
@endif
