<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransporteVehiculoAsientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transporte_vehiculo_asientos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('numero_asiento');
            $table->smallInteger('piso');
            $table->unsignedInteger('vehiculo_id');
            $table->foreign('vehiculo_id','tva_tv_foreign_id')->references('id')->on('transporte_vehiculos');
            // $table->unsignedInteger('estado_asiento_id');
            // $table->foreign('estado_asiento_id','tva_tea_foreign_id')->references('id')->on('transporte_estado_asientos');
            $table->string('left')->default('50px');
            $table->string('top')->default('50px');
            $table->string('type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transporte_vehiculo_asientos');
    }
}
