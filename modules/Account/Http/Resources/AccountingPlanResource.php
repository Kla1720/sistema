<?php

namespace Modules\Account\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountingPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'id' => $this->id,
            'cuenta'=> $this->cuenta,
            'nombre'=> $this->nombre,
            'nivel'=> $this->nivel,
            'tipo'=> $this->tipo,
            'analisis'=> $this->analisis,
            'currency_type_id'=> $this->currency_type_id,
            'balance_comprobacion'=> $this->balance_comprobacion,
            'cuenta_equivalente'=> $this->cuenta_equivalente,
            'amarre_debe'=> $this->amarre_haber,
            'amarre_haber'=> $this->amarre_haber,
            'enabled'=> $this->enabled,
        ];
    }
}
