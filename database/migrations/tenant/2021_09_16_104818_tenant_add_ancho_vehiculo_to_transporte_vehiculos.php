<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddAnchoVehiculoToTransporteVehiculos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transporte_vehiculos', function (Blueprint $table) {
            $table->integer('ancho_vehiculo')->default(1073);
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
            $table->dropColumn('ancho_vehiculo');
        });
    }
}
