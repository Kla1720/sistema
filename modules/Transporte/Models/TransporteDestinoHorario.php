<?php

namespace Modules\Transporte\Models;

use App\Models\Tenant\ModelTenant;

class TransporteDestinoHorario extends ModelTenant
{
    //

    protected $table = 'transporte_destinos_horarios';

    protected $fillable = [
        'origen_id',
        'destino_id',
        'programacion_id',
        'tiempo_estimado'
    ];
}
