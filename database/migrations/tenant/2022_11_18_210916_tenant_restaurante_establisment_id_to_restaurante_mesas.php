<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantRestauranteEstablismentIdToRestauranteMesas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('restaurante_mesas', function (Blueprint $table) {
            $table->integer('establishment_id')->default(1)->unsigned();
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
            $table->dropColumn('establishment_id');
        });
    }
}
