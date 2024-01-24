<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldNameTransportePasajes extends Migration
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
            $table->string('nombre_pasajero')->after('estado_asiento_id')->nullable();
            $table->unsignedInteger('document_id')->nullable()->change();
            $table->unsignedInteger('pasajero_id')->nullable()->change();
            
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
            $table->dropColumn('nombre')->after('numero_asiento');
            
            
        });
    }
}
