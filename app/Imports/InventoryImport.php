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

class InventoryImport implements ToCollection
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
                    $itemInv = Inventory::where('item_id','=', $item->id)
                        ->Where('type','=',1)
                        ->Where('warehouse_id','=',$warehouse_id)
                        ->first();
                }
                else{
                    $noexiste.=' '.$internal_id. '<br>';
                }

            }

            if(!$itemInv && $item && $internal_id!="") {   //crea nuevos productos

                $inventory = new Inventory();
                $inventory->type = 1;
                $inventory->description = 'Stock inicial';
                $inventory->item_id = $item->id;
                $inventory->warehouse_id = $warehouse_id;
                $inventory->quantity = $quantity;
                $inventory->save();


                if (!$this->checkInventory($item->id, $warehouse_id)) {
                    $this->createInitialInventory($item->id, $quantity, $warehouse_id);
                }

                $registered += 1;

            }
            else if($itemInv && $item && $internal_id!="")
            { //actualiza stock

                if($this->checkInventory($item->id, $warehouse_id)) {
                    $this->updateStockInitial($item->id, $quantity, $warehouse_id);

                    $kardex=Kardex::where('item_id','=',$item->id)
                        ->whereNull('type')
                        ->whereNull('document_id')
                        ->whereNull('purchase_id')
                        ->whereNull('sale_note_id')
                        ->first();

                    if($kardex){
                        $kardex = Kardex::findOrFail($kardex->id);
                        $kardex->quantity =$quantity;
                        $kardex->save();
                    }

                }
                else{
                    $noexiste.=' '.$internal_id. '<br>';
                }

                $registered += 1;

            }
        }
        $this->data = compact('total', 'registered','noexiste');

    }

    public function getData()
    {
        return $this->data;
    }
}
