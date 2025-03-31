<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Repositories\Eloquent\InternalEventRepository as Internal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\helpers\JsonResponse;
use App\Entities\Empleados;
use Mail;

class EmpleadoServices
{
    protected $internal;
    protected $user;

    // Si ya tienes un servicio para eventos internos, inyectalo.
    // Si no, puedes omitirlo o comentar esta parte.
    public function __construct(Internal $internal)
    {
        $this->internal = $internal;
    }
    
    public function getEmpleados()
    {
        // Se aplica el scope para filtrar según el rol del usuario
        $empleados = Empleados::with('inventarios', 'perifericos')->withCount(['inventarios', 'perifericos'])->get();
        
        return $empleados;
    }

    public function getEmpleado($id_empleado)
    {
        // Se aplica el scope para filtrar según el rol del usuario
        $empleado = Empleados::with('inventarios', 'perifericos')->withCount(['inventarios', 'perifericos'])->where('id', $id_empleado)->get();
        //$localidades = DB::table('localidades')->get();

        return $empleado;
    }

    public function updateLocalidad($request, $id_localidad)
    {
        try {

            $data = $request->all();
            $now = Carbon::now('America/Mexico_City');
            $user = Auth::user();

            DB::table('localidades')->where('id_localidad', $id_localidad)->update(array(
                'activo'        => array_get($request, 'activo'),
                'fecha_alta'    => array_get($request, 'fecha_alta'),
                'fecha_baja'    => array_get($request, 'fecha_baja'),
                'loc_tipo'      => array_get($request, 'loc_tipo'),
                'calle'         => array_get($request, 'calle'),
                'colonia'       => array_get($request, 'colonia'),
                'estado'        => array_get($request, 'estado'),
                'delegacion'    => array_get($request, 'delegacion'),
                'edo'           => array_get($request, 'edo'),
                'codigo_postal' => array_get($request, 'codigo_postal'),
                'lada'          => array_get($request, 'lada'),
                'telefono1'     => array_get($request, 'telefono1'),
                'telefono2'     => array_get($request, 'telefono2'),
                'telefono3'     => array_get($request, 'telefono3'),
                'telefono4'     => array_get($request, 'telefono4'),
                'telefono5'     => array_get($request, 'telefono5'),
                'updated_at'    => $now
            ));

            $this->internal->create(array(
                'user_id'   => $user['id'],
                'evento'    => 'El usuario: '.$user['name']. ' ha actualizado una localidad con ID: ' . $id_localidad,
                'created_at'    => $now,
                'updated_at'    => $now
            ));
            
        } catch (Exception $e) {
            \Log::error("Error al actualizar localidad...", [ 
                "model"   => $exception->getModel(), 
                "message" => $exception->getMessage(), 
                "code"    => $exception->getCode() 
            ]);
        }
    }
}