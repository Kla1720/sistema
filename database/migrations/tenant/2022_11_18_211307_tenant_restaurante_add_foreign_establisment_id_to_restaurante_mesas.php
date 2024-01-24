<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantRestauranteAddForeignEstablismentIdToRestauranteMesas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('restaurante_mesas', function (Blueprint $table) {
            $table->foreign('establishment_id')->references('id')->on('establishments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('restaurante_mesas', function (Blueprint $table) {
//            $table->dropForeign('restaurante_mesas_establishment_id_foreign')
//                ->dropIndex('restaurante_mesas_establishment_id_foreign');
        });
    }
}
