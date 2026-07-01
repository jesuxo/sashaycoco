<?php

namespace App\Models;

use App\Http\Traits\Hashidable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class Cwtransferencia extends Model
{
    use HasFactory;
    use Hashidable;

    protected $table    = 'cwtransferencia';
    protected $fillable = [
        'fecha', 'monto', 'observacion', 'numero', 'status',
        'bs', 'pesos', 'dolares', 'fkbanco', 'fksucursal',
        'imagen', 'imagen_original', 'tipo', 'categoria',
        'comentario_validacion', 'fecha_validacion', 'usuario_valida',
        'proveedor_id', 'ahorro_id', 'referencia'
    ];

    protected $casts = [
        'fecha_validacion' => 'datetime',
    ];

    public function banco(){
        return $this->belongsTo(Cwbancos::class, 'fkbanco', 'id');
    }

    public function sucursal(){
        return $this->belongsTo(Sasucursal::class, 'fksucursal', 'id');
    }

    public function usuarioValidador(){
        return $this->belongsTo(User::class, 'usuario_valida', 'id');
    }

    protected $appends = ['hashid', 'fechaformat', 'currency', 'imagen_url', 'tipo_texto'];

    public function getRouteKeyName()
    {
        return 'hashid';
    }

    public function getHashidAttribute()
    {
        return Hashids::connection(Cwtransferencia::class)->encode($this->id);
    }

    public function getFechaformatAttribute(){
        $date = $this->fecha;
        if(isset($date)){
            list($y,$m,$d) = explode('-',$date);
            return "$d/$m/$y";
        }
    }

    public function getCurrencyAttribute(){
        if($this->bs)
            return 'Bs ';
        if($this->dolares)
            return '$ ';
        if($this->pesos)
            return 'COP ';
    }

    public function getImagenUrlAttribute(){
        if($this->imagen){
            return asset($this->imagen);
        }
        return null;
    }

    public function getTipoTextoAttribute(){
        $tipos = [
            'venta' => '💰 Venta/Cobranza',
            'pago' => '💸 Pago General',
            'ahorro' => '🏦 Ahorro',
            'proveedor' => '📦 Pago Proveedor',
            'gasto' => '🧾 Gasto',
            'otro' => '📌 Otro'
        ];
        return $tipos[$this->tipo] ?? $this->tipo;
    }
}
