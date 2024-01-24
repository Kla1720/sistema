<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddForeignDocumentIdRestaurantePedidos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('restaurante_pedidos', function (Blueprint $table) {
            $table->foreign('mesa_id')->references('id')->on('restaurante_mesas');
            $table->foreign('document_id')->references('id')->on('documents');
            $table->foreign('user_id')->references('id')->on('users');
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
            $table->dropForeign('restaurante_pedidos_mesa_id_foreign');
            $table->dropForeign('restaurante_pedidos_document_id_foreign');
            $table->dropForeign('restaurante_pedidos_user_id_foreign');
        });
    }
}
