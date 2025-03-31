<?php

namespace App\Http\Controllers\v1;
use App\helpers\JsonResponse;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Traits\JWTTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Eloquent\InternalEventRepository as Internal;
use Carbon\Carbon;

use App\Services\EmpleadoServices;

class EmpleadosController extends Controller
{
    use JWTTrait;

    protected $empleadoServices;

    public function __construct(EmpleadoServices $empleadoServices)
    {
        $this->empleadoServices = $empleadoServices;
    }

    public function index()
    {
        $data = $this->empleadoServices->getEmpleados();
        
        return JsonResponse::singleResponse([
            "message"   => "Info encontrada",
            "Empleados" => $data,
        ],200);
    }

    public function show($id_empleado)
    {
        $data = $this->empleadoServices->getEmpleado($id_empleado);

        return JsonResponse::singleResponse([
            "message"  => "Info encontrada",
            "Empleado" => $data,
        ],200);
    }

    public function updateEmpleado(Request $request, $id_empleado)
    {
        $data = $this->empleadoServices->updateLocalidad($request, $id_empleado);

        return JsonResponse::singleResponse([
            "message"   => "Info actualizada",
            //"Localidad" => $data,
        ],200);
    }
}