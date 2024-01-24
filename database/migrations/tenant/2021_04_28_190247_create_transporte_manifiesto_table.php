<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransporteManifiestoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transporte_manifiesto', function (Blueprint $table) {
            $table->increments('id');
            $table->string('serie');
            $table->string('numero');
            $table->string('tipo');
            $table->unsignedInteger('chofer_id');
            $table->foreign('chofer_id')->references('id')->on('transporte_choferes');
            $table->unsignedInteger('copiloto_id');
            $table->foreign('copiloto_id')->references('id')->on('transporte_choferes');
            $table->date('fecha');
            $table->time('hora');
            $table->string('observaciones')->nullable();
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
        Schema::dropIfExists('transporte_manifiesto');
    }
}
