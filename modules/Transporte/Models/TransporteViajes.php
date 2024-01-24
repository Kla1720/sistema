<?php
namespace Modules\Transporte\Models;

use App\Models\Tenant\ModelTenant;

class TransporteViajes extends ModelTenant
{
    //
    protected $table = 'transporte_viajes';

    protected $fillable = [
        'terminal_origen_id',
        'hora_salida',
        'fecha_salida',
        'vehiculo_id',
        'terminal_destino_id',
        'programacion_id'
    ];

    public function origen(){
        return $this->belongsTo(TransporteTerminales::class,'terminal_origen_id');
    }

    public function destino(){
        return $this->belongsTo(TransporteTerminales::class,'terminal_destino_id');
    }

    public function vehiculo(){
        return $this->belongsTo(TransporteVehiculo::class,'vehiculo_id');
    }
}
