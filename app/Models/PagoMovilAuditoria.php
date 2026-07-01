<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoMovilAuditoria extends Model
{
    protected $table = 'pago_movil_auditoria';

    protected $fillable = [
        'referencia',
        'monto',
        'id_receptor',
        'estado',
        'mensaje',
        'ip_usuario',
        'user_agent',
        'user_id',
        'email_usuario',
        'imagen',
        'imagen_original',
        'consultado_en'
    ];

    protected $casts = [
        'consultado_en' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor para obtener URL completa de la imagen
    public function getImagenUrlAttribute()
    {
        if ($this->imagen) {
            return asset($this->imagen);
        }
        return null;
    }
}
