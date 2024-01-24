<?php

namespace Modules\Transporte\Models;

use App\Models\Tenant\ModelTenant;

class District extends ModelTenant
{
    protected $table = 'districts';

    protected $fillable = ['province_id','description','active'];
}
