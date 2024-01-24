<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantFixDestinoToTransporteEncomiendas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transporte_encomiendas', function (Blueprint $table) {
            $table->dropForeign(['destino_id']);
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
        Schema::table('transporte_encomiendas', function (Blueprint $table) {
            //
        });
    }
}
