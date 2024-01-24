<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransporteDestinosHorariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transporte_destinos_horarios', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('origen_id');
            $table->foreign('origen_id')->references('id')->on('transporte_terminales');
            $table->unsignedInteger('destino_id');
            $table->foreign('destino_id')->references('id')->on('transporte_terminales');
            $table->double('tiempo_estimado');
            $table->unsignedInteger('programacion_id')->references('id')->on('transporte_programaciones');
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
        Schema::dropIfExists('transporte_destinos_horarios');
    }
}
