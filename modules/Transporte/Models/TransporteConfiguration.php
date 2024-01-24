<?php

namespace Modules\Transporte\Models;

use App\Models\Tenant\ModelTenant;

class TransporteConfiguration extends ModelTenant
{
    protected $fillable = [
        'pasaje_afecto_igv',
        'encomienda_afecto_igv',
    ];

    public $timestamps = false;

    public function getCollectionData() {
        return [
            'pasaje_afecto_igv' => (bool)$this->pasaje_afecto_igv,
            'encomienda_afecto_igv' => (bool)$this->encomienda_afecto_igv,
        ];
    }
}
