<?php
namespace Modules\Transporte\Http\Controllers;

use App\Models\System\User as SystemUser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Transporte\Models\TransporteUserTerminal;
use Modules\Transporte\Http\Requests\TransporteUserTerminalRequest;
use App\Models\Tenant\User;
use Modules\Transporte\Models\TransporteTerminales;
use Symfony\Component\Console\Terminal;

class TransporteUsersTerminalController extends Controller
{
	/**
	 * Display a listing of the resource.
	 * @return Response
	 */
	public function index()
	{

		$usuarios_terminales = TransporteUserTerminal::with('user')->orderBy('id', 'DESC')
		->get();

        return view('transporte::usuariosterminales.index', compact('usuarios_terminales'));
	}

	/**
	 * Store a newly created resource in storage.
	 * @param Request $request
	 * @return Response
	 */
	public function store(TransporteUserTerminalRequest $request)
	{

		try{
			$registro = TransporteUserTerminal::where('user_id',$request->user_id)->first();
            if($registro){
                throw new Exception('Ya exite terminal asignado a este usuario, edite en el listado.',888);
            } 
            $usuario_terminal = TransporteUserTerminal::create($request->only('terminal_id','user_id'));
			$usuario_terminal->terminal;
			$usuario_terminal->user;
			return response()->json([
				'success' => true,
				'data'    => $usuario_terminal
			], 200);
        }catch(\Throwable $th){
            return response()->json([
                'success' => false,
                'message' => $th->getCode() == 888 ? $th->getMessage() : 'Ocurrió un error al procesar su petición'
            ],500);
        }
	
	}

	/**
	 * Update the specified resource in storage.
	 * @param Request $request
	 * @param int $id
	 * @return Response
	 */
	public function update(TransporteUserTerminalRequest $request, TransporteUserTerminal $usuario_terminal)
	{

		$usuario_terminal->update($request->only([
            'terminal_id',
            'user_id'
        ]));
        $usuario_terminal->user;

        return response()->json([
            'success' => true,
            'message' => 'Información actualizada',
            'data'    => $usuario_terminal
        ]);
	}

	/**
	 * Remove the specified resource from storage.
	 * @param int $id
	 * @return Response
	 */
	public function destroy($id)
	{
		try {

			TransporteUserTerminal::where('id', $id)
				->delete();

			return response()->json([
				'success' => true,
				'message' => 'Registro Eliminado'
			], 200);
		} catch (\Throwable $th) {
			return response()->json([
				'success' => false,
				'message'    => $th->getCode() == 888 ? $th->getMessage() : 'Ocurrió un error al procesar su petición. Detalles: ' . $th->getMessage()
			], 500);
		}
	}

	public function getTables()
	{
		$usuarios = User::get();
		$terminales = TransporteTerminales::get();

		return response()->json([
			'success' => true,
			'usuarios'   => $usuarios,
			'terminales'   => $terminales,
		], 200);
	}

}
