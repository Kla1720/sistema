<?php

namespace Modules\Account\Models;

use App\Models\Tenant\ModelTenant;
use App\Models\Tenant\Catalogs\CurrencyType;

class AccountingPlan extends ModelTenant
{

    protected $with = ['currency_type'];

    protected $fillable = [
        'cuenta',
        'nombre',
        'nivel',
        'tipo',
        'analisis',
        'currency_type_id',
        'state',
    ];

    public function currency_type()
    {
        return $this->belongsTo(CurrencyType::class, 'currency_type_id');
    }


}
