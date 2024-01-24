<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddForeignPedidoIdToRestaurantePedidosDetalle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('restaurante_pedidos_detalles', function (Blueprint $table) {
            $table->foreign('pedido_id')->references('id')->on('restaurante_pedidos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('restaurante_pedidos_detalle', function (Blueprint $table) {
            //
        });
    }
}
