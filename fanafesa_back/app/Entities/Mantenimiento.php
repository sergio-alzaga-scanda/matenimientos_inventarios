<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Entities\HistorialMantenimiento;
use App\Entities\FotosInventario;

class Mantenimiento extends Model
{
    protected $table = 'mantenimientos';

    public function historialMantenimientos()
	{
	    return $this->hasMany(HistorialMantenimiento::class, 'mantenimiento_id');
	}

	public function fotosMantenimiento()
	{
	    return $this->hasMany(FotosInventario::class, 'mantenimiento_id');
	}
    
}