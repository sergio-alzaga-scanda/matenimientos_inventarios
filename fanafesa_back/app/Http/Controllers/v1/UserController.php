<?php

namespace App\Http\Controllers\v1;
use App\helpers\JsonResponse;

use App\Repositories\Eloquent\UserRepository as User;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Traits\JWTTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Eloquent\InternalEventRepository as Internal;
use Carbon\Carbon;

use App\Services\UserServices;

class UserController extends Controller
{
    use JWTTrait;

    protected $user;
    protected $hidden = ['password', 'remember_token'];
    protected $internal;
    protected $userServices;

    /**
     * UserController constructor.$userId = Auth::id();
     *
     * @param User $user
     *
     */

    public function __construct(User $user, Internal $internal, UserServices $userServices)
    {
        $this->user         = $user;
        $this->internal     = $internal;
        $this->userServices = $userServices;
    }

    /**
     * Devuelve todas los usuarios en el almacenamiento.
     *
     * @return \Illuminate\Http\response
     */

    public function index(Request $request)
    {
        $data = $this->userServices->getUsers();
        
        return JsonResponse::singleResponse([
            "message" => "Info encontrada",
            "Usuarios" => $data,
            //"Origenes" => $students2
        ],200);
    }

    /**
     * nuevo usuario  en el sistema
     *
     * @param \Illuminate\http\Request $request
     *
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $this->userServices->createUser($request);        

        return JsonResponse::singleResponse([
            "message" => "se ha registrado un nuevo usuario",
        ],200);
    }

    public function show($user_id)
    {
        $data = $this->userServices->getUserById($user_id);
        return JsonResponse::singleResponse($data->toArray());
        
    }

    /**
     * Actualiza el usuario en especifico  en el almacenamiento
     *
     * @param UpdateUserRequest $request
     * @param  int              $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$user_id )
    {

        $this->userServices->updateUser($request, $user_id);        

        return JsonResponse::singleResponse([
            "message" => "se ha actualizado un usuario",
        ],200);
    }
    /**
     *Elimina un usuario en espesifico dentro del almacenamiento.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $now = Carbon::now('America/Mexico_City');
            $user_delete = $this->user->find($id);
            $user = Auth::user();
            $user_id = Auth::user()->id;
                
            if($user_id == $id){
                return JsonResponse::errorResponse("No es posible auto destruirte", 404);
            }else{
                $this->internal->create(array(
                    'user_id'   => $user['id'],
                    'evento'    => 'El usuario: '.$user['name']. ' ha eliminado el usuario: ' .$user_delete['name'],
                    'created_at'    => $now,
                    'updated_at'    => $now
                ));
                $this->user->delete($id); 
            }
            
            return JsonResponse::singleResponse([ "message" => "El usuario ha sido eliminado." ]);
        } catch (ModelNotFoundException $exception) {
            \Log::error("Eliminando usuario...", [
                "model"   => $exception->getModel(),
                "message" => $exception->getMessage(),
                "code"    => $exception->getCode()
            ]);

            return JsonResponse::errorResponse("No es posible eliminar el usuario, informacion no encontrado.", 404);
        }

    }

    // Actualización de Contraseña Usuario
    public function updatePassword(Request $request,$user_id )
    {
        $data = $request->all();
        $now = Carbon::now('America/Mexico_City');
        $user_pass = $this->user->find($user_id);

        $pass_anterior = array_get($request, 'password_anterior');
        $pass_anterior2 = Hash::check($pass_anterior, $user_pass['password']);

        //var_dump($user['password']);
        //var_dump($pass_anterior2);

        if($pass_anterior2 == true){
            DB::table('users')->where('id', $user_id)
                ->update(array(
                'password'      => Hash::make(array_get($request, 'password')),
                'updated_at'    => $now
            ));
        }else{
            return JsonResponse::errorResponse("No es posible cambiarla, password anterior es incorrecto.", 404);
        }

        $user = Auth::user();
                $this->internal->create(array(
                'user_id'   => $user['id'],
                'evento'    => 'El usuario: '.$user['name']. ' ha actualizado la contraseña del usuario: ' .$user_pass['name'],
                'created_at'    => $now,
                'updated_at'    => $now
            ));

        return JsonResponse::singleResponse(["message" => "Se ha actualizado la contraseña del Usuario"]);

    }

    public function resetPasswordUser($user_id )
    {
        $now = Carbon::now('America/Mexico_City')->subHour();
        $user_pass = $this->user->find($user_id);
        $password = DB::table('reset')->get();
        $pass = $password[0]->pass_reset;


        DB::table('users')->where('id', $user_id)
            ->update(array(
            'password'      => Hash::make($pass),
            'updated_at'    => $now
        ));

        $user = Auth::user();
                $this->internal->create(array(
                'user_id'   => $user['id'],
                'evento'    => 'El usuario: '.$user['name']. ' ha reseteado la contraseña del usuario: ' . $user_pass['name'],
                'created_at'    => $now,
                'updated_at'    => $now
            ));

        return JsonResponse::singleResponse(["message" => "Se ha actualizado la contraseña del Usuario"]);

    }
}
