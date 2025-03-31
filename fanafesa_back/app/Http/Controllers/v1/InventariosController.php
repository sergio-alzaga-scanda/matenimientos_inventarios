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

use App\Services\InventarioServices;

class InventariosController extends Controller
{
    use JWTTrait;

    protected $inventarioServices;

    public function __construct(InventarioServices $inventarioServices)
    {
        $this->inventarioServices = $inventarioServices;
    }

    public function index()
    {
        $data = $this->inventarioServices->getInventarios();
        //$data = DB::table('inventario')->take(4000)->get();
        
        return JsonResponse::singleResponse([
            "message"     => "Info encontrada",
            "Inventarios" => $data,
        ],200);
    }

    public function getLocalidades()
    {
        $data = $this->inventarioServices->geLocations();
        
        return JsonResponse::singleResponse([
            "message"     => "Info encontrada",
            "Data"        => $data,
        ],200);
    }

    public function updatePhotoFolio(Request $request, $id_inventario)
    {
        $this->inventarioServices->createPhotos($request, $id_inventario);
    }

    public function getInfoInventarios($id_inventario){
        return $this->inventarioServices->getInfoInventarios($id_inventario);
    }



}
