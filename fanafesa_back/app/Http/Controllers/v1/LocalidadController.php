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

use App\Services\LocalidadServices;

class LocalidadController extends Controller
{
    use JWTTrait;

    protected $localidadServices;

    public function __construct(LocalidadServices $localidadServices)
    {
        $this->localidadServices = $localidadServices;
    }

    public function index()
    {
        $data = $this->localidadServices->getLocalidades();
        
        return JsonResponse::singleResponse([
            "message"     => "Info encontrada",
            "Localidades" => $data,
        ],200);
    }

    public function show($id_localidad)
    {
        $data = $this->localidadServices->getLocalidad($id_localidad);

        return JsonResponse::singleResponse([
            "message"   => "Info encontrada",
            "Localidad" => $data,
        ],200);
    }

    public function updateLocalidad(Request $request, $id_localidad)
    {
        $data = $this->localidadServices->updateLocalidad($request, $id_localidad);

        return JsonResponse::singleResponse([
            "message"   => "Info actualizada",
            //"Localidad" => $data,
        ],200);
    }
}