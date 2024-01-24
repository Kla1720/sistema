<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsTransporteRutas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transporte_rutas', function (Blueprint $table) {
            //
            $table->time('hora_salida');
            $table->integer('orden');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transporte_rutas', function (Blueprint $table) {
            //
            $table->dropColumn('hora_salida');
            $table->dropColumn('orden');
        });
    }
}
