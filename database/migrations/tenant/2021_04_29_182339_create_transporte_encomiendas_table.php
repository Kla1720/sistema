<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransporteEncomiendasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transporte_encomiendas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('document_id');
            $table->foreign('document_id')->references('id')->on('documents');
            $table->string('descripcion')->nullable();
            // $table->double('precio');
            $table->date('fecha_salida')->nullable();
            $table->unsignedInteger('remitente_id')->index();
            $table->foreign('remitente_id')->references('id')->on('persons');
            $table->unsignedInteger('destinatario_id');
            $table->foreign('destinatario_id')->references('id')->on('persons');
            $table->unsignedInteger('estado_pago_id');
            $table->foreign('estado_pago_id','te_tep_foreign_id')->references('id')->on('transporte_estado_encomienda');
            $table->unsignedInteger('estado_envio_id');
            $table->foreign('estado_pago_id','te_tepe_foreign_id')->references('id')->on('transporte_estado_pago_encomienda');
            $table->unsignedInteger('programacion_id')->nullable();
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
        Schema::dropIfExists('transporte_encomiendas');
    }
}
