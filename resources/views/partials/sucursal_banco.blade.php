<div>
    <label for="bancosucursal" class="form-label">Banco</label>
</div>

<select class="form-control" name="bancosucursal"   required id="bancosucursal">
    <option value="">Seleccione</option>
    @if(isset($bancos))
        @foreach($bancos as $banco)
            <option value="{{$banco->id}}">{{$banco->descrip}}</option>
        @endforeach
    @endif
</select>
<div class="invalid-feedback">
    Seleccione el banco destino
</div>
