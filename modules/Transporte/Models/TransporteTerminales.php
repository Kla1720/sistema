<?php

namespace Modules\Transporte\Models;

use App\Models\Tenant\ModelTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransporteTerminales extends ModelTenant
{
    //
    protected $table = 'transporte_terminales';
    protected $fillable = ['nombre','direccion','destino_id','color'];


    public function destino() : BelongsTo{
        return $this->belongsTo(TransporteDestino::class,'destino_id','id');
    }


    public function programacion_origenes() : HasMany{
        return $this->hasMany(TransporteProgramacion::class,'terminal_origen_id','id');
    }

    public function programacion_destinos() : HasMany{
        return $this->hasMany(TransporteProgramacion::class,'terminal_destino_id','id');
    }


    public function programaciones(){
        return $this->hasMany(TransporteProgramacion::class,'terminal_origen_id','id');
    }
}
