<?php

namespace Modules\Restaurante\Models;

use App\Models\Tenant\ModelTenant;
use http\Env\Request;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nivel extends ModelTenant
{
    protected $table = 'restaurante_niveles';
    protected $fillable = ['nombre', 'activo'];
    protected $casts = [
        'activo' => 'boolean',
    ];

	public function getActiveAttribute($value)
	{
		return $value ? true : false;
	}

    public function mesas() : HasMany{
        return $this->hasMany(Mesa::class);
    }
}
