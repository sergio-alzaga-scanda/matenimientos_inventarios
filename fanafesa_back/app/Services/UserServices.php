<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Repositories\Eloquent\InternalEventRepository as Internal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Eloquent\UserRepository as User;
use App\helpers\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserServices
{
    protected $internal;
    protected $user;

    // Si ya tienes un servicio para eventos internos, inyectalo.
    // Si no, puedes omitirlo o comentar esta parte.
    public function __construct(Internal $internal, User $user)
    {
        $this->internal = $internal;
        $this->user     = $user;
    }
    
    public function getUsers()
    {
        $users = DB::table('users')->where('status', 'Activo')->orWhere('status', 'Inactivo')->select('id', 'name', 'status', 'email', 'role')->get();

        foreach ($users as $user) {
            $item = DB::table('localidad_user')
                  ->where('user_id', $user->id)
                  ->select('localidad_user.*')
                  ->get();

            $user->liderExito=collect($item)->implode('loc_cod_sd',',');
        }

        return $users;
    }

    public function getUserById($user_id){

         try { 
            $user = $this->user->findOrFail($user_id);  

            if($user) 
            $user->localidades = $this->getDivisionesByUser($user->id);

            return $user; 
 
        } catch (ModelNotFoundException $exception) { 
            \Log::error("Mostrando un usuario...", [ 
                "model"   => $exception->getModel(), 
                "message" => $exception->getMessage(), 
                "code"    => $exception->getCode() 
            ]);  
 
            return JsonResponse::errorResponse("No se puede mostrar el usuario, informacion no encontrada", 404); 
        }

    }

    private function getDivisionesByUser($user_id)
    {
        $divisiones = DB::table('users')
            ->join('localidad_user', 'users.id', '=', 'localidad_user.user_id')
            ->where('localidad_user.user_id', $user_id)
            ->select('localidad_user.*')
            ->get();

        return $divisiones;
    }

    public function createUser($request){

        try {

            $data = $request->all();
            $now = Carbon::now('America/Mexico_City');
            $user = Auth::user();

            $localidades = array_get($request,'localidades',[]);

            unset($data['localidades']);

            $usuario_id =  DB::table('users')->insertGetId(array(
                'name'      => array_get($request, 'name'),
                'email'     => array_get($request, 'email'),
                'role'      => array_get($request, 'role'),
                'status'    => array_get($request, 'status'),
                'password'  => Hash::make(array_get($request, 'password')),
                'created_at'    => $now,
                'updated_at'    => $now

            ));

            foreach ($localidades as $id){
                DB::table('localidad_user')
                    ->insert(array(
                        'user_id'    => $usuario_id,
                        'loc_cod_sd' => $id
                ));
            }

            $this->internal->create(array(
                'user_id'   => $user['id'],
                'evento'    => 'El usuario: '.$user['name']. ' ha creado el usuario: ' .array_get($request, 'name'),
                'created_at'    => $now,
                'updated_at'    => $now
            ));
            
        } catch (Exception $e) {
            \Log::error("Error al crear usuario...", [ 
                "model"   => $exception->getModel(), 
                "message" => $exception->getMessage(), 
                "code"    => $exception->getCode() 
            ]);
        }
        
    }

    public function updateUser($request, $user_id){

        try {

                $data = $request->all();
                $now  = Carbon::now('America/Mexico_City');
                $user = Auth::user();

                $localidades = array_get($request,'localidades',[]);

                unset($data['localidades']);

                DB::table('localidad_user')->where('user_id',$user_id)->delete();

                foreach ($localidades as $id){
                    DB::table('localidad_user')
                        ->insert(array(
                            'user_id' => $user_id,
                            'loc_cod_sd' => $id
                        ));
                }

                DB::table('users')->where('id', $user_id)->update(array(
                    'name'       => array_get($request, 'name'),
                    'email'      => array_get($request, 'email'),
                    'role'       => array_get($request, 'role'),
                    'status'     => array_get($request, 'status'),
                    //'created_at' => $now,
                    'updated_at' => $now
                ));

                $this->internal->create(array(
                    'user_id'   => $user['id'],
                    'evento'    => 'El usuario: '.$user['name']. ' ha editado el usuario: ' .array_get($request, 'name'),
                    'created_at'    => $now,
                    'updated_at'    => $now
                ));
            
        } catch (Exception $e) {
            \Log::error("Error al editar usuario...", [ 
                "model"   => $exception->getModel(), 
                "message" => $exception->getMessage(), 
                "code"    => $exception->getCode() 
            ]);
        }
        
    }


}
