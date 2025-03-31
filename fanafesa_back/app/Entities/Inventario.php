<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Entities\Perifericos;
use App\Entities\Localidades;
use App\Entities\Mantenimiento;
//use App\Entities\HistorialMantenimiento;
use App\Entities\FotosInventario;

class Inventario extends Model
{
    protected $table = 'inventario';
    
    // Relación con Localidad (asumiendo que ambas tablas usan 'loc_cod_sd')
    public function localidad()
    {
        return $this->belongsTo(Localidades::class, 'loc_cod_sd', 'loc_cod_sd');
    }

    // Scope para filtrar según el usuario
    public function scopeByUser($query, $user)
    {
        if ($user->role === 'Ingeniero') {
            // Asegurar que Laravel haya cargado la relación
            if (!$user->relationLoaded('localidades')) {
                $user->load('localidades');
            }

            // Verificar si la relación tiene datos
            if (!$user->localidades || $user->localidades->isEmpty()) {
                //\Log::info('El usuario no tiene localidades asignadas');
                return $query->whereRaw('1 = 0'); // Retorna 0 resultados
            }

            // Ahora sí pluck()
            $localidades = $user->localidades->pluck('loc_cod_sd')->map(fn($value) => trim($value))->toArray();

            return $query->whereIn('loc_cod_sd', $localidades);
        }

        return $query; // Super Admin ve todo
    }

    public function perifericos()
    {
        return $this->hasMany(Perifericos::class, 'id_inventario');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleados::class, 'empleado_id', 'id');
    }

    public function mantenimientos()
    {
        return $this->hasMany(Mantenimiento::class, 'inventario_id');
    }

    // Relación con HistorialMantenimiento (indirecta a través de Mantenimiento)
   /* public function historialMantenimientos()
    {
        return $this->hasManyThrough(HistorialMantenimiento::class, Mantenimiento::class, 'inventario_id', 'mantenimiento_id');
        // La relación "hasManyThrough" permite acceder a historial_mantenimientos a través de mantenimientos
    }*/


}