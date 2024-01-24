<?php

namespace Modules\Transporte\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Transporte\Models\TransporteVehiculo;
use Modules\Transporte\Http\Requests\TransporteVehiculoRequest;
use Modules\Transporte\Models\TransporteAsiento;
use Modules\Transporte\Models\TransporteProgramacion;

class TransporteVehiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $vehiculos = TransporteVehiculo::with('seats')->orderBy('id', 'DESC')
            ->get();

        return view('transporte::vehiculos.index', compact('vehiculos'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('transporte::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {

        $data = array_merge($request->only('placa', 'nombre','pisos','nro_hab_veh'),['asientos' => 0]);
        $vehiculo = TransporteVehiculo::create($data);

        return response()->json([
            'success' => true,
            'data'    => $vehiculo
        ], 200);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('transporte::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('transporte::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(TransporteVehiculoRequest $request, $id)
    {
        $vehiculo = TransporteVehiculo::findOrFail($id);
        $vehiculo->fill($request->only('placa', 'nombre','pisos','nro_hab_veh'));
        $vehiculo->save();

        return response()->json([
            'success' => true,
            'data'    => $vehiculo
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        try {


            $programaciones = TransporteProgramacion::where('vehiculo_id',$id)
            ->get();

            if( count($programaciones) > 1){
                throw new Exception('Lo sentimos, no podemos eliminar el vehículo porque tiene programaciones',888);
            }


            TransporteVehiculo::where('id', $id)
            ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Información actualizada'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message'    =>  ($th->getCode() === 888) ? $th->getMessage() : 'Ocurrió un error al procesar su petición. Detalles: ' . $th->getMessage()
            ], 500);
        }
    }

    public function guardarAsientos(Request $request,TransporteVehiculo $vehiculo){


        try{

            DB::connection('tenant')->beginTransaction();

            $asientos = json_decode($request->input('asientos'));

            $imageFront = $request->file('image_front');
            $imageBack = $request->file('image_back');
            $anchoVehiculo = $request->input('ancho_vehiculo');

            if(!is_null($imageFront)){
                $path = 'public\\images\\'.$vehiculo->image_front;
                if(Storage::exists($path)){
                    Storage::delete($path);
                }
                $imageFront->store('public\\images');
                $vehiculo->update([
                    'image_front' => $imageFront->hashName()
                ]);
            }

            if(!is_null($imageBack)){
                $path = 'public\\images\\'.$vehiculo->image_back;
                if(Storage::exists($path)){
                    Storage::delete($path);
                }

                $imageBack->store('public\\images');
                $vehiculo->update([
                    'image_back' => $imageBack->hashName()
                ]);


            }

            if($anchoVehiculo){
                $vehiculo->update([
                    'ancho_vehiculo' => $anchoVehiculo
                ]);
            }

            // return $asientos;
            foreach($asientos as $asiento){
                $asiento = (object) $asiento;

                $seat = TransporteAsiento::find($asiento->id);

                if(!is_null($seat)){
                    $seat->update([
                        'top' => $asiento->top,
                        'left' => $asiento->left,
                        // 'piso' => $asiento->piso,
                        // 'estado_asiento_id' => 1,
                    ]);
                    continue;
                }

                TransporteAsiento::create([
                    'vehiculo_id' => $vehiculo->id,
                    'numero_asiento' => $asiento->numero_asiento,
                    'type' => $asiento->type ,
                    'top' => $asiento->top,
                    'left' => $asiento->left,
                    'piso' => $asiento->piso,
                    // 'estado_asiento_id' => 1,
                ]);
            }

            $vehiculo->update([
                'asientos' => TransporteAsiento::where([
                    'vehiculo_id' => $vehiculo->id,
                    'type'=> 'ss'
                ])->count()
            ]);

            DB::connection('tenant')->commit();

            $vehiculo->load('seats');


            return response()->json([
                'success' => true,
                'vehiculo' => $vehiculo,
                'message' => 'Se ha guardado'
            ], 200);
        }catch(\Throwable $th){
            DB::connection('tenant')->rollBack();
            return response()->json([
                'success' => false,
                'message'    => 'Ocurrió un error al procesar su petición. Detalles: ' . $th->getMessage()
            ], 500);

        }

    }

    public function eliminarAsiento(TransporteAsiento $asiento){
        try{

            DB::connection('tenant')->beginTransaction();
            $asiento->delete();
            $asiento->vehiculo->update([
                'asientos' => TransporteAsiento::where([
                    'vehiculo_id' => $asiento->vehiculo_id,
                    'type'=> 'ss'
                ])->count()
            ]);
            DB::connection('tenant')->commit();

            return response()->json([
                'success' => true,
                'message' => 'Se ha eliminado el asiento'
            ],200);
        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'No se pudo eliminar el asiento'
            ],500);
        }
    }

    public function getAllVehiculos(){
        $data = TransporteVehiculo::all();
        return $data;
    }

    public function getAsientos($id){
        $data = TransporteAsiento::where('vehiculo_id',$id)->get();
        return $data;
    }
}
