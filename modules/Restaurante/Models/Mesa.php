<?php

namespace Modules\Restaurante\Models;

use App\Models\Tenant\ModelTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mesa extends ModelTenant
{
    protected $table = 'restaurante_mesas';
    protected $fillable = ['numero','nivel_id', 'activo','estado'];
    protected $casts = [
        'activo' => 'boolean',
    ];

	public function getActiveAttribute($value)
	{
		return $value ? true : false;
	}

    public function nivel() : BelongsTo{
        return $this->belongsTo(Nivel::class,'nivel_id','id');
    }

    public function pedido() : BelongsTo{
        return $this->hasMany(Pedido::class);
    }
}
