<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransportePasajesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transporte_pasajes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('document_id');
            $table->foreign('document_id')->references('id')->on('documents');
            $table->unsignedInteger('pasajero_id');
            $table->foreign('pasajero_id')->references('id')->on('persons');
            $table->double('precio');
            $table->unsignedInteger('asiento_id');
            $table->foreign('asiento_id','tp_tva_foreign_id')->references('id')->on('transporte_vehiculo_asientos');
            $table->unsignedInteger('estado_asiento_id');
            $table->foreign('estado_asiento_id')->references('id')->on('transporte_estado_asientos');
            $table->dateTime('fecha_salida');
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
        Schema::dropIfExists('transporte_pasajes');
    }
}
