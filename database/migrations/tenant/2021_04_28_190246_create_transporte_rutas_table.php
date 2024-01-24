<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransporteRutasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transporte_rutas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('terminal_id');
            $table->foreign('terminal_id')->references('id')->on('transporte_terminales');
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
        Schema::dropIfExists('transporte_rutas');
    }
}
