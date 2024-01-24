<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsTransportePasajes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transporte_pasajes', function (Blueprint $table) {
            //1 tipo libre
            //2 tipo normal(croquis)
            $table->date('fecha_salida')->change();
            $table->time('hora_salida');
            $table->unsignedInteger('asiento_id')->nullable()->change();
            $table->integer('tipo_venta')->after('fecha_salida');
            $table->integer('numero_asiento')->after('tipo_venta')->nullable();
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
            $table->dropColumn('hora_salida');
            $table->dropColumn('tipo_venta');
            $table->dropColumn('numero_asiento');
        });
    }
}
