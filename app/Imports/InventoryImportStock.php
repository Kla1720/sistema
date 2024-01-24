<?php

namespace App\Imports;

use App\Models\Tenant\Item;
use App\Models\Tenant\Warehouse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Modules\Inventory\Models\Inventory;
use Modules\Inventory\Models\InventoryKardex;
use Modules\Inventory\Models\ItemWarehouse;
use Modules\Inventory\Traits\InventoryTrait;
use App\Models\Tenant\Kardex;

class InventoryImportStock implements ToCollection
{
    use Importable,InventoryTrait;

    protected $data;

    public function collection(Collection $rows)
    {
        $total = count($rows);
        $registered = 0;
        $noexiste="";
        $itemInv=null;
        unset($rows[0]);
        foreach ($rows as $row)
        {
            $internal_id  = $row[0];
            $warehouse_id =$row[1];
            $quantity     = $row[2];

            if($internal_id && $internal_id!="") {
                $item=Item::where('internal_id','=',$internal_id)
                    ->first();

               if($item){
                   $get_item_warehouse=ItemWarehouse::where('item_id','=',$item->id)
                       ->where('warehouse_id','=',$warehouse_id)
                       ->first();

                   if($get_item_warehouse){
                       $item_warehouse = ItemWarehouse::findOrFail($get_item_warehouse->id);
                       $item_warehouse->stock = $quantity;
                       $item_warehouse->save();

                       $registered += 1;
                   }
               }
               else{
                   $noexiste.=' '.$internal_id. '<br>';
               }

            }
        }
        $this->data = compact('total', 'registered','noexiste');

    }

    public function getData()
    {
        return $this->data;
    }
}
