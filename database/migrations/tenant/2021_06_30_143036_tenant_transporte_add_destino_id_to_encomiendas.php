<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantTransporteAddDestinoIdToEncomiendas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transporte_encomiendas', function (Blueprint $table) {
            $table->unsignedInteger('terminal_id')->after('programacion_id');
            $table->foreign('terminal_id')->references('id')->on('transporte_terminales');
            $table->unsignedInteger('destino_id')->after('terminal_id');
            $table->foreign('destino_id')->references('id')->on('transporte_terminales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transporte_encomiendas', function (Blueprint $table) {
            $table->dropColumn('terminal_id');
            $table->dropColumn('destino_id');
        });
    }
}
