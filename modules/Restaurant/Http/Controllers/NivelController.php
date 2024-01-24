<?php
namespace Modules\Restaurant\Http\Controllers;

use Exception;
use App\Http\Controllers\Controller;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Restaurante\Models\Mesa;
use Modules\Restaurante\Models\Nivel;

class NivelController extends Controller
{

  use StorageDocument;

  protected $company;

    public function index()
    {
        $niveles = Nivel::all();

        return view('restaurant::niveles.index', compact('niveles'));
    }

    public function store(Request $request)
    {
        $nivel = Nivel::create($request->only('nombre','activo'));

        return response()->json([
            'success' => true,
            'data'    => $nivel
        ], 200);
    }

    public function update(Request $request, Nivel $nivel){
        try{
            $nivel->update($request->only([
                'nombre',
                'activo'
            ]));

            return response()->json([
                'success' => true,
                'data'    => $nivel
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
//        $establecimiento_id = auth()->user()->establishment_id;
//
//        $niveles = DB::connection('tenant')->transaction(function () use ($establecimiento_id) {
//
//            $results = Nivel::with('mesas')
////                ->select('*')
////                ->join('restaurante_mesas','restaurante_mesas.nivel_id','=','restaurante_niveles.id')
////                ->join('restaurante_pedidos','restaurante_pedidos.mesa_id','=','restaurante_mesas.id')
////                ->where('restaurante_mesas.establishment_id','=',$establecimiento_id)
//                ->get();
//
//            return $results;
//        });

        $niveles = Nivel::with('mesas')
            //->where('establishment_id', auth()->user()->establishment_id)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $niveles
        ], 200);
    }

    public function delete(Request $request){
        $item = Nivel::where('id',$request->nivel);
        $item->delete();

        return response()->json([
            'success' => true,
            'data'    => $item,
            'message' => 'Información actualizada'
        ], 200);
    }

}

