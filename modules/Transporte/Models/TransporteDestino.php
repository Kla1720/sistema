<?php

namespace Modules\Transporte\Models;

use App\Models\Tenant\Catalogs\District;
use App\Models\Tenant\ModelTenant;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransporteDestino extends ModelTenant
{
	protected $table = 'transporte_destinos';

	protected $fillable = ['id','nombre'];


	public function terminales(){
		return $this->hasMany(TransporteTerminales::class,'destino_id','id');
	}

}
