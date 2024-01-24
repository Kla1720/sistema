<?php

namespace Modules\Transporte\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Transporte\Http\Requests\TransporteProgramacionesRequest;
use Modules\Transporte\Models\TransporteProgramacion;
use Modules\Transporte\Models\TransporteTerminales;
use Modules\Transporte\Models\TransporteUserTerminal;
use Modules\Transporte\Models\TransporteVehiculo;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Series;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Transporte\Models\TransporteChofer;
use Illuminate\Support\Facades\Session;
use Modules\Transporte\Models\TransporteDestinoHorario;
use Modules\Transporte\Models\TransporteRuta;

class TransporteProgramacionesController extends Controller
{
    //



    public function index(Request $request){
        $terminales = TransporteTerminales::all();
        $user_terminal = TransporteUserTerminal::where('user_id',auth()->user()->id)->first();

        if(is_null($user_terminal)){
            //redirigirlo
            Session::flash('message','No se pudó acceder. No tiene una terminal asignada');
            return redirect()->back();
        }

        $vehiculos = TransporteVehiculo::all();

        $establishment =  Establishment::where('id', auth()->user()->establishment_id)->first();
        $series = Series::where('establishment_id', $establishment->id)->get();

        $choferes = TransporteChofer::all();



        return view('transporte::programaciones.index',compact(
            'terminales',
            'vehiculos',
            'series',
            'choferes',
            'user_terminal'
        ));
    }
    public function getProgramacionesList(){
        $programaciones = TransporteProgramacion::with('rutas','vehiculo','origen','destino','rutas')
            ->where('hidden',0)
            ->get();

        return $programaciones;
    }

    public function getProgramaciones(){
        $user_terminal = TransporteUserTerminal::where('user_id',auth()->user()->id)->first();
        if(auth()->user()->type=='admin'){

            $programaciones = TransporteProgramacion::with('rutas','vehiculo','origen','destino','rutas')
            ->where('hidden',0)
            ->where('active',1)
            ->get();
        }
        else{
            $programaciones = TransporteProgramacion::with('rutas','vehiculo','origen','destino','rutas')
            ->where('terminal_origen_id',$user_terminal->terminal_id)
            ->where('hidden',0)
            ->where('active',1)
            ->get();
        }

        return $programaciones;
    }


    public function getTerminales(Request $request){
        try{

            extract($request->only(['search']));

            $terminales = TransporteTerminales::query();

            if(isset($search) && !empty($search)){
                $terminales->where('nombre','like',"%{$search}%");
            }

            return response()->json([
                'success' => true,
                'msg' => 'success',
                'data' => $terminales->get()
            ]);


        }catch(Exception $e){

            return response()->json([
                'success' => false,
                'msg' => 'Lo sentimos ocurrio un error',
                'data' => []
            ],204);

        }
    }


    private function routes(TransporteProgramacion $programacion,$terminales,$totalList = null){
        $terminal = $terminales->shift();

        $list = is_null($totalList) ? collect([]) : $totalList;

        if(count($terminales) == 0) return $totalList;

        foreach($terminales as $item){
            $pro = TransporteProgramacion::create([
                'terminal_origen_id' => $terminal->id,
                'terminal_destino_id' => $item->id,
                'hora_salida' => $terminal->pivot->hora_salida,
                'vehiculo_id' => $programacion->vehiculo_id,
                'programacion_id' => $programacion->id,
                'hidden' => true,
                'active' => true,
            ]);
            $list->push($pro);
        }
        return $this->routes($programacion,$terminales,$list);

    }


    private function createRoutes( TransporteProgramacion $programacion, $terminales ){
        $tempTerminales = $terminales;
        $totalList = collect([]);

        foreach($tempTerminales as $terminal){

            // if($terminal['terminal_origen_id'] == $programacion->terminal_destino_id) continue;

            $pro = TransporteProgramacion::create([
                'terminal_origen_id' => $programacion->terminal_origen_id,
                'terminal_destino_id' => $terminal->id,
                'hora_salida' => $programacion->hora_salida,
                'vehiculo_id' => $programacion->vehiculo_id,
                'programacion_id' => $programacion->id,
                'hidden' => true,
                'active' => true,
            ]);

            $totalList->push($pro);
        }
        return $totalList;
    }


