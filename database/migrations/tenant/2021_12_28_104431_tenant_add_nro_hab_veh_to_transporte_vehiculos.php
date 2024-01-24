<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddNroHabVehToTransporteVehiculos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transporte_vehiculos', function (Blueprint $table) {
            $table->string('nro_hab_veh')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transporte_vehiculos', function (Blueprint $table) {
            $table->dropColumn('nro_hab_veh');
        });
    }
}
