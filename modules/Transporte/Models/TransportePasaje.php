<?php

namespace Modules\Transporte\Models;

use App\Models\System\Client;
use App\Models\Tenant\Document;
use App\Models\Tenant\ModelTenant;
use App\Models\Tenant\Person;
use App\Models\Tenant\SaleNote;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Tenant\User;

class TransportePasaje extends ModelTenant
{
    //
    protected $table = 'transporte_pasajes';
    protected $casts = [
        'asistencia' => 'boolean',
    ];
    protected $fillable = [
        'soap_type_id',
        'document_id',
        'note_id',
        'pasajero_id',
        'asiento_id',
        'precio',
        'fecha_salida',
        'programacion_id',
        'estado_asiento_id',
        'tipo_venta',
        'numero_asiento',
        'hora_salida',
        'destino_id',
        'origen_id',
        'cliente_id',
        'nombre_pasajero',
        'telefono',
        'user_name',
        'sucursal_id',
        'color',
        'user_id',
        'viaje_id',
        'ninios',
        'asistencia'
    ];


    public function programacion() : BelongsTo{
        return $this->belongsTo(TransporteProgramacion::class,'programacion_id','id');
    }

    public function asiento() : BelongsTo{
        return $this->belongsTo(TransporteAsiento::class,'asiento_id','id');
    }

    public function pasajero() : BelongsTo{
        return $this->belongsTo(Person::class,'pasajero_id','id');
    }

    public function document() : BelongsTo{
        return $this->belongsTo(Document::class,'document_id','id');
    }
    public function cliente() : BelongsTo{
        return $this->belongsTo(Person::class,'cliente_id','id');
    }
    public function saleNote() : BelongsTo{
        return $this->belongsTo(SaleNote::class,'note_id','id');
    }

    public function origen() : BelongsTo{
        return $this->belongsTo(TransporteTerminales::class,'origen_id');
    }

    public function destino() : BelongsTo{
        return $this->belongsTo(TransporteTerminales::class,'destino_id');
    }

    public function viaje() : BelongsTo{
        return $this->belongsTo(TransporteViajes::class,'viaje_id','id');
    }
}