    public function store(TransporteProgramacionesRequest $request){

        try{

            DB::connection('tenant')->beginTransaction();

            $formProgramacion = $request->input('programacion');
            $progamacionesGeneradas = $request->input('programaciones');
            $destinosHorarios = $request->input('destinos_horarios');


            $formProgramacion['hidden'] = false;
            $programacion = TransporteProgramacion::create( array_merge($formProgramacion, [
                'destinos_horarios' => json_encode($destinosHorarios),
            ]));
            $programacion->update(['programacion_id' => $programacion->id]);

            $intermedios =  collect($request->input('intermedios'));

            $i = 1;
            foreach($intermedios as $intermedio){
                $programacion->rutas()->attach($intermedio['terminal_origen_id'],[
                    'hora_salida' => $intermedio['hora_salida'],
                    'orden' => $i
                ]);
                $i++;
            }

            // foreach($destinosHorarios as $destinoHorario){

            //     TransporteDestinoHorario::create([
            //         'origen_id' => $destinoHorario['origen']['id'],
            //         'destino_id' => $destinoHorario['destino']['id'],
            //         'tiempo_estimado' => $destinoHorario['tiempo_estimado'],
            //         'programacion_id' => $programacion->id
            //     ]);

            // }

            foreach($progamacionesGeneradas as $programation){
                TransporteProgramacion::create([
                    'terminal_origen_id' => $programation['origen']['id'],
                    'terminal_destino_id' => $programation['destino']['id'],
                    'vehiculo_id' => $formProgramacion['vehiculo_id'],
                    'hora_salida' => $programation['hora_salida'],
                    'tiempo_estimado' => $programation['tiempo_estimado'],
                    'programacion_id' => $programacion->id,
                    'hidden' => true,
                    'active' => true
                ]);
            }


            // $terminales = $programacion->rutas;

            // $this->createRoutes($programacion,$terminales);

            // $terminales->push($programacion->destino);

            //agrego el destino para poder hacer las combinaciones


            // $this->routes($programacion,$terminales);


            DB::connection('tenant')->commit();

            return response()->json([
                'success' => true,
                'msg' => 'Se ha agregado la programación',
            ]);

        }catch(Exception $e){

            return response()->json([
                'success' => false,
                'msg' => 'Ocurrio un error al agregar la programación',
            ]);

        }



    }



    private function  listCombination(Collection $terminales,Collection $listMerge = null) : Collection{
        $terminal = $terminales->shift();
        $listMerge = is_null($listMerge) ? new Collection() : $listMerge;


        if(count($terminales) == 0) return $listMerge;

        foreach($terminales as $term){
            $listMerge->push([$terminal->id,$term->id]);
        }

        return $this->listCombination($terminales,$listMerge);

    }


    private function getCombination(TransporteProgramacion $programacion,Collection $terminales) : Collection{

        $terminales->prepend($programacion->origen);
        $terminales->push($programacion->destino);


        return $this->listCombination($terminales);
    }

    private function listExcepts(Collection $list1,Collection $list2) : Collection{
        $excepts = new Collection();
        foreach($list1 as $item1){

            $exist = $list2->first(function($item2) use($item1){
                [$variable1,$variable2] = $item2;
                [$variable3,$variable4] = $item1;
                $indexVar1 = array_search($variable1,$item1);
                $indexVar2 = array_search($variable2,$item1);
                $indexVar3 = array_search($variable3,$item2);
                $indexVar4 = array_search($variable4,$item2);

                return ($variable1 == $variable3
                && $indexVar1 == $indexVar3
                && $variable2 == $variable4
                && $indexVar2 == $indexVar4);
            });
            if(is_null($exist)) $excepts->push($item1);
        }

        return $excepts;

    }

    private function updateOrCreateProgramaciones(TransporteProgramacion $programacion, Collection $collection){

        foreach($collection as $item){
            [$origen,$destino] = $item;



            $oldProgramacion = TransporteProgramacion::where('terminal_origen_id',$origen)
            ->where('terminal_destino_id',$destino)
            ->where('programacion_id',$programacion->id)
            ->first();

            if(!is_null($oldProgramacion)){

                $oldProgramacion->update([
                    'active' => !$oldProgramacion->active,
                ]);

            } else {

                $hora_salida = null;
                if($origen == $programacion->terminal_origen_id){
                    $hora_salida = $programacion->hora_salida;
                }else {
                    $terminal = TransporteRuta::where('programacion_id',$programacion->id)
                    ->where('terminal_id',$origen)
                    ->first();
                    $hora_salida = $terminal->hora_salida;
                }

                TransporteProgramacion::create([
                    'terminal_origen_id' => $origen,
                    'terminal_destino_id' => $destino,
                    'hora_salida' => $hora_salida,
                    'vehiculo_id' => $programacion->vehiculo_id,
                    'active' => true,
                    'hidden' => true,
                    'programacion_id' => $programacion->id
                ]);

            }
        }
    }


    public function checkIfExistOrCreate(TransporteProgramacion $programacion, Collection $collection){

        foreach($collection as $item){
            [$origen,$destino] = $item;

            $hora_salida = null;
            if($origen == $programacion->terminal_origen_id){
                $hora_salida = $programacion->hora_salida;
            }else {
                $terminal = TransporteRuta::where('programacion_id',$programacion->id)
                ->where('terminal_id',$origen)
                ->first();
                $hora_salida = $terminal->hora_salida;
            }

            $exist = TransporteProgramacion::where('terminal_origen_id',$origen)
            ->where('terminal_destino_id',$destino)
            ->where('programacion_id',$programacion->id)
            ->first();


            if(!is_null($exist)) {
                $exist->update([
                    'hora_salida' => $hora_salida
                ]);
            }else {
                $f = TransporteProgramacion::create([
                    'terminal_origen_id' => $origen,
                    'terminal_destino_id' => $destino,
                    'programacion_id' => $programacion->id,
                    'vehiculo_id' => $programacion->vehiculo_id,
                    'hora_salida' => $hora_salida,
                    'hidden' => true,
                    'active' => true
                ]);
            }


        }

    }

