<?php

namespace Modules\Transporte\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Transporte\Http\Requests\TransporteTerminalesRequest;
use Modules\Transporte\Models\TransporteDestino;
use Modules\Transporte\Models\TransporteTerminales;

class TransporteTerminalesController extends Controller
{
    //

    public function index(){
        $terminales = TransporteTerminales::with(['destino'])
        ->orderBy('id', 'DESC')
        ->get();
        $destinos = TransporteDestino::all();
        
        return view('transporte::terminales.index', compact('terminales','destinos'));
    }

    public function store(TransporteTerminalesRequest $request){

        $terminal = TransporteTerminales::create($request->only('direccion','destino_id','nombre','color'));
        $terminal->destino; //cargo el destino o ciudad

        return response()->json([
            'success' => true,
            'data'    => $terminal
        ]);

    }

    public function destroy(TransporteTerminales $terminal){
        try{

            if(count($terminal->programacion_destinos ) > 0 || count($terminal->programacion_origenes) > 0){
                throw new Exception('No se puede elimnar la terminal porque tiene programaciones',888);
            } 
            $terminal->delete();
            return response()->json([
                'success' => true,
                'message' => 'Información actualizada'
            ],200);
        }catch(\Throwable $th){
            return response()->json([
                'success' => false,
                'message' => $th->getCode() == 888 ? $th->getMessage() : 'Ocurrió un error al procesar su petición'
            ],500);
        }
    }

    public function update(TransporteTerminalesRequest $request,TransporteTerminales $terminal){
        
        $terminal->update($request->only([
            'nombre',
            'direccion',
            'destino_id',
            'color'
        ]));
        $terminal->destino;

        return response()->json([
            'success' => true,
            'message' => 'Información actualizada',
            'data'    => $terminal
        ]);
    }
    public function getAll(){
        $terminales = TransporteTerminales::with(['destino'])
        ->orderBy('id', 'DESC')
        ->get();
        
        return response()->json([
            'success' => true,
            'data'    => $terminales
        ]);
    }
}
