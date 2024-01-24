<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantDeleteForeignDeleteToRestaurantePedidos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::disableForeignKeyConstraints();
        Schema::table('restaurante_pedidos', function (Blueprint $table) {
            $table->dropForeign('restaurante_pedidos_document_id_foreign')
                ->dropIndex('restaurante_pedidos_document_id_foreign');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('restaurante_pedidos', function (Blueprint $table) {
            //
        });
    }
}
