<?php

namespace Modules\Transporte\Models;

use App\Models\Tenant\ModelTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Transporte\Models\TransporteDestino;
use Modules\Transporte\Models\TransporteProgramacion;

class TransporteRuta extends ModelTenant
{
    //

    protected $table = 'transporte_rutas';
    protected $fillable = ['terminal_id','programacion_id','hora_salida','orden'];

    public function terminal() : BelongsTo{
        return $this->belongsTo(TransporteDestino::class,'terminal_id','id');
    }

    public function programacion() : BelongsTo{
        return $this->belongsTo(TransporteProgramacion::class,'programacion_id','id');
    }
}
