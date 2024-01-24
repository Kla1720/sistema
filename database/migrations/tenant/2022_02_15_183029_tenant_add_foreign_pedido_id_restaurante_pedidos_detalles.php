<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddForeignPedidoIdRestaurantePedidosDetalles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('restaurante_pedidos_detalles', function (Blueprint $table) {
            $table->foreign('pedido_id')->references('id')->on('restaurante_pedidos');
            $table->foreign('producto_id')->references('id')->on('items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('restaurante_pedidos', function (Blueprint $table) {
            $table->dropForeign('restaurante_pedidos_detalles_pedido_id_foreign');
            $table->dropForeign('restaurante_pedidos_detalles_producto_id_foreign');
        });
    }
}
