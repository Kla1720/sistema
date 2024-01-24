<?php

namespace Modules\Transporte\Models;
use App\Models\Tenant\ModelTenant;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransporteVehiculo extends ModelTenant
{
    protected $table = 'transporte_vehiculos';
    protected $appends = [
        'img_front',
        'img_back'
    ];

    protected $fillable = ['placa','nombre','asientos','pisos','image_front','image_back','ancho_vehiculo','nro_hab_veh'];


    public function getImgFrontAttribute(){

        if(!is_null($this->image_front)) return asset('storage\\images\\'.$this->image_front);
        return null;

    }

    public function getImgBackAttribute(){
        if(!is_null($this->image_back)) return asset('storage\\images\\'.$this->image_back);

        return null;

    }

    public function seats() : HasMany{
        return $this->hasMany(TransporteAsiento::class,'vehiculo_id','id');
    }

    public function asientos() : HasMany{
        return $this->hasMany(TransporteAsiento::class,'vehiculo_id','id');
    }

    public function programaciones() : HasMany{
        return $this->hasMany(TransporteProgramacion::class,'vehiculo_id');
    }

    public function viajes() : HasMany{
        return $this->hasMany(TransporteViajes::class,'vehiculo_id');
    }

}
