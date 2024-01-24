<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransporteViajesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transporte_viajes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('terminal_origen_id');
            $table->foreign('terminal_origen_id')->references('id')->on('transporte_terminales');
            $table->time('hora_salida');
            $table->date('fecha_salida');
            $table->unsignedInteger('vehiculo_id');
            $table->foreign('vehiculo_id')->references('id')->on('transporte_vehiculos');
            $table->unsignedInteger('terminal_destino_id');
            $table->foreign('terminal_destino_id')->references('id')->on('transporte_terminales');
            $table->unsignedInteger('programacion_id');
            $table->foreign('programacion_id')->references('id')->on('transporte_programaciones');
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
        Schema::dropIfExists('transporte_viajes');
    }
}
