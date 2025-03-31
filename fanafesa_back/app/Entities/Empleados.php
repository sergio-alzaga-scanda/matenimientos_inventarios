<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Empleados extends Model
{
    protected $table = 'empleados';
    
    // Relación con Inventario
    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'empleado_id', 'id');
    }

    public function perifericos()
    {
        return $this->hasMany(Perifericos::class, 'empleado_id', 'id');
    }
}
