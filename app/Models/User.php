<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log; // Agregar para logging opcional
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'first_name', 'last_name', 'type', 'email', 'avatar', 'password'
    ];

    /**
     * Relación con las sucursales
     */
    public function sucursales(): BelongsToMany
    {
        return $this->belongsToMany(
            Sasucursal::class,
            'usersucursal',
            'fk_user',
            'fk_sucursal'
        )->withTimestamps();
    }

    /**
     * Obtener todas las sucursales del usuario (sin filtrar por comercial)
     */
    public function getTodasSucursales()
    {
        return $this->sucursales()->orderBy('id')->get();
    }

    /**
     * Obtener las sucursales del usuario filtradas por la comercial actual
     * CORREGIDA: Maneja correctamente cuando no hay sucursales
     */
    public function getSucursalesComercialActual()
    {
        $comercialId = session('comercialid');

        // Obtener todas las sucursales del usuario
        $todasSucursales = $this->sucursales()->with('comercial')->orderBy('id')->get();

        // Si no tiene sucursales, retornar colección vacía
        if ($todasSucursales->isEmpty()) {
            return collect();
        }

        // Si no hay comercial en sesión, obtener de la primera sucursal
        if (!$comercialId) {
            $primeraSucursal = $todasSucursales->first();

            // Verificar si la sucursal tiene relación comercial
            if ($primeraSucursal && $primeraSucursal->fk_comercial) {
                $comercialId = $primeraSucursal->fk_comercial;
                Session::put('comercialid', $comercialId);

                if ($primeraSucursal->comercial) {
                    Session::put('comercialdata', $primeraSucursal->comercial);
                }
            } else {
                // Si la primera sucursal no tiene comercial asociado
                return $todasSucursales; // Retorna todas las sucursales sin filtrar
            }
        }

        // Filtrar sucursales por el comercial actual
        $sucus = $todasSucursales->filter(function($sucursal) use ($comercialId) {
            return $sucursal->fk_comercial == $comercialId;
        });

        return $sucus;
    }

    /**
     * Obtener la primera sucursal del comercial actual
     */
    public function getPrimeraSucursalComercialActual()
    {
        return $this->getSucursalesComercialActual()->first();
    }

    /**
     * Obtener los IDs de las sucursales del comercial actual
     * CORREGIDA: Maneja cuando no hay sucursales
     */
    public function getSucursalesIdsComercialActual()
    {
        $sucursales = $this->getSucursalesComercialActual();

        // Si es null o colección vacía, retornar array vacío
        if (!$sucursales || $sucursales->isEmpty()) {
            return [];
        }

        return $sucursales->pluck('id')->toArray();
    }

    /**
     * Verificar si una sucursal pertenece al comercial actual del usuario
     */
    public function sucursalPerteneceComercialActual($sucursalId)
    {
        $sucursalesIds = $this->getSucursalesIdsComercialActual();
        return in_array($sucursalId, $sucursalesIds);
    }

    /**
     * Obtener la comercial actual del usuario
     */
    public function getComercialActual()
    {
        // Primero intentar obtener de la sesión
        $comercial = Session::get('comercialdata');

        if ($comercial) {
            return $comercial;
        }

        // Si no está en sesión, obtener de la primera sucursal
        $primeraSucursal = $this->sucursales()->with('comercial')->orderBy('id')->first();

        if ($primeraSucursal && $primeraSucursal->comercial) {
            Session::put('comercialdata', $primeraSucursal->comercial);
            Session::put('comercialid', $primeraSucursal->comercial->id);
            return $primeraSucursal->comercial;
        }

        return null;
    }

    /**
     * Obtener los comerciales a los que el usuario tiene acceso
     * a través de sus sucursales asignadas
     */
    public function getComercialesAcceso()
    {
        // Obtener todas las sucursales del usuario con sus comerciales
        $sucursales = $this->sucursales()->with('comercial')->get();

        // Extraer comerciales únicos (evitar duplicados)
        $comerciales = collect();

        foreach ($sucursales as $sucursal) {
            if ($sucursal->comercial && !$comerciales->contains('id', $sucursal->comercial->id)) {
                $comerciales->push($sucursal->comercial);
            }
        }

        // Ordenar por ID
        return $comerciales->sortBy('id');
    }

    /**
     * Verificar si el usuario tiene acceso a un comercial específico
     */
    public function tieneAccesoAComercial($comercialId)
    {
        return $this->sucursales()
            ->where('fk_comercial', $comercialId)
            ->exists();
    }

    /**
     * Obtener el comercial principal (primera sucursal ordenada por ID)
     */
    public function getComercialPrincipal()
    {
        $sucursal = $this->sucursales()->with('comercial')->orderBy('id')->first();

        if ($sucursal && $sucursal->comercial) {
            return $sucursal->comercial;
        }

        return null;
    }

    /**
     * Verificar si el usuario tiene sucursales asignadas
     */
    public function tieneSucursalesAsignadas()
    {
        return $this->sucursales()->exists();
    }

    /**
     * Cambiar a una comercial específica
     */
    public function cambiarComercial($comercialId)
    {
        if (!$this->tieneAccesoAComercial($comercialId)) {
            return false;
        }

        $comercial = Sacomercial::find($comercialId);

        if ($comercial) {
            Session::put('comercialdata', $comercial);
            Session::put('comercialid', $comercial->id);
            return true;
        }

        return false;
    }
}
