<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransporteProgramacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transporte_programaciones', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('terminal_origen_id');
            $table->foreign('terminal_origen_id','tp_tt_foreign_id')->references('id')->on('transporte_terminales');
            // $table->date('fecha_salida');
            $table->time('hora_salida');
            $table->unsignedInteger('vehiculo_id');
            $table->foreign('vehiculo_id','tp_tv_foreign_id')->references('id')->on('transporte_vehiculos');
            $table->unsignedInteger('terminal_destino_id');
            $table->foreign('terminal_destino_id','tp_td_foreign_id')->references('id')->on('transporte_terminales');
            // $table->time('tiempo_aproximado');
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
        Schema::dropIfExists('transporte_programaciones');
    }
}
