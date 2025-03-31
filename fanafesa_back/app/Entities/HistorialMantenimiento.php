<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Entities\Perifericos;
use App\Entities\FotosInventario;

class HistorialMantenimiento extends Model
{
    protected $table = 'historial_mantenimientos';

    public function periferico()
	{
	    return $this->belongsTo(Perifericos::class, 'periferico_id');
	}

	public function fotos()
	{
	    return $this->hasMany(FotosInventario::class, 'mantenimiento_id');
	}
    
}