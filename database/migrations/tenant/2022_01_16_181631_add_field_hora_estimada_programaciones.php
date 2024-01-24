<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldHoraEstimadaProgramaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transporte_programaciones', function (Blueprint $table) {
            //
            $table->integer('tiempo_estimado')->nullable();
            $table->json('destinos_horarios')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transporte_programaciones', function (Blueprint $table) {
            $table->dropColumn('tiempo_estimado');
            $table->dropColumn('destinos_horarios');
        });
    }
}
