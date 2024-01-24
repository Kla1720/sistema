<?php

namespace Modules\Transporte\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Transporte\Models\TransporteChofer;
use Modules\Transporte\Http\Requests\TransporteChoferRequest;

class TransporteChoferController extends Controller
{
	/**
	 * Display a listing of the resource.
	 * @return Response
	 */
	public function index()
	{
        $choferes = TransporteChofer::orderBy('id', 'DESC')
			->get();

		return view('transporte::choferes.index', compact('choferes'));
	}

	/**
	 * Store a newly created resource in storage.
	 * @param Request $request
	 * @return Response
	 */
	public function store(TransporteChoferRequest $request)
	{
		$rate = TransporteChofer::create($request->only('dni', 'nombre','licencia','categoria'));

		return response()->json([
			'success' => true,
			'data'    => $rate
		], 200);
	}

	/**
	 * Update the specified resource in storage.
	 * @param Request $request
	 * @param int $id
	 * @return Response
	 */
	public function update(TransporteChoferRequest $request, $id)
	{
		$rate = TransporteChofer::findOrFail($id);
		$rate->fill($request->only('dni', 'nombre','licencia','categoria'));
		$rate->save();

		return response()->json([
			'success' => true,
			'data'    => $rate
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
			TransporteChofer::where('id', $id)
				->delete();

			return response()->json([
				'success' => true,
				'message' => 'Información actualizada'
			], 200);
		} catch (\Throwable $th) {
			return response()->json([
				'success' => false,
				'data'    => 'Ocurrió un error al procesar su petición. Detalles: ' . $th->getMessage()
			], 500);
		}
	}
}
