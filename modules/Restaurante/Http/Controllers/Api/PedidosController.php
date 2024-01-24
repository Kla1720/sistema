<?php

namespace Modules\Restaurante\Http\Controllers\Api;

use App\Models\Tenant\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Restaurante\Models\Pedido;

class PedidosController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('restaurante::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('restaurante::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('restaurante::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('restaurante::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
    public function  records(){

        $establishment_id = auth()->user()->establishment_id;
        $warehouse = Warehouse::where('establishment_id', $establishment_id)->first();

        $pedidos = Pedido::where('estado',1)
            ->take(20)
            ->get()
            ->transform(function($row){
                    return [
                    'id' => $row->id,
                    'mesa_id' => $row->mesa_id,
                    'document_id' => $row->document_id,
                    'user_id' => $row->user_id,
                    'tipo' => $row->tipo
                ];
            });


        return [
            'success' => true,
            'data' => array('pedidos' => $pedidos)
        ];
    }
}
