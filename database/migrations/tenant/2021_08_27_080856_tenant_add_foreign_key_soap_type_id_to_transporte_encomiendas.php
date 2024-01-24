<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddForeignKeySoapTypeIdToTransporteEncomiendas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transporte_encomiendas', function (Blueprint $table) {
            $table->foreign('soap_type_id')->references('id')->on('soap_types');
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
            //
        });
    }
}
