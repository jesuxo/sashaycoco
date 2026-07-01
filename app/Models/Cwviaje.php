<?php
// app/Models/Viaje.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Cwviaje extends Model
{
    //use SoftDeletes;

    protected $table = 'cwviajes';

    protected $fillable = [
        'folio', 'camion_id', 'chofer_id', 'fecha_inicio', 'fecha_fin', 'anticipo_chofer',
        'origen', 'destino', 'distancia_km', 'estado', 'notas'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'distancia_km' => 'decimal:2',
    ];

    public function getDiferenciaViaticosAttribute()
    {
        $totalGastos = $this->gastos->sum('monto');
        return ($this->anticipo_chofer ?? 0) - $totalGastos;
    }

    public function getTotalViaticosAttribute()
    {
        return $this->gastos->where('es_viatico', true)->sum('monto');
    }

    // Relaciones existentes
    public function camion(){
        return $this->belongsTo(Cwcamion::class, 'camion_id', 'id');
    }

    public function chofer(){
        return $this->belongsTo(Cwchofer::class, 'chofer_id', 'id');
    }

    public function motosTransportadas(): HasMany
    {
        return $this->hasMany(Cwviajemoto::class,'viaje_id','id');
    }

    // NUEVA: Relación polimórfica para gastos
    public function gastos(): MorphMany
    {
        return $this->morphMany(Cwgasto::class, 'gastable');
    }

    // --- MÉTODOS PARA CÁLCULOS DE RENTABILIDAD (ACTUALIZADOS) ---

    /**
     * Calcula el ingreso total del viaje basado en las motos transportadas.
     */
    public function getIngresoTotalAttribute(): float
    {
        return $this->motosTransportadas->sum(function ($moto) {
            return $moto->cantidad * $moto->precio_por_moto;
        });
    }

    /**
     * Calcula el gasto total del viaje sumando todos los gastos asociados.
     */
    public function getGastoTotalAttribute(): float
    {
        return $this->gastos->sum('monto');
    }

    /**
     * Calcula la ganancia neta del viaje.
     */
    public function getGananciaNetaAttribute(): float
    {
        return $this->ingreso_total - $this->gasto_total;
    }

    /**
     * Calcula el margen de ganancia.
     */
    public function getMargenGananciaAttribute(): ?float
    {
        if ($this->ingreso_total > 0) {
            return round(($this->ganancia_neta / $this->ingreso_total) * 100, 2);
        }
        return null;
    }

    /**
     * Obtiene los gastos agrupados por tipo para este viaje.
     */
    public function getGastosPorTipoAttribute()
    {
        return $this->gastos->groupBy('tipoGasto.nombre')->map(function ($gastos) {
            return [
                'total' => $gastos->sum('monto'),
                'cantidad' => $gastos->count(),
                'items' => $gastos
            ];
        });
    }

    public function etapas(): HasMany
    {
        return $this->hasMany(CwetapaViaje::class, 'viaje_id')->orderBy('orden');
    }

    public function seguimientos(): HasMany
    {
        return $this->hasMany(CwseguimientoViaje::class, 'viaje_id')->orderBy('fecha_hora', 'desc');
    }

    public function ultimoSeguimiento()
    {
        return $this->hasOne(CwseguimientoViaje::class, 'viaje_id')->latest('fecha_hora');
    }

    public function getEtapaActualAttribute()
    {
        return $this->etapas()->where('estado', 'en_curso')->first();
    }

    /**
     * Métodos para seguimiento del viaje
     */
    public function getProgresoAttribute(): int
    {
        $totalEtapas = $this->etapas()->count();
        if ($totalEtapas === 0) {
            return 0;
        }

        $etapasCompletadas = $this->etapas()->where('estado', 'completado')->count();
        return round(($etapasCompletadas / $totalEtapas) * 100);
    }



    public function getSiguienteEtapaAttribute(): ?CwetapaViaje
    {
        $etapaActual = $this->etapa_actual;

        if ($etapaActual) {
            return $this->etapas()
                ->where('orden', '>', $etapaActual->orden)
                ->where('estado', 'pendiente')
                ->orderBy('orden')
                ->first();
        }

        // Si no hay etapa en curso, devolver la primera pendiente
        return $this->etapas()
            ->where('estado', 'pendiente')
            ->orderBy('orden')
            ->first();
    }

    public function getKilometrajeTotalAttribute(): float
    {
        return $this->etapas()->sum('kilometraje_real') ?? 0;
    }

    public function getKilometrajeRestanteAttribute(): float
    {
        $totalEstimado = $this->etapas()->sum('kilometraje_estimado');
        return max(0, $totalEstimado - $this->kilometraje_total);
    }

    public function getTiempoTranscurridoAttribute(): ?string
    {
        if (!$this->fecha_inicio) {
            return null;
        }

        $inicio = $this->fecha_inicio->startOfDay();
        $ahora = now();

        if ($this->estado === 'completado' && $this->fecha_fin) {
            $fin = $this->fecha_fin->endOfDay();
            return $inicio->diffForHumans($fin, true);
        }

        return $inicio->diffForHumans($ahora, true);
    }

    public function getEstadoProcesoAttribute(): array
    {
        $etapas = $this->etapas;
        $total = $etapas->count();
        $completadas = $etapas->where('estado', 'completado')->count();
        $enCurso = $etapas->where('estado', 'en_curso')->count();
        $pendientes = $etapas->where('estado', 'pendiente')->count();

        return [
            'total' => $total,
            'completadas' => $completadas,
            'en_curso' => $enCurso,
            'pendientes' => $pendientes,
            'progreso' => $this->progreso,
        ];
    }

    public function getResumenRutaAttribute(): array
    {
        $origen = $this->etapas()->where('orden', 1)->first();
        $destino = $this->etapas()->where('orden', $this->etapas()->count())->first();

        return [
            'origen' => $origen ? $origen->ubicacion : $this->origen,
            'destino' => $destino ? $destino->ubicacion : $this->destino,
            'paradas' => $this->etapas()->count() - 2, // Restamos origen y destino
        ];
    }

    /**
     * Métodos para gestionar etapas
     */
    public function iniciarEtapa(int $etapaId): bool
    {
        $etapa = $this->etapas()->findOrFail($etapaId);

        // Verificar que todas las etapas anteriores estén completadas
        $etapasPrevias = $this->etapas()
            ->where('orden', '<', $etapa->orden)
            ->where('estado', '!=', 'completado')
            ->count();

        if ($etapasPrevias > 0) {
            return false;
        }

        $etapa->update([
            'estado' => 'en_curso',
            'fecha_real_inicio' => now(),
        ]);

        return true;
    }

    public function completarEtapa(int $etapaId, array $data = []): bool
    {
        $etapa = $this->etapas()->findOrFail($etapaId);

        $etapa->update(array_merge([
            'estado' => 'completado',
            'fecha_real_fin' => now(),
        ], $data));

        // Si es la última etapa, completar el viaje
        if ($etapa->orden === $this->etapas()->count()) {
            $this->update([
                'estado' => 'completado',
                'fecha_fin' => now(),
            ]);
        }

        return true;
    }

    /**
     * Eventos de modelo para manejar eliminación en cascada
     */
    protected static function booted()
    {
        static::deleting(function ($viaje) {
            // Obtener todas las motos del viaje para eliminar su historial
            $motoIds = $viaje->motosTransportadas()->pluck('id')->toArray();

            // Eliminar historial de estas motos (si existe)
            if (!empty($motoIds)) {
                \App\Models\Cxchistorial::whereIn('viaje_moto_id', $motoIds)->delete();
            }

            // Eliminar gastos
            foreach ($viaje->gastos as $gasto) {
                $gasto->delete();
            }

            // Eliminar etapas
            foreach ($viaje->etapas as $etapa) {
                $etapa->delete();
            }

            // Eliminar seguimientos
            foreach ($viaje->seguimientos as $seguimiento) {
                $seguimiento->delete();
            }

            // NOTA: Las motos no se eliminan porque son necesarias para reportes históricos
        });
    }

    public function puntosSeguimiento()
    {
        return $this->hasMany(CwseguimientoViaje::class, 'viaje_id')->orderBy('fecha_hora', 'desc');
    }

    public function puntosSeguimientoMapa()
    {
        return $this->hasMany(CwseguimientoViaje::class, 'viaje_id')
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->orderBy('fecha_hora', 'asc');
    }

    public function clientes()
    {
        return $this->belongsToMany(Saclie::class, 'cwviajemotos', 'viaje_id', 'cliente_codclie', 'id', 'codclie')
            ->distinct();
    }

    public function getResumenClientesAttribute()
    {
        return $this->motosTransportadas()
            ->with('cliente')
            ->get()
            ->groupBy('cliente_codclie')
            ->map(function($motos, $codclie) {
                $cliente = $motos->first()->cliente;
                return [
                    'cliente' => $cliente ? $cliente->descrip : 'Sin cliente',
                    'codclie' => $codclie,
                    'total_motos' => $motos->sum('cantidad'),
                    'total_pagar' => $motos->sum(function($m) {
                        return $m->cantidad * $m->precio_por_moto;
                    }),
                    'facturado' => $motos->where('facturado', false)->count() === 0,
                    'motos' => $motos
                ];
            });
    }
}
