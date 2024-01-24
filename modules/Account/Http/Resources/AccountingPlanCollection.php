<?php

namespace Modules\Account\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AccountingPlanCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function toArray($request)
    {
        return $this->collection->transform(function($row, $key) {
            return [
                'id' => $row->id,
                'cuenta' => $row->cuenta,
                'nombre' => $row->nombre,
                'nivel' => $row->nivel,
                'tipo' => $row->tipo,
                'analisis' => $row->analisis,
                'currency_type_id' => $row->currency_type_id,
                'balance_comprobacion'=> $row->balance_comprobacion,
                'cuenta_equivalente'=> $row->cuenta_equivalente,
                'amarre_debe' => $row->amarre_debe,
                'amarre_haber' => $row->amarre_haber,
                'enabled'=> (bool) $row->enabled,
//                'created_at' => $row->created_at->format('Y-m-d H:i:s'),
//                'updated_at' => $row->updated_at->format('Y-m-d H:i:s'),
            ];
        });
    }
}
