<?php

namespace Modules\Transporte\Models;

use App\Models\Tenant\Document;
use App\Models\Tenant\ModelTenant;
use App\Models\Tenant\Person;
use App\Models\Tenant\SaleNote;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransporteEncomienda extends ModelTenant
{
    protected $table = 'transporte_encomiendas';
    protected $fillable = [
        'soap_type_id',
        'document_id',
        'note_id',
        'remitente_id',
        'destinatario_id',
        'fecha_salida',
        'programacion_id',
        'estado_envio_id',
        'estado_pago_id',
        'terminal_id',
        'destino_id',
        'destinatario_nombre',
        'clave'
    ];


    public function programacion(){
        return $this->belongsTo(TransporteProgramacion::class,'programacion_id','id');
    }

    public function destinatario(){
        return $this->belongsTo(Person::class,'destinatario_id','id');
    }

    public function remitente(){
        return $this->belongsTo(Person::class,'remitente_id','id');
    }

    public function document() : BelongsTo{
        return $this->belongsTo(Document::class,'document_id','id');
    }
    public function saleNote() : BelongsTo{
        return $this->belongsTo(SaleNote::class,'note_id','id');
    }

    public function estadoPago() : BelongsTo{
        return $this->belongsTo(TransporteEstadoPagoEncomienda::class,'estado_pago_id','id');
    }

    public function estadoEnvio() : BelongsTo{
        return $this->belongsTo(TransporteEstadoEnvio::class,'estado_envio_id','id');
    }

    public function terminal() : BelongsTo{
        return $this->belongsTo(TransporteTerminales::class,'terminal_id','id');
    }

    public function destino() : BelongsTo{
        return $this->belongsTo(TransporteDestino::class,'destino_id','id');
    }
}
