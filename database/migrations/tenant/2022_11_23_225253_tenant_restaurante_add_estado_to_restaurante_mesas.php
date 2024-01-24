<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantRestauranteAddEstadoToRestauranteMesas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('restaurante_mesas', function (Blueprint $table) {
            $table->integer('estado')->default(0)->comment('0=libre; 1=ocupado');
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
            $table->dropColumn('estado');
        });
    }
}
