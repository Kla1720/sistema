<?php

namespace Modules\Restaurante\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Restaurante\Models\Nivel;

class NivelesController extends Controller
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
        $nivel = new Nivel();
        $data = $request->all();
        $nivel->fill($data);
        $nivel->save();

        return [
            'success' => true,
            'message' => 'Nivel agregado con corréctamente',
            'id' => $nivel->id
        ];
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
    public function update(Request $request)
    {
        $id = $request->id;
        $nivel = Nivel::findOrFail($id);
        $data = $request->all();
        $nivel->fill($data);
        $nivel->save();

        return [
            'success' => true,
            'message' => 'Nivel editado con éxito',
        ];
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request)
    {
        try {

            $item = Nivel::findOrFail($request->id);
            $item->delete();

            return [
                'success' => true,
                'message' => 'Nivel eliminado con éxito'
            ];

        } catch (Exception $e) {

            return [
                'success' => false,
                'message' =>  $e->getMessage()
            ];

        }
    }
    public function records()
    {
        $nivel = Nivel::orderBy('id','DESC')->get()->transform(function($row) {
            return [
                'id' => $row->id,
                'activo' => $row->activo,
                'nombre' => $row->nombre
            ];
        });

        return [
            'success' => true,
            'nivel' => $nivel
        ];

    }
}
