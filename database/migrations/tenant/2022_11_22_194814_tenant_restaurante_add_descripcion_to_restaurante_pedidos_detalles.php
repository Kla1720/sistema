<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantRestauranteAddDescripcionToRestaurantePedidosDetalles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('restaurante_pedidos_detalles', function (Blueprint $table) {
            $table->string('descripcion',150)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('restaurante_pedidos_detalles', function (Blueprint $table) {
            $table->dropColumn('descripcion');
        });
    }
}
