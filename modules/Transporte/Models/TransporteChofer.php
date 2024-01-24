<?php

namespace Modules\Transporte\Models;

use App\Models\Tenant\ModelTenant;

class TransporteChofer extends ModelTenant
{
	protected $table = 'transporte_choferes';

	protected $fillable = ['dni', 'nombre','licencia','categoria'];

	public function getActiveAttribute($value)
	{
		return $value ? true : false;
	}
}
