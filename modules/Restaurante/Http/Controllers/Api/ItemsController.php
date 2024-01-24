<?php

namespace Modules\Restaurante\Http\Controllers\Api;

use App\Models\Tenant\Catalogs\AffectationIgvType;
use App\Models\Tenant\Item;
use App\Models\Tenant\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Inventory\Models\ItemWarehouse;

class ItemsController extends Controller
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
        $affectation_igv_types = AffectationIgvType::whereActive()->get();
        $establishment_id = auth()->user()->establishment_id;
        $warehouse = Warehouse::where('establishment_id', $establishment_id)->first();

        $items = Item::with(['brand', 'category'])
            ->whereWarehouse()
            ->whereHasInternalId()
            // ->whereNotIsSet()
            ->whereIsActive()
            ->orderBy('description')
            ->take(20)
            ->get()
            ->transform(function($row) use($warehouse){
                $full_description = ($row->internal_id)?$row->internal_id.' - '.$row->description:$row->description;

                return [
                    'id' => $row->id,
                    'item_id' => $row->id,
                    'name' => $row->name,
                    'full_description' => $full_description,
                    'description' => $row->description,
                    'currency_type_id' => $row->currency_type_id,
                    'internal_id' => $row->internal_id,
                    'item_code' => $row->item_code,
                    'currency_type_symbol' => $row->currency_type->symbol,
                    'sale_unit_price' => number_format( $row->sale_unit_price, 2),
                    'purchase_unit_price' => $row->purchase_unit_price,
                    'unit_type_id' => $row->unit_type_id,
                    'sale_affectation_igv_type_id' => $row->sale_affectation_igv_type_id,
                    'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
                    'calculate_quantity' => (bool) $row->calculate_quantity,
                    'has_igv' => (bool) $row->has_igv,
                    'is_set' => (bool) $row->is_set,
                    'aux_quantity' => 1,
                    'brand' => $row->brand->name,
                    'category' => $row->brand->name,
                    'stock' => $row->unit_type_id!='ZZ' ? ItemWarehouse::where([['item_id', $row->id],['warehouse_id', $warehouse->id]])->first()->stock : '0',
                    'image' => $row->image != "imagen-no-disponible.jpg" ? url("/storage/uploads/items/" . $row->image) : url("/logo/" . $row->image),

                ];
            });


        return [
            'success' => true,
            'data' => array('items' => $items, 'affectation_types' => $affectation_igv_types)
        ];
    }
}
