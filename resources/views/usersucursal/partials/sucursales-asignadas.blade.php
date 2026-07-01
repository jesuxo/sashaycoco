{{-- resources/views/usersucursal/partials/sucursales-asignadas.blade.php --}}
@if(count($sucursales) > 0)
    @foreach($sucursales as $sucursal)
        <div class="sucursal-item">
            <div>
                <strong>{{ strtoupper($sucursal->descrip) }}</strong><br>
                <small class="text-muted">{{ $sucursal->direccion }}</small>
            </div>
            <a href="javascript:void(0)"
               onclick="quitarSucursal({{ $userId }}, {{ $sucursal->id }})"
               class="btn-quitar-sucursal"
               title="Quitar sucursal">
                <i class="mdi mdi-close-circle"></i>
            </a>
        </div>
    @endforeach
@else
    <div class="empty-message text-center text-muted">
        <small><i class="mdi mdi-information-outline"></i> No hay sucursales asignadas</small>
    </div>
@endif
