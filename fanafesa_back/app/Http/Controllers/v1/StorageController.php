<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;

use App\helpers\JsonResponse;
use App\Http\Requests;
use App\Http\Controllers\Controller;
//use Zipper;
use DB;

class StorageController extends Controller
{

    public function save(Request $request)
    {
        try {

            if($request->input('seccion') == 'ANTES'){
                $localidad       = $request->input('localidad');
                $nombre_empleado = $request->input('empleado');
                $serie_principal = $request->input('serie_principal');
                $seccion         = $request->input('seccion');
                $namecarpeta     = $request->input('carpeta');

                // Rutas para guardar las fotos
                $carpeta1 = $localidad . '/' . $nombre_empleado . '/' . $serie_principal . '/' . $seccion ;
                $carpetaC = $carpeta1 . '/' . $namecarpeta;

                // Creamos la carpeta si no existe
                if (!file_exists(storage_path('app/public/' . $carpetaC))) {
                    mkdir(storage_path('app/public/' . $carpetaC), 0777, true);
                }

                // Recorremos todas las fotos del array 'file[]'
                if ($request->hasFile('file')) {
                    foreach ($request->file('file') as $file) {
                        if ($file != null) {
                            // 📸 Obtenemos el nombre original del archivo
                            $name = $file->getClientOriginalName();

                            // Guardamos el archivo en la carpeta específica
                            \Storage::disk('local')->put($carpetaC . '/' . $name, \File::get($file));

                            // LOG DE ARCHIVO GUARDADO
                            \Log::info('Foto subida:', ['nombre' => $name, 'carpeta' => $carpetaC]);
                        }
                    }
                }
            }

            if($request->input('seccion') == 'DESPUES'){
                $localidad       = $request->input('localidad');
                $nombre_empleado = $request->input('empleado');
                $serie_principal = $request->input('serie_principal');
                $seccion         = $request->input('seccion');
                $namecarpeta     = $request->input('carpeta');

                // Rutas para guardar las fotos
                $carpeta1 = $localidad . '/' . $nombre_empleado . '/' . $serie_principal . '/' . $seccion ;
                $carpetaC = $carpeta1 . '/' . $namecarpeta;

                // Creamos la carpeta si no existe
                if (!file_exists(storage_path('app/public/' . $carpetaC))) {
                    mkdir(storage_path('app/public/' . $carpetaC), 0777, true);
                }

                // Recorremos todas las fotos del array 'file[]'
                if ($request->hasFile('file')) {
                    foreach ($request->file('file') as $file) {
                        if ($file != null) {
                            // 📸 Obtenemos el nombre original del archivo
                            $name = $file->getClientOriginalName();

                            // Guardamos el archivo en la carpeta específica
                            \Storage::disk('local')->put($carpetaC . '/' . $name, \File::get($file));

                            // LOG DE ARCHIVO GUARDADO
                            \Log::info('Foto subida:', ['nombre' => $name, 'carpeta' => $carpetaC]);
                        }
                    }
                }
            }

            

            // TODO SALIÓ BIEN
            return JsonResponse::singleResponse([
                "message" => "Las fotos se guardaron exitosamente."
            ], 200);

        } catch (\Exception $e) {
            // SI ALGO FALLA, CAPTURAMOS EL ERROR
            \Log::error('Error al subir las fotos:', [$e->getMessage()]);
            return JsonResponse::errorResponse("No se pudieron guardar las fotos.", 500);
        }
    }
    
    public function comprimirDescargarQuotationArea($quotation_area_id)
    {
        $quotation_area = DB::table('quotation_area')->where('id', $quotation_area_id)->get();
        /*Añadimos la ruta donde se encuentran los archivos que queramos comprimir,
          en este ejemplo comprimimos todos los que se encuentran en la carpeta 
          storage/app/public*/
        $files = glob(storage_path('app/quotation_area/'.$quotation_area_id.'/imagen/'.$quotation_area[0]->photo));
        /* Le indicamos en que carpeta queremos que se genere el zip y los comprimimos*/
        Zipper::make(storage_path('app/quotation_area/'.$quotation_area_id.'/imagen/'.$quotation_area[0]->photo.'.zip'))->add($files)->close();
        
        /* Por último, si queremos descargarlos, indicaremos la ruta del archivo, su nombre
        y lo descargaremos*/
        
        return response()->download(storage_path('app/quotation_area/'.$quotation_area_id.'/imagen/'.$quotation_area[0]->photo.'.zip'));
    }

}