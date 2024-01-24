<?php
namespace Modules\Restaurant\Http\Controllers;

use Exception;
use App\Http\Controllers\Controller;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Restaurante\Models\Mesa;
use Modules\Restaurante\Models\Nivel;

class MesaController extends Controller
{

  use StorageDocument;

  protected $company;

    public function index()
    {
        $mesas = Mesa::with('nivel')->get();
        $niveles = Nivel::all();

        return view('restaurant::mesas.index', compact('mesas','niveles'));
    }

    public function store(Request $request)
    {
        $mesa = Mesa::create($request->only('numero','nivel_id','activo'));
        $mesa->nivel;

        return response()->json([
            'success' => true,
            'data'    => $mesa
        ], 200);
    }

    public function update(Request $request, Mesa $mesa){
        try{
            $mesa->update($request->only([
                'numero',
                'nivel_id',
                'activo'
            ]));

            $mesa->nivel;

            return response()->json([
                'success' => true,
                'data'    => $mesa
            ], 200);

        } catch (\Throwable $th) {

            return response()->json([
            'success' => false,
            'data'    => 'Ocurrió un error al procesar su petición. Detalles: ' . $th->getMessage()
            ], 500);

        }
    }

    public function records()
    {
        $mesas = Mesa::with('nivel')->get();

        return response()->json([
            'success' => true,
            'data'    => $mesas
        ], 200);
    }
    public function delete(Request $request){
        $item = Mesa::where('id',$request->mesa);
        $item->delete();

        return response()->json([
            'success' => true,
            'data'    => $item,
            'message' => 'Información actualizada'
        ], 200);
    }

}

