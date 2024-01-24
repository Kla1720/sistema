<?php

namespace Modules\Transporte\Models;

use App\Models\Tenant\ModelTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class TransporteProgramacion extends ModelTenant
{
    //

    protected $table = 'transporte_programaciones';
    protected $fillable = [
        'terminal_destino_id',
        'terminal_origen_id',
        'vehiculo_id',
        'fecha_salida',
        'hora_salida',
        'tiempo_estimado',
        'programacion_id',
        'hidden',
        'active',
        'destinos_horarios'
    ];

    protected $appends = [
        'destino_horarios'
    ];


    public function origen() : BelongsTo{
        return $this->belongsTo(TransporteTerminales::class,'terminal_origen_id','id');
    }

    public function destino() : belongsTo{
        return $this->belongsTo(TransporteTerminales::class,'terminal_destino_id','id');
    }

    public function vehiculo() : BelongsTo{
        return $this->belongsTo(TransporteVehiculo::class,'vehiculo_id','id');
    }

    

    public function encomiendas() : HasMany{
        return $this->hasMany(TransporteEncomienda::class,'programacion_id','id');
    }

    public function pasajes() : HasMany{
        return $this->hasMany(TransportePasaje::class,'programacion_id','id');
    }

    public function manifiestos() : HasMany{
        return $this->hasMany(TransporteManifiesto::class,'programacion_id','id');
    }

    /*
        esta funcion la uso para traer todas las programaciones que sean iguales 
        o mayores a la fecha aca
    */
    public function scopeWhereEqualsOrBiggerDate($query,$now = null){
        $now = is_null($now) ? date('Y-m-d') : $now;
        $month = (int) date('m');
        return $query->whereRaw("DATE_FORMAT(NOW(),'%Y-%m-%d') <= '{$now}'")
        ->whereMonth(DB::raw("NOW()"),$month);
    }


    public function rutas() : BelongsToMany{
        return $this->belongsToMany(TransporteTerminales::class,'transporte_rutas', 'programacion_id','terminal_id')
        ->withPivot('hora_salida','orden')
        ->orderBy('orden');
    }

    public function programacion() : BelongsTo{
        return $this->belongsTo(self::class);
    }

    public function programaciones() : HasMany{
        return $this->hasMany(self::class,'programacion_id');
    }

    public function routes() : HasMany{
        DB::connection('tenant')->statement("SET sql_mode = '' ");
        return $this->hasMany(self::class,'programacion_id')
        ->where('terminal_destino_id','!=',$this->terminal_destino_id)
        ->groupBy('terminal_origen_id');
    }

    public function syncRutas(array $rutas){

        TransporteRuta::where('programacion_id',$this->id)
        ->delete();

        foreach($rutas as $ruta){
            TransporteRuta::create([
                'terminal_id' => $ruta,
                'programacion_id' => $this->id
            ]);
        }
    }


    

    public function getDestinoHorariosAttribute(){
        if(is_null($this->destinos_horarios)) return [];
        return json_decode($this->destinos_horarios,true);
    }


    
}
