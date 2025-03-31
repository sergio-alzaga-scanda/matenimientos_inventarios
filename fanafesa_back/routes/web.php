<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\v1\UserController;
use App\Http\Controllers\v1\InventariosController;
use App\Http\Controllers\v1\StorageController;
use App\Http\Controllers\v1\LocalidadController;
use App\Http\Controllers\v1\EmpleadosController;


Route::get('/', function () {
    return view('welcome');
});

Route::post('login', [APILoginController::class,'login']);

Route::group(['namespace' => 'v1', 'prefix' => 'v1/archivos'], function (\Illuminate\Routing\Router $router) {
    $router->post('storage/create',  [StorageController::class,'save']);
});

Route::group(['middleware' => ['jwt.auth', 'cors'],'prefix' => 'v1'], function () {

    Route::group(['namespace' => 'v1'], function () {
       

        //Usuarios
        Route::get('usuarios', [UserController::class, 'index']);
        Route::get('usuarios/{user_id}', [UserController::class,'show']);
        Route::post('usuarios', [UserController::class,'store']);
        Route::post('usuarios/{user_id}', [UserController::class,'update']);
        Route::delete('usuarios/{user_id}', [UserController::class,'destroy']);
        Route::put('usuarios/{user_id}/resetPassword', [UserController::class,'updatePassword']);
        Route::get('lideres', [UserController::class,'getLideres']);
        Route::get('divisiones_user', [UserController::class,'getDivisiones']);
        Route::put('usuarios_resetPassword/{user_id}', [UserController::class,'resetPasswordUser']);


        //Inventarios
        Route::get('inventarios', [InventariosController::class, 'index']);
        Route::get('localidad_user', [InventariosController::class, 'getLocalidades']);
        Route::put('agregar/photo/{id_inventario}', [InventariosController::class, 'updatePhotoFolio']);
        Route::get('infoInventario/{id_inventario}', [InventariosController::class, 'getInfoInventarios']);

        //Localidades
        Route::get('localidades', [LocalidadController::class, 'index']);
        Route::get('localidad/{id_localidad}', [LocalidadController::class,'show']);
        Route::put('localidad/{id_localidad}', [LocalidadController::class,'updateLocalidad']);

        //Empleados
        Route::get('empleados', [EmpleadosController::class, 'index']);
        Route::get('empleado/{id_empleado}', [EmpleadosController::class,'show']);
        Route::put('empleado/{id_empleado}', [EmpleadosController::class,'updateLocalidad']);

       


        Route::group(['prefix' => 'archivos'], function (\Illuminate\Routing\Router $router) {
            $router->post('storage/create',  [StorageController::class,'save']);
        });



        
    });

});