    public function update(TransporteProgramacionesRequest $request,TransporteProgramacion $programacion){


        try{

            DB::connection('tenant')->beginTransaction();

            $destinosHorarios = $request->input('destinos_horarios');
            $formProgramacion = Arr::except($request->input('programacion'),['destinos_horarios']) ;
            $progamacionesGeneradas = $request->input('programaciones');
            $programacion->update( array_merge($formProgramacion,[
                'destinos_horarios' => json_encode($destinosHorarios),
            ]));
            $programacion->update(['programacion_id' => $programacion->id]);

            $intermedios = collect($request->input('intermedios'));

            TransporteRuta::where('programacion_id',$programacion->id)
            ->delete();

            TransporteProgramacion::where('programacion_id',$programacion->id)
            ->where('id','!=', $programacion->id)
            ->delete();


            $orden = 1;
            foreach($intermedios as $terminal){
                $programacion->rutas()->attach($terminal['terminal_origen_id'],[
                    'hora_salida' => $terminal['hora_salida'],
                    'orden' => $orden
                ]);
                $orden++;
            }


            foreach($progamacionesGeneradas as $programation){
                TransporteProgramacion::create([
                    'terminal_origen_id' => $programation['origen']['id'],
                    'terminal_destino_id' => $programation['destino']['id'],
                    'vehiculo_id' => $formProgramacion['vehiculo_id'],
                    'hora_salida' => $programation['hora_salida'],
                    'tiempo_estimado' => $programation['tiempo_estimado'],
                    'programacion_id' => $programacion->id,
                    'hidden' => true,
                    'active' => true
                ]);
            }


            DB::connection('tenant')->commit();

            return response()->json([
                'success' => true,
                'data'    => $programacion
            ]);

        }catch(Exception $e){
            DB::connection('tenant')->rollBack();
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'data'    => null
            ]);

        }


    }

    public function deleteRuta(TransporteProgramacion $programacion,$terminal){

        try{

            DB::connection('tenant')->beginTransaction();
            TransporteProgramacion::where('programacion_id',$programacion->id)
            ->where('terminal_origen_id',$terminal)
            ->orWhere('terminal_destino_id',$terminal)
            ->update(['active' => false]);

            TransporteRuta::where('terminal_id',$terminal)
            ->where('programacion_id',$programacion->id)
            ->delete();

            DB::connection('tenant')->commit();

            return response()->json([
                'status' => true,
                'msg' => 'Se ha eliminado correctamente'
            ]);
        }catch(Exception $e){

            return response()->json([
                'status' => true,
                'msg' => 'Ocurrio un error al realizar la petición'
            ]);

        }




    }

    public function destroy(TransporteProgramacion $programacion){
        try {

            if(count($programacion->encomiendas) > 0){
                throw new Exception('Lo sentimos no se puede eliminar la programación, tiene encomiendas',888);
            }

            if(count($programacion->pasajes) > 0){
                throw new Exception('Lo sentimos no se puede eliminar la programación, tiene pasajes',888);
            }

            if(count($programacion->manifiestos) > 0){
                throw new Exception('Lo sentimos no se puede eliminar la programación, tiene manifiestos',888);
            }

            $programacion->syncRutas([]);
            $programacion->delete();

            return response()->json([
                'success' => true,
                'message' => 'Información actualizada'
            ],200);

        } catch (\Throwable $th) {

            return response()->json([
                'success' => false,
                'message' => $th->getCode() == 888 ? $th->getMessage() : 'Ocurrió un error al procesar su petición'
            ],500);

        }
    }


    public function configuracionRutas(Request $request,TransporteProgramacion $programacion){

        $rutas = (array) $request->rutas;
        $programacion->syncRutas($rutas);
        $programacion->load(
            'origen',
            'destino',
            'vehiculo',
            'rutas'
        );
        $programacion->hora_view = date('g:i a',strtotime($programacion->hora_salida));

        return response()->json([
            'success' => true,
            'data'    => $programacion
        ]);

    }
    public function desactivar(Request  $request){
        try {
            $programacion = new TransporteProgramacion();

            $programacion->where('programacion_id',$request->id)
                ->update(['active' => 0]);

            return response()->json([
                'success' => true,
                'data' => $programacion,
                'message'=> "Se desactivó la programación"
            ], 200);
        }
        catch (\Throwable $th) {

            return response()->json([
            'success' => false,
            'message' => $th->getCode() == 888 ? $th->getMessage() : 'Ocurrió un error al procesar su petición'
            ],500);

        }
    }

    public function activar(Request  $request){

        try {
            $programacion = new TransporteProgramacion();

            $programacion->where('programacion_id',$request->id)
                ->update(['active' => 1]);

            return response()->json([
                'success' => true,
                'data' => $programacion,
                'message'=> "Se activó la programación"
            ], 200);
        }
        catch (\Throwable $th) {

            return response()->json([
                'success' => false,
                'message' => $th->getCode() == 888 ? $th->getMessage() : 'Ocurrió un error al procesar su petición'
            ],500);

        }


    }



}
