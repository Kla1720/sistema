<?php

namespace Modules\Restaurante\Models;

use App\Models\Tenant\Document;
use App\Models\Tenant\ModelTenant;
use App\Models\Tenant\Person;
use App\Models\Tenant\SaleNote;
use App\Models\Tenant\User;
use http\Env\Request;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pedido extends ModelTenant
{

    protected $table = 'restaurante_pedidos';
    protected $fillable = ['mesa_id', 'document_id','user_id','tipo','estado','establishment_id'];

    public function pedido_detalle(){
        return $this->hasMany(PedidoDetalle::class);
    }

    public function mesa(){
        return $this->belongsTo(Mesa::class, 'mesa_id','id');
    }

    public function mozo(){
        return $this->belongsTo(User::class, 'user_id','id');
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

}
