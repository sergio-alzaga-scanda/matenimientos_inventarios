<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Localidades extends Model
{
    protected $table = 'localidades';
    protected $primaryKey = 'id_localidad';
    protected $casts = [
	    'loc_cod_sd' => 'string', // Asegura que se maneja como string en Eloquent
	];
    
    // Relación con Inventario
    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'loc_cod_sd', 'loc_cod_sd');
    }

    public function perifericos()
    {
        return $this->hasMany(Perifericos::class, 'loc_cod_sd', 'loc_cod_sd');
    }
    
}
