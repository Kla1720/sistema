<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeingDestinoIdPasajes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transporte_pasajes', function (Blueprint $table) {
            //
            $table->unsignedInteger('origen_id');
            $table->foreign('origen_id')->references('id')->on('transporte_terminales');
            $table->unsignedInteger('destino_id');
            $table->foreign('destino_id')->references('id')->on('transporte_destinos');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transporte_pasajes', function (Blueprint $table) {
            //
            $table->dropForeign('origen_id');
            $table->dropColumn('origen_id');
            $table->dropForeign('destino_id');
            $table->dropColumn('destino_id');
        });
    }
}
