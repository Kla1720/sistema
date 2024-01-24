<?php

namespace Modules\Restaurante\Models;

use App\Models\Tenant\Item;
use App\Models\Tenant\ModelTenant;
use http\Env\Request;

class PedidoDetalle extends ModelTenant
{
    protected $table = 'restaurante_pedidos_detalles';
    protected $fillable = ['pedido_id', 'producto_id','cantidad','precio','impreso','dividir'];

    protected $casts = [
        'cantidad' => 'integer',
        'precio' => 'decimal:2',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
    public function item()
    {
        return $this->belongsTo(Item::class, 'producto_id', 'id');
    }

}
