<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Repositories\Eloquent\InternalEventRepository as Internal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\helpers\JsonResponse;
use App\Entities\Inventario;

class InventarioServices
{
    protected $internal;
    protected $user;

    // Si ya tienes un servicio para eventos internos, inyectalo.
    // Si no, puedes omitirlo o comentar esta parte.
    public function __construct(Internal $internal)
    {
        $this->internal = $internal;
    }
    
    public function getInventarios()
    {
        $user = auth()->user();

        // Se aplica el scope para filtrar según el rol del usuario
        $inventarios = Inventario::byUser($user)
                        ->with('perifericos', 'empleado', 'localidad', 'mantenimientos.historialMantenimientos', 'mantenimientos.fotosMantenimiento')
                        ->withCount(['mantenimientos'])
                        ->get();
        //$inventarios = Inventario::with('perifericos')->get();

        return $inventarios;
    }

    public function geLocations()
    {
        $localidades = DB::table('localidades')->get();

        return $localidades;
    }

    public function createPhotos($request, $id_inventario)
    {
        try {

            $data                          = $request->all();
            $now                           = Carbon::now('America/Mexico_City');
            $localidad_id                  = $data['localidad']['id_localidad'] ?? null;
            $empleado_id                   = $data['empleado']['id'] ?? null;
            $farmacia                      = $data['ubicacion'] ?? null;
            $fecha_mantenimiento           = $data['fecha_mantenimiento'] ?? null;
            $nombre_jefe                   = $data['empleado']['nombre_jefe'] ?? null;
            $ed_jefe                       = $data['empleado']['ed_jefe'] ?? null;
            $posicion                      = $data['posicion'] ?? null;
            $version_windows               = $data['version_windows'] ?? null;
            $hostname                      = $data['hostname'] ?? null;
            $ip_name                       = $data['ip_name'] ?? null;
            $estado_inventario             = $data['estado_inventario'] ?? null;
            $seus                          = $data['seus'] ?? null;
            $tickets_puntos_de_venta       = $data['tickets_puntos_de_venta'] ?? null;
            $impresora_ricoh               = $data['impresora_ricoh'] ?? null;
            $system_center                 = $data['system_center'] ?? null;
            $verificar_firewall            = $data['verificar_firewall'] ?? null;
            $verificar_office_actualizado  = $data['verificar_office_actualizado'] ?? null;
            $comentarios                   = $data['comentarios'] ?? null;
            $corregir_hostname_equipo      = $data['corregir_hostname_equipo'] ?? null;
            $verificar_activar_wake_on_lan = $data['verificar_activar_wake_on_lan'] ?? null;
            $instalar_agente_sysaid        = $data['instalar_agente_sysaid'] ?? null;
            $verificar_instalar_antivirus  = $data['verificar_instalar_antivirus'] ?? null;
            $user                          = Auth::user();

            $mantenimiento_id = DB::table('mantenimientos')->insertGetId([
                'inventario_id'                 => $id_inventario,
                'localidad_id'                  => $localidad_id,
                'empleado_id'                   => $empleado_id,
                'farmacia'                      => $farmacia,
                'fecha_mantenimiento'           => $fecha_mantenimiento,
                'nombre_jefe'                   => $nombre_jefe,
                'ed_jefe'                       => $ed_jefe,
                'posicion'                      => $posicion,
                'version_windows'               => $version_windows,
                'hostname'                      => $hostname,
                'ip_name'                       => $ip_name,
                'estado_inventario'             => $estado_inventario,
                'seus'                          => $seus,
                'tickets_puntos_de_venta'       => $tickets_puntos_de_venta,
                'impresora_ricoh'               => $impresora_ricoh,
                'system_center'                 => $system_center,
                'verificar_firewall'            => $verificar_firewall,
                'verificar_office_actualizado'  => $verificar_office_actualizado,
                'corregir_hostname_equipo'      => $corregir_hostname_equipo,
                'verificar_activar_wake_on_lan' => $verificar_activar_wake_on_lan,
                'instalar_agente_sysaid'        => $instalar_agente_sysaid,
                'verificar_instalar_antivirus'  => $verificar_instalar_antivirus,
                'user_id'                       => Auth::user()->id,
                'created_at'                    => $now,
                'updated_at'                    => $now
            ]);
          
            $perifericos = $data['perifericos'];
            foreach ($perifericos as $periferico) {
                DB::table('historial_mantenimientos')->insertGetId([
                    'periferico_id'       => $periferico['id'] ?? null,
                    'mantenimiento_id'    => $mantenimiento_id,
                    'fecha_mantenimiento' => $fecha_mantenimiento,
                    'estado'              => $periferico['estado'] ?? null,
                    'user_id'             => Auth::user()->id,
                    'comentarios'         => $comentarios,
                    'created_at'          => $now,
                    'updated_at'          => $now
                ]);
            }

            $photosAntes = $data['photosAntes'];
            foreach ($photosAntes as $photoName) {
                DB::table('fotos_inventario')->insert([
                    'photo'            => $photoName,
                    'id_inventario'    => $id_inventario,
                    'mantenimiento_id' => $mantenimiento_id,
                    'tiempo_fotos'     => 'ANTES',
                    'user_name'        => Auth::user()->name,
                    'format_archivo'   => substr($photoName, -3),
                    'comentarios'      => $comentarios,
                    'created_at'       => $now,
                    'updated_at'       => $now
                ]);
            }

            $photosDespues = $data['photosDespues'];
            foreach ($photosDespues as $photoNameDespues) {
                DB::table('fotos_inventario')->insert([
                    'photo'            => $photoNameDespues,
                    'id_inventario'    => $id_inventario,
                    'mantenimiento_id' => $mantenimiento_id,
                    'tiempo_fotos'     => 'DESPUES',
                    'user_name'        => Auth::user()->name,
                    'format_archivo'   => substr($photoNameDespues, -3),
                    'comentarios'      => $comentarios,
                    'created_at'       => $now,
                    'updated_at'       => $now
                ]);
            }

            $this->internal->create(array(
                'user_id'   => $user['id'],
                'evento'    => 'El usuario: '. $user['name'] . ' ha subido fotos del inventario con ID: ' . $id_inventario,
                'created_at'    => $now,
                'updated_at'    => $now
            ));

            return JsonResponse::singleResponse(["message" => "Se han guardado las fotos."]);
        } catch (\Exception $e) {
            return JsonResponse::errorResponse("Error al subir archivos: " . $e->getMessage(), 500);
        }
    }

    public function getInfoInventarios($id_inventario)
    {

        $data = Inventario::with([
            'perifericos', 
            'empleado', 
            'localidad', 
            'mantenimientos.historialMantenimientos.periferico', 
            'mantenimientos.fotosMantenimiento'
        ])
        ->withCount(['mantenimientos'])
        ->where('id', $id_inventario)
        ->get();


        return JsonResponse::singleResponse(["message" => "Info Folio",
            //"General"     => $general[0],
            //"Fotos"       => $fotos,
            "Data" => $data
         ]);
    }
}